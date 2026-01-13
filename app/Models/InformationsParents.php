<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-08-18 19:34:55
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class InformationsParents extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'informations_parents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'adresse_mere', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'adresse_pere', 'profession_pere', 'profession_mere', 'nom_pere', 'id_profil_visa', 'numero_person', 'nom_mere', 'prenom_pere', 'prenom_mere', 'num_whatsapp_pere', 'num_whatsapp_mere', 'email_pere', 'email_mere',
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
