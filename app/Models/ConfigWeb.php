<?php

/**
 * @Author: ManestEtoo
 * @Date:   2024-06-21 11:08:48
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-07-05 11:38:32
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class ConfigWeb extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'config_web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'denomination', 'user1d', 'contact', 'etat', 'update_user', 'updated_at', 'description', 'adresse', 'link_facebook', 'logo_ent', 'link_linkedin', 'link_twitter', 'link_instagram','email', 'link_video', 'img_pub', 'num_whatsapp',
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
