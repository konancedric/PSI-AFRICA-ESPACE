<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\StatutsEtat;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * ✅ CORRECTION PRINCIPALE : Dashboard avec redirection vers profil-visa pour utilisateurs publics
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est connecté
            if (!$user) {
                return redirect()->route('login');
            }

            Log::info('🎯 Dashboard accédé par: ' . $user->name . ' (' . $user->type_user . ')', [
                'user_id' => $user->id,
                'user_type' => $user->type_user,
                'user_roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
                'referrer' => $request->headers->get('referer')
            ]);

            // ✅ CORRECTION : Redirection intelligente selon le type d'utilisateur ET les rôles
            return $this->redirectToCorrectDashboard($user);

        } catch (\Exception $e) {
            Log::error('❌ Erreur Dashboard: ' . $e->getMessage());
            return $this->loadGeneralDashboard();
        }
    }

    /**
     * ✅ CORRECTION COMPLÈTE : Rediriger vers le bon dashboard selon le type d'utilisateur ET les rôles
     */
    private function redirectToCorrectDashboard($user)
    {
        try {
            // Vérifier si l'utilisateur est actif
            if (isset($user->etat) && $user->etat != 1) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Votre compte n\'est pas actif.');
            }

            // ✅ CORRECTION PRINCIPALE : Priorité aux rôles, puis fallback sur type_user
            
            // 1. Vérifier d'abord par rôles (priorité)
            try {
                if (method_exists($user, 'hasRole')) {
                    if ($user->hasRole('Super Admin')) {
                        Log::info('📊 Redirection Super Admin vers: /admin/dashboard');
                        return redirect('/admin/dashboard');
                    } elseif ($user->hasRole('Admin')) {
                        Log::info('📊 Redirection Admin vers: /admin/dashboard');
                        return redirect('/admin/dashboard');
                    } elseif ($user->hasRole('Commercial')) {
                        Log::info('💼 Redirection Commercial (par rôle) vers: /commercial/dashboard');
                        return redirect('/commercial/dashboard');
                    } elseif ($user->hasRole('Agent Comptoir')) {
                        Log::info('🏢 Redirection Agent Comptoir (par rôle) vers: /comptoir/dashboard');
                        return redirect('/comptoir/dashboard');
                    }
                }
            } catch (\Exception $roleError) {
                Log::error('❌ Erreur vérification rôles dans redirection:', [
                    'user_id' => $user->id,
                    'error' => $roleError->getMessage()
                ]);
            }

            // 2. Fallback sur type_user si pas de rôles ou erreur
            switch ($user->type_user) {
                case 'admin':
                    Log::info('📊 Redirection admin (par type_user) vers: /admin/dashboard');
                    return redirect('/admin/dashboard');

                case 'agent_comptoir':
                    Log::info('🏢 Redirection agent comptoir (par type_user) vers: /comptoir/dashboard');
                    return redirect('/comptoir/dashboard');

                case 'commercial':
                    Log::info('💼 Redirection commercial (par type_user) vers: /commercial/dashboard');
                    return redirect('/commercial/dashboard');

                case 'public':
                default:
                    // ✅ CORRECTION CRITIQUE : Pour les utilisateurs publics, rediriger vers PROFIL-VISA directement
                    Log::info('👤 Redirection utilisateur public vers: /profil-visa');
                    return redirect('/profil-visa');
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur redirection dashboard:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            // En cas d'erreur, rediriger vers profil-visa pour les utilisateurs publics
            if ($user->type_user === 'public' || empty($user->type_user)) {
                return redirect('/profil-visa');
            }
            
            return $this->loadGeneralDashboard();
        }
    }

    /**
     * ✅ AMÉLIORATION : Charger le dashboard général pour les utilisateurs sans redirection spécifique
     */
    private function loadGeneralDashboard()
    {
        try {
            $user = Auth::user();
            $currentDate = Carbon::now();

            Log::info('📈 Chargement dashboard général pour: ' . ($user ? $user->name : 'Utilisateur inconnu'));

            // ==================== STATISTIQUES GÉNÉRALES ====================

            // Utilisateurs
            $totalUsers = User::where('ent1d', 1)->count();
            $newUsersToday = User::where('ent1d', 1)
                ->whereDate('created_at', $currentDate->toDateString())
                ->count();
            $newUsersThisWeek = User::where('ent1d', 1)
                ->whereBetween('created_at', [
                    $currentDate->copy()->startOfWeek(),
                    $currentDate->copy()->endOfWeek()
                ])
                ->count();
            $newUsersThisMonth = User::where('ent1d', 1)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count();

            // Profils Visa - Filtrer selon le type d'utilisateur
            $profilVisaQuery = ProfilVisa::where('ent1d', 1);
            
            // ✅ CORRECTION : Si utilisateur public, ne voir que ses propres profils
            if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                $profilVisaQuery->where('user1d', $user->id);
                Log::info("👤 Statistiques filtrées pour utilisateur public: {$user->name}");
            }

            $totalProfilVisa = (clone $profilVisaQuery)->count();
            $newProfilVisaToday = (clone $profilVisaQuery)
                ->whereDate('created_at', $currentDate->toDateString())
                ->count();
            $newProfilVisaThisWeek = (clone $profilVisaQuery)
                ->whereBetween('created_at', [
                    $currentDate->copy()->startOfWeek(),
                    $currentDate->copy()->endOfWeek()
                ])
                ->count();

            // Profils en attente
            $pendingVisas = 0;
            $urgentVisas = 0;
            
            try {
                $pendingVisasQuery = clone $profilVisaQuery;
                $pendingVisas = $pendingVisasQuery->where(function($query) {
                    $query->whereNull('id_statuts_etat');
                    
                    if (Schema::hasTable('statuts_etat')) {
                        $query->orWhereHas('statutEtat', function($subQuery) {
                            $subQuery->where('libelle', 'like', '%attente%')
                                     ->orWhere('libelle', 'like', '%pending%');
                        });
                    }
                })->count();

                $urgentVisasQuery = clone $profilVisaQuery;
                $urgentVisas = $urgentVisasQuery->where('created_at', '<', $currentDate->copy()->subDays(7))
                    ->where(function($query) {
                        $query->whereNull('id_statuts_etat');
                        
                        if (Schema::hasTable('statuts_etat')) {
                            $query->orWhereHas('statutEtat', function($subQuery) {
                                $subQuery->where('libelle', 'like', '%attente%');
                            });
                        }
                    })
                    ->count();
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul profils en attente dashboard général:', ['error' => $e->getMessage()]);
            }

            // Calcul du taux de réussite
            $approvedVisas = 0;
            try {
                // ✅ CORRECTION : Vérifier d'abord l'existence de la table statuts_etat
                if (Schema::hasTable('statuts_etat')) {
                    $approvedVisasQuery = clone $profilVisaQuery;
                    $approvedVisas = $approvedVisasQuery->whereHas('statutEtat', function($query) {
                        $query->whereIn('libelle', ['Approuvé', 'Visa délivré', 'Terminé']);
                    })->count();
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul profils approuvés:', ['error' => $e->getMessage()]);
            }
            
            $successRate = $totalProfilVisa > 0 ? round(($approvedVisas / $totalProfilVisa) * 100, 1) : 0;

            // Temps moyen de traitement
            $avgProcessingTime = 0;
            try {
                $avgProcessingTimeQuery = clone $profilVisaQuery;
                $avgProcessingTime = $avgProcessingTimeQuery->whereNotNull('updated_at')
                    ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                    ->first();
                $avgProcessingTime = round($avgProcessingTime->avg_days ?? 0, 1);
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul temps moyen traitement:', ['error' => $e->getMessage()]);
            }

            // ==================== DONNÉES POUR GRAPHIQUES ====================

            // Statistiques mensuelles (12 derniers mois)
            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = $currentDate->copy()->subMonths($i);
                
                $monthlyUserQuery = User::where('ent1d', 1)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year);
                
                $monthlyProfilQuery = ProfilVisa::where('ent1d', 1)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year);
                
                // Filtrer pour utilisateur public
                if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                    $monthlyProfilQuery->where('user1d', $user->id);
                }
                
                $monthlyStats[] = [
                    'month' => $month->format('M Y'),
                    'users' => $monthlyUserQuery->count(),
                    'profil_visa' => $monthlyProfilQuery->count(),
                ];
            }

            // Répartition par statut des profils visa
            $profilVisaByStatus = collect();
            try {
                // ✅ CORRECTION : Vérifier l'existence des tables avant de faire les jointures
                if (Schema::hasTable('statuts_etat')) {
                    $statusQuery = DB::table('profil_visa')
                        ->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                        ->where('profil_visa.ent1d', 1);
                    
                    // Filtrer pour utilisateur public
                    if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                        $statusQuery->where('profil_visa.user1d', $user->id);
                    }
                    
                    $profilVisaByStatus = $statusQuery->select(
                            DB::raw('COALESCE(statuts_etat.libelle, "Sans statut") as status_name'),
                            DB::raw('COALESCE(statuts_etat.couleur, "gray") as couleur'),
                            DB::raw('COUNT(profil_visa.id) as total')
                        )
                        ->groupBy('statuts_etat.id', 'statuts_etat.libelle', 'statuts_etat.couleur')
                        ->orderBy('total', 'desc')
                        ->get();
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul répartition par statut:', ['error' => $e->getMessage()]);
                $profilVisaByStatus = collect();
            }

            // Statistiques des 30 derniers jours
            $dailyStats = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = $currentDate->copy()->subDays($i);
                
                $dailyUserQuery = User::where('ent1d', 1)
                    ->whereDate('created_at', $date->toDateString());
                
                $dailyProfilQuery = ProfilVisa::where('ent1d', 1)
                    ->whereDate('created_at', $date->toDateString());
                
                // Filtrer pour utilisateur public
                if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                    $dailyProfilQuery->where('user1d', $user->id);
                }
                
                $dailyStats[] = [
                    'date' => $date->format('d/m'),
                    'users' => $dailyUserQuery->count(),
                    'profil_visa' => $dailyProfilQuery->count(),
                ];
            }

            // Répartition par type de profil visa
            $profilVisaByType = collect();
            try {
                $typeQuery = ProfilVisa::where('ent1d', 1);
                
                // Filtrer pour utilisateur public
                if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                    $typeQuery->where('user1d', $user->id);
                }
                
                $profilVisaByType = $typeQuery->select('type_profil_visa', DB::raw('COUNT(*) as total'))
                    ->groupBy('type_profil_visa')
                    ->orderBy('total', 'desc')
                    ->get();
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul répartition par type:', ['error' => $e->getMessage()]);
                $profilVisaByType = collect();
            }

            // Top utilisateurs actifs (seulement pour les agents)
            $topUsers = collect();
            if ($user && $this->isAgent($user)) {
                try {
                    $topUsers = DB::table('users')
                        ->leftJoin('profil_visa', 'users.id', '=', 'profil_visa.user1d')
                        ->where('users.type_user', 'public')
                        ->where('users.ent1d', 1)
                        ->select(
                            'users.name',
                            'users.email',
                            'users.contact',
                            DB::raw('COUNT(profil_visa.id) as total_demandes')
                        )
                        ->groupBy('users.id', 'users.name', 'users.email', 'users.contact')
                        ->having('total_demandes', '>', 0)
                        ->orderBy('total_demandes', 'desc')
                        ->limit(10)
                        ->get();
                } catch (\Exception $e) {
                    Log::error('❌ Erreur calcul top utilisateurs:', ['error' => $e->getMessage()]);
                    $topUsers = collect();
                }
            }

            // Profils visa récents avec détails
            $recentProfilVisa = collect();
            try {
                $recentQuery = DB::table('profil_visa')
                    ->leftJoin('users', 'profil_visa.user1d', '=', 'users.id')
                    ->where('profil_visa.ent1d', 1);
                
                // Filtrer pour utilisateur public
                if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                    $recentQuery->where('profil_visa.user1d', $user->id);
                }
                
                $recentQuery->select(
                    'profil_visa.id',
                    'profil_visa.numero_profil_visa',
                    'profil_visa.updated_at',
                    'users.name as user_name'
                );

                // ✅ CORRECTION : Ajouter la jointure statuts_etat seulement si la table existe
                if (Schema::hasTable('statuts_etat')) {
                    $recentQuery->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                          ->addSelect(
                              DB::raw('COALESCE(statuts_etat.libelle, "Sans statut") as status_name'),
                              DB::raw('COALESCE(statuts_etat.couleur, "gray") as couleur')
                          );
                } else {
                    $recentQuery->addSelect(
                        DB::raw('"Sans statut" as status_name'),
                        DB::raw('"gray" as couleur')
                    );
                }

                $recentProfilVisa = $recentQuery->orderBy('profil_visa.updated_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('❌ Erreur récupération profils récents:', ['error' => $e->getMessage()]);
                $recentProfilVisa = collect();
            }

            // ✅ CORRECTION : Message d'information pour les utilisateurs publics
            $dashboardMessage = '';
            if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                $dashboardMessage = "Bienvenue {$user->name}! Voici un aperçu de vos demandes de profils visa.";
            }

            // Charger la vue dashboard avec toutes les données
            return view('pages.dashboard', compact(
                'totalUsers', 'newUsersToday', 'newUsersThisWeek', 'newUsersThisMonth',
                'totalProfilVisa', 'newProfilVisaToday', 'newProfilVisaThisWeek',
                'pendingVisas', 'urgentVisas', 'successRate', 'avgProcessingTime',
                'monthlyStats', 'profilVisaByStatus', 'dailyStats', 'profilVisaByType',
                'topUsers', 'recentProfilVisa', 'dashboardMessage'
            ));

        } catch (\Exception $e) {
            Log::error('❌ Erreur loadGeneralDashboard: ' . $e->getMessage());
            
            // Données par défaut en cas d'erreur
            return view('pages.dashboard', [
                'totalUsers' => 0,
                'newUsersToday' => 0,
                'newUsersThisWeek' => 0,
                'newUsersThisMonth' => 0,
                'totalProfilVisa' => 0,
                'newProfilVisaToday' => 0,
                'newProfilVisaThisWeek' => 0,
                'pendingVisas' => 0,
                'urgentVisas' => 0,
                'successRate' => 0,
                'avgProcessingTime' => 0,
                'monthlyStats' => [],
                'profilVisaByStatus' => [],
                'dailyStats' => [],
                'profilVisaByType' => [],
                'topUsers' => [],
                'recentProfilVisa' => [],
                'dashboardMessage' => 'Tableau de bord général'
            ]);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Vérifier si l'utilisateur est un agent
     */
    private function isAgent($user): bool
    {
        try {
            // Vérifier par rôles d'abord
            if (method_exists($user, 'hasAnyRole')) {
                if ($user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial'])) {
                    return true;
                }
            }
            
            // Fallback sur type_user
            return in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial']);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification agent dashboard: ' . $e->getMessage());
            return in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial']);
        }
    }

    /**
     * ✅ API pour obtenir les statistiques en temps réel
     */
    public function getRealtimeStats(Request $request)
    {
        try {
            $user = Auth::user();
            $currentDate = Carbon::now();

            // Query de base pour les profils visa
            $profilVisaQuery = ProfilVisa::where('ent1d', 1);
            
            // Filtrer pour utilisateur public
            if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                $profilVisaQuery->where('user1d', $user->id);
            }

            $stats = [
                'total_users' => User::where('ent1d', 1)->count(),
                'new_users_today' => User::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'total_profil_visa' => (clone $profilVisaQuery)->count(),
                'new_profil_visa_today' => (clone $profilVisaQuery)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'pending_visas' => 0,
                'last_update' => now()->format('H:i:s'),
                'user_type' => $user ? $user->type_user : 'guest',
                'is_agent' => $user ? $this->isAgent($user) : false
            ];

            // Calcul des profils en attente avec gestion d'erreurs
            try {
                $stats['pending_visas'] = (clone $profilVisaQuery)
                    ->where(function($query) {
                        $query->whereNull('id_statuts_etat');
                        
                        if (Schema::hasTable('statuts_etat')) {
                            $query->orWhereHas('statutEtat', function($subQuery) {
                                $subQuery->where('libelle', 'like', '%attente%');
                            });
                        }
                    })
                    ->count();
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul pending_visas:', ['error' => $e->getMessage()]);
            }

            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur getRealtimeStats Dashboard: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * ✅ API pour obtenir les statistiques détaillées
     */
    public function getDetailedStats(Request $request)
    {
        try {
            $user = Auth::user();
            $currentDate = Carbon::now();

            // Query de base pour les profils visa selon le type d'utilisateur
            $profilVisaQuery = ProfilVisa::where('ent1d', 1);
            
            if ($user && ($user->type_user === 'public' || empty($user->type_user))) {
                $profilVisaQuery->where('user1d', $user->id);
            }

            // Statistiques avancées
            $stats = [
                'users' => [
                    'total' => User::where('ent1d', 1)->count(),
                    'public' => User::where('type_user', 'public')->where('ent1d', 1)->count(),
                    'agents' => User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
                        ->where('ent1d', 1)->count(),
                    'new_this_month' => User::where('ent1d', 1)
                        ->whereMonth('created_at', $currentDate->month)
                        ->count(),
                ],
                'profil_visa' => [
                    'total' => (clone $profilVisaQuery)->count(),
                    'pending' => 0,
                    'approved' => 0,
                    'new_this_month' => (clone $profilVisaQuery)
                        ->whereMonth('created_at', $currentDate->month)
                        ->count(),
                ],
                'performance' => [
                    'success_rate' => $this->calculateSuccessRate($profilVisaQuery),
                    'avg_processing_time' => $this->calculateAvgProcessingTime($profilVisaQuery),
                    'monthly_growth' => $this->calculateMonthlyGrowth($profilVisaQuery),
                ]
            ];

            // ✅ CORRECTION : Calcul des statuts avec gestion d'erreurs
            try {
                $stats['profil_visa']['pending'] = (clone $profilVisaQuery)
                    ->where(function($query) {
                        $query->whereNull('id_statuts_etat');
                        
                        if (Schema::hasTable('statuts_etat')) {
                            $query->orWhereHas('statutEtat', function($subQuery) {
                                $subQuery->where('libelle', 'like', '%attente%')
                                         ->orWhere('libelle', 'like', '%pending%')
                                         ->orWhere('libelle', 'like', '%nouveau%');
                            });
                        }
                    })
                    ->count();

                if (Schema::hasTable('statuts_etat')) {
                    $stats['profil_visa']['approved'] = (clone $profilVisaQuery)
                        ->whereHas('statutEtat', function($query) {
                            $query->where('libelle', 'like', '%approuvé%')
                                  ->orWhere('libelle', 'like', '%délivré%')
                                  ->orWhere('libelle', 'like', '%approved%')
                                  ->orWhere('libelle', 'like', '%delivered%');
                        })
                        ->count();
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur calcul statuts détaillées: ' . $e->getMessage());
            }

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('❌ Erreur getDetailedStats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ Exporter les statistiques
     */
    public function exportStats(Request $request, $format = 'excel')
    {
        try {
            // Logique d'export des statistiques
            return response()->json(['message' => 'Export en cours de développement - Format: ' . $format]);
        } catch (\Exception $e) {
            Log::error('❌ Erreur exportStats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'export'], 500);
        }
    }

    // ==================== MÉTHODES PRIVÉES CORRIGÉES ====================

    /**
     * Calculer le taux de réussite avec query spécifique
     */
    private function calculateSuccessRate($query): float
    {
        try {
            $total = (clone $query)->count();
            $approved = 0;

            // ✅ CORRECTION : Vérifier l'existence de la table statuts_etat
            if (Schema::hasTable('statuts_etat')) {
                $approved = (clone $query)
                    ->whereHas('statutEtat', function($subQuery) {
                        $subQuery->where('libelle', 'like', '%approuvé%')
                                 ->orWhere('libelle', 'like', '%délivré%')
                                 ->orWhere('libelle', 'like', '%terminé%');
                    })
                    ->count();
            }

            return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
        } catch (\Exception $e) {
            Log::error('❌ Erreur calculateSuccessRate: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculer le temps moyen de traitement avec query spécifique
     */
    private function calculateAvgProcessingTime($query): float
    {
        try {
            $avg = (clone $query)
                ->whereNotNull('updated_at')
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->first();

            return round($avg->avg_days ?? 0, 1);
        } catch (\Exception $e) {
            Log::error('❌ Erreur calculateAvgProcessingTime: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculer la croissance mensuelle avec query spécifique
     */
    private function calculateMonthlyGrowth($query): array
    {
        try {
            $currentMonth = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();

            $currentMonthCount = (clone $query)
                ->whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->count();

            $lastMonthCount = (clone $query)
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();

            $growth = $lastMonthCount > 0 ? round((($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100, 1) : 0;

            return [
                'current_month' => $currentMonthCount,
                'last_month' => $lastMonthCount,
                'growth_percentage' => $growth
            ];
        } catch (\Exception $e) {
            Log::error('❌ Erreur calculateMonthlyGrowth: ' . $e->getMessage());
            return [
                'current_month' => 0,
                'last_month' => 0,
                'growth_percentage' => 0
            ];
        }
    }

    /**
     * Méthodes pour les APIs et filtres avancés
     */
    public function getFilteredGlobalStats(Request $request)
    {
        try {
            // Logique de filtrage global
            $period = $request->input('period', 'this_month');
            
            // Implémenter selon les besoins
            return response()->json(['message' => 'Filtrage global en cours de développement']);
        } catch (\Exception $e) {
            Log::error('❌ Erreur getFilteredGlobalStats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur filtrage'], 500);
        }
    }

    public function getSystemHealth()
    {
        try {
            $checks = [
                'database' => $this->checkDatabaseConnection(),
                'tables' => $this->checkTablesExistence(),
                'permissions' => $this->checkPermissionsSystem(),
                'cache' => $this->checkCacheSystem(),
            ];

            $allHealthy = array_reduce($checks, function($carry, $check) {
                return $carry && $check;
            }, true);

            return response()->json([
                'status' => $allHealthy ? 'healthy' : 'warning',
                'checks' => $checks,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkTablesExistence(): bool
    {
        try {
            $requiredTables = ['users', 'profil_visa'];
            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkPermissionsSystem(): bool
    {
        try {
            return Schema::hasTable('roles') && Schema::hasTable('permissions');
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheSystem(): bool
    {
        try {
            cache()->put('health_check_dashboard', 'ok', 60);
            return cache()->get('health_check_dashboard') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}