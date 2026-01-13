<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\DocumentsVoyage;
use App\Models\RendezVous;
use App\Models\ReservationAchat;
use App\Models\SouscrireForfaits;
use App\Models\Forfaits;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommercialDashboardController extends Controller
{
    /**
     * Dashboard Commercial PSI Africa - VERSION COMPLÈTE CORRIGÉE
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $currentDate = Carbon::now();
            
            // Vérification d'accès CORRIGÉE
            $hasAccess = $this->checkCommercialAccess($user);
            
            if (!$hasAccess) {
                Log::warning('Accès refusé au dashboard commercial', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_type' => $user->type_user
                ]);
                
                return redirect('/dashboard')->with('error', 'Accès non autorisé - Réservé aux commerciaux');
            }

            // ==================== GESTION DES FILTRES PAR PÉRIODE CORRIGÉE ====================
            
            $periodFilter = $request->input('period', 'this_month');
            $customStartDate = $request->input('start_date');
            $customEndDate = $request->input('end_date');
            
            $dateRange = $this->getDateRange($periodFilter, $customStartDate, $customEndDate);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
            
            Log::info('Filtres appliqués dans Dashboard Commercial', [
                'period' => $periodFilter,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'user_id' => $user->id
            ]);

            // ==================== STATISTIQUES PROFIL VISA AVEC FILTRES CORRIGÉES ====================
            
            $profilVisaStats = [
                'total' => ProfilVisa::where('ent1d', 1)->count(),
                'periode' => ProfilVisa::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'aujourd_hui' => ProfilVisa::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'ce_mois' => ProfilVisa::where('ent1d', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count(),
                'en_attente' => 0,
                'approuves' => 0,
                'taux_reussite' => 0
            ];

            try {
                // Calcul en attente - CORRIGÉ
                $profilVisaStats['en_attente'] = ProfilVisa::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where(function($query) {
                        $query->whereNull('id_statuts_etat')
                              ->orWhereHas('statutEtat', function($subQuery) {
                                  $subQuery->where('libelle', 'like', '%attente%')
                                            ->orWhere('libelle', 'like', '%pending%')
                                            ->orWhere('libelle', 'like', '%en cours%');
                              });
                    })
                    ->count();

                // Calcul approuvés - CORRIGÉ
                $profilVisaStats['approuves'] = ProfilVisa::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('statutEtat', function($query) {
                        $query->where('libelle', 'like', '%approuvé%')
                              ->orWhere('libelle', 'like', '%délivré%')
                              ->orWhere('libelle', 'like', '%terminé%')
                              ->orWhere('libelle', 'like', '%validé%');
                    })
                    ->count();

                $profilVisaStats['taux_reussite'] = $profilVisaStats['periode'] > 0 
                    ? round(($profilVisaStats['approuves'] / $profilVisaStats['periode']) * 100, 1) 
                    : 0;
            } catch (\Exception $e) {
                Log::error('Erreur calcul stats profil visa:', ['error' => $e->getMessage()]);
            }

            // ==================== STATISTIQUES SOUSCRIPTION FORFAITS CORRIGÉES - PROBLÈME PRINCIPAL RÉSOLU ====================
            
            $souscriptionStats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'chiffre_affaires_periode' => 0,
                'chiffre_affaires_mois' => 0,
                'panier_moyen' => 0,
                'forfaits_actifs' => 0,
                'en_attente' => 0,
                'actives' => 0
            ];

            try {
                // CORRECTION MAJEURE : Récupérer TOUTES les souscriptions, pas seulement etat=1
                $souscriptionStats['total'] = SouscrireForfaits::count();
                
                // Souscriptions pour la période filtrée (TOUTES les souscriptions)
                $souscriptionStats['periode'] = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                    
                // Souscriptions aujourd'hui
                $souscriptionStats['aujourd_hui'] = SouscrireForfaits::whereDate('created_at', $currentDate->toDateString())
                    ->count();
                    
                // Souscriptions ce mois
                $souscriptionStats['ce_mois'] = SouscrireForfaits::whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count();
                
                // Souscriptions actives dans la période
                $souscriptionStats['actives'] = SouscrireForfaits::where('etat', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                    
                // Souscriptions en attente dans la période
                $souscriptionStats['en_attente'] = SouscrireForfaits::where('etat', 0)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                // Chiffre d'affaires pour la période filtrée - CORRIGÉ (toutes les souscriptions avec montant)
                $souscriptionStats['chiffre_affaires_periode'] = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotNull('montant')
                    ->where('montant', '>', 0)
                    ->sum('montant') ?? 0;
                
                // Chiffre d'affaires ce mois - CORRIGÉ
                $souscriptionStats['chiffre_affaires_mois'] = SouscrireForfaits::whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->whereNotNull('montant')
                    ->where('montant', '>', 0)
                    ->sum('montant') ?? 0;

                // Panier moyen corrigé
                $souscriptionStats['panier_moyen'] = $souscriptionStats['periode'] > 0 
                    ? round($souscriptionStats['chiffre_affaires_periode'] / $souscriptionStats['periode'], 0) 
                    : 0;

                // Forfaits actifs
                if (class_exists('App\Models\Forfaits')) {
                    $souscriptionStats['forfaits_actifs'] = Forfaits::where('etat', 1)->count();
                }

                Log::info('Stats souscriptions calculées - CORRIGÉ', [
                    'total' => $souscriptionStats['total'],
                    'periode' => $souscriptionStats['periode'],
                    'actives' => $souscriptionStats['actives'],
                    'ca_periode' => $souscriptionStats['chiffre_affaires_periode'],
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString()
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur calcul stats souscriptions:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // ==================== STATISTIQUES DOCUMENTS VOYAGE CORRIGÉES ====================
            
            $documentsVoyageStats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'en_cours' => 0,
                'traites' => 0
            ];

            try {
                $documentsVoyageStats['total'] = DocumentsVoyage::where('ent1d', 1)->count();
                $documentsVoyageStats['periode'] = DocumentsVoyage::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                $documentsVoyageStats['aujourd_hui'] = DocumentsVoyage::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count();
                $documentsVoyageStats['ce_mois'] = DocumentsVoyage::where('ent1d', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count();
                $documentsVoyageStats['en_cours'] = DocumentsVoyage::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 0)
                    ->count();
                $documentsVoyageStats['traites'] = DocumentsVoyage::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 1)
                    ->count();
            } catch (\Exception $e) {
                Log::error('Erreur calcul stats documents voyage:', ['error' => $e->getMessage()]);
            }

            // ==================== STATISTIQUES RENDEZ-VOUS CORRIGÉES ====================
            
            $rendezVousStats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'confirmes' => 0,
                'en_attente' => 0,
                'a_venir' => 0
            ];

            try {
                $rendezVousStats['total'] = RendezVous::count();
                $rendezVousStats['periode'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                $rendezVousStats['aujourd_hui'] = RendezVous::whereDate('created_at', $currentDate->toDateString())
                    ->count();
                $rendezVousStats['ce_mois'] = RendezVous::whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count();
                $rendezVousStats['confirmes'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 1)
                    ->count();
                $rendezVousStats['en_attente'] = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 0)
                    ->count();
                
                // RDV à venir (dans la période filtrée) - CORRIGÉ
                $rendezVousStats['a_venir'] = RendezVous::whereDate('date_rdv', '>=', $currentDate->toDateString())
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            } catch (\Exception $e) {
                Log::error('Erreur calcul stats rendez-vous:', ['error' => $e->getMessage()]);
            }

            // ==================== STATISTIQUES RESERVATION ACHAT CORRIGÉES ====================
            
            $reservationStats = [
                'total' => 0,
                'periode' => 0,
                'aujourd_hui' => 0,
                'ce_mois' => 0,
                'confirmees' => 0,
                'en_attente' => 0,
                'type_populaire' => 'Aller-Retour'
            ];

            try {
                $reservationStats['total'] = ReservationAchat::where('ent1d', 1)->count();
                $reservationStats['periode'] = ReservationAchat::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                $reservationStats['aujourd_hui'] = ReservationAchat::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count();
                $reservationStats['ce_mois'] = ReservationAchat::where('ent1d', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count();
                $reservationStats['confirmees'] = ReservationAchat::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 1)
                    ->count();
                $reservationStats['en_attente'] = ReservationAchat::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('etat', 0)
                    ->count();

                // Type de voyage le plus populaire dans la période - CORRIGÉ
                $typePopulaire = ReservationAchat::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('type_voyage, COUNT(*) as total')
                    ->whereNotNull('type_voyage')
                    ->groupBy('type_voyage')
                    ->orderBy('total', 'desc')
                    ->first();
                
                $reservationStats['type_populaire'] = $typePopulaire->type_voyage ?? 'Aller-Retour';
            } catch (\Exception $e) {
                Log::error('Erreur calcul stats réservations:', ['error' => $e->getMessage()]);
            }

            // ==================== ÉVOLUTION ADAPTÉE À LA PÉRIODE CORRIGÉE ====================
            
            $evolutionModules = $this->getEvolutionData($startDate, $endDate, $periodFilter);

            // ==================== TOP DESTINATIONS POUR LA PÉRIODE CORRIGÉES ====================
            
            $topDestinations = collect();
            try {
                $topDestinations = ReservationAchat::where('ent1d', 1)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('pays_destination, COUNT(*) as total')
                    ->whereNotNull('pays_destination')
                    ->where('pays_destination', '!=', '')
                    ->groupBy('pays_destination')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Erreur calcul top destinations:', ['error' => $e->getMessage()]);
                $topDestinations = collect();
            }

            // ==================== ACTIVITÉS RÉCENTES POUR LA PÉRIODE CORRIGÉES ====================
            
            $activitesRecentes = $this->getActivitiesForPeriod($startDate, $endDate);

            // ==================== OBJECTIFS ET PERFORMANCES CORRIGÉS ====================
            
            $objectifs = $this->calculateObjectives($profilVisaStats, $souscriptionStats, $rendezVousStats, $periodFilter);

            // ==================== DONNÉES POUR LES FILTRES CORRIGÉES ====================
            
            $filterData = [
                'current_period' => $periodFilter,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'period_label' => $this->getPeriodLabel($periodFilter, $startDate, $endDate)
            ];

            Log::info('Dashboard Commercial chargé avec succès', [
                'user_id' => $user->id,
                'period_filter' => $periodFilter,
                'modules' => 5,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'souscriptions_total' => $souscriptionStats['total'],
                'souscriptions_periode' => $souscriptionStats['periode']
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

            return view('pages.commercial-dashboard', compact(
                'profilVisaStats',
                'souscriptionStats', 
                'documentsVoyageStats',
                'rendezVousStats',
                'reservationStats',
                'evolutionModules',
                'topDestinations',
                'activitesRecentes',
                'objectifs',
                'filterData'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur Dashboard Commercial: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Erreur lors du chargement des données'], 500);
            }
            
            return view('pages.commercial-dashboard', $this->getDefaultModulesData());
        }
    }

    /**
     * API pour filtrer les statistiques par période - MÉTHODE CORRIGÉE
     */
    public function filterByPeriod(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkCommercialAccess($user)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Validation des entrées - CORRIGÉE
            $request->validate([
                'period' => 'required|string|in:today,yesterday,this_week,last_week,this_month,last_month,this_quarter,last_quarter,this_year,last_year,last_30_days,last_90_days,custom',
                'start_date' => 'nullable|date|required_if:period,custom',
                'end_date' => 'nullable|date|required_if:period,custom|after_or_equal:start_date'
            ]);

            $periodFilter = $request->input('period', 'this_month');
            $customStartDate = $request->input('start_date');
            $customEndDate = $request->input('end_date');
            
            $dateRange = $this->getDateRange($periodFilter, $customStartDate, $customEndDate);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            Log::info('Filtrage par période demandé', [
                'user_id' => $user->id,
                'period' => $periodFilter,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            // Récupérer toutes les statistiques avec les nouveaux filtres - SOUSCRIPTIONS CORRIGÉES
            $stats = [
                'profil_visa' => $this->getProfilVisaStatsForPeriod($startDate, $endDate),
                'souscriptions' => $this->getSouscriptionStatsForPeriodCorrected($startDate, $endDate),
                'documents_voyage' => $this->getDocumentsVoyageStatsForPeriod($startDate, $endDate),
                'rendez_vous' => $this->getRendezVousStatsForPeriod($startDate, $endDate),
                'reservations' => $this->getReservationStatsForPeriod($startDate, $endDate),
                'evolution' => $this->getEvolutionData($startDate, $endDate, $periodFilter),
                'top_destinations' => $this->getTopDestinationsForPeriod($startDate, $endDate),
                'activites_recentes' => $this->getActivitiesForPeriod($startDate, $endDate),
                'objectifs' => $this->calculateObjectivesForPeriod($periodFilter, $startDate, $endDate),
                'filter_info' => [
                    'period' => $periodFilter,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'label' => $this->getPeriodLabel($periodFilter, $startDate, $endDate)
                ]
            ];

            Log::info('Stats filtrées retournées', [
                'souscriptions_total' => $stats['souscriptions']['total'] ?? 0,
                'period' => $periodFilter
            ]);

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur filterByPeriod: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du filtrage: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtenir la plage de dates selon le filtre - MÉTHODE CORRIGÉE
     */
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
                        $start = Carbon::parse($customStartDate)->startOfDay();
                        $end = Carbon::parse($customEndDate)->endOfDay();
                        
                        if ($start->greaterThan($end)) {
                            Log::warning('Date de début postérieure à la date de fin', [
                                'start' => $customStartDate,
                                'end' => $customEndDate
                            ]);
                            return [
                                'start' => $now->copy()->startOfMonth(),
                                'end' => $now->copy()->endOfMonth()
                            ];
                        }
                        
                        return ['start' => $start, 'end' => $end];
                    } catch (\Exception $e) {
                        Log::error('Erreur parsing dates personnalisées: ' . $e->getMessage());
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

    /**
     * Obtenir le libellé de la période - MÉTHODE CORRIGÉE
     */
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

    /**
     * Obtenir les données d'évolution adaptées à la période - MÉTHODE CORRIGÉE
     */
    private function getEvolutionData($startDate, $endDate, $periodFilter)
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
                        'souscriptions' => SouscrireForfaits::whereBetween('created_at', [$hourStart, $hourEnd])
                            ->count(),
                        'documents_voyage' => DocumentsVoyage::where('ent1d', 1)
                            ->whereBetween('created_at', [$hourStart, $hourEnd])
                            ->count(),
                        'rendez_vous' => RendezVous::whereBetween('created_at', [$hourStart, $hourEnd])
                            ->count(),
                        'reservations' => ReservationAchat::where('ent1d', 1)
                            ->whereBetween('created_at', [$hourStart, $hourEnd])
                            ->count(),
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
                        'souscriptions' => SouscrireForfaits::whereDate('created_at', $day->toDateString())
                            ->count(),
                        'documents_voyage' => DocumentsVoyage::where('ent1d', 1)
                            ->whereDate('created_at', $day->toDateString())
                            ->count(),
                        'rendez_vous' => RendezVous::whereDate('created_at', $day->toDateString())
                            ->count(),
                        'reservations' => ReservationAchat::where('ent1d', 1)
                            ->whereDate('created_at', $day->toDateString())
                            ->count(),
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
                        'souscriptions' => SouscrireForfaits::whereMonth('created_at', $startMonth->month)
                            ->whereYear('created_at', $startMonth->year)
                            ->count(),
                        'documents_voyage' => DocumentsVoyage::where('ent1d', 1)
                            ->whereMonth('created_at', $startMonth->month)
                            ->whereYear('created_at', $startMonth->year)
                            ->count(),
                        'rendez_vous' => RendezVous::whereMonth('created_at', $startMonth->month)
                            ->whereYear('created_at', $startMonth->year)
                            ->count(),
                        'reservations' => ReservationAchat::where('ent1d', 1)
                            ->whereMonth('created_at', $startMonth->month)
                            ->whereYear('created_at', $startMonth->year)
                            ->count(),
                    ];
                    
                    $startMonth->addMonth();
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur getEvolutionData: ' . $e->getMessage());
        }
        
        return $evolutionData;
    }

    /**
     * Obtenir les activités pour une période donnée - MÉTHODE CORRIGÉE
     */
    private function getActivitiesForPeriod($startDate, $endDate)
    {
        $activitesRecentes = collect();
        
        try {
            // Nouveaux profils visa dans la période
            $nouveauxProfils = ProfilVisa::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($profil) {
                    return [
                        'type' => 'profil_visa',
                        'message' => "Nouvelle demande de visa de " . ($profil->user->name ?? 'Client inconnu'),
                        'date' => $profil->created_at,
                        'date_formatted' => $profil->created_at->diffForHumans(),
                        'icon' => 'fas fa-passport',
                        'color' => 'primary'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($nouveauxProfils);

            // Nouvelles souscriptions dans la période - CORRIGÉ
            $nouvellesSouscriptions = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($souscription) {
                    return [
                        'type' => 'souscription',
                        'message' => "Nouvelle souscription de " . $souscription->nom . " " . $souscription->prenom,
                        'date' => $souscription->created_at,
                        'date_formatted' => $souscription->created_at->diffForHumans(),
                        'icon' => 'fas fa-tags',
                        'color' => 'success'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($nouvellesSouscriptions);

            // Nouveaux rendez-vous dans la période
            $nouveauxRdv = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($rdv) {
                    return [
                        'type' => 'rendez_vous',
                        'message' => "Nouveau RDV de " . $rdv->nom . " " . $rdv->prenom,
                        'date' => $rdv->created_at,
                        'date_formatted' => $rdv->created_at->diffForHumans(),
                        'icon' => 'fas fa-calendar-check',
                        'color' => 'info'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($nouveauxRdv);

            // Nouvelles réservations dans la période
            $nouvellesReservations = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($reservation) {
                    return [
                        'type' => 'reservation',
                        'message' => "Nouvelle réservation vers " . ($reservation->pays_destination ?? 'Destination inconnue'),
                        'date' => $reservation->created_at,
                        'date_formatted' => $reservation->created_at->diffForHumans(),
                        'icon' => 'fas fa-plane',
                        'color' => 'warning'
                    ];
                });

            $activitesRecentes = $activitesRecentes->merge($nouvellesReservations);

            return $activitesRecentes->sortByDesc('date')->take(15);
        } catch (\Exception $e) {
            Log::error('Erreur getActivitiesForPeriod: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Calculer les objectifs selon la période - MÉTHODE CORRIGÉE
     */
    private function calculateObjectives($profilVisaStats, $souscriptionStats, $rendezVousStats, $periodFilter)
    {
        // Objectifs adaptatifs selon la période
        $baseObjectives = [
            'profil_visa_daily' => 5,
            'profil_visa_weekly' => 25,
            'profil_visa_monthly' => 100,
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
            'progression_profils' => $profilVisaStats['periode'] > 0 ? 
                min(100, ($profilVisaStats['periode'] / $objectifs['profil_visa_periode']) * 100) : 0,
            'progression_souscriptions' => $souscriptionStats['periode'] > 0 ? 
                min(100, ($souscriptionStats['periode'] / $objectifs['souscriptions_periode']) * 100) : 0,
            'progression_rdv' => $rendezVousStats['periode'] > 0 ? 
                min(100, ($rendezVousStats['periode'] / $objectifs['rdv_periode']) * 100) : 0
        ];
    }

    // ==================== MÉTHODES UTILITAIRES CORRIGÉES ====================

    private function getProfilVisaStatsForPeriod($startDate, $endDate)
    {
        try {
            $total = ProfilVisa::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $en_attente = ProfilVisa::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNull('id_statuts_etat')
                          ->orWhereHas('statutEtat', function($subQuery) {
                              $subQuery->where('libelle', 'like', '%attente%')
                                        ->orWhere('libelle', 'like', '%pending%');
                          });
                })
                ->count();
                
            $approuves = ProfilVisa::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('statutEtat', function($query) {
                    $query->where('libelle', 'like', '%approuvé%')
                          ->orWhere('libelle', 'like', '%délivré%')
                          ->orWhere('libelle', 'like', '%terminé%');
                })
                ->count();

            return [
                'total' => $total,
                'en_attente' => $en_attente,
                'approuves' => $approuves,
                'taux_reussite' => $total > 0 ? round(($approuves / $total) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Erreur getProfilVisaStatsForPeriod: ' . $e->getMessage());
            return ['total' => 0, 'en_attente' => 0, 'approuves' => 0, 'taux_reussite' => 0];
        }
    }

    /**
     * Obtenir les statistiques des souscriptions pour une période - VERSION CORRIGÉE
     */
    private function getSouscriptionStatsForPeriodCorrected($startDate, $endDate)
    {
        try {
            // CORRECTION : Récupérer TOUTES les souscriptions, pas seulement les actives
            $totalPeriode = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $activesPeriode = SouscrireForfaits::where('etat', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $enAttentePeriode = SouscrireForfaits::where('etat', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            // Chiffre d'affaires pour toutes les souscriptions avec montant
            $chiffre_affaires = SouscrireForfaits::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('montant')
                ->where('montant', '>', 0)
                ->sum('montant') ?? 0;

            $stats = [
                'total' => $totalPeriode,
                'actives' => $activesPeriode,
                'en_attente' => $enAttentePeriode,
                'chiffre_affaires' => $chiffre_affaires,
                'panier_moyen' => $totalPeriode > 0 ? round($chiffre_affaires / $totalPeriode, 0) : 0
            ];

            Log::info('Stats souscriptions période calculées', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'stats' => $stats
            ]);

            return $stats;
        } catch (\Exception $e) {
            Log::error('Erreur getSouscriptionStatsForPeriodCorrected: ' . $e->getMessage());
            return [
                'total' => 0, 
                'actives' => 0, 
                'en_attente' => 0, 
                'chiffre_affaires' => 0, 
                'panier_moyen' => 0
            ];
        }
    }

    private function getDocumentsVoyageStatsForPeriod($startDate, $endDate)
    {
        try {
            $total = DocumentsVoyage::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $traites = DocumentsVoyage::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();

            return [
                'total' => $total,
                'traites' => $traites,
                'en_cours' => $total - $traites
            ];
        } catch (\Exception $e) {
            Log::error('Erreur getDocumentsVoyageStatsForPeriod: ' . $e->getMessage());
            return ['total' => 0, 'traites' => 0, 'en_cours' => 0];
        }
    }

    private function getRendezVousStatsForPeriod($startDate, $endDate)
    {
        try {
            $total = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $confirmes = RendezVous::whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();

            return [
                'total' => $total,
                'confirmes' => $confirmes,
                'en_attente' => $total - $confirmes
            ];
        } catch (\Exception $e) {
            Log::error('Erreur getRendezVousStatsForPeriod: ' . $e->getMessage());
            return ['total' => 0, 'confirmes' => 0, 'en_attente' => 0];
        }
    }

    private function getReservationStatsForPeriod($startDate, $endDate)
    {
        try {
            $total = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $confirmees = ReservationAchat::where('ent1d', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('etat', 1)
                ->count();

            return [
                'total' => $total,
                'confirmees' => $confirmees,
                'en_attente' => $total - $confirmees
            ];
        } catch (\Exception $e) {
            Log::error('Erreur getReservationStatsForPeriod: ' . $e->getMessage());
            return ['total' => 0, 'confirmees' => 0, 'en_attente' => 0];
        }
    }

    private function getTopDestinationsForPeriod($startDate, $endDate)
    {
        try {
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
            Log::error('Erreur getTopDestinationsForPeriod: ' . $e->getMessage());
            return collect();
        }
    }

    private function calculateObjectivesForPeriod($periodFilter, $startDate, $endDate)
    {
        // Logique simplifiée pour les objectifs selon la période
        $baseDaily = ['profil_visa' => 5, 'souscriptions' => 3, 'rdv' => 4];
        $baseWeekly = ['profil_visa' => 25, 'souscriptions' => 15, 'rdv' => 20];
        $baseMonthly = ['profil_visa' => 100, 'souscriptions' => 60, 'rdv' => 80];

        switch ($periodFilter) {
            case 'today':
            case 'yesterday':
                return $baseDaily;
            case 'this_week':
            case 'last_week':
                return $baseWeekly;
            default:
                return $baseMonthly;
        }
    }

    /**
     * API pour obtenir les statistiques en temps réel - MÉTHODE CORRIGÉE
     */
    public function getRealtimeStats()
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkCommercialAccess($user)) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
            
            $currentDate = Carbon::now();

            $stats = [
                'profil_visa_aujourd_hui' => ProfilVisa::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'souscriptions_aujourd_hui' => SouscrireForfaits::whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'rdv_aujourd_hui' => RendezVous::whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'reservations_aujourd_hui' => ReservationAchat::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'last_update' => now()->format('H:i:s')
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur getRealtimeStats Commercial: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * Vérification d'accès commercial - MÉTHODE CORRIGÉE
     */
    private function checkCommercialAccess($user): bool
    {
        try {
            // Vérifier d'abord par rôle
            if ($user->hasRole('Commercial')) {
                return true;
            }

            // Puis par type_user
            if ($user->type_user === 'commercial') {
                return true;
            }

            // Accès admin
            if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
                return true;
            }

            // Vérifier les permissions spécifiques
            $commercialPermissions = [
                'manage_clients', 'view_clients', 'manage_forfaits', 'view_forfaits',
                'view_dashboard_commercial', 'manage_souscrire_forfaits'
            ];

            if ($user->hasAnyPermission($commercialPermissions)) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Erreur checkCommercialAccess:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback sur type_user
            return ($user->type_user === 'commercial');
        }
    }

    /**
     * Données par défaut en cas d'erreur - MÉTHODE CORRIGÉE
     */
    private function getDefaultModulesData(): array
    {
        return [
            'profilVisaStats' => [
                'total' => 0, 'periode' => 0, 'aujourd_hui' => 0, 'ce_mois' => 0,
                'en_attente' => 0, 'approuves' => 0, 'taux_reussite' => 0
            ],
            'souscriptionStats' => [
                'total' => 0, 'periode' => 0, 'aujourd_hui' => 0, 'ce_mois' => 0,
                'chiffre_affaires_periode' => 0, 'chiffre_affaires_mois' => 0, 
                'panier_moyen' => 0, 'forfaits_actifs' => 0
            ],
            'documentsVoyageStats' => [
                'total' => 0, 'periode' => 0, 'aujourd_hui' => 0, 'ce_mois' => 0,
                'en_cours' => 0, 'traites' => 0
            ],
            'rendezVousStats' => [
                'total' => 0, 'periode' => 0, 'aujourd_hui' => 0, 'ce_mois' => 0,
                'confirmes' => 0, 'en_attente' => 0, 'a_venir' => 0
            ],
            'reservationStats' => [
                'total' => 0, 'periode' => 0, 'aujourd_hui' => 0, 'ce_mois' => 0,
                'confirmees' => 0, 'en_attente' => 0, 'type_populaire' => 'Aller-Retour'
            ],
            'evolutionModules' => [],
            'topDestinations' => collect(),
            'activitesRecentes' => collect(),
            'objectifs' => [
                'profil_visa_mois' => 100, 'souscriptions_mois' => 60, 'rdv_mois' => 80,
                'profil_visa_periode' => 100, 'souscriptions_periode' => 60, 'rdv_periode' => 80,
                'progression_profils' => 0, 'progression_souscriptions' => 0, 'progression_rdv' => 0
            ],
            'filterData' => [
                'current_period' => 'this_month',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
                'period_label' => 'Ce mois'
            ]
        ];
    }
}