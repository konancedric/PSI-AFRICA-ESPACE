<button class="btn btn-primary btn-sm" href="#MFNewTemoignage" data-toggle="modal" data-target="#MFNewTemoignage" title="Nouveau Site"><i class="fa fa-users"></i> Nouveau Temoignage
</button>
<div class="modal fade edit-layout-modal" id="MFNewTemoignage" tabindex="-1" role="dialog" aria-labelledby="MFNewTemoignageLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="MFNewTemoignageLabel">
                    <b>Création d'un nouveau temoignage</b> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <iframe src="{{$linkEditor}}/add-temoignage.php/?id_check={{ $ent1d }}&user1d={{ $user1d }}" width="100%" height="750" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}} </button>
            </div>
        </div>
    </div>
</div>