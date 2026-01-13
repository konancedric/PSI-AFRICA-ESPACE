<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CaisseEntree extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caisse_entrees';

    protected $fillable = [
        'uuid',
        'date',
        'ref',
        'nom',
        'prenoms',
        'categorie',
        'nature',
        'montant',
        'mode_paiement',
        'detail_prestations',
        'tiers_nom',
        'montant_verse_tiers',
        'type_payeur',
        'payeur_nom_prenom',
        'payeur_telephone',
        'payeur_relation',
        'payeur_reference_dossier',
        'created_by_user_id',
        'created_by_username',
        'cloture_id'
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
        'montant_verse_tiers' => 'decimal:2',
        'detail_prestations' => 'array'
    ];

    /**
     * Boot method pour gérer les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement uuid et ref avant la création
        static::creating(function ($entree) {
            // Générer UUID si non fourni
            if (empty($entree->uuid)) {
                $entree->uuid = (string) Str::uuid();
            }

            // Générer référence si non fournie
            if (empty($entree->ref)) {
                $entree->ref = static::generateRef();
            }
        });
    }

    /**
     * Générer une référence unique pour l'entrée
     * Format: ENT-YYYYMMDD-0001
     */
    public static function generateRef()
    {
        $date = now();
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        $prefix = "ENT-{$year}{$month}{$day}";

        // Trouver le dernier numéro pour aujourd'hui (en incluant les soft deleted pour éviter les doublons)
        $lastEntry = static::withTrashed()
            ->where('ref', 'LIKE', "{$prefix}-%")
            ->orderBy('ref', 'desc')
            ->first();

        if ($lastEntry) {
            // Extraire le numéro et l'incrémenter
            $lastNumber = (int) substr($lastEntry->ref, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Relation avec la clôture
     */
    public function cloture()
    {
        return $this->belongsTo(CaisseCloture::class, 'cloture_id');
    }

    /**
     * Vérifier si l'entrée est clôturée
     */
    public function isCloturee()
    {
        return !is_null($this->cloture_id);
    }

    /**
     * Scope pour les entrées du mois en cours (non clôturées)
     */
    public function scopeMoisActif($query)
    {
        return $query->whereNull('cloture_id');
    }

    /**
     * Scope pour les entrées clôturées
     */
    public function scopeCloturees($query)
    {
        return $query->whereNotNull('cloture_id');
    }
}
