<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaisseDepense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caisse_depenses';

    protected $fillable = [
        'uuid',
        'date',
        'categorie',
        'description',
        'montant',
        'beneficiaire',
        'mode_paiement',
        'created_by_user_id',
        'created_by_username'
    ];

    protected $casts = [
        'date' => 'date',
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
