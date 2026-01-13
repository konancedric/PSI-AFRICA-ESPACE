<button class="btn btn-success btn-sm" href="#MFViewForfait{{ $tabForfaits->id }}" data-toggle="modal" data-target="#MFViewForfait{{ $tabForfaits->id }}" title="Voir {{ $tabForfaits->titre }}"><i class="fa fa-eye"></i> Voir le forfait
</button>
<div class="modal fade edit-layout-modal" id="MFViewForfait{{ $tabForfaits->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewForfait{{ $tabForfaits->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MFViewForfait{{ $tabForfaits->id }}Label">
                    <b>Voir {{ $tabForfaits->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                {!! $tabForfaits->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>