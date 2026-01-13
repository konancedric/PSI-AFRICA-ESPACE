<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaisseCloture extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caisse_clotures';

    protected $fillable = [
        'uuid',
        'mois',
        'date',
        'total_entrees',
        'total_sorties',
        'solde',
        'marge_cabinet',
        'total_cabinet',
        'marge_docs',
        'total_docs',
        'verse_tiers',
        'dime',
        'cloture',
        'nb_entrees',
        'nb_sorties',
        'remarques',
        'created_by_user_id',
        'created_by_username'
    ];

    protected $casts = [
        'date' => 'date',
        'total_entrees' => 'decimal:2',
        'total_sorties' => 'decimal:2',
        'solde' => 'decimal:2',
        'marge_cabinet' => 'decimal:2',
        'total_cabinet' => 'decimal:2',
        'marge_docs' => 'decimal:2',
        'total_docs' => 'decimal:2',
        'verse_tiers' => 'decimal:2',
        'dime' => 'decimal:2',
        'cloture' => 'boolean',
        'nb_entrees' => 'integer',
        'nb_sorties' => 'integer'
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
