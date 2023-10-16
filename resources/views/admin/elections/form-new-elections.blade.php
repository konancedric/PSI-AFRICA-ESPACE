<button class="btn btn-warning btn-sm" href="#MFNewElection" data-toggle="modal" data-target="#MFNewElection" title="Nouveau Election"><i class="fas fa-vote-yea"></i> Nouvelle Election
</button>
<div class="modal fade edit-layout-modal" id="MFNewElection" tabindex="-1" role="dialog" aria-labelledby="MFNewElectionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <h5 class="modal-title text-white" id="MFNewElectionLabel">
                    <b>Création d'un nouvelle Election</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times; </span></button>
            </div>
            <div class="modal-body text-justify">
                <div class="card">
                    <div class="card-header bg-dark text-white"> ELECTION EN TENANT COMPTE DU GRADE & DE LA CATÉGORIE </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('elections/create')}}">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="user1d" value="{{$user1d}}">
                                <input type="hidden" name="type_election" value="1">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="libelle"><i class="fas fa-vote-yea"></i> {{ __('Titre')}}<span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_debut"><i class="fas fa-calendar"></i> {{ __('Date Debut')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" placeholder="Date Début">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_fin"><i class="fas fa-calendar"></i> {{ __('Date Fin')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" placeholder="Date Fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_grade"><i class="fas fa-award"></i> {{ __('Grade')}}<span class="text-red">*</span></label>
                                        <select id="id_grade" class="form-control select2" name="id_grade" required>
                                            <option value=''>Selectionner le Grade</option>
                                            @foreach ($dataGrades as $tabGrades)
                                                <option value="{{ $tabGrades->id }}"  @if(!empty (session()->get('grade')) && session()->get('grade')== $tabGrades->id)  selected  @endif>
                                                    {{ strtoupper($tabGrades->libelle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_categorie"><i class="fas fa-user-cog"></i> {{ __('Categorie')}}<span class="text-red">*</span></label>
                                        <select id="id_categorie" class="form-control select2" name="id_categorie" required>
                                            <option value=''>Selectionner le Categorie</option>
                                            @foreach ($dataCategories as $tabCategories)
                                                <option value="{{ $tabCategories->id }}"  @if(!empty (session()->get('categorie')) && session()->get('categorie')== $tabCategories->id)  selected  @endif>
                                                    {{ strtoupper($tabCategories->libelle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                     <label for="get-user-by-grade-categorie"><i class="fas fa-users"></i> Tête (s) de liste (s) <span class="text-red">*</span></label>
                                     <select id="get-user-by-grade-categorie" class="form-control select2"multiple="multiple"  name="tete_liste[]" required>
                                         <!-- Options de la deuxième sélection -->
                                     </select> 
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark text-white"> ELECTION EN TENANT COMPTE QUE DU GRADE  </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('elections/create')}}">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="user1d" value="{{$user1d}}">
                                <input type="hidden" name="type_election" value="3">
                                <input type="hidden" name="id_categorie" value="0">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="libelle"><i class="fas fa-vote-yea"></i> {{ __('Titre')}}<span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_debut"><i class="fas fa-calendar"></i> {{ __('Date Debut')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" placeholder="Date Début">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_fin"><i class="fas fa-calendar"></i> {{ __('Date Fin')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" placeholder="Date Fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_grade_unique"><i class="fas fa-award"></i> {{ __('Grade')}}<span class="text-red">*</span></label>
                                        <select id="id_grade_unique" class="form-control select2" name="id_grade" required>
                                            <option value=''>Selectionner le Grade</option>
                                            @foreach ($dataGrades as $tabGrades)
                                                <option value="{{ $tabGrades->id }}"  @if(!empty (session()->get('grade')) && session()->get('grade')== $tabGrades->id)  selected  @endif>
                                                    {{ strtoupper($tabGrades->libelle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                     <label for="get-user-by-grade"><i class="fas fa-users"></i> Tête (s) de liste (s) <span class="text-red">*</span></label>
                                     <select id="get-user-by-grade" class="form-control select2"multiple="multiple"  name="tete_liste[]" required>
                                         <!-- Options de la deuxième sélection -->
                                     </select> 
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-dark text-white"> ELECTION EN TENANT COMPTE QUE DE LA CATÉGORIE </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{url('elections/create')}}">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="user1d" value="{{$user1d}}">
                                <input type="hidden" name="type_election" value="2">
                                <input type="hidden" name="id_grade" value="0">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="libelle"><i class="fas fa-vote-yea"></i> {{ __('Titre')}}<span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Libelle" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_debut"><i class="fas fa-calendar"></i> {{ __('Date Debut')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" placeholder="Date Début">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_fin"><i class="fas fa-calendar"></i> {{ __('Date Fin')}}<span class="text-red"></span></label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" placeholder="Date Fin">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_categorie_unique"><i class="fas fa-user-cog"></i> {{ __('Categorie')}}<span class="text-red">*</span></label>
                                        <select id="id_categorie_unique" class="form-control select2" name="id_categorie" required>
                                            <option value=''>Selectionner le Categorie</option>
                                            @foreach ($dataCategories as $tabCategories)
                                                <option value="{{ $tabCategories->id }}"  @if(!empty (session()->get('categorie')) && session()->get('categorie')== $tabCategories->id)  selected  @endif>
                                                    {{ strtoupper($tabCategories->libelle) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                     <label for="get-user-by-categorie"><i class="fas fa-users"></i> Tête (s) de liste (s) <span class="text-red">*</span></label>
                                     <select id="get-user-by-categorie" class="form-control select2"multiple="multiple"  name="tete_liste[]" required>
                                         <!-- Options de la deuxième sélection -->
                                     </select> 
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-warning btn-rounded"><i class="ik ik-check-circle"></i> {{ __('Enregistrer')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fermer')}}</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Lorsque la première sélection change
        $('#id_grade').change(function() {
            var id_grade = $(this).val();
            $('#get-user-by-grade-categorie').empty();
            $('#get-user-by-grade-categorie').append('<option value=""> Selectionner le Service </option>');
            $('#id_categorie').change(function() {
                var id_categorie = $(this).val();
                // Effectuez une demande AJAX vers la route avec la valeur sélectionnée pour les services
                $.ajax({
                    url: '/get-data-user-by-grade-categorie/' + id_grade + '/' + id_categorie,
                    method: 'GET',
                    success: function(data) {
                        // Remplissez la deuxième sélection avec les données reçues
                        $('#get-user-by-grade-categorie').empty();
                        $.each(data, function(key, user) {
                            $('#get-user-by-grade-categorie').append('<option value="' + user.id + '">' + user.name + ' | ' + user.matricule + '</option>');
                        });
                    }
                });
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Lorsque la première sélection change
        $('#id_grade_unique').change(function() {
            var id_grade = $(this).val();
            $('#get-user-by-grade').empty();
            $.ajax({
                url: '/get-data-user-by-grade/' + id_grade ,
                method: 'GET',
                success: function(data) {
                    // Remplissez la deuxième sélection avec les données reçues
                    $('#get-user-by-grade').empty();
                    $.each(data, function(key, user) {
                        $('#get-user-by-grade').append('<option value="' + user.id + '">' + user.name + ' | ' + user.matricule + '</option>');
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Lorsque la première sélection change
        $('#id_categorie_unique').change(function() {
            var id_categorie = $(this).val();
            $('#get-user-by-categorie').empty();
            $.ajax({
                url: '/get-data-user-by-categorie/' + id_categorie ,
                method: 'GET',
                success: function(data) {
                    // Remplissez la deuxième sélection avec les données reçues
                    $('#get-user-by-categorie').empty();
                    $.each(data, function(key, user) {
                        $('#get-user-by-categorie').append('<option value="' + user.id + '">' + user.name + ' | ' + user.matricule + '</option>');
                    });
                }
            });
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#get-user-by-grade-categorie').select2();
        $('#get-user-by-grade').select2();
        $('#get-user-by-categorie').select2();
    });
</script>