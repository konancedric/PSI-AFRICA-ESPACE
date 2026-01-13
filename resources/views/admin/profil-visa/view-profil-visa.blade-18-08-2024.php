<button class="btn btn-{{$bgColor}} btn-sm" href="#MFViewProfilVisa{{ $tabProfilVisa->id }}" data-toggle="modal" data-target="#MFViewProfilVisa{{ $tabProfilVisa->id }}" title="Voir {{ $tabProfilVisa->numero_profil_visa }}"><i class="fa fa-eye"></i> Voir le Profil Visa
</button>
<div class="modal fade edit-layout-modal" id="MFViewProfilVisa{{ $tabProfilVisa->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewProfilVisa{{ $tabProfilVisa->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="MFViewProfilVisa{{ $tabProfilVisa->id }}Label">
                    <b>Demande N° {{ $tabProfilVisa->numero_profil_visa }}</b> | Type Profil :  @if($tabProfilVisa->type_profil_visa == "tourisme")
                        <span class="badge badge-primary badge-pill text-white"> VISA Tourisme</span>
                    @elseif($tabProfilVisa->type_profil_visa == "mineur")
                        <span class="badge badge-warning badge-pill text-white"> VISA Mineur</span>
                    @elseif($tabProfilVisa->type_profil_visa == "etude")
                        <span class="badge badge-info badge-pill text-white"> VISA Etude</span>
                    @elseif($tabProfilVisa->type_profil_visa == "travail")
                        <span class="badge badge-success badge-pill text-white"> VISA Travail</span>
                    @else
                        <span class="badge badge-dark badge-pill text-white">Autre</span>
                    @endif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <div class="card-header bg-{{$bgColor}} text-white text-center">
                    ETAT DE LA DEMANDE
                    @foreach(App\Models\StatutsEtat::where('id', $tabProfilVisa->id_statuts_etat)->get() as $tabStatutsEtat)
                        <span class="badge badge-pill" style="background-color:<?=$tabStatutsEtat->bg_color?>">{{ $tabStatutsEtat->libelle }}</span>
                    @endforeach
                </div>
                <div class="card-body">
                    <p>{{$tabProfilVisa->message}} </p>
                </div>
                @foreach(App\Models\InformationsPersonnelles::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabInformationsPersonnelle)
                    <div class="card">
                        <div class="card-header bg-{{$bgColor}} text-white text-center">
                            INFORMATIONS PERSONNELLES
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Nom</th>
                                    <td>{{$tabInformationsPersonnelle->nom}}</td>
                                </tr>
                                <tr>
                                    <th>Prénom</th>
                                    <td>{{$tabInformationsPersonnelle->prenom}}</td>
                                </tr>
                                <tr>
                                    <th>Sexe</th>
                                    <td>{{$tabInformationsPersonnelle->sexe}}</td>
                                </tr>
                                <tr>
                                    <th>Lieu Naissance</th>
                                    <td>{{$tabInformationsPersonnelle->lieu_naiss}}</td>
                                </tr>
                                <tr>
                                    <th>Date Naissance</th>
                                    <td>{{$tabInformationsPersonnelle->date_naiss}}</td>
                                </tr>
                                <tr>
                                    <th>Numéro Personne</th>
                                    <td>{{$tabInformationsPersonnelle->numero_person}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endforeach
                @foreach(App\Models\CoordonneesPersonnelles::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabCoordonneesPersonnelle)
                    <div class="card">
                        <div class="card-header bg-{{$bgColor}} text-white text-center">
                            COORDONNEES PERSONNELLES
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Contact</th>
                                    <td>{{$tabCoordonneesPersonnelle->contact}}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{$tabCoordonneesPersonnelle->email}}</td>
                                </tr>
                                <tr>
                                    <th>Adresse postale</th>
                                    <td>{{$tabCoordonneesPersonnelle->adresse_postale}}</td>
                                </tr>
                                <tr>
                                    <th>Adresse</th>
                                    <td>{{$tabCoordonneesPersonnelle->adresse}}</td>
                                </tr>
                                <tr>
                                    <th>Numéro Whatsapp</th>
                                    <td>{{$tabCoordonneesPersonnelle->num_whatsapp}}</td>
                                </tr>
                                <tr>
                                    <th>Numéro Telegram</th>
                                    <td>{{$tabCoordonneesPersonnelle->num_telegram}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-header">
                        
                        </div>
                    </div>
                @endforeach
                @foreach(App\Models\PiecesIdentites::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabPiecesIdentite)
                    <div class="card">
                        <div class="card-header bg-{{$bgColor}} text-white text-center">
                            PIÈCES IDENTITÉ
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Numéro passeport</th>
                                    <td>{{$tabPiecesIdentite->numero_passeport}}</td>
                                </tr>
                                <tr>
                                    <th>Date validité pièce identité</th>
                                    <td>{{$tabPiecesIdentite->date_validite_passeport}}</td>
                                </tr>
                                <tr>
                                    <th>Date validité passeport</th>
                                    <td>{{$tabPiecesIdentite->date_validite_passeport}}</td>
                                </tr>
                                <tr>
                                    <th>Type passeport</th>
                                    <td>
                                        @foreach(App\Models\type_passeport::where('id', $tabPiecesIdentite->type_passeport)->get() as $TABtype_passeport)
                                            {{$TABtype_passeport->libelle}}
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nationalité actuelle</th>
                                    <td>{{$tabPiecesIdentite->nationalite_actuelle}}</td>
                                </tr>
                                <tr>
                                    <th>Nationalité naissance</th>
                                    <td>{{$tabPiecesIdentite->nationalite_naissance}}</td>
                                </tr>
                                <tr>
                                    <th>Pays de résidence</th>
                                    <td>
                                         @foreach(App\Models\Countries::where('id', $tabPiecesIdentite->pays_residence)->get() as $TABCountries)
                                            {{$TABCountries->name}}
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Numéro  pièce identité</th>
                                    <td>{{$tabPiecesIdentite->numero_piece_identite}}</td>
                                </tr>
                                <tr>
                                    <th>Date validité pièce identité</th>
                                    <td>{{$tabPiecesIdentite->date_validite_piece_identite}}</td>
                                </tr>
                                <tr>
                                    <th>Type pièce identité</th>
                                    <td>
                                        @foreach(App\Models\type_piece_identite::where('id', $tabPiecesIdentite->type_piece_identite)->get() as $TABtype_piece_identite)
                                            {{$TABtype_piece_identite->libelle}}
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date et Lieux d'établissement</th>
                                    <td>{{$tabPiecesIdentite->date_lieux_piece_identite}}</td>
                                </tr>
                                <tr>
                                    <th>N. enfant</th>
                                    <td>{{$tabPiecesIdentite->nbre_enfant}}</td>
                                </tr>
                                <tr>
                                    <th>Stuation matrimonial</th>
                                    <td>
                                        @foreach(App\Models\stuation_matrimonial::where('id', $tabPiecesIdentite->stuation_matrimonial)->get() as $TABstuation_matrimonial)
                                            {{$TABstuation_matrimonial->libelle}}
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-header">
                        
                        </div>
                    </div>
                @endforeach
                @foreach(App\Models\SituationProfessionnelle::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabSituationProfessionnelle)
                    <div class="card">
                        <div class="card-header bg-{{$bgColor}} text-white text-center">
                            SITUATION PROFESSIONNELLE
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Niveau etude</th>
                                    <td>
                                        @if($tabSituationProfessionnelle->niveau_etude != 100)
                                            @foreach(App\Models\niveau_etude::where('id', $tabSituationProfessionnelle->niveau_etude)->get() as $TABniveau_etude)
                                                {{$TABniveau_etude->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabSituationProfessionnelle->autre_niveau_etude}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dernier diplome</th>
                                    <td>
                                        @if($tabSituationProfessionnelle->dernier_diplome != 100)
                                            @foreach(App\Models\niveau_etude::where('id', $tabSituationProfessionnelle->dernier_diplome)->get() as $TABdernier_diplome)
                                                {{$TABdernier_diplome->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabSituationProfessionnelle->autre_dernier_diplome}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stuation professionnel</th>
                                    <td>
                                        @if($tabSituationProfessionnelle->stuation_professionnel != 100)
                                            @foreach(App\Models\stuation_professionnel::where('id', $tabSituationProfessionnelle->stuation_professionnel)->get() as $TABstuation_professionnel)
                                                {{$TABstuation_professionnel->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabSituationProfessionnelle->autre_stuation_professionnel}}
                                        @endif
                                    </td>
                                </tr>
                                @if($tabProfilVisa->type_profil_visa == "travail")
                                    <tr>
                                        <th>Avez-vous déjà fait la prison ?</th>
                                        <td>{{$tabSituationProfessionnelle->fait_prison}}</td>
                                    </tr>
                                    <tr>
                                        <th>Années d'expériences</th>
                                        <td>{{$tabSituationProfessionnelle->annee_experience}}</td>
                                    </tr>
                                    <tr>
                                        <th>Travailler sous pression</th>
                                        <td>{{$tabSituationProfessionnelle->travailler_sous_pression}}</td>
                                    </tr>
                                    <tr>
                                        <th>Travailler à l’étranger</th>
                                        <td>{{$tabSituationProfessionnelle->travailler_etranger}}</td>
                                    </tr>
                                    <tr>
                                        <th>Retour au pays d'origine</th>
                                        <td>{{$tabSituationProfessionnelle->retourner_pays}}</td>
                                    </tr>
                                    <tr>
                                        <th>3 qualités</th>
                                        <td>{{$tabSituationProfessionnelle->trois_qualites}}</td>
                                    </tr>
                                    <tr>
                                        <th>3 défauts</th>
                                        <td>{{$tabSituationProfessionnelle->trois_defauts}}</td>
                                    </tr>
                                    <tr>
                                        <th>Personnalité en trois mots</th>
                                        <td>{{$tabSituationProfessionnelle->personnalite_en_trois_mots}}</td>
                                    </tr>
                                    <tr>
                                        <th>Travaillez en équipe</th>
                                        <td>{{$tabSituationProfessionnelle->traivailler_en_equipe}}</td>
                                    </tr>
                                    <tr>
                                        <th>Bon leader</th>
                                        <td>{{$tabSituationProfessionnelle->bon_leader}}</td>
                                    </tr>
                                    <tr>
                                        <th>Être discrète</th>
                                        <td>{{$tabSituationProfessionnelle->etre_discret}}</td>
                                    </tr>
                                    <tr>
                                        <th>Savoir nager</th>
                                        <td>{{$tabSituationProfessionnelle->savoir_nager}}</td>
                                    </tr>
                                    <tr>
                                        <th>Compétences en secourisme</th>
                                        <td>{{$tabSituationProfessionnelle->competences_en_secourisme}}</td>
                                    </tr>
                                    <tr>
                                        <th>3 loisirs favoris</th>
                                        <td>{{$tabSituationProfessionnelle->trois_loisirs_favoris}}</td>
                                    </tr>
                                    <tr>
                                        <th>A des enfants</th>
                                        <td>{{$tabSituationProfessionnelle->avoir_des_enfants}}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre enfants</th>
                                        <td>{{$tabSituationProfessionnelle->nbre_enfant}}</td>
                                    </tr>
                                    <tr>
                                        <th>Âge des enfants</th>
                                        <td>{{$tabSituationProfessionnelle->age_enfant}}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endforeach
                @foreach(App\Models\QuestionnairesDocuments::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabQuestionnairesDocument)
                    <div class="card">
                        <div class="card-header bg-{{$bgColor}} text-white text-center">
                            QUESTIONNAIRES DOCUMENTS
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Vue psi africa</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->vue_psi_africa != 100)
                                            @foreach(App\Models\vue_psi_africa::where('id', $tabQuestionnairesDocument->vue_psi_africa)->get() as $TABvue_psi_africa)
                                                {{$TABvue_psi_africa->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_vue_psi_africa}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Avis partager</th>
                                    <td>{{$tabQuestionnairesDocument->avis_partager}}</td>
                                </tr>
                                <tr>
                                    <th>Documents possedes</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->documents_possedes != 100)
                                            @foreach(App\Models\documents_possedes::where('id', $tabQuestionnairesDocument->documents_possedes)->get() as $TABdocuments_possedes)
                                                {{$TABdocuments_possedes->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_documents_possedes}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Biens personnels</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->biens_personnels != 100)
                                            @foreach(App\Models\biens_personnels::where('id', $tabQuestionnairesDocument->biens_personnels)->get() as $TABbiens_personnels)
                                                {{$TABbiens_personnels->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_biens_personnels}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Type visa demande</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->type_visa_demande != 100)
                                            @foreach(App\Models\type_visa_demande::where('id', $tabQuestionnairesDocument->type_visa_demande)->get() as $TABtype_visa_demande)
                                                {{$TABtype_visa_demande->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_type_visa_demande}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pays a immigrer</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->pays_a_immigrer != 100)
                                            @foreach(App\Models\pays_a_immigrer::where('id', $tabQuestionnairesDocument->pays_a_immigrer)->get() as $TABpays_a_immigrer)
                                                {{$TABpays_a_immigrer->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_pays_a_immigrer}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>N. jours souhaiter</th>
                                    <td>{{$tabQuestionnairesDocument->nbre_jours_souhaiter}}</td>
                                </tr>
                                <tr>
                                    <th>Lien parente</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->lien_parente != 100)
                                            @foreach(App\Models\lien_parente::where('id', $tabQuestionnairesDocument->lien_parente)->get() as $TABlien_parente)
                                                {{$TABlien_parente->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_lien_parente}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Finance voyage</th>
                                    <td>
                                        @if($tabQuestionnairesDocument->finance_voyage != 100)
                                            @foreach(App\Models\finance_voyage::where('id', $tabQuestionnairesDocument->finance_voyage)->get() as $TABfinance_voyage)
                                                {{$TABfinance_voyage->libelle}}
                                            @endforeach
                                        @else
                                            {{$tabQuestionnairesDocument->autre_finance_voyage}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date voyage prevu</th>
                                    <td>{{$tabQuestionnairesDocument->date_voyage_prevu}}</td>
                                </tr>
                                <tr>
                                    <th>Raisons voyage</th>
                                    <td>{{$tabQuestionnairesDocument->raisons_voyage}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="submit" onClick="FuncPrint('MFViewProfilVisa{{ $tabProfilVisa->id }}')" class="btn btn-dark btn-block col-md-5 ml-2 mt-2"><i class="fa fa-print"></i> Imprimer le reçu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/script.js"></script><script>
    function FuncPrint(divName) 
    {
        var printContents = document.getElementById(divName).innerHTML;    
        var originalContents = document.body.innerHTML;      
        document.body.innerHTML = printContents;     
        window.print();     
        document.body.innerHTML = originalContents;
    } 
</script>