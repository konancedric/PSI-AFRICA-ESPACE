<?php
    $tabMessageProfilVisa = DB::table('profil_visa')
            ->join('add_message_profil_visa', 'add_message_profil_visa.id_profil_visa', '=', 'profil_visa.id')
            ->where('profil_visa.id', $tabProfilVisa->id)
            ->select('add_message_profil_visa.*', 'profil_visa.numero_profil_visa as numero_profil_visa')
            ->get();
    $nbrMessageProfilVisa = count($tabMessageProfilVisa);
?>

@if($nbrMessageProfilVisa > 0)
     <button class="btn btn-warning btn-sm mt-2 mb-2" href="#MViewProfilVisaMessage{{ $tabProfilVisa->id }}" data-toggle="modal" data-target="#MViewProfilVisaMessage{{ $tabProfilVisa->id }}" title="Voir {{ $tabProfilVisa->numero_profil_visa }}"><i class="fa fa-comments"></i> Voir les messages
        </button>
@endif
<div class="modal fade edit-layout-modal" id="MViewProfilVisaMessage{{ $tabProfilVisa->id }}" tabindex="-1" role="dialog" aria-labelledby="MViewProfilVisaMessage{{ $tabProfilVisa->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="Messageument">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="MViewProfilVisaMessage{{ $tabProfilVisa->id }}Label">
                    <b>Demande N° {{ $tabProfilVisa->numero_profil_visa }}</b> | Type Profil :  @if(is_null($tabProfilVisa->type_profil_visa) || $tabProfilVisa->type_profil_visa === '')
                        <span class="badge badge-warning badge-pill text-dark" style="background-color: #f0ad4e;">
                            <i class="fas fa-clock"></i> Profil en attente
                        </span>
                    @elseif($tabProfilVisa->type_profil_visa == "tourisme")
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
                    @foreach($tabMessageProfilVisa as $dataMessageProfilVisa)
                        <div class="offcanvas-header">
                            <h5 id="offcanvasEndLabelMViewProfilVisaMessage{{ $tabProfilVisa->id }}" class="offcanvas-title alert alert-success w-100 text-center"><b><u>DEMANDE N° : {{$dataMessageProfilVisa->numero_profil_visa}}</u></b></h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
                            <div class="container-xxl py-5" style="margin-top:-150px !important;" id="">
                                <div class="container py-5 px-lg-5">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12 mt-5"><br/><br/><br/><br/>
                                            <h3>{{$dataMessageProfilVisa->objet}}</h3>
                                            <p>{{$dataMessageProfilVisa->message}}</p>
                                        </div>
                                        <div class="col-md-12">
                                            @if($dataMessageProfilVisa->photo != "NO")
                                                <a href="upload/profil-visa/{{$tabProfilVisa->id}}/{{$dataMessageProfilVisa->photo}}" download="photo_{{$dataMessageProfilVisa->photo}}" class="btn btn-primary mt-2"><i class="fa fa-download"></i> Telecharger le fichier</a><br/><br/>
                                               <img src="upload/profil-visa/{{$tabProfilVisa->id}}/{{$dataMessageProfilVisa->photo}}" class="w-100">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary d-grid w-50" data-bs-dismiss="offcanvas" >  Fermer</button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>