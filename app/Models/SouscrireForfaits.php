<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS - CORRECTION COMPLÈTE SOUSCRIPTIONS
 * @Last Modified time: 2025-07-14 16:00:00
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SouscrireForfaits extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'souscrire_forfaits';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom', 'prenom', 'email', 'etat', 'id_type_forfait', 'update_user', 'updated_at',
        'contact', 'numero_whatsapp', 'message', 'montant', 'user1d', 'ent1d'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'montant' => 'decimal:2',
        'etat' => 'integer',
        'id_type_forfait' => 'integer',
        'user1d' => 'integer',
        'ent1d' => 'integer',
        'update_user' => 'integer'
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
        'status_label',
        'formatted_amount',
        'days_since_creation'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    // ==================== RELATIONS ====================

    /**
     * Relation avec l'utilisateur qui a créé la souscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1d');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour la souscription
     */
    public function updateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user');
    }

    /**
     * Relation avec le type de forfait
     */
    public function typeForfait(): BelongsTo
    {
        return $this->belongsTo(Forfaits::class, 'id_type_forfait');
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprises::class, 'ent1d');
    }

    // ==================== ACCESSORS (GETTERS) ====================

    /**
     * Obtenir le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        switch ($this->etat) {
            case 0:
                return 'En attente';
            case 1:
                return 'Actif';
            case 2:
                return 'Suspendu';
            case 3:
                return 'Annulé';
            case 4:
                return 'Expiré';
            default:
                return 'Inconnu';
        }
    }

    /**
     * Obtenir le montant formaté
     */
    public function getFormattedAmountAttribute(): string
    {
        if (!$this->montant) {
            return '0 FCFA';
        }
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le nombre de jours depuis la création
     */
    public function getDaysSinceCreationAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(Carbon::now()) : 0;
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour filtrer par entreprise
     */
    public function scopeForEntreprise($query, $entrepriseId = 1)
    {
        return $query->where('ent1d', $entrepriseId);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('etat', $status);
    }

    /**
     * Scope pour les souscriptions actives
     */
    public function scopeActive($query)
    {
        return $query->where('etat', 1);
    }

    /**
     * Scope pour les souscriptions en attente
     */
    public function scopePending($query)
    {
        return $query->where('etat', 0);
    }

    /**
     * Scope pour les souscriptions suspendues
     */
    public function scopeSuspended($query)
    {
        return $query->where('etat', 2);
    }

    /**
     * Scope pour les souscriptions annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('etat', 3);
    }

    /**
     * Scope pour les souscriptions avec montant
     */
    public function scopeWithAmount($query)
    {
        return $query->whereNotNull('montant')->where('montant', '>', 0);
    }

    /**
     * Scope pour les souscriptions d'aujourd'hui
     */
    public function scopeCreatedToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope pour les souscriptions de cette semaine
     */
    public function scopeCreatedThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour les souscriptions de ce mois
     */
    public function scopeCreatedThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope pour les souscriptions de cette année
     */
    public function scopeCreatedThisYear($query)
    {
        return $query->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope pour filtrer par période - CORRIGÉ
     */
    public function scopeInPeriod($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour les souscriptions récentes (derniers N jours)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // ==================== MÉTHODES STATIQUES DE STATISTIQUES CORRIGÉES ====================

    /**
     * Obtenir les statistiques par période - VERSION COMPLÈTEMENT CORRIGÉE
     */
    public static function getStatsByPeriod($startDate, $endDate, $entrepriseId = 1)
    {
        try {
            Log::info('Calcul stats souscriptions par période', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'entreprise_id' => $entrepriseId
            ]);

            // CORRECTION MAJEURE : Utiliser une seule requête optimisée
            $stats = self::forEntreprise($entrepriseId)
                       ->whereBetween('created_at', [$startDate, $endDate])
                       ->selectRaw('
                           COUNT(*) as total,
                           COUNT(CASE WHEN etat = 1 THEN 1 END) as active,
                           COUNT(CASE WHEN etat = 0 THEN 1 END) as pending,
                           COUNT(CASE WHEN etat = 2 THEN 1 END) as suspended,
                           COUNT(CASE WHEN etat = 3 THEN 1 END) as cancelled,
                           COUNT(CASE WHEN etat = 4 THEN 1 END) as expired,
                           COALESCE(SUM(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as total_amount,
                           COALESCE(AVG(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE NULL END), 0) as average_amount,
                           COUNT(CASE WHEN montant IS NOT NULL AND montant > 0 THEN 1 END) as with_amount,
                           COUNT(CASE WHEN montant IS NULL OR montant = 0 THEN 1 END) as without_amount
                       ')
                       ->first();

            if (!$stats) {
                Log::warning('Aucun résultat pour les stats souscriptions');
                return self::getEmptyStats();
            }

            $result = [
                'total' => (int) $stats->total,
                'active' => (int) $stats->active,
                'pending' => (int) $stats->pending,
                'suspended' => (int) $stats->suspended,
                'cancelled' => (int) $stats->cancelled,
                'expired' => (int) $stats->expired,
                'total_amount' => (float) $stats->total_amount,
                'average_amount' => (float) $stats->average_amount,
                'with_amount' => (int) $stats->with_amount,
                'without_amount' => (int) $stats->without_amount,
                'conversion_rate' => $stats->total > 0 ? round(($stats->active / $stats->total) * 100, 2) : 0
            ];

            Log::info('Stats calculées avec succès', $result);
            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur getStatsByPeriod SouscrireForfaits: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::getEmptyStats();
        }
    }

    /**
     * Obtenir les statistiques par type de forfait - CORRIGÉ
     */
    public static function getStatsByForfaitType($entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->join('forfaits', 'souscrire_forfaits.id_type_forfait', '=', 'forfaits.id')
                       ->selectRaw('
                           forfaits.nom as forfait_name,
                           forfaits.id as forfait_id,
                           forfaits.prix as forfait_prix,
                           COUNT(souscrire_forfaits.id) as total_souscriptions,
                           COUNT(CASE WHEN souscrire_forfaits.etat = 1 THEN 1 END) as active_souscriptions,
                           COUNT(CASE WHEN souscrire_forfaits.etat = 0 THEN 1 END) as pending_souscriptions,
                           COALESCE(SUM(CASE WHEN souscrire_forfaits.montant IS NOT NULL AND souscrire_forfaits.montant > 0 THEN souscrire_forfaits.montant ELSE 0 END), 0) as total_revenue,
                           COALESCE(AVG(CASE WHEN souscrire_forfaits.montant IS NOT NULL AND souscrire_forfaits.montant > 0 THEN souscrire_forfaits.montant ELSE NULL END), 0) as average_revenue
                       ')
                       ->groupBy('forfaits.id', 'forfaits.nom', 'forfaits.prix')
                       ->orderBy('total_souscriptions', 'desc')
                       ->get();
        } catch (\Exception $e) {
            Log::error('Erreur getStatsByForfaitType: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir l'évolution mensuelle des souscriptions - CORRIGÉ
     */
    public static function getMonthlyEvolution($months = 12, $entrepriseId = 1)
    {
        $data = [];
        $currentDate = Carbon::now();

        try {
            for ($i = $months - 1; $i >= 0; $i--) {
                $month = $currentDate->copy()->subMonths($i);
                
                $stats = self::forEntreprise($entrepriseId)
                            ->whereMonth('created_at', $month->month)
                            ->whereYear('created_at', $month->year)
                            ->selectRaw('
                                COUNT(*) as count,
                                COUNT(CASE WHEN etat = 1 THEN 1 END) as active_count,
                                COUNT(CASE WHEN etat = 0 THEN 1 END) as pending_count,
                                COALESCE(SUM(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as revenue,
                                COALESCE(AVG(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE NULL END), 0) as avg_revenue
                            ')
                            ->first();
                
                $data[] = [
                    'month' => $month->format('M Y'),
                    'month_short' => $month->format('M'),
                    'count' => (int) ($stats->count ?? 0),
                    'active_count' => (int) ($stats->active_count ?? 0),
                    'pending_count' => (int) ($stats->pending_count ?? 0),
                    'revenue' => (float) ($stats->revenue ?? 0),
                    'avg_revenue' => (float) ($stats->avg_revenue ?? 0),
                    'month_number' => $month->month,
                    'year' => $month->year,
                    'conversion_rate' => $stats && $stats->count > 0 ? round(($stats->active_count / $stats->count) * 100, 1) : 0
                ];
            }

            return collect($data);
        } catch (\Exception $e) {
            Log::error('Erreur getMonthlyEvolution: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir l'évolution journalière des souscriptions - CORRIGÉ
     */
    public static function getDailyEvolution($days = 30, $entrepriseId = 1)
    {
        $data = [];
        $currentDate = Carbon::now();

        try {
            for ($i = $days - 1; $i >= 0; $i--) {
                $day = $currentDate->copy()->subDays($i);
                
                $stats = self::forEntreprise($entrepriseId)
                            ->whereDate('created_at', $day->toDateString())
                            ->selectRaw('
                                COUNT(*) as count,
                                COUNT(CASE WHEN etat = 1 THEN 1 END) as active_count,
                                COUNT(CASE WHEN etat = 0 THEN 1 END) as pending_count,
                                COALESCE(SUM(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as revenue
                            ')
                            ->first();
                
                $data[] = [
                    'date' => $day->format('d/m'),
                    'date_full' => $day->format('d/m/Y'),
                    'count' => (int) ($stats->count ?? 0),
                    'active_count' => (int) ($stats->active_count ?? 0),
                    'pending_count' => (int) ($stats->pending_count ?? 0),
                    'revenue' => (float) ($stats->revenue ?? 0),
                    'full_date' => $day->toDateString(),
                    'day_name' => $day->format('l')
                ];
            }

            return collect($data);
        } catch (\Exception $e) {
            Log::error('Erreur getDailyEvolution: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir le chiffre d'affaires total - CORRIGÉ
     */
    public static function getTotalRevenue($entrepriseId = 1, $includeAllStatus = false): float
    {
        try {
            $query = self::forEntreprise($entrepriseId)
                        ->whereNotNull('montant')
                        ->where('montant', '>', 0);
            
            // Par défaut, inclure seulement les souscriptions actives pour le CA
            if (!$includeAllStatus) {
                $query->where('etat', 1);
            }
            
            return (float) ($query->sum('montant') ?? 0);
        } catch (\Exception $e) {
            Log::error('Erreur getTotalRevenue: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Obtenir le panier moyen - CORRIGÉ
     */
    public static function getAverageBasket($entrepriseId = 1, $includeAllStatus = false): float
    {
        try {
            $query = self::forEntreprise($entrepriseId)
                        ->whereNotNull('montant')
                        ->where('montant', '>', 0);
            
            if (!$includeAllStatus) {
                $query->where('etat', 1);
            }
            
            $result = $query->selectRaw('AVG(montant) as avg_amount')->first();
            
            return round((float) ($result->avg_amount ?? 0), 2);
        } catch (\Exception $e) {
            Log::error('Erreur getAverageBasket: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Obtenir le taux de conversion - CORRIGÉ
     */
    public static function getConversionRate($entrepriseId = 1): float
    {
        try {
            $total = self::forEntreprise($entrepriseId)->count();
            
            if ($total === 0) {
                return 0.0;
            }

            $active = self::forEntreprise($entrepriseId)->where('etat', 1)->count();
            
            return round(($active / $total) * 100, 2);
        } catch (\Exception $e) {
            Log::error('Erreur getConversionRate: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Obtenir les top clients par montant de souscriptions - CORRIGÉ
     */
    public static function getTopCustomers($limit = 10, $entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->selectRaw('
                           CONCAT(prenom, " ", nom) as full_name,
                           email,
                           contact,
                           numero_whatsapp,
                           COUNT(*) as total_souscriptions,
                           COUNT(CASE WHEN etat = 1 THEN 1 END) as active_souscriptions,
                           COUNT(CASE WHEN etat = 0 THEN 1 END) as pending_souscriptions,
                           COALESCE(SUM(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as total_spent,
                           COALESCE(AVG(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE NULL END), 0) as avg_spent,
                           MAX(created_at) as last_subscription
                       ')
                       ->groupBy('prenom', 'nom', 'email', 'contact', 'numero_whatsapp')
                       ->orderBy('total_spent', 'desc')
                       ->limit($limit)
                       ->get();
        } catch (\Exception $e) {
            Log::error('Erreur getTopCustomers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir les forfaits les plus populaires - CORRIGÉ
     */
    public static function getPopularForfaits($limit = 5, $entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->join('forfaits', 'souscrire_forfaits.id_type_forfait', '=', 'forfaits.id')
                       ->selectRaw('
                           forfaits.nom as forfait_name,
                           forfaits.prix as forfait_price,
                           forfaits.description as forfait_description,
                           COUNT(souscrire_forfaits.id) as subscription_count,
                           COUNT(CASE WHEN souscrire_forfaits.etat = 1 THEN 1 END) as active_subscription_count,
                           COUNT(CASE WHEN souscrire_forfaits.etat = 0 THEN 1 END) as pending_subscription_count,
                           COALESCE(SUM(CASE WHEN souscrire_forfaits.montant IS NOT NULL AND souscrire_forfaits.montant > 0 THEN souscrire_forfaits.montant ELSE 0 END), 0) as total_revenue,
                           COALESCE(AVG(CASE WHEN souscrire_forfaits.montant IS NOT NULL AND souscrire_forfaits.montant > 0 THEN souscrire_forfaits.montant ELSE NULL END), 0) as avg_revenue
                       ')
                       ->groupBy('forfaits.id', 'forfaits.nom', 'forfaits.prix', 'forfaits.description')
                       ->orderBy('subscription_count', 'desc')
                       ->limit($limit)
                       ->get();
        } catch (\Exception $e) {
            Log::error('Erreur getPopularForfaits: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Rechercher des souscriptions - CORRIGÉ
     */
    public static function search($query, $entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->where(function($q) use ($query) {
                           $q->where('nom', 'like', "%{$query}%")
                             ->orWhere('prenom', 'like', "%{$query}%")
                             ->orWhere('email', 'like', "%{$query}%")
                             ->orWhere('contact', 'like', "%{$query}%")
                             ->orWhere('numero_whatsapp', 'like', "%{$query}%");
                       })
                       ->with(['typeForfait', 'user'])
                       ->orderBy('created_at', 'desc');
        } catch (\Exception $e) {
            Log::error('Erreur search: ' . $e->getMessage());
            return self::query()->whereRaw('1 = 0'); // Retourner une requête vide
        }
    }

    /**
     * Obtenir les souscriptions récentes - CORRIGÉ
     */
    public static function getRecent($limit = 10, $entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->with(['typeForfait', 'user'])
                       ->orderBy('created_at', 'desc')
                       ->limit($limit)
                       ->get();
        } catch (\Exception $e) {
            Log::error('Erreur getRecent: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir les souscriptions en attente de validation - CORRIGÉ
     */
    public static function getPendingValidation($entrepriseId = 1)
    {
        try {
            return self::forEntreprise($entrepriseId)
                       ->where('etat', 0)
                       ->with(['typeForfait', 'user'])
                       ->orderBy('created_at', 'asc')
                       ->get();
        } catch (\Exception $e) {
            Log::error('Erreur getPendingValidation: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * MÉTHODE PRINCIPALE : Obtenir les statistiques complètes pour une période donnée
     */
    public static function getCompleteStatsForPeriod($startDate, $endDate, $entrepriseId = 1)
    {
        try {
            Log::info('Calcul des statistiques complètes pour la période', [
                'start_date' => $startDate instanceof Carbon ? $startDate->toDateString() : $startDate,
                'end_date' => $endDate instanceof Carbon ? $endDate->toDateString() : $endDate,
                'entreprise_id' => $entrepriseId
            ]);

            // Convertir les dates si nécessaire
            if (!$startDate instanceof Carbon) {
                $startDate = Carbon::parse($startDate);
            }
            if (!$endDate instanceof Carbon) {
                $endDate = Carbon::parse($endDate);
            }

            // Requête principale optimisée
            $stats = self::forEntreprise($entrepriseId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as total,
                    COUNT(CASE WHEN etat = 1 THEN 1 END) as active,
                    COUNT(CASE WHEN etat = 0 THEN 1 END) as pending,
                    COUNT(CASE WHEN etat = 2 THEN 1 END) as suspended,
                    COUNT(CASE WHEN etat = 3 THEN 1 END) as cancelled,
                    COUNT(CASE WHEN etat = 4 THEN 1 END) as expired,
                    COALESCE(SUM(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as total_revenue,
                    COALESCE(SUM(CASE WHEN etat = 1 AND montant IS NOT NULL AND montant > 0 THEN montant ELSE 0 END), 0) as active_revenue,
                    COUNT(CASE WHEN montant IS NOT NULL AND montant > 0 THEN 1 END) as with_amount,
                    COUNT(CASE WHEN montant IS NULL OR montant = 0 THEN 1 END) as without_amount,
                    COALESCE(MIN(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant END), 0) as min_amount,
                    COALESCE(MAX(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant END), 0) as max_amount,
                    COALESCE(AVG(CASE WHEN montant IS NOT NULL AND montant > 0 THEN montant END), 0) as avg_amount
                ')
                ->first();

            if (!$stats) {
                Log::warning('Aucun résultat trouvé pour la période');
                return self::getEmptyStats();
            }

            // Construction du résultat final
            $result = [
                'total' => (int) $stats->total,
                'active' => (int) $stats->active,
                'pending' => (int) $stats->pending,
                'suspended' => (int) $stats->suspended,
                'cancelled' => (int) $stats->cancelled,
                'expired' => (int) $stats->expired,
                'total_revenue' => (float) $stats->total_revenue,
                'active_revenue' => (float) $stats->active_revenue,
                'with_amount' => (int) $stats->with_amount,
                'without_amount' => (int) $stats->without_amount,
                'min_amount' => (float) $stats->min_amount,
                'max_amount' => (float) $stats->max_amount,
                'avg_amount' => round((float) $stats->avg_amount, 2),
                'conversion_rate' => $stats->total > 0 ? round(($stats->active / $stats->total) * 100, 2) : 0,
                'average_basket' => $stats->with_amount > 0 ? round($stats->total_revenue / $stats->with_amount, 2) : 0,
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
                'period_days' => $startDate->diffInDays($endDate) + 1
            ];

            Log::info('Statistiques calculées avec succès', $result);
            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur getCompleteStatsForPeriod: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return self::getEmptyStats();
        }
    }

    /**
     * Obtenir les statistiques vides par défaut
     */
    private static function getEmptyStats(): array
    {
        return [
            'total' => 0,
            'active' => 0,
            'pending' => 0,
            'suspended' => 0,
            'cancelled' => 0,
            'expired' => 0,
            'total_revenue' => 0.0,
            'active_revenue' => 0.0,
            'with_amount' => 0,
            'without_amount' => 0,
            'min_amount' => 0.0,
            'max_amount' => 0.0,
            'avg_amount' => 0.0,
            'conversion_rate' => 0.0,
            'average_basket' => 0.0,
            'period_start' => null,
            'period_end' => null,
            'period_days' => 0
        ];
    }

    // ==================== MÉTHODES UTILITAIRES ====================

    /**
     * Obtenir les statistiques de base (utilisé par le dashboard)
     */
    public static function getBasicStats($entrepriseId = 1): array
    {
        try {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            return self::getCompleteStatsForPeriod($startOfMonth, $endOfMonth, $entrepriseId);
        } catch (\Exception $e) {
            Log::error('Erreur getBasicStats: ' . $e->getMessage());
            return self::getEmptyStats();
        }
    }

    /**
     * Obtenir les statistiques pour aujourd'hui
     */
    public static function getTodayStats($entrepriseId = 1): array
    {
        try {
            $today = Carbon::today();
            return self::getCompleteStatsForPeriod($today, $today, $entrepriseId);
        } catch (\Exception $e) {
            Log::error('Erreur getTodayStats: ' . $e->getMessage());
            return self::getEmptyStats();
        }
    }

    /**
     * Vérifier si la table contient des données
     */
    public static function hasData($entrepriseId = 1): bool
    {
        try {
            return self::forEntreprise($entrepriseId)->exists();
        } catch (\Exception $e) {
            Log::error('Erreur hasData: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir un résumé rapide pour le débogage
     */
    public static function getDebugSummary($entrepriseId = 1): array
    {
        try {
            $total = self::forEntreprise($entrepriseId)->count();
            $today = self::forEntreprise($entrepriseId)->whereDate('created_at', Carbon::today())->count();
            $thisMonth = self::forEntreprise($entrepriseId)->whereMonth('created_at', Carbon::now()->month)->count();
            $withAmount = self::forEntreprise($entrepriseId)->whereNotNull('montant')->where('montant', '>', 0)->count();
            $totalRevenue = self::forEntreprise($entrepriseId)->whereNotNull('montant')->sum('montant') ?? 0;

            return [
                'total_souscriptions' => $total,
                'souscriptions_aujourd_hui' => $today,
                'souscriptions_ce_mois' => $thisMonth,
                'avec_montant' => $withAmount,
                'chiffre_affaires_total' => $totalRevenue,
                'derniere_souscription' => self::forEntreprise($entrepriseId)->orderBy('created_at', 'desc')->first()?->created_at,
                'table_existe' => DB::getSchemaBuilder()->hasTable('souscrire_forfaits'),
                'colonnes' => DB::getSchemaBuilder()->getColumnListing('souscrire_forfaits')
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'table_existe' => false
            ];
        }
    }

    // ==================== ÉVÉNEMENTS DU MODÈLE ====================

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Événement lors de la création
        static::creating(function ($souscription) {
            if (!$souscription->ent1d) {
                $souscription->ent1d = 1; // Entreprise par défaut
            }
        });

        // Événement après création
        static::created(function ($souscription) {
            Log::info('Nouvelle souscription créée', [
                'id' => $souscription->id,
                'nom' => $souscription->full_name,
                'montant' => $souscription->montant
            ]);
        });

        // Événement après mise à jour
        static::updated(function ($souscription) {
            Log::info('Souscription mise à jour', [
                'id' => $souscription->id,
                'etat' => $souscription->etat,
                'changes' => $souscription->getChanges()
            ]);
        });
    }
}