<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent_by',
        'recipient_name',
        'recipient_phone',
        'message',
        'status',
        'error_message',
        'campaign_id',
        'api_response',
        'sent_at',
    ];

    protected $casts = [
        'api_response' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur qui a envoyé le SMS
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Relation avec la campagne
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(SmsCampaign::class, 'campaign_id', 'campaign_id');
    }

    /**
     * Scope pour les SMS envoyés
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope pour les SMS échoués
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour les SMS en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
