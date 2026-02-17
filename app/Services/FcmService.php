<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FcmService
{
    protected $projectId;
    protected $serviceAccountPath;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->serviceAccountPath = storage_path('app/firebase/service-account.json');
    }

    /**
     * Send notification to a specific user (all their devices)
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $tokens = FcmToken::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::info("No FCM tokens found for user {$userId}");
            return false;
        }

        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount > 0;
    }

    /**
     * Send notification to a specific token
     */
    public function sendToToken($token, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('Failed to get Firebase access token');
                return false;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_merge($data, [
                        'click_action' => $data['click_action'] ?? config('app.url'),
                    ]),
                    'webpush' => [
                        'fcm_options' => [
                            'link' => $data['click_action'] ?? config('app.url'),
                        ],
                        'notification' => [
                            'icon' => $data['icon'] ?? asset('favicon.png'),
                            'badge' => $data['badge'] ?? asset('favicon.png'),
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $this->markTokenAsUsed($token);
                Log::info('FCM notification sent successfully', ['token' => substr($token, 0, 20) . '...']);
                return true;
            } else {
                $error = $response->json();
                Log::error('FCM notification failed', [
                    'token' => substr($token, 0, 20) . '...',
                    'error' => $error,
                ]);

                // If token is invalid, mark as inactive
                if (isset($error['error']['status']) &&
                    in_array($error['error']['status'], ['NOT_FOUND', 'INVALID_ARGUMENT', 'UNREGISTERED'])) {
                    $this->invalidateToken($token);
                }

                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM exception', [
                'message' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);
            return false;
        }
    }

    /**
     * Get OAuth2 access token for Firebase
     */
    protected function getAccessToken()
    {
        try {
            if (!file_exists($this->serviceAccountPath)) {
                Log::error('Firebase service account file not found at: ' . $this->serviceAccountPath);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);

            if (!$serviceAccount) {
                Log::error('Failed to parse Firebase service account JSON');
                return null;
            }

            // Create JWT token
            $now = time();
            $exp = $now + 3600; // 1 hour

            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT',
            ];

            $payload = [
                'iss' => $serviceAccount['client_email'],
                'sub' => $serviceAccount['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $exp,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ];

            $jwt = $this->createJWT($header, $payload, $serviceAccount['private_key']);

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            } else {
                Log::error('Failed to exchange JWT for access token', ['response' => $response->json()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception getting Firebase access token', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create JWT token
     */
    protected function createJWT($header, $payload, $privateKey)
    {
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = '';
        openssl_sign(
            $headerEncoded . '.' . $payloadEncoded,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Base64 URL encode
     */
    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Register a new FCM token
     */
    public function registerToken($userId, $token, $deviceInfo = [])
    {
        try {
            return FcmToken::updateOrCreate(
                [
                    'user_id' => $userId,
                    'token' => $token,
                ],
                [
                    'device_type' => $deviceInfo['device_type'] ?? 'web',
                    'browser' => $deviceInfo['browser'] ?? null,
                    'os' => $deviceInfo['os'] ?? null,
                    'ip_address' => $deviceInfo['ip_address'] ?? request()->ip(),
                    'last_used_at' => Carbon::now(),
                    'is_active' => true,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to register FCM token', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Unregister a token
     */
    public function unregisterToken($token)
    {
        try {
            FcmToken::where('token', $token)->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unregister FCM token', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Mark token as used
     */
    protected function markTokenAsUsed($token)
    {
        FcmToken::where('token', $token)->update([
            'last_used_at' => Carbon::now(),
        ]);
    }

    /**
     * Invalidate a token (mark as inactive)
     */
    protected function invalidateToken($token)
    {
        FcmToken::where('token', $token)->update([
            'is_active' => false,
        ]);

        Log::info('FCM token invalidated', ['token' => substr($token, 0, 20) . '...']);
    }

    /**
     * Clean up stale tokens (not used in 30 days)
     */
    public function cleanupStaleTokens()
    {
        $deleted = FcmToken::where('last_used_at', '<', Carbon::now()->subDays(30))
            ->delete();

        Log::info("Cleaned up {$deleted} stale FCM tokens");
        return $deleted;
    }

    /**
     * Get user's active tokens count
     */
    public function getUserActiveTokensCount($userId)
    {
        return FcmToken::where('user_id', $userId)
            ->where('is_active', true)
            ->count();
    }
}
