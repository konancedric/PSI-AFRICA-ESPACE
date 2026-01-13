<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#MEditEtat{{ $tabForfaits->id }}" title="Editer l'état - {{ $tabForfaits->titre }}"><i class="ik ik-trash-2"></i></button>
<div class="modal fade" id="MEditEtat{{ $tabForfaits->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-blue text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Désactiver le forfait {{ $tabForfaits->titre }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('forfaits/update')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabForfaits->id }}">
                        <input type="hidden" name="titre" value="{{ $tabForfaits->titre }}">
                        <input type="hidden" name="etat" value="0">
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-primary btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Désactiver')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>
