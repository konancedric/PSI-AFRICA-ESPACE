<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendrierEvent extends Model
{
    use HasFactory;

    protected $table = 'calendrier_events';

    protected $fillable = [
        'title',
        'candidateName',
        'candidateContact',
        'date',
        'time',
        'eventType',
        'priority',
        'agent',
        'description',
        'alarm',
        'alarmDate',
        'alarmTime',
        'alarmFrequency',
        'status',
        'decision',
        'user_id'
    ];

    protected $casts = [
        'alarm' => 'boolean',
        'decision' => 'boolean',
        'date' => 'date:Y-m-d',
        'alarmDate' => 'date:Y-m-d',
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour obtenir les événements d'une date spécifique
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope pour obtenir les événements d'un agent
     */
    public function scopeByAgent($query, $agent)
    {
        return $query->where('agent', $agent);
    }

    /**
     * Scope pour obtenir les événements à venir
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                    ->orderBy('date', 'asc')
                    ->orderBy('time', 'asc');
    }

    /**
     * Scope pour obtenir les événements avec alarme
     */
    public function scopeWithAlarm($query)
    {
        return $query->where('alarm', true);
    }
}
