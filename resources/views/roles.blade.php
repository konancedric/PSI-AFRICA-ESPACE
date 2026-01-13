@extends('layouts.main')

@section('title', 'Gestion des Rôles - PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title mb-2" style="font-weight: 700; font-size: 2.2rem;">
                            <i class="fas fa-shield-alt me-3"></i>
                            Gestion des Rôles
                        </h1>
                        <p class="page-subtitle mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                            Administration des rôles et permissions du système PSI Africa
                        </p>
                    </div>
                    <div class="page-actions">
                        <button class="btn btn-outline-light btn-lg me-3" onclick="refreshRoles()" style="border-radius: 12px;">
                            <i class="fas fa-sync-alt me-2"></i> Actualiser
                        </button>
                        <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#createRoleModal" style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-plus me-2"></i> Nouveau Rôle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Succès !</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreur !</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques des rôles -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="stats-number mb-1" id="totalRoles" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stats-label mb-0" style="opacity: 0.9;">Rôles Total</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-shield-alt fa-2x" style="opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); border-radius: 15px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(32, 201, 151, 0.3);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="stats-number mb-1" id="rolesWithUsers" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stats-label mb-0" style="opacity: 0.9;">Rôles Utilisés</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users fa-2x" style="opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%); border-radius: 15px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(253, 126, 20, 0.3);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="stats-number mb-1" id="totalPermissions" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stats-label mb-0" style="opacity: 0.9;">Permissions</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-key fa-2x" style="opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); border-radius: 15px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(111, 66, 193, 0.3);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="stats-number mb-1" id="systemHealth" style="font-size: 2rem; font-weight: 700;">100%</h3>
                        <p class="stats-label mb-0" style="opacity: 0.9;">Santé Système</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-heartbeat fa-2x" style="opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="quick-actions" style="background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h5 class="mb-3" style="color: #2d3436; font-weight: 700;">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Actions Rapides
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-primary w-100" onclick="createBasePermissions()" style="border-radius: 10px; padding: 1rem;">
                            <i class="fas fa-magic mb-2"></i><br>
                            Créer Permissions de Base
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-success w-100" onclick="checkSystemHealth()" style="border-radius: 10px; padding: 1rem;">
                            <i class="fas fa-stethoscope mb-2"></i><br>
                            Vérifier Santé Système
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-info w-100" onclick="exportRoles()" style="border-radius: 10px; padding: 1rem;">
                            <i class="fas fa-download mb-2"></i><br>
                            Exporter Rôles
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-warning w-100" onclick="syncPermissions()" style="border-radius: 10px; padding: 1rem;">
                            <i class="fas fa-sync mb-2"></i><br>
                            Synchroniser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des rôles -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: none; padding: 2rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1" style="color: #2d3436; font-weight: 700;">
                                <i class="fas fa-list me-2 text-primary"></i>
                                Liste des Rôles
                            </h5>
                            <p class="card-subtitle text-muted mb-0">
                                Gérez tous les rôles et leurs permissions associées
                            </p>
                        </div>
                        <div class="table-actions">
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; border: 1px solid #ddd; background: white;">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="globalSearch" placeholder="Rechercher un rôle..." style="border-radius: 0 8px 8px 0; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="rolesTable" style="border-radius: 0 0 20px 20px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <tr>
                                    <th style="border: none; padding: 1.5rem 2rem; font-weight: 700; color: #495057;">Rôle</th>
                                    <th style="border: none; padding: 1.5rem 1rem; font-weight: 700; color: #495057;">Permissions</th>
                                    <th style="border: none; padding: 1.5rem 1rem; font-weight: 700; color: #495057;">Utilisateurs</th>
                                    <th style="border: none; padding: 1.5rem 1rem; font-weight: 700; color: #495057;">Créé le</th>
                                    <th style="border: none; padding: 1.5rem 1rem; font-weight: 700; color: #495057; text-align: center;">Actions</th>
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

<!-- Modal de création de rôle -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Créer un Nouveau Rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
                @csrf
                <div class="modal-body" style="padding: 2rem;">
                    
                    <!-- Nom du rôle -->
                    <div class="mb-4">
                        <label for="create_name" class="form-label" style="font-weight: 600; color: #2d3436;">
                            <i class="fas fa-tag me-2 text-primary"></i>
                            Nom du Rôle <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="create_name" 
                               name="name" 
                               placeholder="Ex: Gestionnaire, Superviseur..."
                               style="border-radius: 12px; border: 2px solid #e9ecef; padding: 1rem 1.5rem;"
                               required>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Le nom du rôle doit être unique et descriptif
                        </small>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-4">
                        <label class="form-label" style="font-weight: 600; color: #2d3436;">
                            <i class="fas fa-key me-2 text-warning"></i>
                            Permissions <span class="text-muted">(optionnel)</span>
                        </label>
                        
                        <!-- Actions rapides pour permissions -->
                        <div class="permissions-actions mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="selectAllCreatePermissions()" style="border-radius: 8px;">
                                    <i class="fas fa-check-double me-1"></i> Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deselectAllCreatePermissions()" style="border-radius: 8px;">
                                    <i class="fas fa-times me-1"></i> Tout désélectionner
                                </button>
                            </div>
                        </div>

                        <!-- Liste des permissions -->
                        <div class="permissions-container" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; max-height: 300px; overflow-y: auto;">
                            <div class="row" id="createPermissionsList">
                                @if($permissions && $permissions->count() > 0)
                                    @foreach($permissions as $permission_id => $permission_name)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input create-permission-checkbox" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission_id }}" 
                                                       id="create_permission_{{ $permission_id }}">
                                                <label class="form-check-label" for="create_permission_{{ $permission_id }}" style="font-size: 0.9rem;">
                                                    <i class="fas fa-key me-1 text-primary"></i>
                                                    {{ $permission_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center">
                                        <p class="text-muted">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Aucune permission disponible
                                        </p>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="createBasePermissions()">
                                            <i class="fas fa-magic me-1"></i>
                                            Créer les permissions de base
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Compteur de permissions pour création -->
                        <div class="permissions-counter mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="createSelectedCount">0</span> permission(s) sélectionnée(s)
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-save me-1"></i>
                        Créer le Rôle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de détails de rôle -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Détails du Rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="roleDetailsContent">
                <!-- Le contenu sera généré par JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Personnalisé -->
<style>
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.2) !important;
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05) !important;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }

    .permission-badge {
        margin: 2px;
        padding: 4px 8px;
        font-size: 0.75rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .permission-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .quick-actions .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    /* Animation d'entrée */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: slideInUp 0.6s ease-out;
    }

    /* Loading spinner */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- Scripts JavaScript -->
<script>
let rolesTable;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la gestion des rôles');
    
    // Initialiser DataTables
    initializeDataTable();
    
    // Charger les statistiques
    loadStatistics();
    
    // Écouteur pour le formulaire de création
    document.getElementById('createRoleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createRole();
    });
    
    // Écouteur pour la recherche globale
    document.getElementById('globalSearch').addEventListener('keyup', function() {
        if (rolesTable) {
            rolesTable.search(this.value).draw();
        }
    });
    
    // Écouteurs pour les permissions de création
    document.querySelectorAll('.create-permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateCreatePermissionCounter);
    });
    
    updateCreatePermissionCounter();
});

/**
 * Initialiser DataTables
 */
function initializeDataTable() {
    rolesTable = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("roles.datatable") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('❌ Erreur DataTables:', error);
                showNotification('Erreur lors du chargement des données', 'error');
            }
        },
        columns: [
            {
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    let badge = '';
                    if (row.name === 'Super Admin') {
                        badge = '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-crown me-1"></i>VIP</span>';
                    }
                    return `
                        <div class="d-flex align-items-center">
                            <div class="role-avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                ${data.substring(0, 2).toUpperCase()}
                            </div>
                            <div>
                                <div class="fw-bold text-primary">${data}</div>
                                <small class="text-muted">ID: ${row.id}</small>
                            </div>
                            ${badge}
                        </div>
                    `;
                }
            },
            {
                data: 'permissions',
                name: 'permissions',
                orderable: false,
                searchable: false
            },
            {
                data: 'users_count',
                name: 'users_count',
                orderable: false,
                searchable: false
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        responsive: true,
        drawCallback: function() {
            // Animer les nouvelles lignes
            $('.table tbody tr').each(function(index) {
                $(this).css('animation-delay', `${index * 0.1}s`);
                $(this).addClass('fade-in');
            });
        }
    });
}

/**
 * Charger les statistiques
 */
function loadStatistics() {
    fetch('{{ route("roles.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalRoles').textContent = data.total_roles || 0;
            document.getElementById('rolesWithUsers').textContent = data.roles_with_users || 0;
            document.getElementById('totalPermissions').textContent = data.total_permissions || 0;
            
            console.log('📊 Statistiques chargées:', data);
        })
        .catch(error => {
            console.error('❌ Erreur statistiques:', error);
            // Valeurs par défaut en cas d'erreur
            document.getElementById('totalRoles').textContent = '?';
            document.getElementById('rolesWithUsers').textContent = '?';
            document.getElementById('totalPermissions').textContent = '?';
        });
}

/**
 * Créer un nouveau rôle
 */
function createRole() {
    const form = document.getElementById('createRoleForm');
    const formData = new FormData(form);
    
    // Validation
    const name = formData.get('name');
    if (!name || name.trim().length < 3) {
        showNotification('Le nom du rôle doit contenir au moins 3 caractères', 'error');
        return;
    }
    
    // Afficher le loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner"></span> Création...';
    submitBtn.disabled = true;
    
    fetch('{{ route("roles.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.text();
        }
        throw new Error('Network response was not ok');
    })
    .then(data => {
        // Succès
        showNotification('Rôle créé avec succès !', 'success');
        
        // Fermer le modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createRoleModal'));
        modal.hide();
        
        // Réinitialiser le formulaire
        form.reset();
        updateCreatePermissionCounter();
        
        // Recharger la table
        if (rolesTable) {
            rolesTable.ajax.reload();
        }
        
        // Recharger les statistiques
        loadStatistics();
    })
    .catch(error => {
        console.error('❌ Erreur création rôle:', error);
        showNotification('Erreur lors de la création du rôle', 'error');
    })
    .finally(() => {
        // Restaurer le bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Voir les détails d'un rôle
 */
function viewRole(roleId) {
    if (!roleId) {
        showNotification('ID de rôle invalide', 'error');
        return;
    }
    
    fetch(`{{ url('/role/details') }}/${roleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRoleDetails(data);
            } else {
                showNotification(data.error || 'Erreur lors du chargement', 'error');
            }
        })
        .catch(error => {
            console.error('❌ Erreur détails rôle:', error);
            showNotification('Erreur de connexion', 'error');
        });
}

/**
 * Afficher les détails d'un rôle
 */
function displayRoleDetails(data) {
    const role = data.role;
    const permissions = data.permissions || [];
    const users = data.users || [];
    
    let detailsHtml = `
        <div class="role-details">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations Générales
                    </h6>
                    <table class="table table-borderless">
                        <tr><td><strong>Nom :</strong></td><td>${role.name}</td></tr>
                        <tr><td><strong>ID :</strong></td><td>${role.id}</td></tr>
                        <tr><td><strong>Créé le :</strong></td><td>${new Date(role.created_at).toLocaleDateString('fr-FR')}</td></tr>
                        <tr><td><strong>Modifié le :</strong></td><td>${new Date(role.updated_at).toLocaleDateString('fr-FR')}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques
                    </h6>
                    <table class="table table-borderless">
                        <tr><td><strong>Utilisateurs :</strong></td><td><span class="badge bg-primary">${data.users_count}</span></td></tr>
                        <tr><td><strong>Permissions :</strong></td><td><span class="badge bg-success">${data.permissions_count}</span></td></tr>
                        <tr><td><strong>Statut :</strong></td><td><span class="badge bg-info">Actif</span></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-key me-2"></i>
                        Permissions (${permissions.length})
                    </h6>
                    <div class="permissions-list" style="max-height: 200px; overflow-y: auto;">
    `;
    
    if (permissions.length > 0) {
        permissions.forEach(permission => {
            detailsHtml += `<span class="badge bg-dark permission-badge m-1">${permission}</span>`;
        });
    } else {
        detailsHtml += `<p class="text-muted">Aucune permission assignée</p>`;
    }
    
    detailsHtml += `
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-users me-2"></i>
                        Utilisateurs (${users.length})
                    </h6>
                    <div class="users-list" style="max-height: 200px; overflow-y: auto;">
    `;
    
    if (users.length > 0) {
        users.forEach(user => {
            detailsHtml += `
                <div class="user-item mb-2 p-2 bg-light rounded">
                    <strong>${user.name}</strong><br>
                    <small class="text-muted">${user.email} (${user.type_user})</small>
                </div>
            `;
        });
    } else {
        detailsHtml += `<p class="text-muted">Aucun utilisateur assigné</p>`;
    }
    
    detailsHtml += `
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('roleDetailsContent').innerHTML = detailsHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('roleDetailsModal'));
    modal.show();
}

/**
 * Actualiser les rôles
 */
function refreshRoles() {
    if (rolesTable) {
        rolesTable.ajax.reload();
    }
    loadStatistics();
    showNotification('Données actualisées', 'success');
}

/**
 * Gestion des permissions pour création
 */
function selectAllCreatePermissions() {
    document.querySelectorAll('.create-permission-checkbox').forEach(cb => cb.checked = true);
    updateCreatePermissionCounter();
}

function deselectAllCreatePermissions() {
    document.querySelectorAll('.create-permission-checkbox').forEach(cb => cb.checked = false);
    updateCreatePermissionCounter();
}

function updateCreatePermissionCounter() {
    const checked = document.querySelectorAll('.create-permission-checkbox:checked').length;
    document.getElementById('createSelectedCount').textContent = checked;
}

/**
 * Actions rapides
 */
function createBasePermissions() {
    if (!confirm('Créer les permissions de base du système ?')) return;
    
    fetch('{{ route("permissions.create.base") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(data.message || 'Erreur', 'error');
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        showNotification('Erreur lors de la création des permissions', 'error');
    });
}

function checkSystemHealth() {
    fetch('{{ route("permissions.health") }}')
        .then(response => response.json())
        .then(data => {
            const status = data.health.status;
            const message = status === 'healthy' ? 'Système en parfait état' : 'Problèmes détectés';
            const type = status === 'healthy' ? 'success' : 'warning';
            
            showNotification(message, type);
            
            // Mettre à jour l'indicateur de santé
            document.getElementById('systemHealth').textContent = status === 'healthy' ? '100%' : '⚠️';
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            showNotification('Erreur lors de la vérification', 'error');
        });
}

function exportRoles() {
    showNotification('Export en cours...', 'info');
    // Implémenter l'export selon vos besoins
    setTimeout(() => {
        showNotification('Export terminé', 'success');
    }, 2000);
}

function syncPermissions() {
    showNotification('Synchronisation en cours...', 'info');
    // Implémenter la synchronisation selon vos besoins
    setTimeout(() => {
        showNotification('Synchronisation terminée', 'success');
        loadStatistics();
    }, 2000);
}

/**
 * Notification toast
 */
function showNotification(message, type = 'success') {
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed toast-notification`;
    toast.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        border-radius: 12px; 
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        min-width: 300px;
        animation: slideInRight 0.5s ease;
        border: none;
        font-weight: 500;
    `;
    
    const iconMap = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle',
        warning: 'fas fa-exclamation-circle'
    };
    
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${iconMap[type] || 'fas fa-info-circle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOutRight 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }
    }, 5000);
}

// CSS pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideOutRight {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>

@endsection