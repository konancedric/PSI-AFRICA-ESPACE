<?php

/**
 * @Author: Zie MC
 * @Date:   2024-04-23 10:36:56
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-06-09 21:56:03
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Statuts extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'statuts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'libelle', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'statuts', 'bg_color', 'numero_etape', 'description',
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