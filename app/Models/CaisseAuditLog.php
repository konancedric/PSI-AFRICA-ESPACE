<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaisseAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'timestamp',
        'user',
        'action',
        'details'
    ];

    protected $casts = [
        'timestamp' => 'datetime'
    ];
}
