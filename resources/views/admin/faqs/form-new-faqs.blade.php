<button class="btn btn-primary btn-sm" href="#MFNewFaq" data-toggle="modal" data-target="#MFNewFaq" title="Nouveau Faqs"><i class="fa fa-info-circle"></i> Nouveau Faq
</button>
<div class="modal fade edit-layout-modal" id="MFNewFaq" tabindex="-1" role="dialog" aria-labelledby="MFNewFaqLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="MFNewFaqLabel">
                    <b>Création d'un nouveau faq</b> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <iframe src="{{$linkEditor}}/add-faq.php/?id_check={{ $ent1d }}&user1d={{ $user1d }}" width="100%" height="750" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}} </button>
            </div>
        </div>
    </div>
</div>