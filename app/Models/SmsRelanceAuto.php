<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRelanceAuto extends Model
{
    protected $table = 'sms_relances_auto';

    protected $fillable = [
        'client_id',
        'statut',
        'message_index',
        'status_changed_at',
        'last_sent_at',
        'total_sent',
        'active',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'last_sent_at'      => 'datetime',
        'active'            => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(CRMClient::class, 'client_id');
    }

    /**
     * Retourne true si $days jours se sont écoulés depuis le dernier envoi
     * (ou si jamais envoyé). Par défaut 7 jours (hebdomadaire).
     */
    public function isDueForRelance(int $days = 7): bool
    {
        if (is_null($this->last_sent_at)) {
            return true;
        }
        return $this->last_sent_at->addDays($days)->isPast();
    }
}
