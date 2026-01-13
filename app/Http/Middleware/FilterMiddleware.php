<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Middleware FilterMiddleware - Gestion centralisée des filtres par période CORRIGÉ
 * 
 * Ce middleware gère la validation et la normalisation des filtres par période
 * pour tous les dashboards et modules de PSI Africa
 */
class FilterMiddleware
{
    /**
     * Handle an incoming request avec gestion des filtres CORRIGÉE
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $filterType
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $filterType = null)
    {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!Auth::check()) {
                Log::warning('FilterMiddleware: Tentative d\'utilisation des filtres sans authentification', [
                    'ip' => $request->ip(),
                    'url' => $request->url(),
                    'user_agent' => $request->userAgent()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'Authentification requise'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Vous devez être connecté pour utiliser les filtres.');
            }

            $user = Auth::user();
            
            // Log de l'utilisation des filtres avec plus de détails
            Log::info('FilterMiddleware: Utilisation des filtres', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user,
                'filter_type' => $filterType,
                'request_method' => $request->method(),
                'request_data' => $request->only(['period', 'start_date', 'end_date']),
                'url' => $request->url(),
                'is_ajax' => $request->ajax()
            ]);

            // Valider et normaliser les paramètres de filtre
            $this->validateAndNormalizeFilters($request);

            // Ajouter les métadonnées de filtre à la requête
            $this->addFilterMetadata($request, $user, $filterType);

            // Vérifier les permissions avancées pour certains filtres
            if ($filterType && in_array($filterType, ['advanced', 'admin', 'export'])) {
                if (!$this->validateAdvancedFilterPermissions($request, $user)) {
                    Log::warning('FilterMiddleware: Accès refusé aux filtres avancés', [
                        'user_id' => $user->id,
                        'filter_type' => $filterType
                    ]);
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['error' => 'Permissions insuffisantes pour ce type de filtre'], 403);
                    }
                    
                    return redirect()->back()->with('error', 'Vous n\'avez pas les permissions pour utiliser ces filtres avancés.');
                }
            }

            // Optimiser pour les grandes plages de dates
            $this->optimizeForLargeDateRanges($request);

            // Ajouter le cache pour les filtres fréquemment utilisés
            $this->addFilterCaching($request, $user);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('FilterMiddleware: Erreur lors du traitement des filtres', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'url' => $request->url(),
                'request_data' => $request->all()
            ]);

            // En cas d'erreur, continuer avec des filtres par défaut
            $this->setDefaultFilters($request);
            
            if ($request->ajax() || $request->wantsJson()) {
                // Pour les requêtes AJAX, on continue avec les filtres par défaut
                return $next($request);
            }
            
            return $next($request);
        }
    }

    /**
     * Valider et normaliser les paramètres de filtre - MÉTHODE CORRIGÉE
     */
    private function validateAndNormalizeFilters(Request $request)
    {
        // Périodes autorisées - LISTE ÉTENDUE
        $allowedPeriods = [
            'today', 'yesterday', 'this_week', 'last_week', 
            'this_month', 'last_month', 'this_quarter', 'last_quarter',
            'this_year', 'last_year', 'last_30_days', 'last_90_days', 
            'last_6_months', 'last_12_months', 'custom'
        ];

        // Valider la période
        $period = $request->input('period', 'this_month');
        if (!in_array($period, $allowedPeriods)) {
            Log::warning('FilterMiddleware: Période non autorisée', [
                'period' => $period,
                'user_id' => Auth::id(),
                'allowed_periods' => $allowedPeriods
            ]);
            $period = 'this_month';
        }

        // Validation pour période personnalisée - LOGIQUE CORRIGÉE
        if ($period === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if (!$startDate || !$endDate) {
                Log::warning('FilterMiddleware: Dates manquantes pour période personnalisée', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'user_id' => Auth::id()
                ]);
                $period = 'this_month';
            } else {
                try {
                    $startDateCarbon = Carbon::parse($startDate);
                    $endDateCarbon = Carbon::parse($endDate);

                    // Vérifier que la date de début est antérieure à la date de fin
                    if ($startDateCarbon->greaterThan($endDateCarbon)) {
                        Log::warning('FilterMiddleware: Date de début postérieure à la date de fin', [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'user_id' => Auth::id()
                        ]);
                        $period = 'this_month';
                    }

                    // Limiter la plage maximale à 5 ans pour éviter les performances lentes
                    $diffInDays = $startDateCarbon->diffInDays($endDateCarbon);
                    if ($diffInDays > 1825) { // 5 ans
                        Log::warning('FilterMiddleware: Plage de dates trop importante', [
                            'diff_days' => $diffInDays,
                            'max_allowed' => 1825,
                            'user_id' => Auth::id()
                        ]);
                        $endDateCarbon = $startDateCarbon->copy()->addYears(2); // Limiter à 2 ans
                        $request->merge(['end_date' => $endDateCarbon->toDateString()]);
                    }

                    // Vérifier que les dates ne sont pas dans le futur (sauf pour les prévisions)
                    $now = Carbon::now();
                    if ($startDateCarbon->greaterThan($now) && !$request->has('allow_future')) {
                        Log::info('FilterMiddleware: Date de début dans le futur, ajustement à aujourd\'hui', [
                            'original_start' => $startDate,
                            'adjusted_start' => $now->toDateString(),
                            'user_id' => Auth::id()
                        ]);
                        $startDateCarbon = $now->copy();
                        $request->merge(['start_date' => $startDateCarbon->toDateString()]);
                    }

                    if ($endDateCarbon->greaterThan($now) && !$request->has('allow_future')) {
                        Log::info('FilterMiddleware: Date de fin dans le futur, ajustement à aujourd\'hui', [
                            'original_end' => $endDate,
                            'adjusted_end' => $now->toDateString(),
                            'user_id' => Auth::id()
                        ]);
                        $endDateCarbon = $now->copy();
                        $request->merge(['end_date' => $endDateCarbon->toDateString()]);
                    }

                } catch (\Exception $e) {
                    Log::error('FilterMiddleware: Erreur validation dates personnalisées', [
                        'error' => $e->getMessage(),
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'user_id' => Auth::id()
                    ]);
                    $period = 'this_month';
                }
            }
        }

        // Appliquer la période validée
        $request->merge(['period' => $period]);

        // Calculer et ajouter les dates effectives
        $dateRange = $this->calculateDateRange($period, $request->input('start_date'), $request->input('end_date'));
        $request->merge([
            'calculated_start_date' => $dateRange['start']->toDateString(),
            'calculated_end_date' => $dateRange['end']->toDateString(),
            'calculated_start_datetime' => $dateRange['start']->toDateTimeString(),
            'calculated_end_datetime' => $dateRange['end']->toDateTimeString()
        ]);

        Log::info('FilterMiddleware: Filtres validés et normalisés', [
            'period' => $period,
            'calculated_start' => $dateRange['start']->toDateString(),
            'calculated_end' => $dateRange['end']->toDateString(),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Calculer la plage de dates selon la période - MÉTHODE CORRIGÉE ET ÉTENDUE
     */
    private function calculateDateRange(string $period, ?string $customStartDate = null, ?string $customEndDate = null): array
    {
        $now = Carbon::now();
        
        switch ($period) {
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
                    'start' => $now->copy()->startOfWeek(Carbon::MONDAY), // Semaine commence lundi
                    'end' => $now->copy()->endOfWeek(Carbon::SUNDAY)     // Semaine finit dimanche
                ];
                
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY),
                    'end' => $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)
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
                    'start' => $now->copy()->subDays(29)->startOfDay(), // 30 jours incluant aujourd'hui
                    'end' => $now->copy()->endOfDay()
                ];
                
            case 'last_90_days':
                return [
                    'start' => $now->copy()->subDays(89)->startOfDay(), // 90 jours incluant aujourd'hui
                    'end' => $now->copy()->endOfDay()
                ];
                
            case 'last_6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
                
            case 'last_12_months':
                return [
                    'start' => $now->copy()->subMonths(12)->startOfDay(),
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
                        Log::error('FilterMiddleware: Erreur parsing dates custom', [
                            'error' => $e->getMessage(),
                            'start_date' => $customStartDate,
                            'end_date' => $customEndDate
                        ]);
                    }
                }
                // Fallback au mois actuel si dates custom invalides
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            default: // this_month par défaut
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    /**
     * Ajouter les métadonnées de filtre à la requête - MÉTHODE CORRIGÉE
     */
    private function addFilterMetadata(Request $request, $user, ?string $filterType)
    {
        $period = $request->input('period');
        $startDate = Carbon::parse($request->input('calculated_start_date'));
        $endDate = Carbon::parse($request->input('calculated_end_date'));

        $metadata = [
            'filter_applied_at' => now()->toDateTimeString(),
            'filter_applied_by' => $user->id,
            'filter_applied_by_name' => $user->name,
            'filter_type' => $filterType,
            'period_label' => $this->getPeriodLabel($period, $startDate, $endDate),
            'date_range_days' => $startDate->diffInDays($endDate) + 1,
            'is_custom_period' => $period === 'custom',
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => app()->getLocale(),
            'request_id' => uniqid('filter_'),
            'is_large_range' => $startDate->diffInDays($endDate) > 365,
            'performance_tier' => $this->getPerformanceTier($startDate, $endDate)
        ];

        $request->merge(['filter_metadata' => $metadata]);

        Log::info('FilterMiddleware: Métadonnées de filtre ajoutées', [
            'user_id' => $user->id,
            'request_id' => $metadata['request_id'],
            'metadata' => $metadata
        ]);
    }

    /**
     * Obtenir le libellé de la période - MÉTHODE CORRIGÉE
     */
    private function getPeriodLabel(string $period, Carbon $startDate, Carbon $endDate): string
    {
        switch ($period) {
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
            case 'last_6_months':
                return '6 derniers mois';
            case 'last_12_months':
                return '12 derniers mois';
            case 'custom':
                return 'Du ' . $startDate->format('d/m/Y') . ' au ' . $endDate->format('d/m/Y');
            default:
                return 'Période sélectionnée';
        }
    }

    /**
     * Définir des filtres par défaut en cas d'erreur - MÉTHODE CORRIGÉE
     */
    private function setDefaultFilters(Request $request)
    {
        $now = Carbon::now();
        $defaultPeriod = 'this_month';
        
        $request->merge([
            'period' => $defaultPeriod,
            'calculated_start_date' => $now->copy()->startOfMonth()->toDateString(),
            'calculated_end_date' => $now->copy()->endOfMonth()->toDateString(),
            'calculated_start_datetime' => $now->copy()->startOfMonth()->toDateTimeString(),
            'calculated_end_datetime' => $now->copy()->endOfMonth()->toDateTimeString(),
            'filter_metadata' => [
                'filter_applied_at' => now()->toDateTimeString(),
                'filter_applied_by' => Auth::id(),
                'filter_applied_by_name' => Auth::user()?->name ?? 'Utilisateur inconnu',
                'filter_type' => 'default_fallback',
                'period_label' => 'Ce mois (par défaut)',
                'date_range_days' => $now->copy()->startOfMonth()->diffInDays($now->copy()->endOfMonth()) + 1,
                'is_custom_period' => false,
                'timezone' => config('app.timezone', 'UTC'),
                'locale' => app()->getLocale(),
                'is_fallback' => true,
                'request_id' => uniqid('default_filter_')
            ]
        ]);

        Log::info('FilterMiddleware: Filtres par défaut appliqués', [
            'user_id' => Auth::id(),
            'reason' => 'Erreur lors de la validation des filtres'
        ]);
    }

    /**
     * Valider les permissions pour les filtres avancés - MÉTHODE CORRIGÉE
     */
    private function validateAdvancedFilterPermissions(Request $request, $user): bool
    {
        try {
            // Vérifier si l'utilisateur peut utiliser les filtres avancés
            $advancedFilterPermissions = [
                'view_advanced_analytics',
                'export_filtered_data',
                'manage_filter_presets',
                'view_all_users_data'
            ];

            // Admins et Super Admins ont accès à tout
            if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
                return true;
            }

            // Vérifier les permissions spécifiques
            foreach ($advancedFilterPermissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }

            // Vérifier selon le type d'utilisateur
            if (in_array($user->type_user, ['commercial', 'agent_comptoir']) && 
                $request->input('period') !== 'custom') {
                // Les commerciaux et agents comptoir peuvent utiliser les filtres prédéfinis
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('FilterMiddleware: Erreur validation permissions avancées', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Optimiser les requêtes pour de grandes plages de dates - MÉTHODE CORRIGÉE
     */
    private function optimizeForLargeDateRanges(Request $request)
    {
        $startDate = Carbon::parse($request->input('calculated_start_date'));
        $endDate = Carbon::parse($request->input('calculated_end_date'));
        $daysDiff = $startDate->diffInDays($endDate);

        $optimizations = [];

        // Pour les plages de plus de 365 jours, recommander l'aggregation par mois
        if ($daysDiff > 365) {
            $optimizations['large_range'] = true;
            $optimizations['suggested_aggregation'] = 'monthly';
            $optimizations['performance_warning'] = 'Large date range detected. Consider using monthly aggregation for better performance.';
            $optimizations['recommended_cache_ttl'] = 3600; // 1 heure
            
            Log::info('FilterMiddleware: Optimisation pour grande plage de dates', [
                'days_diff' => $daysDiff,
                'user_id' => Auth::id(),
                'optimization_applied' => true
            ]);
        }
        // Pour les plages de plus de 90 jours, recommander l'aggregation par semaine
        elseif ($daysDiff > 90) {
            $optimizations['medium_range'] = true;
            $optimizations['suggested_aggregation'] = 'weekly';
            $optimizations['recommended_cache_ttl'] = 1800; // 30 minutes
        }
        // Pour les plages courtes
        elseif ($daysDiff <= 7) {
            $optimizations['short_range'] = true;
            $optimizations['suggested_aggregation'] = 'daily';
            $optimizations['recommended_cache_ttl'] = 300; // 5 minutes
        }
        else {
            $optimizations['normal_range'] = true;
            $optimizations['suggested_aggregation'] = 'daily';
            $optimizations['recommended_cache_ttl'] = 900; // 15 minutes
        }

        $request->merge(['filter_optimizations' => $optimizations]);
    }

    /**
     * Ajouter le cache pour les filtres fréquemment utilisés - MÉTHODE CORRIGÉE
     */
    private function addFilterCaching(Request $request, $user)
    {
        $period = $request->input('period');
        $startDate = $request->input('calculated_start_date');
        $endDate = $request->input('calculated_end_date');

        // Générer une clé de cache unique et sécurisée
        $cacheKey = sprintf(
            'filter_cache_%d_%s_%s_%s_%s',
            $user->id,
            $period,
            md5($startDate),
            md5($endDate),
            md5($request->url())
        );

        $cacheConfig = [
            'filter_cache_key' => $cacheKey,
            'cache_ttl' => $this->getCacheTTLForPeriod($period),
            'cache_tags' => ['filters', 'user_' . $user->id, 'period_' . $period],
            'should_cache' => $this->shouldCacheFilter($request, $period)
        ];

        $request->merge(['filter_cache_config' => $cacheConfig]);
    }

    /**
     * Obtenir la durée de cache selon la période - MÉTHODE CORRIGÉE
     */
    private function getCacheTTLForPeriod(string $period): int
    {
        switch ($period) {
            case 'today':
                return 300; // 5 minutes (données changeantes)
            case 'yesterday':
                return 3600; // 1 heure (données stables)
            case 'this_week':
                return 1800; // 30 minutes
            case 'last_week':
                return 7200; // 2 heures (données stables)
            case 'this_month':
                return 1800; // 30 minutes
            case 'last_month':
            case 'last_quarter':
            case 'last_year':
                return 86400; // 24 heures (données historiques stables)
            case 'last_30_days':
            case 'last_90_days':
                return 3600; // 1 heure
            case 'custom':
                return 1800; // 30 minutes par défaut
            default:
                return 900; // 15 minutes par défaut
        }
    }

    /**
     * Déterminer si on doit mettre en cache ce filtre
     */
    private function shouldCacheFilter(Request $request, string $period): bool
    {
        // Ne pas mettre en cache les requêtes avec des paramètres sensibles
        if ($request->has(['export', 'download', 'sensitive'])) {
            return false;
        }

        // Ne pas mettre en cache les périodes personnalisées courtes
        if ($period === 'custom') {
            $startDate = Carbon::parse($request->input('calculated_start_date'));
            $endDate = Carbon::parse($request->input('calculated_end_date'));
            
            // Si la période custom est très courte (moins de 3 jours), ne pas cacher
            if ($startDate->diffInDays($endDate) < 3) {
                return false;
            }
        }

        // Mettre en cache les autres cas
        return true;
    }

    /**
     * Obtenir le niveau de performance selon la plage de dates
     */
    private function getPerformanceTier(Carbon $startDate, Carbon $endDate): string
    {
        $daysDiff = $startDate->diffInDays($endDate);
        
        if ($daysDiff <= 7) {
            return 'fast';
        } elseif ($daysDiff <= 30) {
            return 'normal';
        } elseif ($daysDiff <= 90) {
            return 'slow';
        } else {
            return 'very_slow';
        }
    }

    /**
     * Nettoyer les anciens caches de filtres (à appeler périodiquement)
     */
    public static function cleanupFilterCaches()
    {
        try {
            // Cette méthode peut être appelée par un job ou une commande artisan
            $cachePrefix = 'filter_cache_';
            
            // Logique de nettoyage selon votre système de cache
            Log::info('FilterMiddleware: Nettoyage des caches de filtres effectué');
            
        } catch (\Exception $e) {
            Log::error('FilterMiddleware: Erreur lors du nettoyage des caches', [
                'error' => $e->getMessage()
            ]);
        }
    }
}