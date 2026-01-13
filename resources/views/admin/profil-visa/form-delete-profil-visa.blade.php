<button type="button" class="btn btn-danger btn-sm mt-2 mb-2" data-toggle="modal" data-target="#MDeleteProfilVisa{{ $tabProfilVisa->id }}" title="Editer l'état - {{ $tabProfilVisa->objet }}"><b><i><i class="ik ik-trash-2"></i> Supprimer</i></b></button>
<div class="modal fade" id="MDeleteProfilVisa{{ $tabProfilVisa->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-danger text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Confirmer la suppression du profil-visa " {{ $tabProfilVisa->numero_profil_visa }} " <br/><b><i> Cette action est irréversible !</i></b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('profil-visa/delete')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabProfilVisa->id }}">
                        <input type="hidden" name="etat" value="0">
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-danger btn-rounded"><i class="ik ik-check-circle"></i><b><i> {{ __('Confirmer la suppression')}}</i></b></button>
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
