<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grades extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle',
        'description',
        'niveau',
        'salaire_min',
        'salaire_max',
        'etat',
        'ent1d',
        'user1d',
        'update_user',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'salaire_min' => 'decimal:2',
        'salaire_max' => 'decimal:2',
        'niveau' => 'integer',
        'etat' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();
        
        // Événement avant création
        static::creating(function ($grade) {
            if (auth()->check()) {
                $grade->user1d = auth()->id();
                $grade->ent1d = 1; // PSI Africa
            }
        });

        // Événement avant mise à jour
        static::updating(function ($grade) {
            if (auth()->check()) {
                $grade->update_user = auth()->id();
            }
        });
    }

    // ==================== RELATIONS ====================

    /**
     * Relation avec les utilisateurs qui ont ce grade
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_grade');
    }

    /**
     * Relation avec l'utilisateur qui a créé ce grade
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user1d');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour ce grade
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_user');
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprises::class, 'ent1d');
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les grades actifs
     */
    public function scopeActif($query)
    {
        return $query->where('etat', 1);
    }

    /**
     * Scope pour une entreprise donnée
     */
    public function scopeForEntreprise($query, $entrepriseId = 1)
    {
        return $query->where('ent1d', $entrepriseId);
    }

    /**
     * Scope pour ordonner par niveau
     */
    public function scopeOrderByLevel($query, $direction = 'asc')
    {
        return $query->orderBy('niveau', $direction);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtenir le libellé formaté
     */
    public function getLibelleFormatAttribute(): string
    {
        return ucfirst($this->libelle);
    }

    /**
     * Obtenir la fourchette de salaire
     */
    public function getFourchetteSalaireAttribute(): string
    {
        if ($this->salaire_min && $this->salaire_max) {
            return number_format($this->salaire_min, 0, ',', ' ') . ' - ' . 
                   number_format($this->salaire_max, 0, ',', ' ') . ' FCFA';
        } elseif ($this->salaire_min) {
            return 'À partir de ' . number_format($this->salaire_min, 0, ',', ' ') . ' FCFA';
        } elseif ($this->salaire_max) {
            return 'Jusqu\'à ' . number_format($this->salaire_max, 0, ',', ' ') . ' FCFA';
        }
        
        return 'Non défini';
    }

    /**
     * Obtenir le nombre d'utilisateurs avec ce grade
     */
    public function getNombreUtilisateursAttribute(): int
    {
        return $this->users()->count();
    }

    // ==================== MÉTHODES MÉTIER ====================

    /**
     * Vérifier si le grade est utilisé
     */
    public function isUsed(): bool
    {
        return $this->users()->exists();
    }

    /**
     * Obtenir le grade suivant (niveau supérieur)
     */
    public function getNextGrade()
    {
        return self::where('niveau', '>', $this->niveau)
                   ->where('ent1d', $this->ent1d)
                   ->orderBy('niveau', 'asc')
                   ->first();
    }

    /**
     * Obtenir le grade précédent (niveau inférieur)
     */
    public function getPreviousGrade()
    {
        return self::where('niveau', '<', $this->niveau)
                   ->where('ent1d', $this->ent1d)
                   ->orderBy('niveau', 'desc')
                   ->first();
    }

    /**
     * Obtenir la couleur du badge selon le niveau
     */
    public function getBadgeColor(): string
    {
        return match(true) {
            $this->niveau <= 2 => 'secondary',
            $this->niveau <= 4 => 'info',
            $this->niveau <= 6 => 'primary',
            $this->niveau <= 8 => 'success',
            default => 'warning'
        };
    }

    /**
     * Vérifier si un salaire est dans la fourchette
     */
    public function isSalaireInRange(float $salaire): bool
    {
        if ($this->salaire_min && $salaire < $this->salaire_min) {
            return false;
        }
        
        if ($this->salaire_max && $salaire > $this->salaire_max) {
            return false;
        }
        
        return true;
    }

    // ==================== MÉTHODES STATIQUES ====================

    /**
     * Obtenir les grades par niveau
     */
    public static function getByLevel(): \Illuminate\Database\Eloquent\Collection
    {
        return self::actif()
                   ->forEntreprise()
                   ->orderByLevel()
                   ->get();
    }

    /**
     * Obtenir les statistiques des grades
     */
    public static function getStatistics(): array
    {
        return [
            'total_grades' => self::actif()->count(),
            'grades_utilises' => self::actif()->has('users')->count(),
            'grade_le_plus_utilise' => self::actif()
                ->withCount('users')
                ->orderBy('users_count', 'desc')
                ->first(),
            'salaire_moyen_min' => self::actif()
                ->whereNotNull('salaire_min')
                ->avg('salaire_min'),
            'salaire_moyen_max' => self::actif()
                ->whereNotNull('salaire_max')
                ->avg('salaire_max'),
        ];
    }

    /**
     * Créer les grades par défaut
     */
    public static function createDefaults(): void
    {
        $defaultGrades = [
            ['libelle' => 'Stagiaire', 'niveau' => 1, 'salaire_min' => 50000, 'salaire_max' => 100000],
            ['libelle' => 'Assistant', 'niveau' => 2, 'salaire_min' => 80000, 'salaire_max' => 150000],
            ['libelle' => 'Agent', 'niveau' => 3, 'salaire_min' => 120000, 'salaire_max' => 200000],
            ['libelle' => 'Agent Senior', 'niveau' => 4, 'salaire_min' => 180000, 'salaire_max' => 280000],
            ['libelle' => 'Superviseur', 'niveau' => 5, 'salaire_min' => 250000, 'salaire_max' => 400000],
            ['libelle' => 'Chef de Service', 'niveau' => 6, 'salaire_min' => 350000, 'salaire_max' => 550000],
            ['libelle' => 'Manager', 'niveau' => 7, 'salaire_min' => 500000, 'salaire_max' => 800000],
            ['libelle' => 'Directeur', 'niveau' => 8, 'salaire_min' => 750000, 'salaire_max' => 1200000],
        ];

        foreach ($defaultGrades as $gradeData) {
            $gradeData['description'] = 'Grade ' . $gradeData['libelle'];
            $gradeData['etat'] = 1;
            $gradeData['ent1d'] = 1;
            
            self::firstOrCreate(
                ['libelle' => $gradeData['libelle'], 'ent1d' => 1],
                $gradeData
            );
        }
    }
}