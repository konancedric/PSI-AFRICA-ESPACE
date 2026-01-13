 <button class="btn btn-info btn-sm" href="#MViewHistory{{ $tabVilles->id }}" data-toggle="modal" data-target="#MViewHistory{{ $tabVilles->id }}" title="Voir L'historique de - {{ $tabVilles->libelle}}"><i class="fa fa-eye"></i> Voir l'historique
</button>
<div class="modal fade edit-layout-modal" id="MViewHistory{{ $tabVilles->id }}" tabindex="-1" role="dialog" aria-labelledby="MViewHistory{{ $tabVilles->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="MViewHistory{{ $tabVilles->id }}Label">
                    <b>Voir L'historique de {{ $tabVilles->libelle}}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <div class="card col-md-12">
                    {!! $tabVilles->historique !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>