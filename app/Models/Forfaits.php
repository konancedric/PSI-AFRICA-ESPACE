<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-06-09 12:18:42
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Forfaits extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'forfaits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle', 'user1d', 'ent1d', 'etat', 'prix', 'update_user', 'updated_at', 'resume', 'titre', 'description', 'id_categorie','img_forfait',
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
