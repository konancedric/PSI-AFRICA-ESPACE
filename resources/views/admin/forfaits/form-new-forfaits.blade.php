<button class="btn btn-primary btn-sm" href="#MFNewForfait" data-toggle="modal" data-target="#MFNewForfait" title="Nouveau Forfait"><i class="fa fa-comment"></i> Nouvelle Forfait
</button>
<div class="modal fade edit-layout-modal" id="MFNewForfait" tabindex="-1" role="dialog" aria-labelledby="MFNewForfaitLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="MFNewForfaitLabel">
                    <b>Création d'un nouveau forfait</b> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <iframe src="{{$linkEditor}}/add-forfait.php/?id_check={{ $ent1d }}&user1d={{ $user1d }}" width="100%" height="750" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}} </button>
            </div>
        </div>
    </div>
</div>