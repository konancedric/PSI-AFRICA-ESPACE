<button type="button" class="btn btn-primary btn-sm mt-2 mb-2" data-toggle="modal" data-target="#MEditAddMessageProfilVisa{{ $tabProfilVisa->id }}" title="Ajouter un message de la commande {{ $tabProfilVisa->numero_commande }}"><i class="fa fa-comments"></i> Message</button>
<div class="modal fade" id="MEditAddMessageProfilVisa{{ $tabProfilVisa->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-blue text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Ajouter un message de la demande N° {{ $tabProfilVisa->numero_commande }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" method="POST" action="{{url('profil-visa/add-message-profil-visa')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="user1d" value="{{$user1d}}">
                        <input type="hidden" name="id" value="{{ $tabProfilVisa->id }}">
                        <input type="hidden" name="id_profil_visa" value="{{ $tabProfilVisa->id }}">
                        <input type="hidden" name="numero_commande" value="{{ $tabProfilVisa->numero_commande }}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="objet"><i class="fa fa-magic"></i> {{ __('Objet')}}<span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="objet" id="objet" / >
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="message"><i class="fa fa-comments"></i> {{ __('Message')}}<span class="text-red">*</span></label>
                                <textarea type="text" class="form-control" id="message" name="message" placeholder="message" value="{{ $tabProfilVisa->message }}"> </textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="photo"><i class="fa fa-file"></i> {{ __('Photo')}}<span class="text-red">*</span></label>
                                <input type="file" class="form-control" id="photo" name="photo" placeholder="Photo" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group"><br/>
                                <button type="submit" class="btn btn-primary btn-rounded"><i class="fa fa-check-circle"></i> {{ __('Envoyer')}}</button>
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
