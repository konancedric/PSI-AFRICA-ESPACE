<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessagerieMessage extends Model
{
    use HasFactory;

    protected $fillable = [
    'sender_id',
    'recipient_id',
    'text',
    'audio',
    'audio_duration',
    'type',
    'is_private',
    'read',        // ✅ Ajouter
    'read_at'      // ✅ Ajouter
];

    protected $casts = [
    'is_private' => 'boolean',
    'read' => 'boolean',         // ✅ Ajouter
    'read_at' => 'datetime',     // ✅ Ajouter
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
    /**
     * Relation avec l'expéditeur
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relation avec le destinataire
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope pour les messages publics
     */
    public function scopePublic($query)
    {
        return $query->whereNull('recipient_id');
    }

    /**
     * Scope pour les messages privés
     */
    public function scopePrivate($query)
    {
        return $query->whereNotNull('recipient_id');
    }

    /**
     * Scope pour les messages d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            // Messages publics OU messages privés pour cet utilisateur
            $q->whereNull('recipient_id')
              ->orWhere('recipient_id', $userId)
              ->orWhere('sender_id', $userId);
        });
    }
}