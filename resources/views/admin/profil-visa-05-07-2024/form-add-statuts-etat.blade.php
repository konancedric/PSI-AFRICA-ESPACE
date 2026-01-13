<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#MEditStatutsEtatProfilVisa{{ $tabProfilVisa->id }}" title="Editer le statuts-etat de la commande {{ $tabProfilVisa->numero_commande }}"><i class="fa fa-magic"></i> Statuts Etat</button>
<div class="modal fade" id="MEditStatutsEtatProfilVisa{{ $tabProfilVisa->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-blue text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Editer le statuts-etat de la commande {{ $tabProfilVisa->numero_commande }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('profil-visa/add-statuts-etat')}}">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabProfilVisa->id }}">
                        <input type="hidden" name="numero_commande" value="{{ $tabProfilVisa->numero_commande }}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="id_statuts_etat"><i class="fa fa-magic"></i> {{ __('StatutsEtat')}}<span class="text-red">*</span></label>
                                <select class="form-control" id="id_statuts_etat" name="id_statuts_etat"  required placeholder="Changer le statuts-etat">
                                     @foreach ($dataStatutsEtat as $tabStatutsEtat)
                                        <option value="{{ $tabStatutsEtat->id }}" @if($tabStatutsEtat->id == $tabProfilVisa->id_statuts_etat) selected @endif>{{ $tabStatutsEtat->libelle }}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="message"><i class="fa fa-comments"></i> {{ __('Message')}}<span class="text-red">*</span></label>
                                <textarea type="text" class="form-control" id="message" name="message" placeholder="message" required> </textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-primary btn-rounded"><i class="fa fa-check-circle"></i> {{ __('Mettre à jour')}}</button>
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
