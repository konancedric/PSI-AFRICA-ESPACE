<button class="btn btn-primary btn-sm" href="#MFUpdateFaq{{ $tabFaqs->id }}" data-toggle="modal" data-target="#MFUpdateFaq{{ $tabFaqs->id }}" title="Mettre à jour {{ $tabFaqs->titre }}"><i class="fa fa-edit"></i> Mettre à jour
</button>
<div class="modal fade edit-layout-modal" id="MFUpdateFaq{{ $tabFaqs->id }}" tabindex="-1" role="dialog" aria-labelledby="MFUpdateFaq{{ $tabFaqs->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="MFUpdateFaq{{ $tabFaqs->id }}Label">
                    <b>Mettre à jour {{ $tabFaqs->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <iframe src="{{$linkEditor}}/update-faq.php/?id_check={{ $tabFaqs->id }}&user1d={{ $user1d }}" width="100%" height="750" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>