<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#MEdit{{ $tabSousCategories->id }}" title="Editer {{ $tabSousCategories->libelle }}"><i class="ik ik-edit-2"></i></button>
<div class="modal fade" id="MEdit{{ $tabSousCategories->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-orange text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Mettre à jour la sous categorie {{ $tabSousCategories->libelle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('souscategories/update')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabSousCategories->id }}">
                        <input type="hidden" name="etat" value="{{ $tabSousCategories->etat }}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="id_categorie"><i class="fa fa-tags"></i> {{ __('Categorie')}}<span class="text-red">*</span></label>
                                <select class="form-control" id="id_categorie" name="id_categorie" required>
                                    <option value="">Selectionnez la categorie</option>
                                     @foreach ($dataCategories as $tabCategories)
                                        <option value="{{ $tabCategories->id }}" @if($tabSousCategories->id_categorie == $tabCategories->id) selected @endif>{{ $tabCategories->libelle }}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="libelle"><i class="fas fa-tag"></i> {{ __('Libelle')}}<span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" value="{{ $tabSousCategories->libelle }}" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Mettre à jour')}}</button>
                                <button type="reset" class="btn btn-dark btn-rounded">{{ __('Annuler')}}</button>
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
