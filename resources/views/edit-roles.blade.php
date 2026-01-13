@extends('layouts.main')

@section('title', 'Modifier le Rôle - PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title mb-2" style="font-weight: 700; font-size: 2rem;">
                            <i class="fas fa-edit me-3"></i>
                            Modifier le Rôle
                        </h1>
                        <p class="page-subtitle mb-0" style="opacity: 0.9;">
                            Modification du rôle <strong>{{ $role->name }}</strong> et de ses permissions
                        </p>
                    </div>
                    <div class="page-actions">
                        <a href="{{ url('/roles') }}" class="btn btn-outline-light btn-lg" style="border-radius: 12px;">
                            <i class="fas fa-arrow-left me-2"></i> Retour
                        </a>
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                
                <!-- En-tête du formulaire -->
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: none; padding: 2rem;">
                    <div class="d-flex align-items-center">
                        <div class="role-icon me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shield-alt fa-2x text-white"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" style="color: #2d3436; font-weight: 700;">
                                Informations du Rôle
                            </h5>
                            <p class="card-subtitle text-muted mb-0">
                                Modifiez les informations et permissions du rôle
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Corps du formulaire -->
                <div class="card-body" style="padding: 2rem;">
                    <form action="{{ route('roles.update') }}" method="POST" id="editRoleForm">
                        @csrf
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        
                        <!-- Nom du rôle -->
                        <div class="mb-4">
                            <label for="name" class="form-label" style="font-weight: 600; color: #2d3436;">
                                <i class="fas fa-tag me-2 text-primary"></i>
                                Nom du Rôle <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $role->name) }}"
                                   placeholder="Entrez le nom du rôle"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 1rem 1.5rem; font-size: 1.1rem;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Le nom du rôle doit être unique et descriptif
                            </small>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 600; color: #2d3436;">
                                <i class="fas fa-key me-2 text-warning"></i>
                                Permissions Assignées
                            </label>
                            
                            <!-- Protection pour Super Admin -->
                            @if($role->name === 'Super Admin')
                                <div class="alert alert-info" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); color: white;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-crown fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1" style="font-weight: 700;">
                                                Rôle Super Administrateur
                                            </h6>
                                            <p class="mb-0">
                                                Ce rôle possède automatiquement toutes les permissions du système.
                                                Les permissions ne peuvent pas être modifiées.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Sélection des permissions -->
                                <div class="permissions-container" style="background: #f8f9fa; border-radius: 15px; padding: 1.5rem; border: 2px solid #e9ecef;">
                                    
                                    <!-- Actions rapides -->
                                    <div class="permissions-actions mb-3">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="selectAllPermissions()" style="border-radius: 8px;">
                                                <i class="fas fa-check-double me-1"></i> Tout sélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deselectAllPermissions()" style="border-radius: 8px;">
                                                <i class="fas fa-times me-1"></i> Tout désélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleSelection()" style="border-radius: 8px;">
                                                <i class="fas fa-exchange-alt me-1"></i> Inverser
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Recherche de permissions -->
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text" style="background: white; border-radius: 8px 0 0 8px; border: 1px solid #ddd;">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="permissionSearch" 
                                                   placeholder="Rechercher une permission..."
                                                   style="border-radius: 0 8px 8px 0; border: 1px solid #ddd;"
                                                   onkeyup="filterPermissions()">
                                        </div>
                                    </div>

                                    <!-- Liste des permissions -->
                                    <div class="permissions-grid" style="max-height: 400px; overflow-y: auto;">
                                        <div class="row">
                                            @if($permissions && $permissions->count() > 0)
                                                @foreach($permissions as $permission_id => $permission_name)
                                                    <div class="col-md-6 col-lg-4 mb-3 permission-item" data-permission="{{ strtolower($permission_name) }}">
                                                        <div class="form-check permission-check" style="padding: 1rem; background: white; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                            <input class="form-check-input permission-checkbox" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission_id }}" 
                                                                   id="permission_{{ $permission_id }}"
                                                                   @if(in_array($permission_id, $role_permission)) checked @endif
                                                                   style="margin-top: 0.3rem;">
                                                            <label class="form-check-label" for="permission_{{ $permission_id }}" style="cursor: pointer; font-weight: 500; color: #2d3436;">
                                                                <i class="fas fa-key me-2 text-primary"></i>
                                                                {{ $permission_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-12">
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                                        <h5 class="text-muted">Aucune permission disponible</h5>
                                                        <p class="text-muted">Veuillez créer des permissions d'abord.</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Compteur de permissions sélectionnées -->
                                    <div class="permissions-counter mt-3 p-2 bg-white rounded" style="border: 1px solid #dee2e6;">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <span id="selectedCount">{{ count($role_permission) }}</span> permission(s) sélectionnée(s)
                                            sur <span id="totalCount">{{ $permissions ? $permissions->count() : 0 }}</span>
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du rôle -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; border: 1px solid #e9ecef;">
                                        <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 1rem;">
                                            <i class="fas fa-info-circle me-2 text-info"></i>
                                            Informations
                                        </h6>
                                        <div class="info-item mb-2">
                                            <strong>ID :</strong> {{ $role->id }}
                                        </div>
                                        <div class="info-item mb-2">
                                            <strong>Créé le :</strong> {{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </div>
                                        <div class="info-item">
                                            <strong>Modifié le :</strong> {{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stats-card" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; border: 1px solid #e9ecef;">
                                        <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 1rem;">
                                            <i class="fas fa-chart-bar me-2 text-success"></i>
                                            Statistiques
                                        </h6>
                                        <div class="stat-item mb-2">
                                            <strong>Utilisateurs :</strong> 
                                            <span class="badge bg-primary">{{ $role->users ? $role->users->count() : 0 }}</span>
                                        </div>
                                        <div class="stat-item mb-2">
                                            <strong>Permissions :</strong> 
                                            <span class="badge bg-success">{{ count($role_permission) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <strong>Statut :</strong> 
                                            <span class="badge bg-info">Actif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center pt-3" style="border-top: 2px solid #e9ecef;">
                            <a href="{{ url('/roles') }}" class="btn btn-outline-secondary btn-lg" style="border-radius: 12px; padding: 0.75rem 2rem;">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info btn-lg" onclick="previewChanges()" style="border-radius: 12px; padding: 0.75rem 2rem;">
                                    <i class="fas fa-eye me-2"></i>
                                    Aperçu
                                </button>
                                
                                @if($role->name !== 'Super Admin')
                                    <button type="submit" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 0.75rem 2rem;">
                                        <i class="fas fa-save me-2"></i>
                                        Enregistrer les Modifications
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'aperçu -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        Aperçu des Modifications
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Le contenu sera généré par JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">
                        <i class="fas fa-save me-1"></i>
                        Confirmer et Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Personnalisé -->
<style>
    .permission-check:hover {
        border-color: #667eea !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }

    .permission-check input:checked + label {
        color: #667eea;
        font-weight: 600;
    }

    .permission-check input:checked ~ * {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .permission-item.hidden {
        display: none !important;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .info-card:hover, .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .btn-lg {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    /* Animation pour les permissions */
    .permission-item {
        animation: slideInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

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

    /* Délai d'animation pour chaque élément */
    .permission-item:nth-child(1) { animation-delay: 0.1s; }
    .permission-item:nth-child(2) { animation-delay: 0.2s; }
    .permission-item:nth-child(3) { animation-delay: 0.3s; }
    .permission-item:nth-child(4) { animation-delay: 0.4s; }
    .permission-item:nth-child(5) { animation-delay: 0.5s; }
    .permission-item:nth-child(6) { animation-delay: 0.6s; }
</style>

<!-- JavaScript pour la gestion des permissions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Initialisation de l\'éditeur de rôles');
    
    // Mise à jour du compteur de permissions
    updatePermissionCounter();
    
    // Écouteur pour les changements de permissions
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionCounter);
    });

    // Animation d'entrée pour les éléments de permission
    animatePermissions();
});

/**
 * Sélectionner toutes les permissions
 */
function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        if (!checkbox.closest('.permission-item').classList.contains('hidden')) {
            checkbox.checked = true;
        }
    });
    updatePermissionCounter();
    showNotification('Toutes les permissions ont été sélectionnées', 'success');
}

/**
 * Désélectionner toutes les permissions
 */
function deselectAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionCounter();
    showNotification('Toutes les permissions ont été désélectionnées', 'info');
}

/**
 * Inverser la sélection
 */
function toggleSelection() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        if (!checkbox.closest('.permission-item').classList.contains('hidden')) {
            checkbox.checked = !checkbox.checked;
        }
    });
    updatePermissionCounter();
    showNotification('Sélection inversée', 'info');
}

/**
 * Filtrer les permissions par recherche
 */
function filterPermissions() {
    const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
    const permissionItems = document.querySelectorAll('.permission-item');
    
    let visibleCount = 0;
    
    permissionItems.forEach(item => {
        const permissionName = item.getAttribute('data-permission');
        const label = item.querySelector('label').textContent.toLowerCase();
        
        if (permissionName.includes(searchTerm) || label.includes(searchTerm)) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });
    
    // Mettre à jour le compteur total
    document.getElementById('totalCount').textContent = visibleCount;
    updatePermissionCounter();
}

/**
 * Mettre à jour le compteur de permissions
 */
function updatePermissionCounter() {
    const allCheckboxes = document.querySelectorAll('.permission-checkbox');
    const visibleCheckboxes = Array.from(allCheckboxes).filter(checkbox => 
        !checkbox.closest('.permission-item').classList.contains('hidden')
    );
    const checkedCheckboxes = visibleCheckboxes.filter(checkbox => checkbox.checked);
    
    document.getElementById('selectedCount').textContent = checkedCheckboxes.length;
    
    // Mise à jour de la couleur du compteur
    const counter = document.querySelector('.permissions-counter');
    if (checkedCheckboxes.length === 0) {
        counter.style.borderColor = '#dc3545';
    } else if (checkedCheckboxes.length === visibleCheckboxes.length) {
        counter.style.borderColor = '#28a745';
    } else {
        counter.style.borderColor = '#ffc107';
    }
}

/**
 * Animer les permissions au chargement
 */
function animatePermissions() {
    const permissions = document.querySelectorAll('.permission-item');
    permissions.forEach((permission, index) => {
        permission.style.animationDelay = `${index * 0.1}s`;
        permission.classList.add('fade-in');
    });
}

/**
 * Aperçu des modifications
 */
function previewChanges() {
    const roleName = document.getElementById('name').value;
    const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
        .map(checkbox => checkbox.nextElementSibling.textContent.trim());
    
    let previewHtml = `
        <div class="preview-content">
            <h6 class="text-primary mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Résumé des Modifications
            </h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="preview-section mb-3">
                        <strong>Nom du Rôle :</strong>
                        <div class="mt-1">
                            <span class="badge bg-primary fs-6">${roleName}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="preview-section mb-3">
                        <strong>Nombre de Permissions :</strong>
                        <div class="mt-1">
                            <span class="badge bg-success fs-6">${selectedPermissions.length} permission(s)</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="preview-section">
                <strong>Permissions Sélectionnées :</strong>
                <div class="mt-2">
    `;
    
    if (selectedPermissions.length > 0) {
        selectedPermissions.forEach(permission => {
            previewHtml += `<span class="badge bg-dark m-1">${permission}</span>`;
        });
    } else {
        previewHtml += `<span class="text-muted">Aucune permission sélectionnée</span>`;
    }
    
    previewHtml += `
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

/**
 * Soumettre le formulaire
 */
function submitForm() {
    const form = document.getElementById('editRoleForm');
    
    // Validation basique
    const roleName = document.getElementById('name').value.trim();
    if (!roleName) {
        showNotification('Le nom du rôle est requis', 'error');
        return;
    }
    
    // Fermer le modal et soumettre
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
    modal.hide();
    
    showNotification('Enregistrement en cours...', 'info');
    form.submit();
}

/**
 * Notification toast
 */
function showNotification(message, type = 'success') {
    // Supprimer les notifications existantes
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
    
    // Auto-remove après 5 secondes
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOutRight 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }
    }, 5000);
}

// CSS pour les animations des notifications
const style = document.createElement('style');
style.textContent = `
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
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
document.head.appendChild(style);

// Validation en temps réel
document.getElementById('name').addEventListener('input', function() {
    const name = this.value.trim();
    const feedback = this.nextElementSibling;
    
    if (name.length < 3) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});
</script>

@endsection