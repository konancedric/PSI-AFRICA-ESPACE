<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-24 09:50:31
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class PiecesIdentites extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pieces_identites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numero_passeport', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'date_validite_passeport', 'type_passeport', 'numero_piece_identite', 'nationalite_actuelle', 'id_profil_visa', 'nationalite_naissance', 'date_validite_piece_identite', 'type_piece_identite', 'nbre_enfant', 'stuation_matrimonial', 'pays_residence', 'date_lieux_piece_identite', 'date_lieux_passport'
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
