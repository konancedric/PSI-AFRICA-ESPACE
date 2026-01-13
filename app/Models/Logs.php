<?php

/**
 * @Author: Zie MC
 * @Date:   2024-05-01 09:59:27
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-01 10:19:13
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Logs extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_detail', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'log_ip', 'priorite',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function saveLogs($log_detail, $user1d, $ent1d, $log_ip, $priorite)
    {
        $Logs = new Logs;
        $Logs->log_detail =  $log_detail;
        $Logs->user1d =  $user1d;
        $Logs->ent1d =  $ent1d;
        $Logs->log_ip =  $log_ip;
        $Logs->priorite =  $priorite;
        $Logs->save();
        if ($Logs)
        {
            $resultatSave = "OK";
        }
        else
        {
            $resultatSave = "NO";
        }
        return $resultatSave;
    }
}
