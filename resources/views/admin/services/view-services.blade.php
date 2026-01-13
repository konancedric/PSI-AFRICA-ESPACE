<button class="btn btn-success btn-sm" href="#MFViewService{{ $tabServices->id }}" data-toggle="modal" data-target="#MFViewService{{ $tabServices->id }}" title="Voir {{ $tabServices->titre }}"><i class="fa fa-eye"></i> Voir le service
</button>
<div class="modal fade edit-layout-modal" id="MFViewService{{ $tabServices->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewService{{ $tabServices->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MFViewService{{ $tabServices->id }}Label">
                    <b>Voir {{ $tabServices->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                {!! $tabServices->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>