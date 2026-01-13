<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CRMContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_contracts';

    protected $fillable = [
        'numero_contrat',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'sexe',
        'etat_civil',
        'profession',
        'employeur',
        'adresse',
        'ville',
        'telephone_mobile',
        'telephone_fixe',
        'email',
        'type_visa',
        'pays_destination',
        'montant_contrat',
        'avance',
        'reste_payer',
        'montant_lettres',
        'date_echeance',
        'mode_paiement',
        'conseiller',
        'lieu_contrat',
        'date_contrat',
        'statut',
        'signature_token',
        'token_expires_at',
        'token_used_at',
        'view_token',
        'view_token_generated_at',
        'signature',
        'nom_signataire',
        'date_signature',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_echeance' => 'date',
        'date_contrat' => 'date',
        'date_signature' => 'datetime',
        'token_expires_at' => 'datetime',
        'token_used_at' => 'datetime',
        'view_token_generated_at' => 'datetime',
        'montant_contrat' => 'decimal:2',
        'avance' => 'decimal:2',
        'reste_payer' => 'decimal:2'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le numéro de contrat
        static::creating(function ($contract) {
            if (empty($contract->numero_contrat)) {
                $contract->numero_contrat = self::generateNumeroContrat();
            }
            
            // Enregistrer l'utilisateur qui crée
            if (auth()->check()) {
                $contract->created_by = auth()->id();
            }
        });

        static::updating(function ($contract) {
            // Enregistrer l'utilisateur qui modifie
            if (auth()->check()) {
                $contract->updated_by = auth()->id();
            }
        });
    }

    /**
     * Générer un numéro de contrat unique
     */
    public static function generateNumeroContrat()
    {
        $year = date('Y');
        $lastContract = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastContract) {
            // Extraire le numéro de la dernière commande
            $lastNumber = (int) substr($lastContract->numero_contrat, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return 'CNT-' . $year . '-' . $newNumber;
    }

    /**
     * Relations
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeSigned($query)
    {
        return $query->where('statut', 'Signé');
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'En attente');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', date('m'))
                     ->whereYear('created_at', date('Y'));
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', date('Y'));
    }

    /**
     * Accesseurs
     */
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getIsSignedAttribute()
    {
        return $this->statut === 'Signé';
    }

    /**
     * Méthodes utiles
     */

    /**
     * Générer un token unique de signature
     */
    public function generateSignatureToken($expirationHours = 72)
    {
        $this->update([
            'signature_token' => bin2hex(random_bytes(32)),
            'token_expires_at' => now()->addHours($expirationHours),
            'token_used_at' => null
        ]);

        return $this->signature_token;
    }

    /**
     * Vérifier si le token est valide
     */
    public function isTokenValid()
    {
        // Token doit exister
        if (!$this->signature_token) {
            return false;
        }

        // Token ne doit pas avoir été utilisé
        if ($this->token_used_at) {
            return false;
        }

        // Token ne doit pas être expiré
        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        // Contrat ne doit pas déjà être signé
        if ($this->statut === 'Signé') {
            return false;
        }

        return true;
    }

    /**
     * Marquer le token comme utilisé et signer le contrat
     */
    public function signer($signature, $nomSignataire)
    {
        $this->update([
            'statut' => 'Signé',
            'signature' => $signature,
            'nom_signataire' => $nomSignataire,
            'date_signature' => now(),
            'token_used_at' => now()
        ]);
    }

    /**
     * Annuler un contrat
     */
    public function annuler()
    {
        $this->update(['statut' => 'Annulé']);
    }

    /**
     * Récupérer un contrat par son token
     */
    public static function findByToken($token)
    {
        return self::where('signature_token', $token)->first();
    }

    /**
     * Générer un token unique pour la consultation du contrat
     */
    public function generateViewToken()
    {
        $this->update([
            'view_token' => bin2hex(random_bytes(32)),
            'view_token_generated_at' => now()
        ]);

        return $this->view_token;
    }

    /**
     * Récupérer un contrat par son view_token
     */
    public static function findByViewToken($token)
    {
        return self::where('view_token', $token)->first();
    }

    /**
     * Obtenir le lien de consultation du contrat
     */
    public function getViewLink()
    {
        if (!$this->view_token) {
            $this->generateViewToken();
        }

        return url("/contrats/view/{$this->view_token}");
    }
}