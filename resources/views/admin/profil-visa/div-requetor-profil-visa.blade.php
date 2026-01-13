<div class="card">
    <div class="card card-header bg-dark text-white text-center">REQUÊTTEUR</div>
    <div class="card card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        PAR (NOM & PRENOM)
                    </div>
                    <div class="card-body row">
                        <form class="forms-sample" method="POST" action="{{url('profil-visa')}}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="id_user1d"><i class="fa fa-users"></i> {{ __('Nom & Prenom')}}<span class="text-red">*</span></label>
                                        <select class="form-control select2" id="id_user1d" name="id_user1d" required>
                                            <option value="">Selectionnez du nom & prénom</option>
                                            @foreach ($dataAllProfilVisa as $tabProfilVisa)
                                                @foreach(App\Models\InformationsPersonnelles::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabInformationsPersonnelle)
                                                    <option value="{{ $tabInformationsPersonnelle->user1d }}">{{ $tabInformationsPersonnelle->nom }} {{ $tabInformationsPersonnelle->prenom }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-primary btn-rounded" name="btnSend" value="search_by_name"><i class="fa fa-search"></i> {{ __('Rechercher')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-success text-white text-center">
                        PAR NUMERO PROFIL VISA
                    </div>
                    <div class="card-body row">
                        <form class="forms-sample" method="POST" action="{{url('profil-visa')}}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="id_profil_visa"><i class="fa fa-magic"></i> {{ __('Numéro Profil Visa')}}<span class="text-red">*</span></label>
                                        <select class="form-control select2" id="id_profil_visa" name="id_profil_visa" required>
                                            <option value="">Selectionnez du numéro de profil visa</option>
                                            @foreach ($dataAllProfilVisa as $tabProfilVisa)
                                                <option value="{{ $tabProfilVisa->id }}">{{ $tabProfilVisa->numero_profil_visa }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-success btn-rounded" name="btnSend" value="search_by_id_profil_visa"><i class="fa fa-search"></i> {{ __('Rechercher')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-info text-white text-center">
                        PAR INTERVAL DE DATE
                    </div>
                    <div class="card-body row">
                        <form class="forms-sample" method="POST" action="{{url('profil-visa')}}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date_debut"><i class="fa fa-calendar"></i> {{ __('Date Début')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" required />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date_fin"><i class="fa fa-calendar"></i> {{ __('Date Fin')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" />
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-info btn-rounded" name="btnSend" value="search_by_date"><i class="fa fa-search"></i> {{ __('Rechercher')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-warning text-white text-center">
                        PAR TYPE DE PROFIL
                    </div>
                    <div class="card-body row">
                        <form class="forms-sample" method="POST" action="{{url('profil-visa')}}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="type_profil_visa"><i class="fa fa-tag"></i> {{ __('Type Profil')}}<span class="text-red">*</span></label>
                                        <select class="form-control select2" id="type_profil_visa" name="type_profil_visa" required>
                                            <option value="">Selectionnez du type de profil visa</option>
                                            <option value="tourisme">TOURISME</option>
                                            <option value="travail">TRAVAIL</option>
                                            <option value="mineur">MINEUR</option>
                                            <option value="etude">ETUDE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group"><br/>
                                        <button type="submit" class="btn btn-warning btn-rounded" name="btnSend" value="search_by_type_profil"><i class="fa fa-search"></i> {{ __('Rechercher')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            $('#id_user1d').select2();
            $('#id_profil_visa').select2();
        });
    </script>
@endpush