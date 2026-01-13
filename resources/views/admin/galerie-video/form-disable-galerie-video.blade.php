<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#MEditEtat{{ $tabGalerieVideo->id }}" title="Editer l'état - {{ $tabGalerieVideo->libelle }}"><i class="ik ik-trash-2"></i></button>
<div class="modal fade" id="MEditEtat{{ $tabGalerieVideo->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-orange text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Désactiver la video {{ $tabGalerieVideo->libelle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('galerie-video/update')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabGalerieVideo->id }}">
                        <input type="hidden" name="libelle" value="{{ $tabGalerieVideo->libelle }}">
                        <input type="hidden" name="save_url" value="{{ $tabGalerieVideo->save_url }}">
                        <input type="hidden" name="etat" value="0">
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Désactiver')}}</button>
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
