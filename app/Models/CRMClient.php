<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CRMClient extends Model
{
    use SoftDeletes;

    protected $table = 'crm_clients';

    protected $fillable = [
        'uid', 'nom', 'prenoms', 'contact', 'email', 'media',
        'prestation', 'budget', 'statut', 'agent', 'date_creation',
        'commentaire', 'user_id', 'client_portal_token'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'date_creation' => 'date',
    ];

    // ✅ Ne pas inclure portal_url dans appends pour éviter la génération automatique du token à chaque sérialisation
    // protected $appends = ['portal_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->uid)) {
                $client->uid = strtoupper(Str::random(12));
            }
            if (empty($client->date_creation)) {
                $client->date_creation = now();
            }
        });
    }

    /**
     * Generate a unique portal token for the client
     */
    public function generatePortalToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('client_portal_token', $token)->exists());

        $this->client_portal_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Get the facturation URL for the client
     * Note: Ne génère PAS automatiquement le token pour éviter les sauvegardes non désirées
     */
    public function getPortalUrlAttribute()
    {
        if (!$this->client_portal_token) {
            return null; // Retourner null si le token n'existe pas encore
        }
        return url('/facturation/' . $this->client_portal_token);
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(CRMInvoice::class, 'client_id');
    }

    public function relances()
    {
        return $this->hasMany(CRMRelance::class, 'client_id');
    }

    public function commentaires()
    {
        return $this->hasMany(CRMClientCommentaire::class, 'client_id');
    }
}