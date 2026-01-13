<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Pas de middleware par défaut pour permettre l'accès aux pages publiques
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }

            // Redirection automatique selon le type d'utilisateur
            if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
                return redirect('/admin/dashboard');
            } elseif ($user->hasRole('Commercial')) {
                return redirect('/commercial/dashboard');
            } elseif ($user->hasRole('Agent Comptoir')) {
                return redirect('/comptoir/dashboard');
            } else {
                return redirect()->route('profil.visa.index');
            }
        } catch (\Exception $e) {
            Log::error('Erreur HomeController index: ' . $e->getMessage());
            return redirect('/dashboard');
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Convertir URL YouTube en URL embed
     */
    public static function convertYoutubeUrl($url)
    {
        try {
            if (empty($url)) {
                return '';
            }

            // Patterns pour différents formats d'URL YouTube
            $patterns = [
                // https://www.youtube.com/watch?v=VIDEO_ID
                '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&\n?#]+)/',
                // https://youtu.be/VIDEO_ID
                '/(?:https?:\/\/)?(?:www\.)?youtu\.be\/([^&\n?#]+)/',
                // https://www.youtube.com/embed/VIDEO_ID
                '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([^&\n?#]+)/',
                // https://m.youtube.com/watch?v=VIDEO_ID
                '/(?:https?:\/\/)?(?:m\.)?youtube\.com\/watch\?v=([^&\n?#]+)/',
                // https://www.youtube.com/v/VIDEO_ID
                '/(?:https?:\/\/)?(?:www\.)?youtube\.com\/v\/([^&\n?#]+)/'
            ];

            $videoId = null;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    $videoId = $matches[1];
                    break;
                }
            }

            if ($videoId) {
                // Nettoyer l'ID vidéo de paramètres supplémentaires
                $videoId = preg_replace('/[^a-zA-Z0-9_-].*$/', '', $videoId);
                return "https://www.youtube.com/embed/{$videoId}";
            }

            // Si ce n'est pas une URL YouTube, retourner l'URL originale
            return $url;

        } catch (\Exception $e) {
            Log::error('Erreur convertYoutubeUrl: ' . $e->getMessage());
            return $url; // Retourner l'URL originale en cas d'erreur
        }
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Extraire l'ID vidéo YouTube
     */
    public static function extractYoutubeId($url)
    {
        try {
            if (empty($url)) {
                return null;
            }

            $patterns = [
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/i'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    $videoId = isset($matches[7]) ? $matches[7] : (isset($matches[1]) ? $matches[1] : null);
                    if ($videoId && strlen($videoId) === 11) {
                        return $videoId;
                    }
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erreur extractYoutubeId: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Obtenir thumbnail YouTube
     */
    public static function getYoutubeThumbnail($url, $quality = 'mqdefault')
    {
        try {
            $videoId = self::extractYoutubeId($url);
            
            if ($videoId) {
                // Qualités disponibles: default, mqdefault, hqdefault, sddefault, maxresdefault
                return "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erreur getYoutubeThumbnail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ MÉTHODE UTILITAIRE : Valider URL YouTube
     */
    public static function isValidYoutubeUrl($url)
    {
        try {
            if (empty($url)) {
                return false;
            }

            $videoId = self::extractYoutubeId($url);
            return !empty($videoId) && strlen($videoId) === 11;

        } catch (\Exception $e) {
            Log::error('Erreur isValidYoutubeUrl: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Page d'inscription professionnelle
     */
    public function registerpro()
    {
        try {
            // Récupérer les forfaits disponibles pour l'inscription pro
            $forfaits = collect(); // Collection vide par défaut
            
            if (class_exists('App\Models\Forfaits')) {
                try {
                    $forfaits = \App\Models\Forfaits::where('etat', 1)->orderBy('nom', 'asc')->get();
                } catch (\Exception $e) {
                    Log::warning('Impossible de charger les forfaits: ' . $e->getMessage());
                }
            }
            
            return view('auth.register-pro', compact('forfaits'));
        } catch (\Exception $e) {
            Log::error('Erreur registerpro: ' . $e->getMessage());
            return redirect()->route('register')->with('error', 'Erreur lors du chargement de la page d\'inscription professionnelle');
        }
    }

    /**
     * Clear cache système (pour les admins uniquement)
     */
    public function clearCache()
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->back()->with('error', 'Accès non autorisé');
            }

            // Nettoyer les différents caches
            Cache::flush();
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            Log::info('Cache nettoyé par: ' . $user->name);
            
            return redirect()->back()->with('success', 'Cache nettoyé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur clearCache: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du nettoyage du cache: ' . $e->getMessage());
        }
    }

    /**
     * API pour obtenir les statistiques générales
     */
    public function getGeneralStats()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non connecté'], 401);
            }

            $stats = [
                'users_total' => $this->getUsersCount(),
                'users_today' => $this->getTodayUsersCount(),
                'system_health' => $this->getSystemHealth(),
                'last_update' => now()->format('d/m/Y H:i:s')
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur getGeneralStats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Vérifier la santé du système
     */
    private function getSystemHealth(): array
    {
        try {
            return [
                'database' => $this->checkDatabaseConnection(),
                'cache' => $this->checkCacheSystem(),
                'storage' => $this->checkStorageSystem(),
                'permissions' => $this->checkBasicPermissions()
            ];
        } catch (\Exception $e) {
            return [
                'database' => false,
                'cache' => false,
                'storage' => false,
                'permissions' => false
            ];
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheSystem(): bool
    {
        try {
            Cache::put('health_check', 'ok', 60);
            return Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorageSystem(): bool
    {
        try {
            $uploadPath = public_path('upload');
            return is_dir($uploadPath) && is_writable($uploadPath);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkBasicPermissions(): bool
    {
        try {
            return Schema::hasTable('users') && 
                   Schema::hasTable('roles') && 
                   Schema::hasTable('permissions');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir le nombre total d'utilisateurs
     */
    private function getUsersCount(): int
    {
        try {
            if (Schema::hasTable('users')) {
                return User::where('ent1d', 1)->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getUsersCount: ' . $e->getMessage());
        }
        return 0;
    }

    /**
     * Obtenir le nombre d'utilisateurs créés aujourd'hui
     */
    private function getTodayUsersCount(): int
    {
        try {
            if (Schema::hasTable('users')) {
                return User::where('ent1d', 1)
                    ->whereDate('created_at', now()->toDateString())
                    ->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getTodayUsersCount: ' . $e->getMessage());
        }
        return 0;
    }

    /**
     * Maintenance mode toggle (Admin uniquement)
     */
    public function toggleMaintenance()
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->hasRole('Super Admin')) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Vérifier le statut actuel
            $isDown = app()->isDownForMaintenance();
            
            if ($isDown) {
                Artisan::call('up');
                $message = 'Mode maintenance désactivé';
            } else {
                Artisan::call('down', [
                    '--secret' => 'admin-access',
                    '--render' => 'errors::503'
                ]);
                $message = 'Mode maintenance activé';
            }

            Log::info($message . ' par: ' . $user->name);

            return response()->json([
                'success' => true,
                'message' => $message,
                'maintenance_mode' => !$isDown
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur toggleMaintenance: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du changement de mode'], 500);
        }
    }

    /**
     * Informations système (Admin uniquement)
     */
    public function getSystemInfo()
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Non disponible',
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'disk_free_space' => $this->formatBytes(disk_free_space('.')),
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug'),
                'environment' => app()->environment(),
                'database_connection' => config('database.default'),
                'cache_driver' => config('cache.default')
            ];

            return response()->json($info);

        } catch (\Exception $e) {
            Log::error('Erreur getSystemInfo: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des informations'], 500);
        }
    }

    /**
     * Formater les bytes en unités lisibles
     */
    private function formatBytes($size, $precision = 2): string
    {
        if ($size <= 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Test de performance système
     */
    public function performanceTest()
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->hasRole('Super Admin')) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $startTime = microtime(true);

            // Test de base de données
            $dbStart = microtime(true);
            try {
                if (Schema::hasTable('users')) {
                    User::count();
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de DB pour le test
            }
            $dbTime = microtime(true) - $dbStart;

            // Test de cache
            $cacheStart = microtime(true);
            try {
                Cache::put('perf_test', 'test', 10);
                Cache::get('perf_test');
            } catch (\Exception $e) {
                // Ignorer les erreurs de cache pour le test
            }
            $cacheTime = microtime(true) - $cacheStart;

            // Test d'écriture fichier
            $fileStart = microtime(true);
            try {
                file_put_contents(storage_path('app/perf_test.txt'), 'test');
                if (file_exists(storage_path('app/perf_test.txt'))) {
                    unlink(storage_path('app/perf_test.txt'));
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de fichier pour le test
            }
            $fileTime = microtime(true) - $fileStart;

            $totalTime = microtime(true) - $startTime;

            $results = [
                'total_time' => round($totalTime * 1000, 2) . ' ms',
                'database_time' => round($dbTime * 1000, 2) . ' ms',
                'cache_time' => round($cacheTime * 1000, 2) . ' ms',
                'file_time' => round($fileTime * 1000, 2) . ' ms',
                'timestamp' => now()->toISOString()
            ];

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Erreur performanceTest: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du test de performance'], 500);
        }
    }

    /**
     * Page 404 personnalisée
     */
    public function pageNotFound()
    {
        return response()->view('errors.404', [], 404);
    }

    /**
     * Statistiques commerciales
     */
    public function commercialStatistiques()
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->hasAnyRole(['Commercial', 'Admin', 'Super Admin'])) {
                return redirect('/dashboard')->with('error', 'Accès non autorisé');
            }

            // Statistiques de base pour les commerciaux
            $stats = [
                'total_clients' => $this->getClientsCount(),
                'nouveaux_clients_mois' => $this->getNewClientsThisMonth(),
                'forfaits_actifs' => $this->getActiveForfaitsCount(),
                'forfaits_total' => $this->getTotalForfaitsCount()
            ];

            return view('pages.commercial-statistiques', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Erreur commercialStatistiques: ' . $e->getMessage());
            return redirect('/commercial/dashboard')->with('error', 'Erreur lors du chargement des statistiques');
        }
    }

    private function getClientsCount(): int
    {
        try {
            if (Schema::hasTable('users')) {
                return User::where('type_user', 'public')->where('ent1d', 1)->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getClientsCount: ' . $e->getMessage());
        }
        return 0;
    }

    private function getNewClientsThisMonth(): int
    {
        try {
            if (Schema::hasTable('users')) {
                return User::where('type_user', 'public')
                    ->where('ent1d', 1)
                    ->whereMonth('created_at', now()->month)
                    ->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getNewClientsThisMonth: ' . $e->getMessage());
        }
        return 0;
    }

    private function getActiveForfaitsCount(): int
    {
        try {
            if (class_exists('App\Models\Forfaits')) {
                return \App\Models\Forfaits::where('etat', 1)->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getActiveForfaitsCount: ' . $e->getMessage());
        }
        return 0;
    }

    private function getTotalForfaitsCount(): int
    {
        try {
            if (class_exists('App\Models\Forfaits')) {
                return \App\Models\Forfaits::count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur getTotalForfaitsCount: ' . $e->getMessage());
        }
        return 0;
    }
}