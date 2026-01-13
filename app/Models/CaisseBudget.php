<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaisseBudget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caisse_budgets';

    protected $fillable = [
        'uuid',
        'mois',
        'montant',
        'remarques',
        'created_by_user_id',
        'created_by_username'
    ];

    protected $casts = [
        'montant' => 'decimal:2'
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
