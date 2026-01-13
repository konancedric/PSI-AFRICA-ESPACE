<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-15 10:56:37
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class GalerieImages extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'galerie_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle', 'user1d', 'ent1d', 'etat', 'etat', 'update_user', 'updated_at', 'save_url', 'id_categorie',
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
