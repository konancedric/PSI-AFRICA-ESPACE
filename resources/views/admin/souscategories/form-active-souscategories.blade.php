<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#MEditEtatSousCategoriese{{ $tabSousCategories->id }}" title="Editer l'état - {{ $tabSousCategories->libelle }}"><i class="fa fa-check-circle"></i></button>
<div class="modal fade" id="MEditEtatSousCategoriese{{ $tabSousCategories->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-orange text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Activer la sous categorie {{ $tabSousCategories->libelle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('souscategories/update')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabSousCategories->id }}">
                        <input type="hidden" name="libelle" value="{{ $tabSousCategories->libelle }}">
                        <input type="hidden" name="etat" value="1">
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Activer')}}</button>
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
