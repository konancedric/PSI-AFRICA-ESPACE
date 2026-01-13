<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMClientCommentaire extends Model
{
    use HasFactory;

    protected $table = 'crm_client_commentaires';

    protected $fillable = [
        'client_id',
        'user_id',
        'agent_name',
        'commentaire',
    ];

    public function client()
    {
        return $this->belongsTo(CRMClient::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}