<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2023-12-17 22:42:51
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Entreprises extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'entreprises';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'denomination', 'user1d','etat','update_user', 'updated_at', 'adresse', 'contact', 'description', 'emailent', 'username','logo_ent', 'map', 'link_facebook', 'link_linkedin', 'link_twitter', 'link_siteweb','bg_color','_tokent_private','_tokent_public', 'id_ville', 'id_souscategorie', 'url_qr', 'save_qr',
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
