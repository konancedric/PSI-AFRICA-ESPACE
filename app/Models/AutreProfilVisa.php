<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-04-18 16:54:18
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class AutreProfilVisa extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'autre_profil_visa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_profil_visa', 'autre_vue_psi_africa', 'autre_niveau_etude', 'autre_dernier_diplome', 'autre_stuation_professionnel', 'etat', 'update_user', 'updated_at', 'autre_documents_possedes', 'autre_biens_personnels', 'autre_type_visa_demande', 'autre_pays_a_immigrer', 'autre_lien_parente', 'autre_finance_voyage', 'autre_type_hebergement_immigration', 'autre_demande_visa_last', 'autre_visa_deja_obtenu', 
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
