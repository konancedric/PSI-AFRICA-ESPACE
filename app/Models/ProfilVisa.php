<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2025-06-30 14:00:00
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ProfilVisa extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'profil_visa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'etat', 
        'etape', 
        'update_user', 
        'updated_at', 
        'log_ip', 
        'user1d', 
        'ent1d', 
        'numero_profil_visa', 
        'id_statuts_etat', 
        'message', 
        'type_profil_visa',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'processing_days',
        'status_color',
        'type_label'
    ];

    /**
     * Relation avec l'utilisateur qui a créé le profil
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1d');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour le profil
     */
    public function updateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user');
    }

    /**
     * Relation avec le statut/état du profil
     */
    public function statutEtat(): BelongsTo
    {
        return $this->belongsTo(StatutsEtat::class, 'id_statuts_etat');
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprises::class, 'ent1d');
    }

    /**
     * Relation avec les messages associés au profil
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AddMessageProfilVisa::class, 'id_profil_visa');
    }

    /**
     * Calculer le nombre de jours de traitement
     */
    public function getProcessingDaysAttribute(): int
    {
        if (!$this->updated_at || !$this->created_at) {
            return 0;
        }
        
        return $this->created_at->diffInDays($this->updated_at);
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->statutEtat) {
            return $this->statutEtat->couleur ?? 'secondary';
        }
        
        return 'secondary';
    }

    /**
     * Obtenir le libellé du type de profil
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            1 => 'Tourisme',
            2 => 'Affaires',
            3 => 'Transit',
            4 => 'Étudiant',
            5 => 'Travail',
            6 => 'Famille',
            7 => 'Autre'
        ];

        return $types[$this->type_profil_visa] ?? 'Non défini';
    }

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
    public function scopeWithStatus($query, $statusId)
    {
        return $query->where('id_statuts_etat', $statusId);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type_profil_visa', $type);
    }

    /**
     * Scope pour les profils actifs
     */
    public function scopeActive($query)
    {
        return $query->where('etat', 1);
    }

    /**
     * Scope pour les profils créés aujourd'hui
     */
    public function scopeCreatedToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope pour les profils créés cette semaine
     */
    public function scopeCreatedThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour les profils créés ce mois
     */
    public function scopeCreatedThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope pour les profils en attente
     */
    public function scopePending($query)
    {
        return $query->whereHas('statutEtat', function($subQuery) {
            $subQuery->where('statuts_etat.libelle', 'like', '%attente%')
                     ->orWhere('statuts_etat.libelle', 'like', '%pending%');
        });
    }

    /**
     * Scope pour les profils urgents (plus de X jours)
     */
    public function scopeUrgent($query, $days = 7)
    {
        return $query->where('profil_visa.created_at', '<', Carbon::now()->subDays($days))
                    ->whereHas('statutEtat', function($subQuery) {
                        $subQuery->where('statuts_etat.libelle', 'like', '%attente%')
                                 ->orWhere('statuts_etat.libelle', 'like', '%pending%');
                    });
    }

    /**
     * Scope pour les profils complétés
     */
    public function scopeCompleted($query)
    {
        return $query->whereHas('statutEtat', function($subQuery) {
            $subQuery->where('statuts_etat.libelle', 'like', '%terminé%')
                     ->orWhere('statuts_etat.libelle', 'like', '%approuvé%')
                     ->orWhere('statuts_etat.libelle', 'like', '%validé%');
        });
    }

    /**
     * Obtenir les statistiques par période
     */
    public static function getStatsByPeriod($startDate, $endDate, $entrepriseId = 1)
    {
        return self::forEntreprise($entrepriseId)
                   ->whereBetween('created_at', [$startDate, $endDate])
                   ->selectRaw('
                       COUNT(*) as total,
                       COUNT(CASE WHEN id_statuts_etat IS NOT NULL THEN 1 END) as with_status,
                       AVG(DATEDIFF(updated_at, created_at)) as avg_processing_days
                   ')
                   ->first();
    }

    /**
     * Obtenir les statistiques par type
     */
    public static function getStatsByType($entrepriseId = 1)
    {
        return self::forEntreprise($entrepriseId)
                   ->selectRaw('type_profil_visa, COUNT(*) as total')
                   ->groupBy('type_profil_visa')
                   ->orderBy('total', 'desc')
                   ->get();
    }

    /**
     * Obtenir les statistiques par statut
     */
    public static function getStatsByStatus($entrepriseId = 1)
    {
        return DB::table('profil_visa')
                   ->join('statuts_etat', 'profil_visa.id_statuts_etat', '=', 'statuts_etat.id')
                   ->where('profil_visa.ent1d', $entrepriseId)
                   ->selectRaw('
                       statuts_etat.libelle as status_name,
                       statuts_etat.couleur as status_color,
                       COUNT(profil_visa.id) as total
                   ')
                   ->groupBy('statuts_etat.id', 'statuts_etat.libelle', 'statuts_etat.couleur')
                   ->orderBy('total', 'desc')
                   ->get();
    }

    /**
     * Obtenir l'évolution mensuelle
     */
    public static function getMonthlyEvolution($months = 12, $entrepriseId = 1)
    {
        $data = [];
        $currentDate = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $currentDate->copy()->subMonths($i);
            $count = self::forEntreprise($entrepriseId)
                        ->whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
            
            $data[] = [
                'month' => $month->format('M Y'),
                'count' => $count,
                'month_number' => $month->month,
                'year' => $month->year
            ];
        }

        return collect($data);
    }

    /**
     * Obtenir l'évolution journalière
     */
    public static function getDailyEvolution($days = 30, $entrepriseId = 1)
    {
        $data = [];
        $currentDate = Carbon::now();

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = $currentDate->copy()->subDays($i);
            $count = self::forEntreprise($entrepriseId)
                        ->whereDate('created_at', $day->toDateString())
                        ->count();
            
            $data[] = [
                'date' => $day->format('d/m'),
                'count' => $count,
                'full_date' => $day->toDateString()
            ];
        }

        return collect($data);
    }

    /**
     * Obtenir le taux de réussite
     */
    public static function getSuccessRate($entrepriseId = 1): float
    {
        $total = self::forEntreprise($entrepriseId)->count();
        
        if ($total === 0) {
            return 0;
        }

        $completed = self::forEntreprise($entrepriseId)->completed()->count();
        
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Obtenir le temps moyen de traitement
     */
    public static function getAvgProcessingTime($entrepriseId = 1): float
    {
        $result = self::forEntreprise($entrepriseId)
                     ->whereNotNull('id_statuts_etat')
                     ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                     ->first();
        
        return round($result->avg_days ?? 0, 1);
    }
}