<button class="btn btn-success btn-sm" href="#MFViewActualite{{ $tabActualites->id }}" data-toggle="modal" data-target="#MFViewActualite{{ $tabActualites->id }}" title="Voir {{ $tabActualites->titre }}"><i class="fa fa-eye"></i> Voir l'actualité
</button>
<div class="modal fade edit-layout-modal" id="MFViewActualite{{ $tabActualites->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewActualite{{ $tabActualites->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MFViewActualite{{ $tabActualites->id }}Label">
                    <b>Voir {{ $tabActualites->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                {!! $tabActualites->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>