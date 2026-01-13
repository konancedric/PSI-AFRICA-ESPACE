<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-08-18 21:41:11
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class ReservationAchat extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reservation_achat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_voyage', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'pays_destination', 'prenom', 'nom', 'date_voyage_aller', 'date_voyage_retour', 'enfant_2_11', 'contact', 'nbre_personne', 'email', 'check_certifi', 'numero_demande', 'enfant_0_2', 'pays_depart',
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
