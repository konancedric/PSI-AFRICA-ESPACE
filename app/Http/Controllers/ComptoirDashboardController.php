<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\DocumentsVoyage;
use App\Models\RendezVous;
use App\Models\ReservationAchat;
use App\Models\SouscrireForfaits;
use App\Models\StatutsEtat;
use App\Models\AddMessageProfilVisa;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ComptoirDashboardController extends Controller
{
    /**
     * 🔥 DASHBOARD COMPTOIR PSI AFRICA - VERSION COMPLÈTE AVEC 5 MODULES
     * Similaire au dashboard commercial mais avec la perspective agent comptoir
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $currentDate = Carbon::now();
            
            // Vérification d'accès
            $hasAccess = $this->checkComptoirAccess($user);
            
            if (!$hasAccess) {
                Log::warning('Accès refusé au dashboard comptoir', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_type' => $user->type_user
                ]);
                
                return redirect('/dashboard')->with('error', 'Accès non autorisé - Réservé aux agents comptoir');
            }

            // ==================== GESTION DES FILTRES PAR PÉRIODE ====================
            
            $periodFilter = $request->input('period', 'this_month');
            $customStartDate = $request->input('start_date');
            $customEndDate = $request->input('end_date');
            
            $dateRange = $this->getDateRange($periodFilter, $customStartDate, $customEndDate);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
            
            Log::info('Filtres appliqués dans Dashboard Comptoir', [
                'period' => $periodFilter,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'user_id' => $user->id
            ]);

            $userId = $user->id;

            // ==================== STATISTIQUES DES 5 MODULES COMPLÈTES ====================
            
            // 1. STATISTIQUES PROFIL VISA - Global + Agent
            $profilVisaStats = $this->calculateProfilVisaStats($userId, $startDate, $endDate, $currentDate);
            
            // 2. STATISTIQUES SOUSCRIPTION FORFAITS - Toutes les souscriptions
            $souscriptionStats = $this->calculateSouscriptionStats($startDate, $endDate, $currentDate);
            
            // 3. STATISTIQUES DOCUMENTS VOYAGE - Global
            $documentsVoyageStats = $this->calculateDocumentsVoyageStats($startDate, $endDate, $currentDate);
            
            // 4. STATISTIQUES RENDEZ-VOUS - Global
            $rendezVousStats = $this->calculateRendezVousStats($startDate, $endDate, $currentDate);
            
            // 5. STATISTIQUES RÉSERVATIONS ACHAT - Global
            $reservationStats = $this->calculateReservationStats($startDate, $endDate, $currentDate);

            // ==================== ÉVOLUTION DES 5 MODULES ====================
            
            $evolutionModules = $this->getEvolutionData($startDate, $endDate, $periodFilter, $userId);

            // ==================== TOP DESTINATIONS ET ACTIVITÉS ====================
            
            $topDestinations = $this->getTopDestinationsForPeriod($startDate, $endDate);
            $activitesRecentes = $this->getActivitiesForPeriod($startDate, $endDate, $userId);

            // ==================== OBJECTIFS ET PERFORMANCES ====================
            
            $objectifs = $this->calculateObjectives($profilVisaStats, $souscriptionStats, $rendezVousStats, $periodFilter);

            // ==================== DONNÉES SPÉCIFIQUES AGENT COMPTOIR ====================
            
            // Performance de l'agent
            $tempsReponse = $this->calculateResponseTime($userId, $startDate, $endDate);
            $tauxResolution = $this->calculateTauxResolution($userId, $startDate, $endDate);
            $tauxSatisfaction = $this->calculateTauxSatisfaction($userId, $startDate, $endDate);
            
            // Productivité et classement
            $productiviteStats = $this->calculateProductiviteStats($userId, $currentDate);
            $classementAgent = $this->getClassementAgent($userId, $currentDate);
            
            // Profils à traiter
            $nouveauxClients = $this->getNouveauxClients($currentDate);
            $derniersProfilsTraites = $this->getDerniersProfilsTraites($userId);
            $profilsAttentionImmediate = $this->getProfilsAttentionImmediate($currentDate);
            
            // Répartitions
            $visaParStatut = $this->getVisaParStatut();
            $visaParType = $this->getVisaParType();

            // ==================== DONNÉES POUR LES FILTRES ====================
            
            $filterData = [
                'current_period' => $periodFilter,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'period_label' => $this->getPeriodLabel($periodFilter, $startDate, $endDate)
            ];

            Log::info('Dashboard Comptoir chargé avec succès - 5 modules', [
                'user_id' => $userId,
                'period_filter' => $periodFilter,
                'modules' => 5,
                'profils_visa_total' => $profilVisaStats['total_global'],
                'profils_traites_agent' => $profilVisaStats['traites_agent'],
                'souscriptions' => $souscriptionStats['total'],
                'evolution_data' => count($evolutionModules)
            ]);

            // Si c'est une requête AJAX, retourner JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'profil_visa' => $profilVisaStats,
                    'souscriptions' => $souscriptionStats,
                    'documents_voyage' => $documentsVoyageStats,
                    'rendez_vous' => $rendezVousStats,
                    'reservations' => $reservationStats,
                    'evolution' => $evolutionModules,
                    'top_destinations' => $topDestinations,
                    'activites_recentes' => $activitesRecentes,
                    'objectifs' => $objectifs,
                    'filter_info' => $filterData
                ]);
            }

            return view('pages.comptoir-dashboard', [
                // Statistiques 5 modules
                'profilVisaStats' => $profilVisaStats,
                'souscriptionStats' => $souscriptionStats,
                'documentsVoyageStats' => $documentsVoyageStats,
                'rendezVousStats' => $rendezVousStats,
                'reservationStats' => $reservationStats,
                
                // Évolution et graphiques
                'evolutionModules' => $evolutionModules,
                'topDestinations' => $topDestinations,
                'activitesRecentes' => $activitesRecentes,
                
                // Objectifs
                'objectifs' => $objectifs,
                
                // Performance agent spécifique
                'tempsReponse' => $tempsReponse,
                'tauxResolution' => $tauxResolution,
                'tauxSatisfaction' => $tauxSatisfaction,
                'productiviteStats' => $productiviteStats,
                'classementAgent' => $classementAgent,
                
                // Listes et détails
                'nouveauxClients' => $nouveauxClients,
                'derniersProfilsTraites' => $derniersProfilsTraites,
                'profilsAttentionImmediate' => $profilsAttentionImmediate,
                
                // Répartitions
                'visaParStatut' => $visaParStatut,
                'visaParType' => $visaParType,
                
                // Filtres
                'filterData' => $filterData
            ]);

        } catch (\Exception $e) {
            Log::error('⚠️ Erreur Dashboard Comptoir: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors du chargement des données'], 500);
            }
            
            return view('pages.comptoir-dashboard', $this->getDefaultComptoirData());
        }
    }

    // ==================== CALCUL STATISTIQUES 5 MODULES ====================

    /**
     * PROFIL VISA - Statistiques globales + agent
     */
    private function calculateProfilVisaStats($userId, $startDate, $endDate, $currentDate): array
    {
        try {
            $stats = [
                // Globales (toute l'entreprise)
                'total_global' => ProfilVisa::where('ent1d', 1)->count(),
                'periode_global' => ProfilVisa::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'aujourd_hui_global' => ProfilVisa::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'ce_mois_global' => ProfilVisa::where('ent1d', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count(),
                
                // Spécifiques agent
                'traites_agent' => 0,
                'traites_periode_agent' => 0,
                'traites_aujourd_hui_agent' => 0,
                
                // Statuts
                'en_attente' => 0,
                'urgents' => 0,
                'taux_reussite' => 0
            ];

            // Profils traités par l'agent
            $stats['traites_agent'] = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->count();

            $stats['traites_periode_agent'] = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->count();

            $stats['traites_aujourd_hui_agent'] = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->whereDate('updated_at', $currentDate->toDateString())
                ->count();

            // En attente (global)
            $stats['en_attente'] = ProfilVisa::where('ent1d', 1)
                ->where(function($query) {
                    $query->whereNull('id_statuts_etat')
                          ->orWhereHas('statutEtat', function($subQuery) {
                              $subQuery->where('libelle', 'like', '%attente%')
                                        ->orWhere('libelle', 'like', '%pending%')
                                        ->orWhere('libelle', 'like', '%en cours%');
                          });
                })
                ->count();

            // Urgents (plus de 7 jours)
            $stats['urgents'] = ProfilVisa::where('ent1d', 1)
                ->where('created_at', '<', $currentDate->copy()->subDays(7))
                ->where(function($query) {
                    $query->whereNull('id_statuts_etat')
                          ->orWhereHas('statutEtat', function($subQuery) {
                              $subQuery->where('libelle', 'like', '%attente%');
                          });
                })
                ->count();

            // Taux de réussite
            if ($stats['periode_global'] > 0) {
                $approuves = ProfilVisa::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statutEtat', function($query) {
                        $query->where('libelle', 'like', '%approuvé%')
                              ->orWhere('libelle', 'like', '%délivré%')
                              ->orWhere('libelle', 'like', '%terminé%');
                    })
                    ->count();

                $stats['taux_reussite'] = round(($approuves / $stats['periode_global']) * 100, 1);
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error("Erreur calcul stats profil visa:", ['error' => $e->getMessage()]);
            return $this->getDefaultProfilVisaStats();
        }
    }

    /**
     * SOUSCRIPTION FORFAITS - Statistiques complètes
     */
    private function calculateSouscriptionStats($startDate, $endDate, $currentDate): array
    {
        try {
            $stats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'chiffre_affaires_periode' => 0,
                'chiffre_affaires_mois' => 0,
                'panier_moyen' => 0,
                'actives' => 0,
                'en_attente' => 0
            ];

            if (!Schema::hasTable('souscrire_forfaits')) {
                return $stats;
            }

            // Total
            $stats['total'] = SouscrireForfaits::count();
            
            // Période
            $stats['periode'] = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Aujourd'hui
            $stats['aujourd_hui'] = SouscrireForfaits::whereDate('created_at', $currentDate->toDateString())
                ->count();
                
            // Ce mois
            $stats['ce_mois'] = SouscrireForfaits::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count();
            
            // Actives dans la période
            $stats['actives'] = SouscrireForfaits::where('etat', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // En attente dans la période
            $stats['en_attente'] = SouscrireForfaits::where('etat', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // Chiffre d'affaires période
            $stats['chiffre_affaires_periode'] = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('montant')
                ->where('montant', '>', 0)
                ->sum('montant') ?? 0;
            
            // Chiffre d'affaires ce mois
            $stats['chiffre_affaires_mois'] = SouscrireForfaits::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->whereNotNull('montant')
                ->where('montant', '>', 0)
                ->sum('montant') ?? 0;

            // Panier moyen
            $stats['panier_moyen'] = $stats['periode'] > 0 
                ? round($stats['chiffre_affaires_periode'] / $stats['periode'], 0) 
                : 0;

            return $stats;

        } catch (\Exception $e) {
            Log::error("Erreur calcul stats souscriptions:", ['error' => $e->getMessage()]);
            return $this->getDefaultSouscriptionStats();
        }
    }

    /**
     * DOCUMENTS VOYAGE - Statistiques complètes
     */
    private function calculateDocumentsVoyageStats($startDate, $endDate, $currentDate): array
    {
        try {
            $stats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'en_cours' => 0,
                'traites' => 0,
                'taux_traitement' => 0
            ];

            if (!Schema::hasTable('documents_voyage')) {
                return $stats;
            }

            $stats['total'] = DocumentsVoyage::where('ent1d', 1)->count();
            $stats['periode'] = DocumentsVoyage::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $stats['aujourd_hui'] = DocumentsVoyage::where('ent1d', 1)
                ->whereDate('created_at', $currentDate->toDateString())
                ->count();
            $stats['ce_mois'] = DocumentsVoyage::where('ent1d', 1)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count();
            $stats['en_cours'] = DocumentsVoyage::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 0)
                ->count();
            $stats['traites'] = DocumentsVoyage::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();

            $stats['taux_traitement'] = $stats['periode'] > 0 
                ? round(($stats['traites'] / $stats['periode']) * 100, 1) 
                : 0;

            return $stats;

        } catch (\Exception $e) {
            Log::error("Erreur calcul stats documents voyage:", ['error' => $e->getMessage()]);
            return $this->getDefaultDocumentsStats();
        }
    }

    /**
     * RENDEZ-VOUS - Statistiques complètes
     */
    private function calculateRendezVousStats($startDate, $endDate, $currentDate): array
    {
        try {
            $stats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'confirmes' => 0,
                'en_attente' => 0,
                'a_venir' => 0,
                'taux_confirmation' => 0
            ];

            if (!Schema::hasTable('rendez_vous')) {
                return $stats;
            }

            $stats['total'] = RendezVous::count();
            $stats['periode'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $stats['aujourd_hui'] = RendezVous::whereDate('created_at', $currentDate->toDateString())
                ->count();
            $stats['ce_mois'] = RendezVous::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count();
            $stats['confirmes'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();
            $stats['en_attente'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 0)
                ->count();
            
            // RDV à venir (date future)
            $stats['a_venir'] = RendezVous::whereDate('date_rdv', '>=', $currentDate->toDateString())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $stats['taux_confirmation'] = $stats['periode'] > 0 
                ? round(($stats['confirmes'] / $stats['periode']) * 100, 1) 
                : 0;

            return $stats;

        } catch (\Exception $e) {
            Log::error("Erreur calcul stats rendez-vous:", ['error' => $e->getMessage()]);
            return $this->getDefaultRendezVousStats();
        }
    }

    /**
     * RÉSERVATIONS ACHAT - Statistiques complètes
     */
    private function calculateReservationStats($startDate, $endDate, $currentDate): array
    {
        try {
            $stats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'confirmees' => 0,
                'en_attente' => 0,
                'type_populaire' => 'Aller-Retour',
                'taux_confirmation' => 0
            ];

            if (!Schema::hasTable('reservation_achat')) {
                return $stats;
            }

            $stats['total'] = ReservationAchat::where('ent1d', 1)->count();
            $stats['periode'] = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $stats['aujourd_hui'] = ReservationAchat::where('ent1d', 1)
                ->whereDate('created_at', $currentDate->toDateString())
                ->count();
            $stats['ce_mois'] = ReservationAchat::where('ent1d', 1)
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count();
            $stats['confirmees'] = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();
            $stats['en_attente'] = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 0)
                ->count();

            // Type de voyage le plus populaire dans la période
            $typePopulaire = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('type_voyage, COUNT(*) as total')
                ->whereNotNull('type_voyage')
                ->groupBy('type_voyage')
                ->orderBy('total', 'desc')
                ->first();
            
            $stats['type_populaire'] = $typePopulaire->type_voyage ?? 'Aller-Retour';

            $stats['taux_confirmation'] = $stats['periode'] > 0 
                ? round(($stats['confirmees'] / $stats['periode']) * 100, 1) 
                : 0;

            return $stats;

        } catch (\Exception $e) {
            Log::error("Erreur calcul stats réservations:", ['error' => $e->getMessage()]);
            return $this->getDefaultReservationStats();
        }
    }

    /**
     * ÉVOLUTION DES 5 MODULES - Adaptée à la période
     */
    private function getEvolutionData($startDate, $endDate, $periodFilter, $userId): array
    {
        $evolutionData = [];
        
        try {
            if (in_array($periodFilter, ['today', 'yesterday'])) {
                // Évolution par heure
                for ($hour = 0; $hour < 24; $hour++) {
                    $hourStart = $startDate->copy()->setHour($hour)->setMinute(0)->setSecond(0);
                    $hourEnd = $hourStart->copy()->setMinute(59)->setSecond(59);
                    
                    $evolutionData[] = [
                        'label' => $hour . 'h',
                        'profil_visa' => ProfilVisa::where('ent1d', 1)
                            ->whereBetween('created_at', [$hourStart, $hourEnd])
                            ->count(),
                        'profil_visa_agent' => ProfilVisa::where('ent1d', 1)
                            ->where(function($query) use ($userId) {
                                $query->where('user1d', $userId)
                                      ->orWhere('update_user', $userId);
                            })
                            ->whereBetween('updated_at', [$hourStart, $hourEnd])
                            ->count(),
                        'souscriptions' => Schema::hasTable('souscrire_forfaits') ? 
                            SouscrireForfaits::whereBetween('created_at', [$hourStart, $hourEnd])->count() : 0,
                        'documents_voyage' => Schema::hasTable('documents_voyage') ?
                            DocumentsVoyage::where('ent1d', 1)
                                ->whereBetween('created_at', [$hourStart, $hourEnd])->count() : 0,
                        'rendez_vous' => Schema::hasTable('rendez_vous') ?
                            RendezVous::whereBetween('created_at', [$hourStart, $hourEnd])->count() : 0,
                        'reservations' => Schema::hasTable('reservation_achat') ?
                            ReservationAchat::where('ent1d', 1)
                                ->whereBetween('created_at', [$hourStart, $hourEnd])->count() : 0,
                    ];
                }
            } elseif (in_array($periodFilter, ['this_week', 'last_week', 'last_30_days'])) {
                // Évolution par jour
                $diffInDays = $startDate->diffInDays($endDate);
                
                for ($i = 0; $i <= $diffInDays; $i++) {
                    $day = $startDate->copy()->addDays($i);
                    
                    $evolutionData[] = [
                        'label' => $day->format('d/m'),
                        'profil_visa' => ProfilVisa::where('ent1d', 1)
                            ->whereDate('created_at', $day->toDateString())
                            ->count(),
                        'profil_visa_agent' => ProfilVisa::where('ent1d', 1)
                            ->where(function($query) use ($userId) {
                                $query->where('user1d', $userId)
                                      ->orWhere('update_user', $userId);
                            })
                            ->whereDate('updated_at', $day->toDateString())
                            ->count(),
                        'souscriptions' => Schema::hasTable('souscrire_forfaits') ? 
                            SouscrireForfaits::whereDate('created_at', $day->toDateString())->count() : 0,
                        'documents_voyage' => Schema::hasTable('documents_voyage') ?
                            DocumentsVoyage::where('ent1d', 1)
                                ->whereDate('created_at', $day->toDateString())->count() : 0,
                        'rendez_vous' => Schema::hasTable('rendez_vous') ?
                            RendezVous::whereDate('created_at', $day->toDateString())->count() : 0,
                        'reservations' => Schema::hasTable('reservation_achat') ?
                            ReservationAchat::where('ent1d', 1)
                                ->whereDate('created_at', $day->toDateString())->count() : 0,
                    ];
                }
            } else {
                // Évolution par mois
                $startMonth = $startDate->copy()->startOfMonth();
                $endMonth = $endDate->copy()->startOfMonth();
                
                while ($startMonth->lte($endMonth)) {
                    $evolutionData[] = [
                        'label' => $startMonth->format('M Y'),
                        'profil_visa' => ProfilVisa::where('ent1d', 1)
                            ->whereMonth('created_at', $startMonth->month)
                            ->whereYear('created_at', $startMonth->year)
                            ->count(),
                        'profil_visa_agent' => ProfilVisa::where('ent1d', 1)
                            ->where(function($query) use ($userId) {
                                $query->where('user1d', $userId)
                                      ->orWhere('update_user', $userId);
                            })
                            ->whereMonth('updated_at', $startMonth->month)
                            ->whereYear('updated_at', $startMonth->year)
                            ->count(),
                        'souscriptions' => Schema::hasTable('souscrire_forfaits') ? 
                            SouscrireForfaits::whereMonth('created_at', $startMonth->month)
                                ->whereYear('created_at', $startMonth->year)->count() : 0,
                        'documents_voyage' => Schema::hasTable('documents_voyage') ?
                            DocumentsVoyage::where('ent1d', 1)
                                ->whereMonth('created_at', $startMonth->month)
                                ->whereYear('created_at', $startMonth->year)->count() : 0,
                        'rendez_vous' => Schema::hasTable('rendez_vous') ?
                            RendezVous::whereMonth('created_at', $startMonth->month)
                                ->whereYear('created_at', $startMonth->year)->count() : 0,
                        'reservations' => Schema::hasTable('reservation_achat') ?
                            ReservationAchat::where('ent1d', 1)
                                ->whereMonth('created_at', $startMonth->month)
                                ->whereYear('created_at', $startMonth->year)->count() : 0,
                    ];
                    
                    $startMonth->addMonth();
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur getEvolutionData: ' . $e->getMessage());
        }
        
        return $evolutionData;
    }

    // ==================== MÉTHODES UTILITAIRES SPÉCIFIQUES COMPTOIR ====================

    private function calculateResponseTime($userId, $startDate, $endDate): float
    {
        try {
            $result = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('update_user', $userId)
                          ->orWhere('user1d', $userId);
                })
                ->whereNotNull('updated_at')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                ->first();

            return round($result->avg_hours ?? 24, 1);
        } catch (\Exception $e) {
            return 24.0;
        }
    }

    private function calculateTauxResolution($userId, $startDate, $endDate): float
    {
        try {
            $totalTraites = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('update_user', $userId)
                          ->orWhere('user1d', $userId);
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->count();

            if ($totalTraites == 0) {
                return 85.0;
            }

            $totalResolus = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('update_user', $userId)
                          ->orWhere('user1d', $userId);
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->whereHas('statutEtat', function($query) {
                    $query->where('libelle', 'like', '%approuvé%')
                          ->orWhere('libelle', 'like', '%délivré%')
                          ->orWhere('libelle', 'like', '%terminé%');
                })
                ->count();

            return round(($totalResolus / $totalTraites) * 100, 1);
        } catch (\Exception $e) {
            return 85.0;
        }
    }

    private function calculateTauxSatisfaction($userId, $startDate, $endDate): float
    {
        try {
            $tempsReponse = $this->calculateResponseTime($userId, $startDate, $endDate);
            $tauxResolution = $this->calculateTauxResolution($userId, $startDate, $endDate);
            
            $scoreTemps = max(0, 100 - ($tempsReponse * 2));
            $satisfaction = ($scoreTemps + $tauxResolution) / 2;
            
            return round(min(100, max(70, $satisfaction)), 1);
        } catch (\Exception $e) {
            return 88.0;
        }
    }

    private function calculateProductiviteStats($userId, $currentDate): array
    {
        try {
            $stats = [
                'moyenne_quotidienne' => 0,
                'pic_productivite' => '10:00',
                'efficacite_score' => 0
            ];

            // Moyenne quotidienne sur 30 jours
            $derniers30Jours = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->where('updated_at', '>=', $currentDate->copy()->subDays(30))
                ->count();

            $stats['moyenne_quotidienne'] = round($derniers30Jours / 30, 1);

            // Score d'efficacité
            $objectifQuotidien = 25;
            $performanceActuelle = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->whereDate('updated_at', $currentDate->toDateString())
                ->count();
                
            $stats['efficacite_score'] = min(100, round(($performanceActuelle / $objectifQuotidien) * 100));

            return $stats;
        } catch (\Exception $e) {
            return [
                'moyenne_quotidienne' => 0,
                'pic_productivite' => '10:00',
                'efficacite_score' => 0
            ];
        }
    }

    private function getClassementAgent($userId, $currentDate): array
    {
        try {
            $agentsPerformance = DB::table('users')
                ->leftJoin('profil_visa', function($join) use ($currentDate) {
                    $join->on('users.id', '=', 'profil_visa.update_user')
                         ->whereMonth('profil_visa.updated_at', $currentDate->month)
                         ->whereYear('profil_visa.updated_at', $currentDate->year);
                })
                ->whereIn('users.type_user', ['agent_comptoir'])
                ->where('users.ent1d', 1)
                ->select('users.id', 'users.name', DB::raw('COUNT(profil_visa.id) as total_traites'))
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_traites', 'desc')
                ->get();

            $position = 1;
            $totalAgents = $agentsPerformance->count();
            
            foreach ($agentsPerformance as $index => $agent) {
                if ($agent->id == $userId) {
                    $position = $index + 1;
                    break;
                }
            }

            return [
                'position' => $position,
                'total_agents' => $totalAgents,
                'percentile' => $totalAgents > 0 ? round((($totalAgents - $position + 1) / $totalAgents) * 100) : 100
            ];
        } catch (\Exception $e) {
            return ['position' => 1, 'total_agents' => 1, 'percentile' => 100];
        }
    }

    // ==================== MÉTHODES COMMUNES AVEC COMMERCIAL ====================

    private function getTopDestinationsForPeriod($startDate, $endDate)
    {
        try {
            if (!Schema::hasTable('reservation_achat')) {
                return collect();
            }
            
            return ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('pays_destination, COUNT(*) as total')
                ->whereNotNull('pays_destination')
                ->where('pays_destination', '!=', '')
                ->groupBy('pays_destination')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getActivitiesForPeriod($startDate, $endDate, $userId)
    {
        $activitesRecentes = collect();
        
        try {
            // Profils traités par l'agent
            $profilsTraites = ProfilVisa::where('ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('user1d', $userId)
                          ->orWhere('update_user', $userId);
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($profil) {
                    return [
                        'type' => 'profil_visa_traite',
                        'message' => "Profil traité : " . ($profil->user->name ?? 'Client inconnu'),
                        'date' => $profil->updated_at,
                        'date_formatted' => $profil->updated_at->diffForHumans(),
                        'icon' => 'fas fa-passport',
                        'color' => 'primary'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($profilsTraites);

            // Nouveaux profils visa (global)
            $nouveauxProfils = ProfilVisa::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function($profil) {
                    return [
                        'type' => 'profil_visa_nouveau',
                        'message' => "Nouveau profil visa : " . ($profil->user->name ?? 'Client inconnu'),
                        'date' => $profil->created_at,
                        'date_formatted' => $profil->created_at->diffForHumans(),
                        'icon' => 'fas fa-plus-circle',
                        'color' => 'success'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($nouveauxProfils);

            // Nouvelles souscriptions
            if (Schema::hasTable('souscrire_forfaits')) {
                $nouvellesSouscriptions = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function($souscription) {
                        return [
                            'type' => 'souscription',
                            'message' => "Nouvelle souscription : " . $souscription->nom . " " . $souscription->prenom,
                            'date' => $souscription->created_at,
                            'date_formatted' => $souscription->created_at->diffForHumans(),
                            'icon' => 'fas fa-tags',
                            'color' => 'info'
                        ];
                    });

                $activitesRecentes = $activitesRecentes->merge($nouvellesSouscriptions);
            }

            return $activitesRecentes->sortByDesc('date')->take(15);
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function calculateObjectives($profilVisaStats, $souscriptionStats, $rendezVousStats, $periodFilter): array
    {
        // Objectifs adaptés au contexte comptoir
        $baseObjectives = [
            'profil_visa_daily' => 25,    // Agent comptoir traite plus de profils
            'profil_visa_weekly' => 175,
            'profil_visa_monthly' => 700,
            'souscriptions_daily' => 3,
            'souscriptions_weekly' => 15,
            'souscriptions_monthly' => 60,
            'rdv_daily' => 4,
            'rdv_weekly' => 20,
            'rdv_monthly' => 80,
        ];

        switch ($periodFilter) {
            case 'today':
            case 'yesterday':
                $objectifs = [
                    'profil_visa_periode' => $baseObjectives['profil_visa_daily'],
                    'souscriptions_periode' => $baseObjectives['souscriptions_daily'],
                    'rdv_periode' => $baseObjectives['rdv_daily'],
                ];
                break;
            case 'this_week':
            case 'last_week':
                $objectifs = [
                    'profil_visa_periode' => $baseObjectives['profil_visa_weekly'],
                    'souscriptions_periode' => $baseObjectives['souscriptions_weekly'],
                    'rdv_periode' => $baseObjectives['rdv_weekly'],
                ];
                break;
            default:
                $objectifs = [
                    'profil_visa_periode' => $baseObjectives['profil_visa_monthly'],
                    'souscriptions_periode' => $baseObjectives['souscriptions_monthly'],
                    'rdv_periode' => $baseObjectives['rdv_monthly'],
                ];
        }

        return [
            'profil_visa_mois' => $baseObjectives['profil_visa_monthly'],
            'souscriptions_mois' => $baseObjectives['souscriptions_monthly'],
            'rdv_mois' => $baseObjectives['rdv_monthly'],
            'profil_visa_periode' => $objectifs['profil_visa_periode'],
            'souscriptions_periode' => $objectifs['souscriptions_periode'],
            'rdv_periode' => $objectifs['rdv_periode'],
            'progression_profils' => $profilVisaStats['traites_periode_agent'] > 0 ? 
                min(100, ($profilVisaStats['traites_periode_agent'] / $objectifs['profil_visa_periode']) * 100) : 0,
            'progression_souscriptions' => $souscriptionStats['periode'] > 0 ? 
                min(100, ($souscriptionStats['periode'] / $objectifs['souscriptions_periode']) * 100) : 0,
            'progression_rdv' => $rendezVousStats['periode'] > 0 ? 
                min(100, ($rendezVousStats['periode'] / $objectifs['rdv_periode']) * 100) : 0
        ];
    }

    // ==================== API ET FILTRAGE ====================

    public function filterByPeriod(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkComptoirAccess($user)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $request->validate([
                'period' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);

            $periodFilter = $request->input('period', 'this_month');
            $customStartDate = $request->input('start_date');
            $customEndDate = $request->input('end_date');
            
            $dateRange = $this->getDateRange($periodFilter, $customStartDate, $customEndDate);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            $userId = $user->id;
            $currentDate = Carbon::now();

            $stats = [
                'profil_visa' => $this->calculateProfilVisaStats($userId, $startDate, $endDate, $currentDate),
                'souscriptions' => $this->calculateSouscriptionStats($startDate, $endDate, $currentDate),
                'documents_voyage' => $this->calculateDocumentsVoyageStats($startDate, $endDate, $currentDate),
                'rendez_vous' => $this->calculateRendezVousStats($startDate, $endDate, $currentDate),
                'reservations' => $this->calculateReservationStats($startDate, $endDate, $currentDate),
                'evolution' => $this->getEvolutionData($startDate, $endDate, $periodFilter, $userId),
                'top_destinations' => $this->getTopDestinationsForPeriod($startDate, $endDate),
                'activites_recentes' => $this->getActivitiesForPeriod($startDate, $endDate, $userId),
                'filter_info' => [
                    'period' => $periodFilter,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'label' => $this->getPeriodLabel($periodFilter, $startDate, $endDate)
                ]
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur filterByPeriod Comptoir: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du filtrage'], 500);
        }
    }

    public function getRealtimeStats()
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkComptoirAccess($user)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
            
            $currentDate = Carbon::now();
            $userId = $user->id;
            
            return response()->json([
                'profil_visa_global_aujourd_hui' => ProfilVisa::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'profils_traites_agent_aujourd_hui' => ProfilVisa::where('ent1d', 1)
                    ->where(function($query) use ($userId) {
                        $query->where('user1d', $userId)
                              ->orWhere('update_user', $userId);
                    })
                    ->whereDate('updated_at', $currentDate->toDateString())
                    ->count(),
                'souscriptions_aujourd_hui' => Schema::hasTable('souscrire_forfaits') ?
                    SouscrireForfaits::whereDate('created_at', $currentDate->toDateString())->count() : 0,
                'rdv_aujourd_hui' => Schema::hasTable('rendez_vous') ?
                    RendezVous::whereDate('created_at', $currentDate->toDateString())->count() : 0,
                'reservations_aujourd_hui' => Schema::hasTable('reservation_achat') ?
                    ReservationAchat::where('ent1d', 1)
                        ->whereDate('created_at', $currentDate->toDateString())->count() : 0,
                'profils_en_attente' => ProfilVisa::where('ent1d', 1)
                    ->where(function($query) {
                        $query->whereNull('id_statuts_etat')
                              ->orWhereHas('statutEtat', function($subQuery) {
                                  $subQuery->where('libelle', 'like', '%attente%');
                              });
                    })
                    ->count(),
                'last_update' => now()->format('H:i:s'),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getRealtimeStats Comptoir: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    // ==================== MÉTHODES UTILITAIRES ====================

    private function getNouveauxClients($currentDate)
    {
        try {
            return DB::table('profil_visa')
                ->join('users', 'profil_visa.user1d', '=', 'users.id')
                ->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                ->where('profil_visa.ent1d', 1)
                ->where('profil_visa.created_at', '>=', $currentDate->copy()->subHours(48))
                ->select(
                    'profil_visa.*',
                    'users.name as client_name',
                    'users.contact as client_contact',
                    'statuts_etat.libelle as status_name',
                    'statuts_etat.couleur as status_color'
                )
                ->orderBy('profil_visa.created_at', 'desc')
                ->limit(15)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getDerniersProfilsTraites($userId)
    {
        try {
            return DB::table('profil_visa')
                ->join('users', 'profil_visa.user1d', '=', 'users.id')
                ->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                ->where('profil_visa.ent1d', 1)
                ->where(function($query) use ($userId) {
                    $query->where('profil_visa.update_user', $userId)
                          ->orWhere('profil_visa.user1d', $userId);
                })
                ->select(
                    'profil_visa.*',
                    'users.name as client_name',
                    'statuts_etat.libelle as status_name',
                    'statuts_etat.couleur as status_color'
                )
                ->orderBy('profil_visa.updated_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getProfilsAttentionImmediate($currentDate)
    {
        try {
            return DB::table('profil_visa')
                ->join('users', 'profil_visa.user1d', '=', 'users.id')
                ->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                ->where('profil_visa.ent1d', 1)
                ->where(function($query) {
                    $query->whereNull('profil_visa.id_statuts_etat')
                          ->orWhereHas('statutEtat', function($subQuery) {
                              $subQuery->where('libelle', 'like', '%attente%');
                          });
                })
                ->where('profil_visa.created_at', '<', $currentDate->copy()->subDays(3))
                ->select(
                    'profil_visa.*',
                    'users.name as client_name',
                    'users.contact as client_contact',
                    'statuts_etat.libelle as status_name',
                    'statuts_etat.couleur as status_color'
                )
                ->orderBy('profil_visa.created_at', 'asc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getVisaParStatut()
    {
        try {
            $stats = DB::table('profil_visa')
                ->leftJoin('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                ->where('profil_visa.ent1d', 1)
                ->select(
                    DB::raw('COALESCE(statuts_etat.libelle, "Sans statut") as status_name'),
                    DB::raw('COALESCE(statuts_etat.couleur, "6c757d") as status_color'),
                    DB::raw('count(profil_visa.id) as total')
                )
                ->groupBy('statuts_etat.id', 'statuts_etat.libelle', 'statuts_etat.couleur')
                ->orderBy('total', 'desc')
                ->get();

            if ($stats->isEmpty()) {
                $totalProfils = ProfilVisa::where('ent1d', 1)->count();
                $stats = collect([
                    (object)[
                        'status_name' => 'Tous les profils',
                        'status_color' => '4facfe',
                        'total' => max(1, $totalProfils)
                    ]
                ]);
            }

            return $stats;
        } catch (\Exception $e) {
            return collect([
                (object)[
                    'status_name' => 'Données indisponibles',
                    'status_color' => '6c757d',
                    'total' => 1
                ]
            ]);
        }
    }

    private function getVisaParType()
    {
        try {
            $types = [
                'tourisme' => 'Tourisme',
                'mineur' => 'Mineur',
                'etude' => 'Étudiant',
                'travail' => 'Travail',
                'affaires' => 'Affaires'
            ];

            $stats = collect();
            foreach ($types as $key => $label) {
                $count = ProfilVisa::where('ent1d', 1)
                    ->where('type_profil_visa', $key)
                    ->count();
                
                if ($count > 0) {
                    $stats->push((object)[
                        'type_name' => $label,
                        'type_key' => $key,
                        'total' => $count
                    ]);
                }
            }

            if ($stats->isEmpty()) {
                $stats->push((object)[
                    'type_name' => 'Profils divers',
                    'type_key' => 'divers',
                    'total' => ProfilVisa::where('ent1d', 1)->count() ?: 1
                ]);
            }

            return $stats->sortByDesc('total');
        } catch (\Exception $e) {
            return collect([
                (object)[
                    'type_name' => 'Non défini',
                    'type_key' => 'undefined',
                    'total' => 1
                ]
            ]);
        }
    }

    private function getDateRange($periodFilter, $customStartDate = null, $customEndDate = null)
    {
        $now = Carbon::now();
        
        switch ($periodFilter) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay()
                ];
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeek()->startOfWeek(),
                    'end' => $now->copy()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth()
                ];
            case 'this_quarter':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            case 'last_quarter':
                return [
                    'start' => $now->copy()->subQuarter()->startOfQuarter(),
                    'end' => $now->copy()->subQuarter()->endOfQuarter()
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            case 'last_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear(),
                    'end' => $now->copy()->subYear()->endOfYear()
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30)->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'last_90_days':
                return [
                    'start' => $now->copy()->subDays(90)->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    try {
                        return [
                            'start' => Carbon::parse($customStartDate)->startOfDay(),
                            'end' => Carbon::parse($customEndDate)->endOfDay()
                        ];
                    } catch (\Exception $e) {
                        Log::error('Erreur parsing dates custom: ' . $e->getMessage());
                    }
                }
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function getPeriodLabel($periodFilter, $startDate, $endDate)
    {
        switch ($periodFilter) {
            case 'today':
                return 'Aujourd\'hui (' . $startDate->format('d/m/Y') . ')';
            case 'yesterday':
                return 'Hier (' . $startDate->format('d/m/Y') . ')';
            case 'this_week':
                return 'Cette semaine (' . $startDate->format('d/m') . ' - ' . $endDate->format('d/m/Y') . ')';
            case 'last_week':
                return 'Semaine dernière (' . $startDate->format('d/m') . ' - ' . $endDate->format('d/m/Y') . ')';
            case 'this_month':
                return 'Ce mois (' . $startDate->format('M Y') . ')';
            case 'last_month':
                return 'Mois dernier (' . $startDate->format('M Y') . ')';
            case 'this_quarter':
                return 'Ce trimestre (T' . $startDate->quarter . ' ' . $startDate->year . ')';
            case 'last_quarter':
                return 'Trimestre dernier (T' . $startDate->quarter . ' ' . $startDate->year . ')';
            case 'this_year':
                return 'Cette année (' . $startDate->year . ')';
            case 'last_year':
                return 'Année dernière (' . $startDate->year . ')';
            case 'last_30_days':
                return '30 derniers jours';
            case 'last_90_days':
                return '90 derniers jours';
            case 'custom':
                return 'Du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y');
            default:
                return 'Période sélectionnée';
        }
    }

    private function checkComptoirAccess($user): bool
    {
        try {
            return $user->hasRole('Agent Comptoir') || 
                   $user->type_user === 'agent_comptoir' || 
                   $user->hasAnyRole(['Super Admin', 'Admin']);
        } catch (\Exception $e) {
            return ($user->type_user === 'agent_comptoir');
        }
    }

    // ==================== DONNÉES PAR DÉFAUT ====================

    private function getDefaultProfilVisaStats(): array
    {
        return [
            'total_global' => 0,
            'periode_global' => 0,
            'aujourd_hui_global' => 0,
            'ce_mois_global' => 0,
            'traites_agent' => 0,
            'traites_periode_agent' => 0,
            'traites_aujourd_hui_agent' => 0,
            'en_attente' => 0,
            'urgents' => 0,
            'taux_reussite' => 0
        ];
    }

    private function getDefaultSouscriptionStats(): array
    {
        return [
            'total' => 0,
            'periode' => 0,
            'aujourd_hui' => 0,
            'ce_mois' => 0,
            'chiffre_affaires_periode' => 0,
            'chiffre_affaires_mois' => 0,
            'panier_moyen' => 0,
            'actives' => 0,
            'en_attente' => 0
        ];
    }

    private function getDefaultDocumentsStats(): array
    {
        return [
            'total' => 0,
            'periode' => 0,
            'aujourd_hui' => 0,
            'ce_mois' => 0,
            'en_cours' => 0,
            'traites' => 0,
            'taux_traitement' => 0
        ];
    }

    private function getDefaultRendezVousStats(): array
    {
        return [
            'total' => 0,
            'periode' => 0,
            'aujourd_hui' => 0,
            'ce_mois' => 0,
            'confirmes' => 0,
            'en_attente' => 0,
            'a_venir' => 0,
            'taux_confirmation' => 0
        ];
    }

    private function getDefaultReservationStats(): array
    {
        return [
            'total' => 0,
            'periode' => 0,
            'aujourd_hui' => 0,
            'ce_mois' => 0,
            'confirmees' => 0,
            'en_attente' => 0,
            'type_populaire' => 'Aller-Retour',
            'taux_confirmation' => 0
        ];
    }

    private function getDefaultComptoirData(): array
    {
        return [
            'profilVisaStats' => $this->getDefaultProfilVisaStats(),
            'souscriptionStats' => $this->getDefaultSouscriptionStats(),
            'documentsVoyageStats' => $this->getDefaultDocumentsStats(),
            'rendezVousStats' => $this->getDefaultRendezVousStats(),
            'reservationStats' => $this->getDefaultReservationStats(),
            'evolutionModules' => [],
            'topDestinations' => collect(),
            'activitesRecentes' => collect(),
            'objectifs' => [
                'profil_visa_mois' => 700,
                'souscriptions_mois' => 60,
                'rdv_mois' => 80,
                'progression_profils' => 0,
                'progression_souscriptions' => 0,
                'progression_rdv' => 0
            ],
            'tempsReponse' => 24.0,
            'tauxResolution' => 85.0,
            'tauxSatisfaction' => 88.0,
            'productiviteStats' => [
                'moyenne_quotidienne' => 0,
                'pic_productivite' => '10:00',
                'efficacite_score' => 0
            ],
            'classementAgent' => [
                'position' => 1,
                'total_agents' => 1,
                'percentile' => 100
            ],
            'nouveauxClients' => collect(),
            'derniersProfilsTraites' => collect(),
            'profilsAttentionImmediate' => collect(),
            'visaParStatut' => collect(),
            'visaParType' => collect(),
            'filterData' => [
                'current_period' => 'this_month',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
                'period_label' => 'Ce mois'
            ]
        ];
    }
}