<?php

/**
 * @Author: Zie MC
 * @Date:   2024-04-22 14:58:49
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-07 10:56:55
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Countries extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ci_countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sortname', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'name', 'slug', 'phonecode', 'status',
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
