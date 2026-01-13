<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'type',
        'reference',
        'date_document',
        'clients',
        'destination',
        'ville',
        'compagnie',
        'date_depart',
        'date_retour',
        'ref_reservation',
        'voyageurs',
        'nom_hotel',
        'adresse_hotel',
        'telephone_hotel',
        'email_hotel',
        'date_arrivee',
        'date_depart_hotel',
        'nuits',
        'type_appartement',
        'adultes',
        'enfants',
        'tarif_euro',
        'tarif_fcfa',
        'agent_name',
        'agent_fonction',
        'user_id',
    ];

    protected $casts = [
        'date_document' => 'date',
        'date_depart' => 'date',
        'date_retour' => 'date',
        'date_arrivee' => 'date',
        'date_depart_hotel' => 'date',
        'voyageurs' => 'array',
        'tarif_euro' => 'decimal:2',
        'tarif_fcfa' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
