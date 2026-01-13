<button class="btn btn-success btn-sm" href="#MFViewTemoignage{{ $tabTemoignages->id }}" data-toggle="modal" data-target="#MFViewTemoignage{{ $tabTemoignages->id }}" title="Voir {{ $tabTemoignages->titre }}"><i class="fa fa-eye"></i> Voir le service
</button>
<div class="modal fade edit-layout-modal" id="MFViewTemoignage{{ $tabTemoignages->id }}" tabindex="-1" role="dialog" aria-labelledby="MFViewTemoignage{{ $tabTemoignages->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="MFViewTemoignage{{ $tabTemoignages->id }}Label">
                    <b>Voir {{ $tabTemoignages->titre }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                {!! $tabTemoignages->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>