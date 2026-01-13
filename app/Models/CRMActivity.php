<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CRMActivity extends Model
{
    protected $table = 'crm_activities';

    protected $fillable = [
        'action', 'details', 'user_name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}