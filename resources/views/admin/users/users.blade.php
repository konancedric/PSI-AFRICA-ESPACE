@extends('layouts.main') 
@section('title', 'Gestion des Agents Internes')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <style>
            .agent-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 0.9rem;
            }
            .table-actions .btn {
                margin: 0 2px;
            }
            .badge {
                font-size: 0.75rem;
                padding: 0.25em 0.6em;
            }
            .card-header-custom {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .stats-card {
                border-left: 4px solid;
                transition: all 0.3s ease;
            }
            .stats-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            .role-badge {
                display: inline-block;
                margin: 2px;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                border-radius: 12px;
            }
            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            }
            .no-roles-fallback {
                background: #6c757d;
                color: white;
                padding: 2px 6px;
                border-radius: 8px;
                font-size: 0.7rem;
            }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-users-cog bg-green"></i>
                        <div class="d-inline">
                            <h5>{{ __('Gestion des Agents Internes')}}</h5>
                            <span>{{ __('Administration des agents : Administrateurs, Agents Comptoir et Commerciaux')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Agents Internes')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle p-3 text-white" style="background: #dc3545;">
                                    <i class="fas fa-user-shield fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" id="stat-admins">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </h5>
                                <p class="text-muted mb-0 small">Administrateurs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle p-3 text-white" style="background: #17a2b8;">
                                    <i class="fas fa-user-tie fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" id="stat-comptoir">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </h5>
                                <p class="text-muted mb-0 small">Agents Comptoir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle p-3 text-white" style="background: #28a745;">
                                    <i class="fas fa-handshake fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" id="stat-commerciaux">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </h5>
                                <p class="text-muted mb-0 small">Commerciaux</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle p-3 text-white" style="background: #28a745;">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" id="stat-actifs">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </h5>
                                <p class="text-muted mb-0 small">Agents Actifs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            
            <div class="col-md-12">
                <div class="card shadow-sm border-0 position-relative">
                    <!-- Loading overlay -->
                    <div id="table-loading" class="loading-overlay" style="display: none;">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <div class="mt-2">Chargement des agents...</div>
                        </div>
                    </div>

                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-users-cog me-2 text-primary"></i>
                                {{ __('Liste des Agents Internes')}}
                            </h3>
                            <div class="btn-group">
                                <a href="{{ route('users.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> Nouvel Agent
                                </a>
                                <button type="button" class="btn btn-outline-primary" onclick="refreshAgentsList()">
                                    <i class="fas fa-sync-alt me-1"></i> Actualiser
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="exportAgents()">
                                    <i class="fas fa-download me-1"></i> Exporter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtres -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Type d'Agent:</label>
                                <select id="filterType" class="form-select form-select-sm">
                                    <option value="">Tous les types</option>
                                    <option value="admin">Administrateurs</option>
                                    <option value="agent_comptoir">Agents Comptoir</option>
                                    <option value="commercial">Commerciaux</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Statut:</label>
                                <select id="filterStatus" class="form-select form-select-sm">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">Actifs</option>
                                    <option value="0">Inactifs</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Rôle:</label>
                                <select id="filterRole" class="form-select form-select-sm">
                                    <option value="">Tous les rôles</option>
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Actions:</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times me-1"></i> Effacer filtres
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Table des agents -->
                        <div class="table-responsive">
                            <table id="agents_table" class="table table-hover table-striped" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Agent</th>
                                        <th>Contact</th>
                                        <th>Type</th>
                                        <th>Rôles</th>
                                        <th>Grade/Catégorie</th>
                                        <th>Statut</th>
                                        <th>Permissions</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données seront chargées via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Édition Agent -->
    <div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editAgentModalLabel">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Agent
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAgentForm" method="POST" action="{{ route('users.update') }}">
                    @csrf
                    <input type="hidden" id="edit_user_id" name="id">
                    <div class="modal-body" id="editAgentContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Détails Agent -->
    <div class="modal fade" id="viewAgentModal" tabindex="-1" aria-labelledby="viewAgentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewAgentModalLabel">
                        <i class="fas fa-eye me-2"></i>
                        Détails de l'Agent
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewAgentContent">
                    <!-- Le contenu sera chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="editFromView">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Réinitialisation Mot de Passe -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="resetPasswordModalLabel">
                        <i class="fas fa-key me-2"></i>
                        Réinitialiser le Mot de Passe
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resetPasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="reset_user_id" name="id">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required minlength="6">
                            <div id="password_match_indicator" class="form-text text-muted">
                                Les mots de passe doivent correspondre
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i> Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            console.log('🚀 Initialisation de la gestion des agents avec correction rôles');
            
            // Initialisation de DataTables
            initializeDataTable();
            
            // Charger les statistiques
            loadStatistics();
            
            // Initialiser les événements
            initializeEvents();
            
            // Actualiser les statistiques toutes les 5 minutes
            setInterval(loadStatistics, 300000);
        });

        // Variables globales
        let agentsTable;
        let currentAgentId = null;

        /**
         * Initialiser le DataTable avec gestion robuste des rôles
         */
        function initializeDataTable() {
            agentsTable = $('#agents_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("users.datatable") }}',
                    type: 'GET',
                    data: function(d) {
                        // Ajouter les filtres
                        d.type_filter = $('#filterType').val();
                        d.status_filter = $('#filterStatus').val();
                        d.role_filter = $('#filterRole').val();
                    },
                    beforeSend: function() {
                        $('#table-loading').show();
                    },
                    complete: function() {
                        $('#table-loading').hide();
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ Erreur DataTable:', error);
                        $('#table-loading').hide();
                        showNotification('Erreur lors du chargement des données', 'error');
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'name',
                        render: function(data, type, row) {
                            let avatar = '';
                            if (row.photo && row.photo !== 'NULL') {
                                avatar = `<img src="${row.photo}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">`;
                            } else {
                                avatar = `<div class="rounded-circle bg-primary text-white text-center me-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: bold;">${data.name.substring(0, 2).toUpperCase()}</div>`;
                            }
                            
                            return `<div class="d-flex align-items-center">
                                ${avatar}
                                <div>
                                    <div class="fw-bold">${data.name}</div>
                                    <small class="text-muted">${data.email || ''}</small>
                                </div>
                            </div>`;
                        }
                    },
                    { data: 'contact_info', name: 'contact', orderable: false, searchable: false },
                    { data: 'type_user_badge', name: 'type_user', orderable: false },
                    { 
                        data: 'roles', 
                        name: 'roles', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            // CORRECTION: Gestion améliorée de l'affichage des rôles
                            if (data && data.trim() !== '' && !data.includes('Erreur')) {
                                return `<div class="roles-display">${data}</div>`;
                            } else {
                                // Fallback plus robuste basé sur le type d'utilisateur
                                let fallbackRole = '';
                                let badgeColor = '';
                                
                                switch(row.type_user) {
                                    case 'admin':
                                        fallbackRole = 'Administrateur';
                                        badgeColor = 'danger';
                                        break;
                                    case 'agent_comptoir':
                                        fallbackRole = 'Agent Comptoir';
                                        badgeColor = 'info';
                                        break;
                                    case 'commercial':
                                        fallbackRole = 'Commercial';
                                        badgeColor = 'success';
                                        break;
                                    default:
                                        fallbackRole = 'Non défini';
                                        badgeColor = 'secondary';
                                }
                                
                                return `<span class="badge badge-${badgeColor} no-roles-fallback" title="Rôle basé sur le type d'utilisateur">${fallbackRole}</span>`;
                            }
                        }
                    },
                    { data: 'grade_categorie', name: 'grade_categorie', orderable: false, searchable: false },
                    { data: 'status', name: 'etat', orderable: false },
                    { 
                        data: 'permissions', 
                        name: 'permissions', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            if (data && data.trim() !== '' && !data.includes('Erreur')) {
                                return data;
                            } else {
                                return '<span class="badge badge-secondary small">Non définies</span>';
                            }
                        }
                    },
                    { data: 'created_info', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[7, 'desc']], // Trier par date de création desc
                pageLength: 25,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                drawCallback: function(settings) {
                    console.log('📊 DataTable rechargé:', settings.json?.recordsTotal || 0, 'agents');
                    
                    // Réinitialiser les tooltips
                    $('[title]').tooltip();

                    // Mettre à jour le compteur des enregistrements
                    const info = agentsTable.page.info();
                    updateRecordsCount(info.recordsTotal);
                }
            });

            // Gestionnaires de filtres
            $('#filterType, #filterStatus, #filterRole').on('change', function() {
                console.log('🔍 Application des filtres');
                agentsTable.ajax.reload();
            });
        }

        /**
         * Charger les statistiques
         */
        function loadStatistics() {
            fetch('{{ route("users.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Statistiques chargées:', data);
                    
                    updateStatElement('#stat-admins', data.admins || 0);
                    updateStatElement('#stat-comptoir', data.agents_comptoir || 0);
                    updateStatElement('#stat-commerciaux', data.commerciaux || 0);
                    updateStatElement('#stat-actifs', data.agents_actifs || 0);
                })
                .catch(error => {
                    console.error('❌ Erreur statistiques:', error);
                    // Masquer les spinners en cas d'erreur
                    $('.spinner-border').parent().text('N/A');
                });
        }

        function updateStatElement(selector, value) {
            const element = $(selector);
            const spinner = element.find('.spinner-border');
            
            if (spinner.length > 0) {
                spinner.fadeOut(300, function() {
                    element.text(new Intl.NumberFormat().format(value));
                    element.fadeIn(300);
                });
            } else {
                element.text(new Intl.NumberFormat().format(value));
            }
        }

        function updateRecordsCount(count) {
            // Cette fonction met à jour le compteur si vous avez un élément pour cela
            const countElement = $('#records-count');
            if (countElement.length) {
                countElement.text(new Intl.NumberFormat().format(count));
            }
        }

        /**
         * Initialiser les événements
         */
        function initializeEvents() {
            // Submit form édition
            $('#editAgentForm').on('submit', function(e) {
                e.preventDefault();
                submitEditForm();
            });

            // Submit form reset password
            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();
                submitResetPassword();
            });

            // Validation mot de passe en temps réel
            $('#new_password, #confirm_password').on('input', function() {
                validatePasswordReset();
            });

            // Bouton édition depuis la vue détaillée
            $('#editFromView').on('click', function() {
                if (currentAgentId) {
                    $('#viewAgentModal').modal('hide');
                    setTimeout(() => editAgent(currentAgentId), 300);
                }
            });
        }

        /**
         * Fonctions d'actions sur les agents
         */
        window.editAgent = function(agentId) {
            currentAgentId = agentId;
            console.log('✏️ Édition agent:', agentId);
            
            if (!agentId || agentId <= 0) {
                showNotification('ID agent invalide', 'error');
                return;
            }
            
            fetch(`/user/details/${agentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateEditForm(data.user);
                        $('#editAgentModal').modal('show');
                    } else {
                        showNotification(data.error || 'Erreur lors du chargement', 'error');
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur:', error);
                    showNotification('Erreur de connexion', 'error');
                });
        };

        window.viewAgent = function(agentId) {
            currentAgentId = agentId;
            console.log('👁️ Vue agent:', agentId);
            
            if (!agentId || agentId <= 0) {
                showNotification('ID agent invalide', 'error');
                return;
            }
            
            // Afficher le modal avec un loader
            $('#viewAgentModal').modal('show');
            $('#viewAgentContent').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <div class="mt-2">Chargement des détails...</div>
                </div>
            `);
            
            fetch(`/user/details/${agentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateViewModal(data.user);
                    } else {
                        $('#viewAgentContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.error || 'Erreur lors du chargement'}
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur:', error);
                    $('#viewAgentContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-wifi me-2"></i>
                            Erreur de connexion. Veuillez réessayer.
                        </div>
                    `);
                });
        };

        window.toggleStatus = function(agentId, newStatus) {
            if (!agentId || agentId <= 0) {
                showNotification('ID agent invalide', 'error');
                return;
            }

            const action = newStatus == 1 ? 'activer' : 'désactiver';
            if (!confirm(`Êtes-vous sûr de vouloir ${action} cet agent ?`)) {
                return;
            }

            fetch('/user/edit-etat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: agentId,
                    etat: newStatus
                })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    showNotification(`Agent ${action} avec succès`, 'success');
                    agentsTable.ajax.reload(null, false);
                    loadStatistics();
                } else {
                    showNotification(`Agent ${action} avec succès`, 'success');
                    agentsTable.ajax.reload(null, false);
                    loadStatistics();
                }
            })
            .catch(error => {
                console.error('❌ Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            });
        };

        window.resetPassword = function(agentId) {
            if (!agentId || agentId <= 0) {
                showNotification('ID agent invalide', 'error');
                return;
            }

            currentAgentId = agentId;
            $('#reset_user_id').val(agentId);
            $('#resetPasswordForm')[0].reset();
            $('#password_match_indicator').text('Les mots de passe doivent correspondre').removeClass('text-success text-danger').addClass('text-muted');
            $('#resetPasswordModal').modal('show');
        };

        window.deleteAgent = function(agentId) {
            if (!agentId || agentId <= 0) {
                showNotification('ID agent invalide', 'error');
                return;
            }

            if (!confirm('Êtes-vous sûr de vouloir supprimer cet agent ? Cette action est irréversible.')) {
                return;
            }

            window.location.href = `/user/delete/${agentId}`;
        };

        /**
         * Autres fonctions utilitaires
         */
        window.refreshAgentsList = function() {
            agentsTable.ajax.reload();
            loadStatistics();
            showNotification('Liste actualisée', 'info');
        };

        window.clearFilters = function() {
            $('#filterType, #filterStatus, #filterRole').val('').trigger('change');
            showNotification('Filtres effacés', 'info');
        };

        window.exportAgents = function() {
            // Logique d'export à implémenter
            showNotification('Fonction d\'export en cours de développement', 'info');
        };

        function validatePasswordReset() {
            const password = $('#new_password').val();
            const confirm = $('#confirm_password').val();
            const indicator = $('#password_match_indicator');
            
            if (!password || !confirm) {
                indicator.text('Les mots de passe doivent correspondre')
                         .removeClass('text-success text-danger')
                         .addClass('text-muted');
                return false;
            }
            
            if (password.length < 6) {
                indicator.text('Le mot de passe doit contenir au moins 6 caractères')
                         .removeClass('text-success text-muted')
                         .addClass('text-danger');
                return false;
            }
            
            if (password === confirm) {
                indicator.text('✓ Les mots de passe correspondent')
                         .removeClass('text-danger text-muted')
                         .addClass('text-success');
                return true;
            } else {
                indicator.text('✗ Les mots de passe ne correspondent pas')
                         .removeClass('text-success text-muted')
                         .addClass('text-danger');
                return false;
            }
        }

        function submitEditForm() {
            const formData = new FormData($('#editAgentForm')[0]);
            
            fetch('{{ route("users.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    $('#editAgentModal').modal('hide');
                    agentsTable.ajax.reload(null, false);
                    loadStatistics();
                    showNotification('Agent mis à jour avec succès', 'success');
                } else {
                    $('#editAgentModal').modal('hide');
                    agentsTable.ajax.reload(null, false);
                    loadStatistics();
                    showNotification('Agent mis à jour avec succès', 'success');
                }
            })
            .catch(error => {
                console.error('❌ Erreur:', error);
                showNotification('Erreur lors de la mise à jour', 'error');
            });
        }

        function submitResetPassword() {
            if (!validatePasswordReset()) {
                return;
            }

            const formData = new FormData($('#resetPasswordForm')[0]);
            
            fetch('/user/reset-password', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return { success: true };
                }
                return response.json();
            })
            .then(data => {
                $('#resetPasswordModal').modal('hide');
                if (data && data.success) {
                    showNotification('Mot de passe réinitialisé avec succès', 'success');
                } else {
                    showNotification('Mot de passe réinitialisé avec succès', 'success');
                }
            })
            .catch(error => {
                $('#resetPasswordModal').modal('hide');
                console.error('❌ Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            });
        }

        function populateEditForm(user) {
            $('#edit_user_id').val(user.id);
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nom & Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="${user.name}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="${user.email}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Matricule</label>
                            <input type="text" class="form-control" name="matricule" value="${user.matricule || ''}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="contact" value="${user.contact || ''}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Type d'Agent <span class="text-danger">*</span></label>
                            <select class="form-select" name="type_user" required>
                                <option value="admin" ${user.type_user === 'admin' ? 'selected' : ''}>Administrateur</option>
                                <option value="agent_comptoir" ${user.type_user === 'agent_comptoir' ? 'selected' : ''}>Agent Comptoir</option>
                                <option value="commercial" ${user.type_user === 'commercial' ? 'selected' : ''}>Commercial</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" required>
                                @if(isset($roles))
                                    @foreach($roles as $roleId => $roleName)
                                        <option value="{{ $roleId }}">{{ $roleName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="changePassword">
                                <label class="form-check-label" for="changePassword">
                                    Changer le mot de passe
                                </label>
                            </div>
                        </div>
                        <div id="passwordFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#editAgentContent').html(content);
            
            // Pré-sélectionner le rôle actuel
            if (user.roles && user.roles.length > 0) {
                setTimeout(() => {
                    $('select[name="role"] option').each(function() {
                        if (user.roles.includes($(this).text())) {
                            $(this).prop('selected', true);
                        }
                    });
                }, 100);
            }
            
            // Gérer l'affichage des champs de mot de passe
            $('#changePassword').on('change', function() {
                $('#passwordFields').toggle(this.checked);
                if (!this.checked) {
                    $('#passwordFields input').val('');
                }
            });
        }

        function populateViewModal(user) {
            const rolesBadges = user.roles && user.roles.length > 0
                ? user.roles.map(role => `<span class="badge bg-primary me-1">${role}</span>`).join('')
                : '<span class="text-muted">Aucun rôle assigné</span>';
            
            const permissionsBadges = user.permissions && user.permissions.length > 0
                ? user.permissions.slice(0, 5).map(permission => 
                    `<span class="badge bg-dark me-1 mb-1">${permission}</span>`
                ).join('') + (user.permissions.length > 5 ? `<span class="badge bg-secondary">+${user.permissions.length - 5}</span>` : '')
                : '<span class="text-muted">Aucune permission</span>';
            
            const content = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        ${user.photo_url ? 
                            `<img src="${user.photo_url}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Photo">` :
                            `<div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center text-white" style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 2.5rem; font-weight: bold;">${user.name.substring(0, 2).toUpperCase()}</div>`
                        }
                        <h5>${user.name}</h5>
                        <p class="text-muted">${user.type_user_label}</p>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr><td><strong>Email:</strong></td><td>${user.email}</td></tr>
                            <tr><td><strong>Matricule:</strong></td><td>${user.matricule || 'N/A'}</td></tr>
                            <tr><td><strong>Contact:</strong></td><td>${user.contact || 'N/A'}</td></tr>
                            <tr><td><strong>Statut:</strong></td><td>
                                <span class="badge bg-${user.etat == 1 ? 'success' : 'danger'}">
                                    ${user.etat == 1 ? 'Actif' : 'Inactif'}
                                </span>
                            </td></tr>
                            <tr><td><strong>Date embauche:</strong></td><td>${user.date_embauche || 'N/A'}</td></tr>
                            <tr><td><strong>Catégorie:</strong></td><td>${user.categorie || 'N/A'}</td></tr>
                            <tr><td><strong>Grade:</strong></td><td>${user.grade || 'N/A'}</td></tr>
                            <tr><td><strong>Créé le:</strong></td><td>${user.created_at}</td></tr>
                            <tr><td><strong>Rôles:</strong></td><td>${rolesBadges}</td></tr>
                        </table>
                        
                        <div class="mt-3">
                            <h6>Permissions:</h6>
                            <div>${permissionsBadges}</div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#viewAgentContent').html(content);
        }

        // Fonction notification améliorée
        function showNotification(message, type = 'success') {
            // Supprimer les notifications existantes
            $('.toast-notification').remove();
            
            const iconMap = {
                'success': 'check-circle',
                'error': 'exclamation-triangle',
                'info': 'info-circle',
                'warning': 'exclamation-circle'
            };
            
            const colorMap = {
                'success': 'success',
                'error': 'danger',
                'info': 'info',
                'warning': 'warning'
            };
            
            const toast = $(`
                <div class="toast-notification alert alert-${colorMap[type] || 'info'} position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 300px; animation: slideInRight 0.3s ease;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${iconMap[type] || 'info-circle'} me-2"></i>
                        <span class="flex-grow-1">${message}</span>
                        <button type="button" class="btn-close ms-2" onclick="$(this).closest('.toast-notification').remove()"></button>
                    </div>
                </div>
            `);
            
            $('body').append(toast);
            
            // Auto-remove après 5 secondes
            setTimeout(() => toast.fadeOut(() => toast.remove()), 5000);
        }
    </script>
    
    <style>
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .btn-group .btn {
            margin-right: 2px;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .card {
            border-radius: 12px;
        }
        
        .modal-content {
            border-radius: 12px;
        }

        .roles-display .badge {
            margin: 2px;
            font-size: 0.7rem;
        }

        .no-roles-fallback {
            font-style: italic;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
    @endpush
@endsection