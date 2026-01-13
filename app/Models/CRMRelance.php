<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CRMRelance extends Model
{
    protected $table = 'crm_relances';
    
    protected $fillable = [
        'client_id',
        'agent_name',
        'user_id',
        'statut',
        'commentaire',
        'date_relance',
        'prochaine_relance',
        'canal'
    ];

    protected $casts = [
        'date_relance' => 'datetime',
        'prochaine_relance' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CRMClient::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}