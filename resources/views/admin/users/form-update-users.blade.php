<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#MEdit{{ $tabUsers->id }}" title="Editer {{ $tabUsers->name }}"><i class="ik ik-edit-2"></i></button>
<div class="modal fade" id="MEdit{{ $tabUsers->id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content card">
            <div class="modal-header bg-orange text-white text-center">
                <h5 class="modal-title" id="demoModalLabel">Mettre à jour le grade {{ $tabUsers->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-justify">
                <form class="forms-sample" enctype="multipart/form-data" method="post" role="form" action="{{ url('users/new-update') }}" >
                    @csrf
                    <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user1d" value="{{$user1d}}">
                    <input type="hidden" name="id" value="{{ $tabUsers->id }}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name"><i class="fa fa-user"></i> {{ __('Nom & Prenom')}}<span class="text-red">*</span></label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $tabUsers->name }}" placeholder="Entrer son nom & prénom(s)" required>
                                <div class="help-block with-errors"></div>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="matricule"><i class="fa fa-id-card"></i> {{ __('Matricule')}}<span class="text-red">*</span></label>
                                <input id="matricule" type="text" class="form-control @error('matricule') is-invalid @enderror" name="matricule" value="{{ $tabUsers->matricule }}" placeholder="Entrer son matricule" required>
                                <div class="help-block with-errors"></div>
                                @error('matricule')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="contact"><i class="fa fa-phone"></i> {{ __('Contact')}}<span class="text-red"> </span></label>
                                <input id="contact" type="text" class="form-control @error('contact') is-invalid @enderror" name="contact" value="{{ $tabUsers->contact }}" placeholder="Entrer son contact">
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
                                    @foreach ($dataCategories as $tabCategories)
                                        <option value="{{ $tabCategories->id }}" @if($tabUsers->id_categorie == $tabCategories->id) selected @endif>{{ $tabCategories->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_grade"><i class="fas fa-award"></i> {{ __('Grade')}}  <span class="text-red">*</span></label>
                                <select class="form-control select2" id="id_grade" name="id_grade" required>
                                    @foreach ($dataGrades as $tabGrades)
                                        <option value="{{ $tabGrades->id }}" @if($tabUsers->id_grade == $tabGrades->id) selected @endif>{{ $tabGrades->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-warning"><i class="fa fa-check-circle"></i> {{ __('Mettre jour')}}</button>
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
