<div class="card">
    <div class="card card-header bg-dark text-white text-center">DEMANDES EFFECTUÉES</div>
    <div class="card card-body">
        {{-- ✅ CORRECTION PRINCIPALE : Système de recherche UNIQUEMENT pour Agents Comptoir et Commerciaux --}}
        @auth
            @php
                $currentUser = auth()->user();
                $isAgent = $currentUser->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial']) || 
                          in_array($currentUser->type_user, ['admin', 'agent_comptoir', 'commercial']);
                $isOwner = false; // Sera défini dans la boucle pour chaque profil
                $isAssigned = false; // Sera défini dans la boucle pour chaque profil
                
                // ✅ CORRECTION : Recherche UNIQUEMENT pour agents comptoir et commerciaux
                $canSearch = $currentUser->hasAnyRole(['Agent Comptoir', 'Commercial']) || 
                           in_array($currentUser->type_user, ['agent_comptoir', 'commercial']);
                
                // ✅ NOUVEAU : Suppression multiple UNIQUEMENT pour Admin et Super Admin
                $canMassDelete = ($currentUser->hasRole(['Super Admin', 'Admin']) || $currentUser->type_user === 'admin') && 
                                $currentUser->can('delete_profil_visa');
                
                $canViewAll = $currentUser->can('view_profil_visa') || 
                            $currentUser->can('manage_profil_visa') || 
                            $isAgent; // Tous les agents peuvent voir tous les profils
            @endphp

            {{-- ✅ SYSTÈME DE RECHERCHE - UNIQUEMENT Agents Comptoir et Commerciaux --}}
            @if($canSearch)
                <div class="search-system mb-4" id="search-system">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-search"></i> Système de Recherche Avancée
                                <small class="badge badge-dark ml-2">Agents Comptoir & Commerciaux</small>
                                <button class="btn btn-sm btn-dark float-right" type="button" data-toggle="collapse" data-target="#searchForm" aria-expanded="false">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </h5>
                        </div>
                        <div class="collapse show" id="searchForm">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Accès Privilégié :</strong> Cette fonction de recherche avancée est réservée aux agents comptoir et commerciaux.
                                </div>
                                
                                <form method="POST" action="{{ route('profil.visa.index') }}" id="searchFormProfils">
                                    @csrf
                                    
                                    {{-- Onglets de recherche --}}
                                    <ul class="nav nav-pills mb-3" id="search-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="search-name-tab" data-toggle="pill" href="#search-name" role="tab">
                                                <i class="fas fa-user"></i> Par Client
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="search-id-tab" data-toggle="pill" href="#search-id" role="tab">
                                                <i class="fas fa-hashtag"></i> Par N° Profil
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="search-date-tab" data-toggle="pill" href="#search-date" role="tab">
                                                <i class="fas fa-calendar"></i> Par Date
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="search-type-tab" data-toggle="pill" href="#search-type" role="tab">
                                                <i class="fas fa-passport"></i> Par Type
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="search-status-tab" data-toggle="pill" href="#search-status" role="tab">
                                                <i class="fas fa-flag"></i> Par Statut
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="search-tabs-content">
                                        {{-- Recherche par nom de client --}}
                                        <div class="tab-pane fade show active" id="search-name" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="client_search">Nom du client :</label>
                                                        <select name="id_user1d" class="form-control select2" id="client_search" style="width: 100%;">
                                                            <option value="">-- Sélectionner un client --</option>
                                                            @if(isset($dataAllProfilVisa))
                                                                @foreach($dataAllProfilVisa->unique('user1d') as $profil)
                                                                    @if($profil->user)
                                                                        <option value="{{ $profil->user1d }}" 
                                                                            {{ request('id_user1d') == $profil->user1d ? 'selected' : '' }}>
                                                                            {{ $profil->user->name ?? 'Client inconnu' }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <button type="submit" name="btnSend" value="search_by_name" class="btn btn-warning btn-block">
                                                            <i class="fas fa-search"></i> Rechercher
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Recherche par ID de profil --}}
                                        <div class="tab-pane fade" id="search-id" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="profil_id_search">Numéro de profil :</label>
                                                        <select name="id_profil_visa" class="form-control select2" id="profil_id_search" style="width: 100%;">
                                                            <option value="">-- Sélectionner un profil --</option>
                                                            @if(isset($dataAllProfilVisa))
                                                                @foreach($dataAllProfilVisa as $profil)
                                                                    <option value="{{ $profil->id }}" 
                                                                        {{ request('id_profil_visa') == $profil->id ? 'selected' : '' }}>
                                                                        {{ $profil->numero_profil_visa ?? 'PSI'.date('Y').'-'.str_pad($profil->id, 6, '0', STR_PAD_LEFT) }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <button type="submit" name="btnSend" value="search_by_id_profil_visa" class="btn btn-warning btn-block">
                                                            <i class="fas fa-search"></i> Rechercher
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Recherche par date --}}
                                        <div class="tab-pane fade" id="search-date" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="date_debut">Date début :</label>
                                                        <input type="date" name="date_debut" class="form-control" id="date_debut" 
                                                               value="{{ request('date_debut') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="date_fin">Date fin :</label>
                                                        <input type="date" name="date_fin" class="form-control" id="date_fin" 
                                                               value="{{ request('date_fin') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <button type="submit" name="btnSend" value="search_by_date" class="btn btn-warning btn-block">
                                                            <i class="fas fa-search"></i> Rechercher
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Recherche par type de profil --}}
                                        <div class="tab-pane fade" id="search-type" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="type_profil_search">Type de profil :</label>
                                                        <select name="type_profil_visa" class="form-control" id="type_profil_search">
                                                            <option value="">-- Tous les types --</option>
                                                            <option value="tourisme" {{ request('type_profil_visa') == 'tourisme' ? 'selected' : '' }}>
                                                                VISA Tourisme
                                                            </option>
                                                            <option value="mineur" {{ request('type_profil_visa') == 'mineur' ? 'selected' : '' }}>
                                                                VISA Mineur
                                                            </option>
                                                            <option value="etude" {{ request('type_profil_visa') == 'etude' ? 'selected' : '' }}>
                                                                VISA Étudiant
                                                            </option>
                                                            <option value="travail" {{ request('type_profil_visa') == 'travail' ? 'selected' : '' }}>
                                                                VISA Travail
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <button type="submit" name="btnSend" value="search_by_type_profil" class="btn btn-warning btn-block">
                                                            <i class="fas fa-search"></i> Rechercher
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Recherche par statut --}}
                                        <div class="tab-pane fade" id="search-status" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="status_search">Statut :</label>
                                                        <select name="id_statuts_etat" class="form-control" id="status_search">
                                                            <option value="">-- Tous les statuts --</option>
                                                            @if(isset($dataStatutsEtat))
                                                                @foreach($dataStatutsEtat as $statut)
                                                                    <option value="{{ $statut->id }}" 
                                                                        {{ request('id_statuts_etat') == $statut->id ? 'selected' : '' }}>
                                                                        {{ $statut->libelle }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label><br>
                                                        <button type="submit" name="btnSend" value="search_by_status" class="btn btn-warning btn-block">
                                                            <i class="fas fa-search"></i> Rechercher
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Boutons d'action globaux --}}
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <hr>
                                            <div class="text-center">
                                                <button type="button" class="btn btn-secondary" onclick="resetSearchForm()">
                                                    <i class="fas fa-undo"></i> Réinitialiser
                                                </button>
                                                @if($currentUser->can('export_profil_visa'))
                                                    <button type="submit" name="btnSend" value="export_search" class="btn btn-success">
                                                        <i class="fas fa-file-excel"></i> Exporter résultats
                                                    </button>
                                                @endif
                                                <a href="{{ route('profil.visa.index') }}" class="btn btn-info">
                                                    <i class="fas fa-list"></i> Voir tout
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ NOUVEAU SYSTÈME DE SUPPRESSION MULTIPLE CORRIGÉ - ADMIN UNIQUEMENT --}}
            @if($canMassDelete)
                <div class="mass-delete-system mb-4" id="mass-delete-system">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trash-alt"></i> Suppression Multiple 
                                <small class="badge badge-warning ml-2">Admin Uniquement</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Attention :</strong> Cette fonction permet de supprimer plusieurs profils visa en une seule opération. Cette action est irréversible !
                            </div>
                            
                            {{-- ✅ FORMULAIRE CORRIGÉ avec gestion d'erreur et validation --}}
                            <form id="massDeleteForm" method="POST" action="{{ route('profil.visa.mass.delete') }}">
                                @csrf
                                <input type="hidden" name="selected_ids" id="selected_ids" value="">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="mass_deletion_reason">Raison de la suppression (obligatoire) :</label>
                                            <textarea name="mass_deletion_reason" id="mass_deletion_reason" class="form-control" rows="3" 
                                                placeholder="Veuillez indiquer la raison de cette suppression multiple..." required></textarea>
                                            <div class="invalid-feedback">
                                                La raison de suppression est obligatoire (minimum 5 caractères).
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Actions :</label><br>
                                            <button type="button" id="selectAllBtn" class="btn btn-info btn-sm btn-block mb-2">
                                                <i class="fas fa-check-square"></i> Tout sélectionner
                                            </button>
                                            <button type="button" id="deselectAllBtn" class="btn btn-secondary btn-sm btn-block mb-2">
                                                <i class="fas fa-square"></i> Tout désélectionner
                                            </button>
                                            <button type="button" id="massDeleteBtn" class="btn btn-danger btn-sm btn-block" disabled>
                                                <i class="fas fa-trash-alt"></i> Supprimer les sélectionnés
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="selected_count_display" class="mt-2 text-info">
                                    <i class="fas fa-info-circle"></i> <span id="selected_count">0</span> profil(s) sélectionné(s)
                                    <div id="selected_list" class="mt-2" style="display: none;">
                                        <strong>Profils sélectionnés :</strong>
                                        <div id="selected_profils_display" class="border p-2 mt-1 bg-light"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        {{-- ✅ Tableau principal des profils avec sélection multiple CORRIGÉE --}}
        <div class="table-responsive">
            <table id="data_table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        {{-- ✅ NOUVEAU : Colonne checkbox pour Admin seulement --}}
                        @if($canMassDelete)
                            <th width="3%" class="text-center">
                                <div class="form-check">
                                    <input type="checkbox" id="master_checkbox" class="form-check-input" title="Sélectionner/Désélectionner tout">
                                    <label for="master_checkbox" class="form-check-label sr-only">Sélectionner tout</label>
                                </div>
                            </th>
                        @endif
                        <th>{{ __('Date Add')}}</th>
                        <th>{{ __('N° D.')}}</th>
                        <th>{{ __('Type Profile')}}</th>
                        <th>{{ __('Etape')}}</th>
                        <th>{{ __('Info Cand.')}}</th>
                        <th>{{ __('Statuts')}}</th>
                        <th>{{ __('Update Add')}}</th>
                        <th width="30%">{{ __('Action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataProfilVisa as $tabProfilVisa)
                        @php
                            $currentUser = auth()->user();
                            $isAgent = $currentUser->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial']) || 
                                      in_array($currentUser->type_user, ['admin', 'agent_comptoir', 'commercial']);
                            $isOwner = $tabProfilVisa->user1d == $currentUser->id;
                            $isAssigned = isset($tabProfilVisa->assigned_agent_id) && $tabProfilVisa->assigned_agent_id == $currentUser->id;
                            
                            // ✅ CORRECTION : Permissions d'ajout de messages plus flexibles
                            $canAddMessage = $currentUser->can('add_message_profil_visa') || 
                                           $isOwner || 
                                           $isAssigned || 
                                           $isAgent; // Tous les agents peuvent ajouter des messages
                            
                            // ✅ Permissions de consultation - tous peuvent voir leurs profils
                            $canView = $isOwner || $isAgent || $currentUser->can('view_profil_visa');
                            
                            // ✅ Permissions de gestion
                            $canManage = $currentUser->can('manage_profil_visa') || 
                                       $currentUser->can('edit_profil_visa') || 
                                       ($isAgent && ($isAssigned || $currentUser->can('edit_profil_visa_status')));
                        @endphp
                        
                        <tr data-profil-id="{{ $tabProfilVisa->id }}" class="profil-row">
                            {{-- ✅ NOUVEAU : Checkbox de sélection multiple pour Admin CORRIGÉE --}}
                            @if($canMassDelete)
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="profil_checkbox form-check-input" 
                                               value="{{ $tabProfilVisa->id }}" 
                                               data-numero="{{ $tabProfilVisa->numero_profil_visa }}"
                                               data-type="{{ $tabProfilVisa->type_profil_visa }}"
                                               id="checkbox_{{ $tabProfilVisa->id }}">
                                        <label for="checkbox_{{ $tabProfilVisa->id }}" class="form-check-label sr-only">
                                            Sélectionner {{ $tabProfilVisa->numero_profil_visa }}
                                        </label>
                                    </div>
                                </td>
                            @endif
                            
                            <td>{{ $tabProfilVisa->created_at }}</td>
                            <td>
                                @if($tabProfilVisa->etat == 1)
                                    <span class="badge badge-success badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                                @elseif($tabProfilVisa->etat == 0)
                                    <span class="badge badge-danger badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                                @else
                                    <span class="badge badge-warning badge-pill"> {{ $tabProfilVisa->numero_profil_visa }}</span>
                                @endif
                            </td>
                            <td>
                                @if(is_null($tabProfilVisa->type_profil_visa) || $tabProfilVisa->type_profil_visa === '')
                                    <span class="badge badge-warning badge-pill text-dark" style="background-color: #f0ad4e;">
                                        <i class="fas fa-clock"></i> Profil en attente
                                    </span>
                                    @php($bgColor="warning")
                                @elseif($tabProfilVisa->type_profil_visa == "tourisme")
                                    <span class="badge badge-primary badge-pill text-white"> VISA Tourisme</span>
                                    @php($bgColor="primary")
                                @elseif($tabProfilVisa->type_profil_visa == "mineur")
                                    <span class="badge badge-warning badge-pill text-white"> VISA Mineur</span>
                                    @php($bgColor="warning")
                                @elseif($tabProfilVisa->type_profil_visa == "etude")
                                    <span class="badge badge-info badge-pill text-white"> VISA Etude</span>
                                    @php($bgColor="info")
                                @elseif($tabProfilVisa->type_profil_visa == "travail")
                                    <span class="badge badge-success badge-pill text-white"> VISA Travail</span>
                                    @php($bgColor="success")
                                @elseif($tabProfilVisa->type_profil_visa == "affaires")
                                    <span class="badge badge-dark badge-pill text-white"> VISA Affaires</span>
                                    @php($bgColor="dark")
                                @elseif($tabProfilVisa->type_profil_visa == "famille")
                                    <span class="badge badge-purple badge-pill text-white"> VISA Famille</span>
                                    @php($bgColor="purple")
                                @elseif($tabProfilVisa->type_profil_visa == "transit")
                                    <span class="badge badge-secondary badge-pill text-white"> VISA Transit</span>
                                    @php($bgColor="secondary")
                                @else
                                    <span class="badge badge-dark badge-pill text-white">Autre</span>
                                    @php($bgColor="dark")
                                @endif
                            </td>
                            <td>{{ $tabProfilVisa->etape }}</td>
                            <td>
                                {{-- Affichage sécurisé des informations client --}}
                                @if($canView)
                                    @foreach(App\Models\InformationsPersonnelles::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabInformationsPersonnelle)
                                      {{ $tabInformationsPersonnelle->nom }} {{ $tabInformationsPersonnelle->prenom }}
                                    @endforeach
                                    <br/>
                                    @foreach(App\Models\CoordonneesPersonnelles::where('id_profil_visa', $tabProfilVisa->id)->get() as $tabCoordonneesPersonnelle)
                                        {{ $tabCoordonneesPersonnelle->email }} <br/>
                                        Contact : {{ $tabCoordonneesPersonnelle->contact }} <br/>
                                        WhatsApp : {{ $tabCoordonneesPersonnelle->num_whatsapp }}
                                    @endforeach
                                @else
                                    <span class="text-muted">Informations masquées</span>
                                @endif
                            </td>
                            <td>
                                @foreach(App\Models\StatutsEtat::where('id', $tabProfilVisa->id_statuts_etat)->get() as $tabStatutsEtat)
                                    <span class="badge badge-pill" style="background-color:{{$tabStatutsEtat->bg_color}}">{{ $tabStatutsEtat->libelle }}</span>
                                @endforeach
                            </td>
                            <td>{{ $tabProfilVisa->updated_at }}</td>
                            <td width="30%">
                                <div class="table-actions">
                                    {{-- ✅ BOUTONS DE CONSULTATION - Selon permissions --}}
                                    @if($canView)
                                        @if($tabProfilVisa->type_profil_visa == "mineur")
                                            @include('admin.profil-visa.view-profil-visa-mineur')
                                        @else
                                            @include('admin.profil-visa.view-profil-visa')
                                        @endif
                                    @endif
                                    
                                    {{-- ✅ DOCUMENTS - Selon permissions --}}
                                    @if($canView)
                                        @can('view_profil_visa_documents')
                                            @include('admin.profil-visa.view-documents')
                                        @endcan
                                    @endif
                                    
                                    {{-- ✅ MESSAGES - CORRECTION : Accès élargi pour agents et propriétaires --}}
                                    @if($canAddMessage)
                                        @include('admin.profil-visa.form-add-message-profil-visa')
                                    @endif
                                    
                                    {{-- ✅ ACTIONS DE GESTION - Selon permissions strictes --}}
                                    @if($canManage)
                                        @can('edit_profil_visa_status')
                                            @include('admin.profil-visa.form-add-statuts-etat')
                                        @endcan
                                    @endif
                                    
                                    {{-- ⚠️ SUPPRESSION INDIVIDUELLE - SEULEMENT POUR ADMIN ET SUPER ADMIN --}}
                                    @if(auth()->user()->hasRole(['Super Admin', 'Admin']) || auth()->user()->can('delete_profil_visa'))
                                        @include('admin.profil-visa.form-delete-profil-visa')
                                    @endif
                                    
                                    {{-- ✅ AUTRES ACTIONS SELON PERMISSIONS --}}
                                    @include('admin.profil-visa.check-update-profil-visa')
                                    @include('admin.profil-visa.view-message-profil-visa')

                                    {{-- ✅ INDICATEUR VISUEL DU TYPE D'ACCÈS --}}
                                    @if($isOwner && !$isAgent)
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-user-circle"></i> Votre profil
                                        </small>
                                    @elseif($isAssigned)
                                        <small class="text-info d-block mt-1">
                                            <i class="fas fa-user-check"></i> Assigné à vous
                                        </small>
                                    @elseif($isAgent)
                                        <small class="text-success d-block mt-1">
                                            <i class="fas fa-shield-alt"></i> Accès agent
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card card-footer">
        {{ $dataProfilVisa->links() }}
    </div>
</div>

{{-- ✅ NOUVEAU : Modal de confirmation pour suppression multiple CORRIGÉ --}}
<div class="modal fade" id="massDeleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="massDeleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="massDeleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmation de Suppression Multiple
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>ATTENTION :</strong> Vous êtes sur le point de supprimer définitivement <span id="confirm_count"></span> profil(s) visa.
                    Cette action est <strong>irréversible</strong> !
                </div>
                
                <h6>Profils qui seront supprimés :</h6>
                <div id="profils_to_delete_list" class="mb-3" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <!-- Liste des profils sera générée par JavaScript -->
                </div>
                
                <div class="form-group">
                    <label for="confirm_deletion_reason"><strong>Raison de la suppression :</strong></label>
                    <div id="confirm_deletion_reason" class="form-control-plaintext border p-2 bg-light">
                        <!-- Raison sera affichée ici -->
                    </div>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="confirm_mass_deletion" required>
                    <label class="form-check-label" for="confirm_mass_deletion">
                        <strong>Je confirme que je souhaite supprimer ces profils de manière définitive</strong>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmMassDeleteBtn" disabled>
                    <i class="fas fa-trash-alt"></i> Supprimer Définitivement
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ✅ SCRIPTS JAVASCRIPT CORRIGÉS POUR LA SUPPRESSION MULTIPLE --}}
@push('script')
<script>
// ✅ VARIABLES GLOBALES CORRIGÉES
let selectedProfilsForDelete = []; // Tableau des profils sélectionnés
let massDeleteEnabled = @json($canMassDelete ?? false); // Autorisation suppression multiple

$(document).ready(function() {
    console.log('🟢 Initialisation liste profils visa - Suppression multiple autorisée:', massDeleteEnabled);
    
    // ✅ CORRECTION : Initialisation améliorée des Select2 pour agents comptoir/commercial
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            placeholder: "Choisir...",
            allowClear: true,
            width: '100%'
        });
    }
    
    // ✅ NOUVEAU : Initialisation système de sélection multiple CORRIGÉ
    if (massDeleteEnabled) {
        initializeMassDeleteSystemCorrected();
    }
    
    // Initialiser le compteur de caractères pour messages
    $('#modal_message').on('input', function() {
        const length = $(this).val().length;
        $('#message_counter').text(length);
        
        if (length > 900) {
            $('#message_counter').addClass('text-warning');
        } else {
            $('#message_counter').removeClass('text-warning');
        }
    });
    
    // ✅ Amélioration : Gestion des onglets de recherche
    $('#search-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        
        // Reset form when changing tabs
        $('#searchFormProfils')[0].reset();
        
        // Re-initialize select2 after tab change
        setTimeout(function() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    placeholder: "Choisir...",
                    allowClear: true,
                    width: '100%'
                });
            }
        }, 100);
    });
});

// ✅ SYSTÈME DE SÉLECTION MULTIPLE ENTIÈREMENT CORRIGÉ
function initializeMassDeleteSystemCorrected() {
    console.log('🔧 Initialisation système suppression multiple corrigé');
    
    try {
        // ✅ CORRECTION 1 : Checkbox maître avec gestion d'erreur
        $('#master_checkbox').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            console.log('Master checkbox changé:', isChecked);
            
            $('.profil_checkbox').each(function() {
                $(this).prop('checked', isChecked);
                
                // Déclencher l'événement change pour chaque checkbox
                $(this).trigger('change');
            });
        });
        
        // ✅ CORRECTION 2 : Gestion des checkboxes individuelles avec débouncing
        let changeTimeout;
        $('.profil_checkbox').off('change').on('change', function() {
            clearTimeout(changeTimeout);
            changeTimeout = setTimeout(() => {
                updateSelectedProfilsCorrected();
                updateMassDeleteUICorrected();
            }, 100);
        });
        
        // ✅ CORRECTION 3 : Boutons de sélection rapide
        $('#selectAllBtn').off('click').on('click', function() {
            console.log('👆 Sélectionner tout cliqué');
            $('.profil_checkbox').prop('checked', true);
            $('#master_checkbox').prop('checked', true);
            updateSelectedProfilsCorrected();
            updateMassDeleteUICorrected();
        });
        
        $('#deselectAllBtn').off('click').on('click', function() {
            console.log('👆 Désélectionner tout cliqué');
            $('.profil_checkbox').prop('checked', false);
            $('#master_checkbox').prop('checked', false);
            updateSelectedProfilsCorrected();
            updateMassDeleteUICorrected();
        });
        
        // ✅ CORRECTION 4 : Bouton de suppression avec validation
        $('#massDeleteBtn').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🗑️ Bouton suppression multiple cliqué');
            validateAndShowConfirmation();
        });
        
        // ✅ CORRECTION 5 : Validation temps réel de la raison
        $('#mass_deletion_reason').off('input').on('input', function() {
            const reason = $(this).val().trim();
            const hasSelection = selectedProfilsForDelete.length > 0;
            const isValid = reason.length >= 5;
            
            $('#massDeleteBtn').prop('disabled', !(reason && hasSelection && isValid));
            
            // Feedback visuel
            if (reason.length > 0 && reason.length < 5) {
                $(this).addClass('is-invalid');
                $('.invalid-feedback').show();
            } else {
                $(this).removeClass('is-invalid');
                $('.invalid-feedback').hide();
            }
        });
        
        // ✅ CORRECTION 6 : Modal de confirmation
        $('#confirm_mass_deletion').off('change').on('change', function() {
            $('#confirmMassDeleteBtn').prop('disabled', !$(this).is(':checked'));
        });
        
        $('#confirmMassDeleteBtn').off('click').on('click', function() {
            executeMassDeleteCorrected();
        });
        
        console.log('✅ Système suppression multiple initialisé avec succès');
        
    } catch (error) {
        console.error('❌ Erreur initialisation système suppression multiple:', error);
        showNotificationCorrected('error', 'Erreur lors de l\'initialisation du système de suppression multiple');
    }
}

// ✅ FONCTION CORRIGÉE : Mettre à jour la liste des profils sélectionnés
function updateSelectedProfilsCorrected() {
    try {
        selectedProfilsForDelete = [];
        
        $('.profil_checkbox:checked').each(function() {
            const profilId = parseInt($(this).val());
            const numeroProfilVisa = $(this).data('numero') || 'N/A';
            const typeProfilVisa = $(this).data('type') || 'N/A';
            
            if (profilId && profilId > 0) {
                selectedProfilsForDelete.push({
                    id: profilId,
                    numero: numeroProfilVisa,
                    type: typeProfilVisa
                });
            }
        });
        
        // Mettre à jour le champ caché
        const idsString = selectedProfilsForDelete.map(p => p.id).join(',');
        $('#selected_ids').val(idsString);
        
        console.log('📊 Profils sélectionnés mis à jour:', {
            count: selectedProfilsForDelete.length,
            ids: idsString,
            profils: selectedProfilsForDelete
        });
        
    } catch (error) {
        console.error('❌ Erreur mise à jour profils sélectionnés:', error);
        selectedProfilsForDelete = [];
        $('#selected_ids').val('');
    }
}

// ✅ FONCTION CORRIGÉE : Mettre à jour l'interface de suppression multiple
function updateMassDeleteUICorrected() {
    try {
        const count = selectedProfilsForDelete.length;
        const reason = $('#mass_deletion_reason').val().trim();
        const isReasonValid = reason.length >= 5;
        
        // Mettre à jour le compteur
        $('#selected_count').text(count);
        
        // Activer/désactiver le bouton
        $('#massDeleteBtn').prop('disabled', count === 0 || !isReasonValid);
        
        // Afficher/masquer la liste des profils sélectionnés
        if (count > 0) {
            let profilsHtml = selectedProfilsForDelete.map(profil => 
                `<span class="badge badge-info mr-1">${profil.numero}</span>`
            ).join(' ');
            
            $('#selected_profils_display').html(profilsHtml);
            $('#selected_list').show();
        } else {
            $('#selected_list').hide();
        }
        
        // Mettre à jour le checkbox maître
        const totalCheckboxes = $('.profil_checkbox').length;
        const checkedCheckboxes = $('.profil_checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#master_checkbox').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#master_checkbox').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#master_checkbox').prop('indeterminate', true);
        }
        
        console.log('🎨 Interface mise à jour:', {
            count: count,
            reasonValid: isReasonValid,
            buttonEnabled: !$('#massDeleteBtn').prop('disabled')
        });
        
    } catch (error) {
        console.error('❌ Erreur mise à jour interface:', error);
    }
}

// ✅ FONCTION CORRIGÉE : Valider et afficher la confirmation
function validateAndShowConfirmation() {
    try {
        const reason = $('#mass_deletion_reason').val().trim();
        
        // Validation côté client
        if (selectedProfilsForDelete.length === 0) {
            showNotificationCorrected('error', 'Veuillez sélectionner au moins un profil à supprimer.');
            return false;
        }
        
        if (reason.length < 5) {
            showNotificationCorrected('error', 'Veuillez indiquer la raison de la suppression (minimum 5 caractères).');
            $('#mass_deletion_reason').focus().addClass('is-invalid');
            return false;
        }
        
        console.log('✅ Validation réussie, affichage modal de confirmation');
        
        // Remplir le modal de confirmation
        $('#confirm_count').text(selectedProfilsForDelete.length);
        $('#confirm_deletion_reason').text(reason);
        
        // Générer la liste des profils
        let profilsList = '<div class="row">';
        selectedProfilsForDelete.forEach(function(profil, index) {
            if (index > 0 && index % 3 === 0) {
                profilsList += '</div><div class="row">';
            }
            profilsList += `
                <div class="col-md-4 mb-2">
                    <div class="card card-sm border-danger">
                        <div class="card-body p-2">
                            <small>
                                <span class="badge badge-danger">ID: ${profil.id}</span><br>
                                <strong>${profil.numero}</strong><br>
                                <span class="text-muted">${profil.type}</span>
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });
        profilsList += '</div>';
        
        $('#profils_to_delete_list').html(profilsList);
        
        // Réinitialiser le checkbox de confirmation
        $('#confirm_mass_deletion').prop('checked', false);
        $('#confirmMassDeleteBtn').prop('disabled', true);
        
        // Afficher le modal
        $('#massDeleteConfirmModal').modal('show');
        
        return true;
        
    } catch (error) {
        console.error('❌ Erreur validation confirmation:', error);
        showNotificationCorrected('error', 'Erreur lors de la validation. Veuillez réessayer.');
        return false;
    }
}

// ✅ FONCTION CORRIGÉE : Exécuter la suppression multiple
function executeMassDeleteCorrected() {
    try {
        console.log('🗑️ Début exécution suppression multiple');
        
        if (!$('#confirm_mass_deletion').is(':checked')) {
            showNotificationCorrected('error', 'Veuillez confirmer la suppression en cochant la case.');
            return;
        }
        
        if (selectedProfilsForDelete.length === 0) {
            showNotificationCorrected('error', 'Aucun profil sélectionné.');
            return;
        }
        
        const reason = $('#mass_deletion_reason').val().trim();
        if (reason.length < 5) {
            showNotificationCorrected('error', 'Raison de suppression trop courte.');
            return;
        }
        
        // Désactiver le bouton et afficher un spinner
        const $btn = $('#confirmMassDeleteBtn');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Suppression en cours...');
        
        // Préparer les données
        const formData = {
            _token: '{{ csrf_token() }}',
            selected_ids: selectedProfilsForDelete.map(p => p.id).join(','),
            mass_deletion_reason: reason
        };
        
        console.log('📤 Envoi des données:', formData);
        
        // Envoyer la requête AJAX
        $.ajax({
            url: '{{ route("profil.visa.mass.delete") }}',
            method: 'POST',
            data: formData,
            timeout: 30000, // 30 secondes timeout
            success: function(response) {
                console.log('✅ Suppression réussie:', response);
                
                $('#massDeleteConfirmModal').modal('hide');
                
                if (response.success) {
                    showNotificationCorrected('success', response.message || 'Suppression multiple effectuée avec succès');
                    
                    // ✅ AMÉLIORATION : Supprimer immédiatement les lignes des profils supprimés
                    if (response.deleted_profils && Array.isArray(response.deleted_profils)) {
                        response.deleted_profils.forEach(function(numeroProfilVisa) {
                            // Chercher et supprimer la ligne correspondante
                            $('tr[data-profil-id]').each(function() {
                                const $row = $(this);
                                const numeroInRow = $row.find('td:contains("' + numeroProfilVisa + '")').length;
                                if (numeroInRow > 0) {
                                    $row.fadeOut('fast', function() {
                                        $(this).remove();
                                    });
                                }
                            });
                        });
                    }
                    
                    // ✅ AMÉLIORATION : Mettre à jour les compteurs
                    const deletedCount = response.deleted_count || selectedProfilsForDelete.length;
                    updateDisplayCounters(deletedCount);
                    
                    // Réinitialiser les sélections
                    selectedProfilsForDelete = [];
                    updateMassDeleteUICorrected();
                    
                    // Recharger la page après un délai plus court
                    setTimeout(function() {
                        showNotificationCorrected('info', 'Actualisation des données...');
                        window.location.reload();
                    }, 1000);  // 1 seconde seulement
                    
                } else {
                    showNotificationCorrected('error', response.error || 'Erreur lors de la suppression');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Erreur suppression:', {xhr: xhr, status: status, error: error});
                
                let errorMessage = 'Erreur lors de la suppression multiple';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 403) {
                    errorMessage = 'Accès refusé : permissions insuffisantes';
                } else if (xhr.status === 422) {
                    errorMessage = 'Données invalides';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erreur serveur';
                } else if (status === 'timeout') {
                    errorMessage = 'Délai d\'attente dépassé';
                }
                
                $('#massDeleteConfirmModal').modal('hide');
                showNotificationCorrected('error', errorMessage);
                $btn.prop('disabled', false).html(originalText);
            },
            complete: function() {
                console.log('🏁 Suppression multiple terminée');
            }
        });
        
    } catch (error) {
        console.error('❌ Erreur critique suppression multiple:', error);
        showNotificationCorrected('error', 'Erreur critique lors de la suppression');
        
        $('#confirmMassDeleteBtn').prop('disabled', false).html('<i class="fas fa-trash-alt"></i> Supprimer Définitivement');
    }
}

// ✅ FONCTION : Réinitialiser le formulaire de recherche
function resetSearchForm() {
    try {
        $('#searchFormProfils')[0].reset();
        
        // Reset Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').val(null).trigger('change');
        }
        
        showNotificationCorrected('info', 'Formulaire de recherche réinitialisé');
        
        // Redirect to show all profiles
        setTimeout(function() {
            window.location.href = '{{ route("profil.visa.index") }}';
        }, 1000);
    } catch (error) {
        console.error('Erreur reset form:', error);
        window.location.href = '{{ route("profil.visa.index") }}';
    }
}

// ✅ FONCTION CORRIGÉE : Afficher des notifications toast
function showNotificationCorrected(type, message, callback) {
    try {
        let alertClass = type === 'success' ? 'alert-success' : 
                       type === 'error' ? 'alert-danger' : 
                       type === 'warning' ? 'alert-warning' : 'alert-info';
        
        let iconClass = type === 'success' ? 'fa-check-circle' :
                       type === 'error' ? 'fa-exclamation-circle' :
                       type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show notification-toast" role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
                <i class="fas ${iconClass} me-2"></i>${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-hide après 7 secondes pour les erreurs, 5 pour les autres
        const hideDelay = type === 'error' ? 7000 : 5000;
        setTimeout(function() {
            notification.fadeOut('slow', function() {
                $(this).remove();
            });
        }, hideDelay);
        
        // Callback si fourni
        if (callback && typeof callback === 'function') {
            notification.on('click', callback);
        }
    } catch (error) {
        console.error('Erreur notification:', error);
        // Fallback simple
        alert(message);
    }
}

// ✅ NETTOYAGE : Fermeture des modals
$('#massDeleteConfirmModal').on('hidden.bs.modal', function() {
    $('#confirm_mass_deletion').prop('checked', false);
    $('#confirmMassDeleteBtn').prop('disabled', true).html('<i class="fas fa-trash-alt"></i> Supprimer Définitivement');
});

// ✅ Initialisation : DataTable avec protection erreur
$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined' && $('#data_table').length) {
        try {
            $('#data_table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
                },
                "responsive": true,
                "pageLength": 25,
                "order": [[ massDeleteEnabled ? 1 : 0, "desc" ]], // ✅ CORRIGÉ : Ajuster l'index selon la présence de checkbox
                "columnDefs": [
                    { "orderable": false, "targets": massDeleteEnabled ? [0, -1] : [-1] }, // Pas de tri sur checkbox et actions
                ],
                "destroy": true,
                "processing": true,
                "deferRender": true,
                "initComplete": function(settings, json) {
                    console.log('✅ DataTable initialisé avec succès');
                    
                    // Réinitialiser le système de sélection multiple après DataTable
                    if (massDeleteEnabled) {
                        setTimeout(() => {
                            initializeMassDeleteSystemCorrected();
                        }, 500);
                    }
                }
            });
        } catch (error) {
            console.error('Erreur DataTable:', error);
            // Continuer sans DataTable si erreur
            if (massDeleteEnabled) {
                initializeMassDeleteSystemCorrected();
            }
        }
    } else if (massDeleteEnabled) {
        // Si pas de DataTable, initialiser quand même le système
        initializeMassDeleteSystemCorrected();
    }
});

// Logs : Actions utilisateur pour audit
$('.btn').on('click', function() {
    const action = $(this).attr('onclick') || $(this).attr('title') || 'action';
    console.log(`Action utilisateur: ${action} par {{ auth()->user()->name ?? 'Inconnu' }}`);
});

console.log('✅ Liste profils visa - JavaScript entièrement initialisé');
</script>
@endpush

{{-- ✅ STYLES CSS POUR LES MESSAGES, INDICATEURS ET SUPPRESSION MULTIPLE --}}
@push('style')
<style>
/* ✅ Styles pour le système de recherche AGENT COMPTOIR/COMMERCIAL */
.search-system {
    animation: slideInDown 0.5s ease-out;
}

.card.border-warning .card-header.bg-warning {
    border: 2px solid #ffc107;
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

/* ✅ NOUVEAU : Styles pour le système de suppression multiple ADMIN */
.mass-delete-system {
    animation: slideInDown 0.5s ease-out;
}

.card.border-danger .card-header.bg-danger {
    border: 2px solid #dc3545;
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

/* ✅ CORRIGÉ : Styles des checkboxes avec meilleure accessibilité */
.profil_checkbox, #master_checkbox {
    transform: scale(1.2);
    cursor: pointer;
    margin: 0 auto;
    display: block;
}

.form-check {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 1.5rem;
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-check-input:focus {
    border-color: #dc3545;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Animation pour les checkboxes sélectionnées */
.profil_checkbox:checked {
    animation: checkboxPulse 0.3s ease-in-out;
}

@keyframes checkboxPulse {
    0% { transform: scale(1.2); }
    50% { transform: scale(1.4); }
    100% { transform: scale(1.2); }
}

/* Indicateur visuel des lignes sélectionnées */
.profil-row:has(.profil_checkbox:checked) {
    background-color: rgba(220, 53, 69, 0.05) !important;
    border-left: 3px solid #dc3545;
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.1);
    transition: all 0.3s ease;
}

/* ✅ CORRIGÉ : Styles pour les compteurs de sélection */
#selected_count_display {
    padding: 15px;
    border-radius: 8px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    margin-top: 15px;
}

#selected_count {
    font-weight: bold;
    font-size: 1.2em;
    color: #dc3545;
}

#selected_list {
    margin-top: 10px;
    padding: 10px;
    background-color: #fff;
    border-radius: 5px;
    border: 1px solid #dc3545;
}

#selected_profils_display .badge {
    margin: 2px;
    font-size: 0.9em;
}

/* ✅ CORRIGÉ : Styles pour le modal de confirmation */
.modal-header.bg-danger {
    border-bottom: 2px solid #c82333;
}

#profils_to_delete_list {
    background-color: #f8f9fa;
    max-height: 250px;
    overflow-y: auto;
    border-radius: 8px;
}

#profils_to_delete_list .card-sm .card-body {
    padding: 8px;
}

#profils_to_delete_list .badge {
    margin-right: 5px;
}

/* ✅ CORRIGÉ : Validation des champs */
.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}

/* ✅ Styles existants conservés et améliorés */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.nav-pills .nav-link {
    margin-right: 5px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: rgba(255, 193, 7, 0.1);
    transform: translateY(-1px);
}

.nav-pills .nav-link.active {
    background-color: #ffc107;
    color: #212529;
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}

/* Select2 customization */
.select2-container .select2-selection--single {
    height: 38px;
    border-color: #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    color: #495057;
}

/* ✅ CORRIGÉ : Indicateurs visuels pour les permissions */
.table-actions .btn-danger {
    border: 2px solid #dc3545;
    position: relative;
}

.table-actions .btn-danger::before {
    content: "🔒";
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 10px;
    background: #fff;
    border-radius: 50%;
    width: 15px;
    height: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ✅ CORRIGÉ : Indicateurs de statut utilisateur */
.table-actions .text-muted {
    border-left: 3px solid #6c757d;
    padding-left: 5px;
}

.table-actions .text-info {
    border-left: 3px solid #17a2b8;
    padding-left: 5px;
}

.table-actions .text-success {
    border-left: 3px solid #28a745;
    padding-left: 5px;
}

/* ✅ CORRIGÉ : Animation pour les nouvelles notifications */
.notification-toast {
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    border-radius: 8px;
    border: none;
    animation: slideInRight 0.5s ease-out;
    max-width: 450px;
    word-wrap: break-word;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* ✅ CORRIGÉ : Responsive design amélioré */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .table-actions {
        text-align: center;
    }
    
    .table-actions .btn {
        margin: 2px;
        display: inline-block;
    }
    
    .nav-pills {
        flex-wrap: wrap;
    }
    
    .nav-pills .nav-link {
        margin-bottom: 5px;
        font-size: 0.85rem;
    }
    
    .search-system .card-body,
    .mass-delete-system .card-body {
        padding: 1rem;
    }
    
    /* Ajustements pour les checkboxes sur mobile */
    .profil_checkbox, #master_checkbox {
        transform: scale(1.5);
    }
    
    .table th, .table td {
        padding: 0.5rem 0.3rem;
    }
    
    /* Ajustement notification mobile */
    .notification-toast {
        max-width: 90vw;
        right: 5px !important;
        left: 5px !important;
    }
}

/* ✅ CORRIGÉ : Amélioration du DataTable */
.dataTables_wrapper {
    margin-top: 1rem;
}

.dataTables_filter input {
    border-radius: 20px;
    padding: 5px 15px;
}

.dataTables_length select {
    border-radius: 5px;
}

.table.dataTable thead th {
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    font-weight: 600;
    vertical-align: middle;
}

.table.dataTable tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* ✅ CORRIGÉ : Amélioration des cartes */
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.card-header {
    border-radius: 10px 10px 0 0;
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

/* ✅ CORRIGÉ : Styles spéciaux pour les alertes de permissions */
.alert-info {
    border-left: 4px solid #17a2b8;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

.alert-danger {
    border-left: 4px solid #dc3545;
}

/* ✅ CORRIGÉ : Styles pour les badges de rôle */
.badge-dark {
    background-color: #343a40;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-purple {
    background-color: #6f42c1;
    color: white;
}

/* ✅ CORRIGÉ : Animation pour les systèmes activés */
.search-system.show, .mass-delete-system.show {
    animation: bounceInDown 0.7s ease-out;
}

@keyframes bounceInDown {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    60% {
        opacity: 1;
        transform: translateY(10px);
    }
    80% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0);
    }
}

/* ✅ CORRIGÉ : Styles pour les boutons d'action spécialisés */
.table-actions .btn {
    margin: 1px;
    min-width: 35px;
    transition: all 0.2s ease;
}

.table-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ✅ CORRIGÉ : Loading states pour les boutons */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn .fa-spinner {
    animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
@endpush

{{-- ✅ BADGE UTILISATEUR AVEC RÔLE ET PERMISSIONS MESSAGES --}}
@auth
    <div class="user-role-badge 
        @if(auth()->user()->hasRole(['Super Admin', 'Admin'])) user-role-admin
        @elseif(auth()->user()->hasRole('Agent Comptoir')) user-role-agent  
        @elseif(auth()->user()->hasRole('Commercial')) user-role-commercial
        @else user-role-public
        @endif" style="position: fixed; top: 10px; left: 10px; z-index: 1000; background: rgba(0,0,0,0.8); color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
        {{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }}
        @if(auth()->user()->can('delete_profil_visa'))
            🔓
        @else
            🔒
        @endif
        @if(auth()->user()->can('add_message_profil_visa') || 
            (!auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial'])))
            📧
        @endif
        {{-- ✅ NOUVEAU : Indicateur de recherche --}}
        @if(auth()->user()->hasAnyRole(['Agent Comptoir', 'Commercial']) || 
            in_array(auth()->user()->type_user, ['agent_comptoir', 'commercial']))
            🔍
        @endif
        {{-- ✅ NOUVEAU : Indicateur de suppression multiple --}}
        @if(auth()->user()->hasRole(['Super Admin', 'Admin']))
            🗑️
        @endif
    </div>
@endauth