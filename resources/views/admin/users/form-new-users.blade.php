<button class="btn btn-warning btn-sm" href="#MFNewUsers" data-toggle="modal" data-target="#MFNewUsers" title="Nouveau Users"><i class="fa fa-user-plus"></i> Nouvelle Personne
</button>
<div class="modal fade edit-layout-modal" id="MFNewUsers" tabindex="-1" role="dialog" aria-labelledby="MFNewUsersLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <h5 class="modal-title text-white" id="MFNewUsersLabel">
                    <b>Création d'un nouvelle Personne</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" enctype="multipart/form-data" method="post" role="form" action="{{ url('users/new-add') }}" >
                    @csrf
                    <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user1d" value="{{$user1d}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name"><i class="fa fa-user"></i> {{ __('Nom & Prenom')}}<span class="text-red">*</span></label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="" placeholder="Entrer son nom & prénom(s)" required>
                                <div class="help-block with-errors"></div>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="matricule"><i class="fa fa-id-card"></i> {{ __('Matricule')}}<span class="text-red">*</span></label>
                                <input id="matricule" type="text" class="form-control @error('matricule') is-invalid @enderror" name="matricule" value="" placeholder="Entrer son matricule" required>
                                <div class="help-block with-errors"></div>
                                @error('matricule')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="contact"><i class="fa fa-phone"></i> {{ __('Contact')}}<span class="text-red"> </span></label>
                                <input id="contact" type="text" class="form-control @error('contact') is-invalid @enderror" name="contact" value="" placeholder="Entrer son contact">
                                <div class="help-block with-errors"></div>
                                @error('contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_categorie"><i class="fas fa-user-cog"></i> {{ __('Catégorie')}}<span class="text-red">*</span></label>
                                <select class="form-control select2" id="id_categorie" name="id_categorie" required>
                                    <option value="">Selectionnez la catégorie</option>
                                     @foreach ($dataCategories as $tabCategories)
                                        <option value="{{ $tabCategories->id }}">{{ $tabCategories->libelle }}</option>
                                     @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_grade"><i class="fas fa-award"></i> {{ __('Grade')}}<span class="text-red">*</span></label>
                                <select class="form-control select2" id="id_grade" name="id_grade" required>
                                    <option value="">Selectionnez le grade</option>
                                     @foreach ($dataGrades as $tabGrades)
                                        <option value="{{ $tabGrades->id }}">{{ $tabGrades->libelle }}</option>
                                     @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="photo_user"><i class="ik ik-file"></i> {{ __('Photo Personne')}} *<span class="text-red"></span></label>
                                <input type="file" class="form-control" id="photo_user" accept="image/*" name="photo_user" placeholder="logo" required>
                                @error('photo_user')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-warning"><i class="fa fa-check-circle"></i> {{ __('Enregistrer')}}</button>
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