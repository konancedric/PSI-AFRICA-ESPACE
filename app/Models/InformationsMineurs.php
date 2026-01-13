<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-08-18 20:44:21
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class InformationsMineurs extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'informations_mineurs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'niveau_etude_mineur', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'autre_niveau_etude_mineur', 'passeport', 'pays_accueil', 'motif', 'classe_enfant', 'date_migration', 'pays_residence', 'nom_hote', 'nationalite_actuelle', 'lien_parente_enfant', 'nationalite_naissance', 'lien_parente_enfant_parent',
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
