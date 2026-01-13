<button class="btn btn-primary btn-sm" href="#MFNewActualite" data-toggle="modal" data-target="#MFNewActualite" title="Nouveau Actualite"><i class="fa fa-comment"></i> Nouvelle Actualite
</button>
<div class="modal fade edit-layout-modal" id="MFNewActualite" tabindex="-1" role="dialog" aria-labelledby="MFNewActualiteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="MFNewActualiteLabel">
                    <b>Création d'une nouvelle actualite</b> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <iframe src="{{$linkEditor}}/add-actualite.php/?id_check={{ $ent1d }}&user1d={{ $user1d }}" width="100%" height="750" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}} </button>
            </div>
        </div>
    </div>
</div>