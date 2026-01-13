<button class="btn btn-success btn-sm" href="#MFViewFaq{{ $tabFaqs->id }}" data-toggle="modal" data-target="#MFViewFaq{{ $tabFaqs->id }}" title="Voir {{ $tabFaqs->titre }}"><i class="fa fa-eye"></i> Voir le faq
</button>
<div class="modal fade edit-layout-modal" id="MFViewFaq{{ $tabFaqs->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewFaq{{ $tabFaqs->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MFViewFaq{{ $tabFaqs->id }}Label">
                    <b>Voir {{ $tabFaqs->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                {!! $tabFaqs->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>