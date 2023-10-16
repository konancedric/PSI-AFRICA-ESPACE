<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   ManestEtoo
 * @Last Modified time: 2023-10-06 10:00:57
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class ModelHasRoles extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'model_has_roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'model_type',  'model_id',
    ];

    
}
