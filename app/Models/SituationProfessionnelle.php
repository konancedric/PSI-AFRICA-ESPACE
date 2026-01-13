<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-28 10:51:31
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class SituationProfessionnelle extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'situation_professionnelle';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'niveau_etude', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'autre_niveau_etude', 'dernier_diplome', 'autre_dernier_diplome', 'stuation_professionnel', 'autre_stuation_professionnel', 'id_profil_visa', 'fait_prison', 'annee_experience', 'travailler_sous_pression', 'travailler_etranger', 'retourner_pays', 'trois_qualites', 'trois_defauts', 'personnalite_en_trois_mots', 'traivailler_en_equipe', 'bon_leader', 'etre_discret', 'savoir_nager', 'competences_en_secourisme', 'trois_loisirs_favoris', 'avoir_des_enfants', 'nbre_enfant', 'age_enfant',
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
