<?php
/**
 * @Author: Zie MC
 * @Date:   2024-04-23 08:53:22
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-06 09:39:26
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class QuestionnairesDocuments extends Authenticatable
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'questionnaires_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vue_psi_africa', 'user1d', 'ent1d', 'etat', 'update_user', 'updated_at', 'autre_vue_psi_africa', 'avis_partager', 'documents_possedes', 'autre_documents_possedes', 'biens_personnels', 'id_profil_visa', 'autre_biens_personnels', 'type_visa_demande', 'autre_type_visa_demande', 'pays_a_immigrer', 'autre_pays_a_immigrer', 'nbre_jours_souhaiter', 'date_voyage_prevu', 'raisons_voyage', 'ascendants_europeens', 'lien_parente', 'autre_lien_parente', 'finance_voyage', 'autre_finance_voyage', 'type_hebergement_immigration', 'autre_type_hebergement_immigration', 'demande_visa_last', 'autre_demande_visa_last', 'visa_deja_obtenu', 'autre_visa_deja_obtenu', 'famille_pays_immigration', 'parler_francais', 'ecrire_francais', 'doses_covid', 'carnet_vaccins_international', 'file_autre_visa_deja_obtenu',
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
