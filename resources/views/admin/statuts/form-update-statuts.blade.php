<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#MEdit{{ $tabStatuts->id }}" title="Editer {{ $tabStatuts->libelle }}"><i class="fa fa-edit"></i></button>
<div class="modal fade" id="MEdit{{ $tabStatuts->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-blue text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Mettre à jour le statuts {{ $tabStatuts->libelle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('statuts/update')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabStatuts->id }}">
                        <input type="hidden" name="etat" value="{{ $tabStatuts->etat }}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="libelle"><i class="fa fa-map"></i> {{ __('Libelle')}}<span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" value="{{ $tabStatuts->libelle }}" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="bg_color"><i class="fa fa-map"></i> {{ __('Couleur')}}<span class="text-red">*</span></label>
                                <input type="color" class="form-control" id="bg_color" name="bg_color" placeholder="Libelle" value="{{ $tabStatuts->bg_color }}" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="numero_etape"><i class="fa fa-tag"></i> {{ __('Numero Etapes')}}<span class="text-red">*</span></label>
                                <input type="number" class="form-control" id="numero_etape" name="numero_etape" value="{{ $tabStatuts->numero_etape }}" placeholder="Numero Etapes" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="description"><i class="fa fa-comments"></i> {{ __('Description')}}<span class="text-red">*</span></label>
                                <textarea type="text" class="form-control" id="description" name="description" placeholder="description" required>{{ $tabStatuts->description }} </textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-primary btn-rounded"><i class="fa fa-check-circle"></i> {{ __('Mettre à jour')}}</button>
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
