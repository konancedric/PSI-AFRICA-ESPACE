<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-07 09:30:22
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class DocumentsVoyage extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'documents_voyage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_sejour_debut', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'date_sejour_fin', 'prenom', 'nom', 'date_voyage_aller', 'date_voyage_retour', 'documents_interess', 'contact', 'check_certifi', 'numero_demande',
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
