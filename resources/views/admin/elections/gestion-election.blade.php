<button class="btn btn-warning btn-sm" href="#MGestElection{{ $tabElections->id }}" data-toggle="modal" data-target="#MGestElection{{ $tabElections->id }}" title="Gérer Election {{ $tabElections->libelle }}"><i class="fas fa-info-circle"></i> Gérer l'election
</button>
<div class="modal fade edit-layout-modal" id="MGestElection{{ $tabElections->id }}" tabindex="-1" role="dialog" aria-labelledby="MGestElection{{ $tabElections->id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <h5 class="modal-title text-white" id="MGestElection{{ $tabElections->id }}Label">
                    <b>Gérer l'election {{ $tabElections->libelle }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <div class="card">
                    <div class="card-header bg-dark text-white text-center"> LES TÊTE(S) DE LISTE </div>
                    <div class="card-body">
                        @include("admin.elections.list-tete2liste-by-election")
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark text-white text-center"> PROGRESSION ET RÉSULTAT DES VOTES {{ strtoupper($tabElections->libelle)}}</div>
                    <div class="card-body">
                       @include("admin.elections.suivi-by-election")
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark text-white text-center"> LISTE DES VOTANTS </div>
                    <div class="card-body">
                       @include("admin.elections.list-votant-by-election")
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>