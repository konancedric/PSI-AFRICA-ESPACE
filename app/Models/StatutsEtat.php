<?php
/**
 * @Author: MARS
 * @Date:   2025-06-30 14:00:00
 * @Last Modified by:   MARS
 * @Last Modified time: 2025-06-30 14:00:00
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatutsEtat extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'statuts_etat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle',
        'description',
        'couleur',
        'icone',
        'etat',
        'ordre',
        'user1d',
        'ent1d'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'etat' => 'boolean',
        'ordre' => 'integer'
    ];

    /**
     * Relation avec les profils visa ayant ce statut
     */
    public function profilsVisa(): HasMany
    {
        return $this->hasMany(ProfilVisa::class, 'id_statuts_etat');
    }

    /**
     * Relation avec l'utilisateur qui a créé le statut
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user1d');
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprises::class, 'ent1d');
    }

    /**
     * Scope pour les statuts actifs
     */
    public function scopeActive($query)
    {
        return $query->where('etat', 1);
    }

    /**
     * Scope pour ordonner par ordre de priorité
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre', 'asc');
    }

    /**
     * Obtenir la classe CSS pour la couleur
     */
    public function getColorClassAttribute(): string
    {
        $colorMap = [
            'primary' => 'text-primary',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
            'secondary' => 'text-secondary',
            'dark' => 'text-dark',
            'light' => 'text-light'
        ];

        return $colorMap[$this->couleur] ?? 'text-secondary';
    }

    /**
     * Obtenir la classe badge pour la couleur
     */
    public function getBadgeClassAttribute(): string
    {
        $colorMap = [
            'primary' => 'badge-primary',
            'success' => 'badge-success',
            'warning' => 'badge-warning',
            'danger' => 'badge-danger',
            'info' => 'badge-info',
            'secondary' => 'badge-secondary',
            'dark' => 'badge-dark',
            'light' => 'badge-light'
        ];

        return $colorMap[$this->couleur] ?? 'badge-secondary';
    }

    /**
     * Vérifier si le statut indique une completion
     */
    public function getIsCompletedAttribute(): bool
    {
        $completedKeywords = ['terminé', 'approuvé', 'validé', 'completed', 'approved', 'finished'];
        
        foreach ($completedKeywords as $keyword) {
            if (stripos($this->libelle, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifier si le statut indique une attente
     */
    public function getIsPendingAttribute(): bool
    {
        $pendingKeywords = ['attente', 'pending', 'en cours', 'traitement', 'review'];
        
        foreach ($pendingKeywords as $keyword) {
            if (stripos($this->libelle, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifier si le statut indique un rejet
     */
    public function getIsRejectedAttribute(): bool
    {
        $rejectedKeywords = ['rejeté', 'refusé', 'rejected', 'denied', 'cancelled', 'annulé'];
        
        foreach ($rejectedKeywords as $keyword) {
            if (stripos($this->libelle, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Obtenir les statistiques d'utilisation du statut
     */
    public function getUsageStatsAttribute(): array
    {
        $total = $this->profilsVisa()->count();
        $thisMonth = $this->profilsVisa()
                         ->whereMonth('created_at', now()->month)
                         ->whereYear('created_at', now()->year)
                         ->count();
        $thisWeek = $this->profilsVisa()
                        ->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                        ->count();

        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'this_week' => $thisWeek
        ];
    }

    /**
     * Créer les statuts par défaut
     */
    public static function createDefaults($entrepriseId = 1, $userId = 1): void
    {
        $defaultStatuses = [
            [
                'libelle' => 'En attente',
                'description' => 'Demande reçue et en attente de traitement',
                'couleur' => 'warning',
                'icone' => 'fas fa-clock',
                'ordre' => 1
            ],
            [
                'libelle' => 'En cours de traitement',
                'description' => 'Demande en cours d\'analyse',
                'couleur' => 'info',
                'icone' => 'fas fa-cogs',
                'ordre' => 2
            ],
            [
                'libelle' => 'Documentaire complémentaire',
                'description' => 'Documents supplémentaires requis',
                'couleur' => 'primary',
                'icone' => 'fas fa-file-alt',
                'ordre' => 3
            ],
            [
                'libelle' => 'Approuvé',
                'description' => 'Demande approuvée avec succès',
                'couleur' => 'success',
                'icone' => 'fas fa-check-circle',
                'ordre' => 4
            ],
            [
                'libelle' => 'Rejeté',
                'description' => 'Demande rejetée',
                'couleur' => 'danger',
                'icone' => 'fas fa-times-circle',
                'ordre' => 5
            ],
            [
                'libelle' => 'Visa délivré',
                'description' => 'Visa délivré et prêt pour retrait',
                'couleur' => 'success',
                'icone' => 'fas fa-passport',
                'ordre' => 6
            ]
        ];

        foreach ($defaultStatuses as $status) {
            if (!self::where('libelle', $status['libelle'])->where('ent1d', $entrepriseId)->exists()) {
                self::create(array_merge($status, [
                    'etat' => 1,
                    'user1d' => $userId,
                    'ent1d' => $entrepriseId
                ]));
            }
        }
    }
}