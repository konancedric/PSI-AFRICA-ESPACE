<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OrangeSmsService
{
    protected $clientId;
    protected $clientSecret;
    protected $tokenUrl;
    protected $apiUrl;
    protected $senderName;
    protected $senderAddress;
    protected $tokenCacheKey;
    protected $tokenCacheDuration;

    public function __construct()
    {
        $this->clientId = config('orange_sms.client_id');
        $this->clientSecret = config('orange_sms.client_secret');
        $this->tokenUrl = config('orange_sms.token_url');
        $this->apiUrl = config('orange_sms.api_url');
        $this->senderName = config('orange_sms.sender_name');
        $this->senderAddress = config('orange_sms.sender_address');
        $this->tokenCacheKey = config('orange_sms.token_cache_key');
        $this->tokenCacheDuration = config('orange_sms.token_cache_duration');
    }

    /**
     * Obtenir un token d'accès OAuth2 (avec cache)
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        try {
            // Vérifier si le token est en cache
            $cachedToken = Cache::get($this->tokenCacheKey);

            if ($cachedToken) {
                Log::info('Orange SMS: Token récupéré du cache');
                return $cachedToken;
            }

            // Sinon, demander un nouveau token
            Log::info('Orange SMS: Demande d\'un nouveau token');

            // Créer l'en-tête Authorization en Base64
            $authHeader = base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post($this->tokenUrl, [
                'grant_type' => 'client_credentials'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;

                if ($accessToken) {
                    // Mettre le token en cache pour 55 minutes
                    Cache::put($this->tokenCacheKey, $accessToken, now()->addMinutes($this->tokenCacheDuration));

                    Log::info('Orange SMS: Nouveau token obtenu et mis en cache', [
                        'expires_in' => $data['expires_in'] ?? 'unknown'
                    ]);

                    return $accessToken;
                }
            }

            Log::error('Orange SMS: Échec obtention token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (Exception $e) {
            Log::error('Orange SMS: Exception lors de l\'obtention du token', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Envoyer un SMS
     *
     * @param string $phoneNumber Numéro au format +225XXXXXXXXX ou tel:+225XXXXXXXXX
     * @param string $message Contenu du SMS (max 160 caractères pour 1 SMS)
     * @return array
     */
    public function sendSms(string $phoneNumber, string $message): array
    {
        try {
            // ✅ VÉRIFIER SI LE NUMÉRO EST UN NUMÉRO CI ORANGE OU MTN
            if (!$this->isOrangeOrMtnNumber($phoneNumber)) {
                Log::warning('Orange SMS: Numéro filtré - Pas Orange/MTN CI', [
                    'phone' => $phoneNumber,
                    'reason' => 'Seuls les numéros Orange et MTN Côte d\'Ivoire sont autorisés'
                ]);

                return [
                    'success' => false,
                    'message' => 'Numéro non autorisé - Seuls les numéros Orange et MTN Côte d\'Ivoire sont acceptés',
                    'error' => 'INVALID_OPERATOR',
                    'filtered' => true
                ];
            }

            // Obtenir le token d'accès
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Impossible d\'obtenir le token d\'accès',
                    'error' => 'TOKEN_FAILED'
                ];
            }

            // Formater le numéro de téléphone
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // Préparer la requête SMS
            $endpoint = $this->apiUrl . '/' . urlencode($this->senderAddress) . '/requests';

            $payload = [
                'outboundSMSMessageRequest' => [
                    'address' => [$formattedPhone],
                    'senderAddress' => $this->senderAddress,
                    'senderName' => $this->senderName,
                    'outboundSMSTextMessage' => [
                        'message' => $message
                    ]
                ]
            ];

            Log::info('Orange SMS: Envoi SMS - Détails complets', [
                'original_phone' => $phoneNumber,
                'formatted_phone' => $formattedPhone,
                'sender_address' => $this->senderAddress,
                'sender_name' => $this->senderName,
                'endpoint' => $endpoint,
                'message_length' => strlen($message),
                'message_preview' => substr($message, 0, 50) . '...'
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Orange SMS: SMS envoyé avec succès', [
                    'to' => $formattedPhone,
                    'response' => $data
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS envoyé avec succès',
                    'data' => $data
                ];
            }

            Log::error('Orange SMS: Échec envoi SMS', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Échec de l\'envoi du SMS',
                'error' => $response->body(),
                'status' => $response->status()
            ];

        } catch (Exception $e) {
            Log::error('Orange SMS: Exception lors de l\'envoi', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer un SMS à plusieurs destinataires
     *
     * @param array $phoneNumbers Tableau de numéros
     * @param string $message Contenu du SMS
     * @return array
     */
    public function sendBulkSms(array $phoneNumbers, string $message): array
    {
        $results = [
            'success' => [],
            'failed' => [],
            'filtered' => [], // ✅ Numéros filtrés (pas Orange/MTN CI)
            'total' => count($phoneNumbers),
            'sent' => 0,
            'errors' => 0,
            'filtered_count' => 0
        ];

        foreach ($phoneNumbers as $phone) {
            $result = $this->sendSms($phone, $message);

            if ($result['success']) {
                $results['success'][] = $phone;
                $results['sent']++;
            } else {
                // Séparer les numéros filtrés des vraies erreurs
                if (isset($result['filtered']) && $result['filtered']) {
                    $results['filtered'][] = [
                        'phone' => $phone,
                        'reason' => $result['message']
                    ];
                    $results['filtered_count']++;
                } else {
                    $results['failed'][] = [
                        'phone' => $phone,
                        'error' => $result['message']
                    ];
                    $results['errors']++;
                }
            }
        }

        return $results;
    }

    /**
     * Vérifier si un numéro est un numéro Orange ou MTN de Côte d'Ivoire
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function isOrangeOrMtnNumber(string $phoneNumber): bool
    {
        // Nettoyer le numéro
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Extraire les 2 premiers chiffres du numéro local (après le 0)
        $prefix = null;

        // Format: +225 + 10 chiffres (ex: +2250709850058)
        if (preg_match('/^\+225([0-9]{10})$/', $cleaned, $matches)) {
            $prefix = substr($matches[1], 0, 2);
        }
        // Format: 225 + 10 chiffres (sans +) (ex: 2250709850058)
        elseif (preg_match('/^225([0-9]{10})$/', $cleaned, $matches)) {
            $prefix = substr($matches[1], 0, 2);
        }
        // Format local: 10 chiffres (ex: 0709850058)
        elseif (preg_match('/^([0-9]{10})$/', $cleaned, $matches)) {
            $prefix = substr($matches[1], 0, 2);
        }

        // Si aucun format valide détecté, rejeter
        if ($prefix === null) {
            return false;
        }

        // MOOV (01, 02, 03) : NON AUTORISÉ
        if (in_array($prefix, ['01', '02', '03'])) {
            return false;
        }

        // MTN : 21 préfixes
        $mtnPrefixes = ['04', '05', '06', '14', '15', '24', '25', '34', '35', '44', '45', '54', '55', '64', '65', '74', '75', '84', '85', '94', '95'];
        if (in_array($prefix, $mtnPrefixes)) {
            return true;
        }

        // ORANGE : 76 préfixes (tous les autres sauf Moov et MTN)
        $orangePrefixes = [
            '07', '08', '09',
            '10', '11', '12', '13', '16', '17', '18', '19',
            '20', '21', '22', '23', '26', '27', '28', '29',
            '30', '31', '32', '33', '36', '37', '38', '39',
            '40', '41', '42', '43', '46', '47', '48', '49',
            '50', '51', '52', '53', '56', '57', '58', '59',
            '60', '61', '62', '63', '66', '67', '68', '69',
            '70', '71', '72', '73', '76', '77', '78', '79',
            '80', '81', '82', '83', '86', '87', '88', '89',
            '90', '91', '92', '93', '96', '97', '98', '99'
        ];
        if (in_array($prefix, $orangePrefixes)) {
            return true;
        }

        // Si le préfixe n'est dans aucune liste, rejeter
        return false;
    }

    /**
     * Formater un numéro de téléphone au format Orange API
     * Format Côte d'Ivoire depuis 2021 : +225 + 10 chiffres (ex: +2250707070707)
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les espaces et caractères spéciaux sauf +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Si le numéro commence déjà par tel:, le retourner tel quel
        if (str_starts_with($phoneNumber, 'tel:')) {
            return $phoneNumber;
        }

        // Si le numéro commence déjà par + (format international), utiliser tel quel
        // Cela évite le double ++ pour tous les codes pays (+225, +228, +243, etc.)
        if (str_starts_with($cleaned, '+')) {
            return 'tel:' . $cleaned;
        }

        // Si le numéro commence par 00 (format international alternatif), remplacer par +
        if (str_starts_with($cleaned, '00') && strlen($cleaned) > 11) {
            return 'tel:+' . substr($cleaned, 2);
        }

        // Si le numéro commence par un code pays sans + (225, 228, 243, 222, etc.)
        // Format: 2XX ou 2XXX suivi de 7-10 chiffres
        if (preg_match('/^(2[0-9]{2,3})[0-9]{7,10}$/', $cleaned)) {
            return 'tel:+' . $cleaned;
        }

        // Numéro local ivoirien (10 chiffres commençant par 0)
        // Format: 0X XX XX XX XX -> +225 0X XX XX XX XX
        if (strlen($cleaned) == 10 && str_starts_with($cleaned, '0')) {
            return 'tel:+225' . $cleaned;
        }

        // Numéro local sans le 0 initial (9 chiffres) - ancien format
        // On ajoute le 0 pour avoir 10 chiffres et le code pays CI
        if (strlen($cleaned) == 9 && !str_starts_with($cleaned, '0')) {
            return 'tel:+2250' . $cleaned;
        }

        // Par défaut, ajouter +225 et le 0 si nécessaire pour numéros courts
        if (strlen($cleaned) < 10 && !str_starts_with($cleaned, '0')) {
            $cleaned = '0' . $cleaned;
        }

        // Pour tout le reste, ajouter le code pays CI par défaut
        if (!str_starts_with($cleaned, '+') && strlen($cleaned) < 11) {
            return 'tel:+225' . $cleaned;
        }

        return 'tel:+' . $cleaned;
    }

    /**
     * Vérifier si les credentials sont configurés
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Tester la connexion à l'API Orange
     *
     * @return array
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Configuration Orange SMS manquante'
            ];
        }

        $token = $this->getAccessToken();

        if ($token) {
            return [
                'success' => true,
                'message' => 'Connexion Orange SMS OK',
                'token_preview' => substr($token, 0, 20) . '...'
            ];
        }

        return [
            'success' => false,
            'message' => 'Échec de connexion à l\'API Orange SMS'
        ];
    }
}
