@extends('layouts.main')
@section('title', 'Créer un Nouvel Agent')
@section('content')
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .form-control:focus, .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            .card {
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            }
            .card-header {
                border-radius: 12px 12px 0 0 !important;
            }
            .form-group {
                margin-bottom: 1.5rem;
            }
            .section-title {
                color: #495057;
                font-weight: 600;
                font-size: 1.1rem;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #e9ecef;
            }
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
            .preview-photo {
                width: 150px;
                height: 150px;
                object-fit: cover;
                border-radius: 8px;
                border: 2px solid #dee2e6;
            }
            .permissions-display {
                max-height: 300px;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 1rem;
                background-color: #f8f9fa;
                min-height: 120px;
                transition: all 0.3s ease;
            }
            .permission-badge {
                font-size: 0.75rem;
                margin: 2px;
                padding: 0.4rem 0.8rem;
                border-radius: 12px;
                transition: all 0.3s ease;
                cursor: default;
            }
            .permission-badge:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .permissions-loading {
                text-align: center;
                padding: 2rem;
                color: #6c757d;
            }
            .permissions-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            .super-admin-badge {
                animation: subtlePulse 3s infinite;
            }
            @keyframes subtlePulse {
                0%, 100% { box-shadow: 0 0 5px rgba(255, 193, 7, 0.3); }
                50% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.5); }
            }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Créer un Nouvel Agent')}}</h5>
                            <span>{{ __('Ajouter un agent interne au système')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('users') }}">{{ __('Agents')}}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ __('Créer')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        
        <div class="row">
            @include('include.message')
            
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            {{ __('Formulaire de Création d\'Agent') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{ route('users.store') }}" id="createUserForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Informations personnelles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="section-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informations Personnelles
                                    </h5>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label fw-bold">
                                            <i class="fas fa-user me-1"></i>
                                            {{ __('Nom & Prénom')}} <span class="text-danger">*</span>
                                        </label>
                                        <input id="name" type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name') }}" 
                                               required placeholder="Nom complet de l'agent">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email" class="form-label fw-bold">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ __('Email')}} <span class="text-danger">*</span>
                                        </label>
                                        <input id="email" type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" value="{{ old('email') }}" 
                                               required placeholder="email@exemple.com">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="contact" class="form-label fw-bold">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ __('Contact')}} <span class="text-danger">*</span>
                                        </label>
                                        <input id="contact" type="text" 
                                               class="form-control @error('contact') is-invalid @enderror" 
                                               name="contact" value="{{ old('contact') }}"
                                               required placeholder="+225 XX XX XX XX XX">
                                        @error('contact')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="matricule" class="form-label fw-bold">
                                            <i class="fas fa-id-card me-1"></i>
                                            {{ __('Matricule')}}
                                        </label>
                                        <input id="matricule" type="text" 
                                               class="form-control @error('matricule') is-invalid @enderror" 
                                               name="matricule" value="{{ old('matricule') }}"
                                               placeholder="Laissez vide pour génération automatique">
                                        <div class="form-text">
                                            <small class="text-muted">Laissez vide pour génération automatique selon le type d'agent</small>
                                        </div>
                                        @error('matricule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="form-label fw-bold">
                                            <i class="fas fa-key me-1"></i>
                                            {{ __('Mot de Passe')}} <span class="text-danger">*</span>
                                        </label>
                                        <input id="password" type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" required minlength="6"
                                               placeholder="Minimum 6 caractères">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="password-confirm" class="form-label fw-bold">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ __('Confirmer le Mot de Passe')}} <span class="text-danger">*</span>
                                        </label>
                                        <input id="password-confirm" type="password" 
                                               class="form-control" name="password_confirmation" 
                                               required minlength="6"
                                               placeholder="Confirmez le mot de passe">
                                        <div id="password-match-indicator" class="form-text text-muted">
                                            Les mots de passe doivent correspondre
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <!-- Type d'agent et rôle -->
                                    <div class="form-group">
                                        <label for="type_user" class="form-label fw-bold">
                                            <i class="fas fa-user-tag me-1"></i>
                                            {{ __('Type d\'Agent')}} <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2 @error('type_user') is-invalid @enderror" 
                                                id="type_user" name="type_user" required>
                                            <option value="">Sélectionner le type d'agent</option>
                                            <option value="admin" {{ old('type_user') == 'admin' ? 'selected' : '' }}>
                                                Administrateur
                                            </option>
                                            <option value="agent_comptoir" {{ old('type_user') == 'agent_comptoir' ? 'selected' : '' }}>
                                                Agent Comptoir
                                            </option>
                                            <option value="commercial" {{ old('type_user') == 'commercial' ? 'selected' : '' }}>
                                                Commercial
                                            </option>
                                        </select>
                                        @error('type_user')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="role" class="form-label fw-bold">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            {{ __('Rôle Système')}} <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2 @error('role') is-invalid @enderror" 
                                                id="role" name="role" required>
                                            <option value="">Sélectionner le rôle</option>
                                            {{-- ✅ CORRECTION: Gestion flexible des rôles --}}
                                            @if(isset($roles) && $roles->count() > 0)
                                                @foreach($roles as $role)
                                                    {{-- Vérifier si c'est un objet Role --}}
                                                    @if(is_object($role) && isset($role->id))
                                                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @else
                                                {{-- Options par défaut si aucun rôle trouvé --}}
                                                <option value="1">Super Admin</option>
                                                <option value="2">Admin</option>
                                                <option value="3">Agent Comptoir</option>
                                                <option value="4">Commercial</option>
                                            @endif
                                        </select>
                                        @error('role')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- ✅ AFFICHAGE AMÉLIORÉ DES PERMISSIONS -->
                                    <div class="form-group">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-key me-1"></i>
                                            {{ __('Permissions du Rôle')}}
                                        </label>
                                        <div id="permission" class="permissions-display">
                                            <div class="permissions-loading">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <span>Sélectionnez un rôle pour voir les permissions</span>
                                            </div>
                                        </div>
                                        <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                        <div class="form-text mt-2">
                                            <small class="text-muted">
                                                Les permissions s'affichent automatiquement selon le rôle sélectionné
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Informations professionnelles -->
                                    @if(isset($dataCategories) && $dataCategories->count() > 0)
                                    <div class="form-group">
                                        <label for="id_categorie" class="form-label fw-bold">
                                            <i class="fas fa-tags me-1"></i>
                                            {{ __('Catégorie')}}
                                        </label>
                                        <select class="form-control select2" id="id_categorie" name="id_categorie">
                                            <option value="">Sélectionner la catégorie</option>
                                            @foreach ($dataCategories as $categorie)
                                                <option value="{{ $categorie->id }}" {{ old('id_categorie') == $categorie->id ? 'selected' : '' }}>
                                                    {{ $categorie->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    
                                    @if(isset($dataGrades) && $dataGrades->count() > 0)
                                    <div class="form-group">
                                        <label for="id_grade" class="form-label fw-bold">
                                            <i class="fas fa-award me-1"></i>
                                            {{ __('Grade')}}
                                        </label>
                                        <select class="form-control select2" id="id_grade" name="id_grade">
                                            <option value="">Sélectionner le grade</option>
                                            @foreach ($dataGrades as $grade)
                                                <option value="{{ $grade->id }}" {{ old('id_grade') == $grade->id ? 'selected' : '' }}>
                                                    {{ $grade->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <!-- Upload photo -->
                                    <div class="form-group">
                                        <label for="photo_user" class="form-label fw-bold">
                                            <i class="fas fa-camera me-1"></i>
                                            {{ __('Photo de Profil')}}
                                        </label>
                                        <input type="file" 
                                               class="form-control @error('photo_user') is-invalid @enderror" 
                                               id="photo_user" name="photo_user" accept="image/*">
                                        <div class="form-text">
                                            <small>Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</small>
                                        </div>
                                        
                                        <!-- Prévisualisation de la photo -->
                                        <div id="photo-preview" class="mt-3" style="display: none;">
                                            <img id="preview-image" src="" alt="Prévisualisation" class="preview-photo">
                                        </div>
                                        
                                        @error('photo_user')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-md-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            {{ __('Retour à la Liste')}}
                                        </a>
                                        
                                        <div>
                                            <button type="reset" class="btn btn-outline-warning btn-lg me-2" id="resetBtn">
                                                <i class="fas fa-undo me-2"></i>
                                                {{ __('Réinitialiser')}}
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                                <i class="fas fa-save me-2"></i>
                                                <span id="submitText">{{ __('Créer l\'Agent')}}</span>
                                                <span id="submitSpinner" class="loading-spinner d-none"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('script') 
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        
        <script>
            $(document).ready(function() {
                console.log('🔧 Initialisation création agent - Version corrigée complète');
                
                // Initialiser Select2 avec gestion d'erreur
                try {
                    $('.select2').select2({
                        theme: 'bootstrap-5',
                        width: '100%'
                    });
                } catch (e) {
                    console.warn('Select2 non disponible, utilisation des selects standards');
                }
                
                // Validation temps réel des mots de passe
                $('#password, #password-confirm').on('input', function() {
                    validatePasswords();
                });
                
                // Synchronisation automatique type_user avec role
                $('#type_user').on('change', function() {
                    syncUserTypeWithRole();
                });

                // ✅ GESTIONNAIRE PRINCIPAL DES PERMISSIONS
                $('#role').on('change', function() {
                    const roleId = $(this).val();
                    console.log('🔄 Changement de rôle:', roleId);
                    
                    if (roleId && roleId !== '') {
                        loadRolePermissions(roleId);
                    } else {
                        resetPermissionsDisplay();
                    }
                });

                // Prévisualisation de la photo
                $('#photo_user').on('change', function() {
                    previewPhoto(this);
                });

                // Validation du formulaire avant soumission
                $('#createUserForm').on('submit', function(e) {
                    console.log('📋 Soumission du formulaire de création');
                    
                    if (!validateForm()) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Afficher le spinner de chargement
                    $('#submitBtn').prop('disabled', true);
                    $('#submitText').addClass('d-none');
                    $('#submitSpinner').removeClass('d-none');
                    
                    // Timeout de sécurité
                    setTimeout(function() {
                        $('#submitBtn').prop('disabled', false);
                        $('#submitText').removeClass('d-none');
                        $('#submitSpinner').addClass('d-none');
                    }, 10000);
                });

                // Reset form handler
                $('#resetBtn').on('click', function() {
                    $('#photo-preview').hide();
                    resetPermissionsDisplay();
                    $('#password-match-indicator').text('Les mots de passe doivent correspondre')
                                                   .removeClass('text-success text-danger')
                                                   .addClass('text-muted');
                });

                // Charger les permissions au chargement de la page si un rôle est déjà sélectionné
                const initialRoleId = $('#role').val();
                if (initialRoleId && initialRoleId !== '') {
                    console.log('🎯 Chargement initial pour rôle:', initialRoleId);
                    setTimeout(() => {
                        loadRolePermissions(initialRoleId);
                    }, 500);
                }
            });

            /**
             * ✅ FONCTION PRINCIPALE CORRIGÉE : Charger les permissions d'un rôle
             */
            function loadRolePermissions(roleId) {
                console.log('🔋 Chargement des permissions pour le rôle:', roleId);
                
                if (!roleId || roleId === '') {
                    resetPermissionsDisplay();
                    return;
                }
                
                const permissionContainer = $('#permission');
                if (permissionContainer.length === 0) {
                    console.error('❌ Container #permission non trouvé');
                    return;
                }
                
                // Afficher un indicateur de chargement
                permissionContainer.html(`
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <span class="text-muted">Chargement des permissions...</span>
                    </div>
                `);
                
                // Obtenir le token CSRF
                const token = getCSRFToken();
                
                // Essayer plusieurs URLs
                const urls = [
                    '/get-role-permissions-badge',
                    '/get-role-permissions',
                    '/role/' + roleId + '/permissions',
                    '/api/permissions/role/' + roleId
                ];
                
                tryPermissionRequest(urls, 0, roleId, token);
            }

            function tryPermissionRequest(urls, index, roleId, token) {
                if (index >= urls.length) {
                    // Toutes les URLs ont échoué, afficher les permissions par défaut
                    console.log('⚠️ Toutes les URLs ont échoué, affichage des permissions par défaut');
                    showDefaultPermissions(roleId);
                    return;
                }
                
                const url = urls[index];
                console.log(`🔄 Tentative ${index + 1}/${urls.length}: ${url}`);
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: { 
                        role_id: roleId,
                        id: roleId,
                        _token: token 
                    },
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    timeout: 8000,
                    success: function(response) {
                        console.log('✅ Succès avec URL:', url, response);
                        handlePermissionResponse(response, roleId);
                    },
                    error: function(xhr, status, error) {
                        console.warn(`⚠️ Échec URL ${url}:`, error);
                        // Essayer la prochaine URL après un petit délai
                        setTimeout(() => {
                            tryPermissionRequest(urls, index + 1, roleId, token);
                        }, 300);
                    }
                });
            }

            function handlePermissionResponse(response, roleId) {
                const permissionContainer = $('#permission');
                
                try {
                    if (response && response.success && response.badges) {
                        permissionContainer.html(response.badges);
                        animatePermissionBadges();
                    } else if (response && response.badges) {
                        permissionContainer.html(response.badges);
                        animatePermissionBadges();
                    } else if (response && typeof response === 'string' && response.trim() !== '') {
                        permissionContainer.html(response);
                        animatePermissionBadges();
                    } else {
                        console.log('🔄 Réponse vide ou invalide, utilisation des permissions par défaut');
                        showDefaultPermissions(roleId);
                    }
                } catch (error) {
                    console.error('❌ Erreur traitement réponse:', error);
                    showDefaultPermissions(roleId);
                }
            }

            function showDefaultPermissions(roleId) {
                console.log('🔄 Affichage des permissions par défaut pour rôle:', roleId);
                
                const roleSelect = $('#role');
                const roleName = roleSelect.find('option[value="' + roleId + '"]').text().trim();
                
                console.log('🏷️ Nom du rôle:', roleName);
                
                const permissionContainer = $('#permission');
                const defaultHtml = generateDefaultPermissions(roleName);
                
                permissionContainer.html(defaultHtml);
                
                // Animer les badges
                setTimeout(() => {
                    animatePermissionBadges();
                }, 100);
            }

            function generateDefaultPermissions(roleName) {
                const rolePermissions = {
                    'Super Admin': [
                        'Toutes les permissions système',
                        'Administration complète',
                        'Accès administrateur',
                        'Configuration système',
                        'Gestion base de données',
                        'Supervision globale'
                    ],
                    'Admin': [
                        'Gestion utilisateurs',
                        'Gestion rôles',
                        'Dashboard admin',
                        'Configuration système',
                        'Export données',
                        'Gestion permissions'
                    ],
                    'Agent Comptoir': [
                        'Gestion profils visa',
                        'Modification statuts',
                        'Dashboard comptoir',
                        'Service client',
                        'Traitement documents',
                        'Gestion rendez-vous'
                    ],
                    'Commercial': [
                        'Gestion clients',
                        'Gestion forfaits',
                        'Dashboard commercial',
                        'Suivi ventes',
                        'Gestion partenaires',
                        'Communication client'
                    ]
                };
                
                // Trouver les permissions correspondantes
                let permissions = ['Accès de base', 'Consultation dashboard'];
                for (const [role, perms] of Object.entries(rolePermissions)) {
                    if (roleName.includes(role) || role.toLowerCase().includes(roleName.toLowerCase())) {
                        permissions = perms;
                        break;
                    }
                }
                
                // Cas spécial pour Super Admin
                if (roleName.includes('Super') || roleName.includes('super')) {
                    return `
                        <div class="alert alert-warning mb-0 super-admin-badge" style="border-radius: 8px; border: 2px solid #ffc107; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-crown text-warning fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1 text-warning fw-bold">
                                        <i class="fas fa-infinity me-1"></i>
                                        Super Administrateur
                                    </h6>
                                    <p class="mb-0 small text-dark">
                                        Accès complet à toutes les fonctionnalités et permissions du système
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // Générer les badges normaux
                let badges = `<div class="default-permissions">`;
                badges += `<div class="mb-2"><small class="text-muted">Permissions pour <strong>${roleName}</strong>:</small></div>`;
                badges += `<div class="permissions-grid">`;
                
                permissions.forEach((permission, index) => {
                    const color = getPermissionColor(permission);
                    badges += `
                        <span class="badge bg-${color} permission-badge" 
                              style="opacity: 0; animation-delay: ${index * 0.1}s;"
                              title="${permission}">
                            <i class="fas fa-key me-1"></i>
                            ${permission}
                        </span>
                    `;
                });
                
                badges += `</div></div>`;
                
                return badges;
            }

            function getPermissionColor(permission) {
                const normalizedPermission = permission.toLowerCase();
                
                if (normalizedPermission.includes('gestion') || normalizedPermission.includes('manage')) {
                    return 'primary';
                } else if (normalizedPermission.includes('dashboard') || normalizedPermission.includes('tableau')) {
                    return 'success';
                } else if (normalizedPermission.includes('admin') || normalizedPermission.includes('système')) {
                    return 'danger';
                } else if (normalizedPermission.includes('service') || normalizedPermission.includes('client')) {
                    return 'info';
                } else if (normalizedPermission.includes('export') || normalizedPermission.includes('traitement')) {
                    return 'warning';
                } else {
                    return 'secondary';
                }
            }

            function getCSRFToken() {
                let token = $('#token').val();
                if (!token) token = $('meta[name="csrf-token"]').attr('content');
                if (!token) token = $('input[name="_token"]').val();
                return token;
            }

            function resetPermissionsDisplay() {
                $('#permission').html(`
                    <div class="permissions-loading">
                        <i class="fas fa-info-circle me-1"></i>
                        <span>Sélectionnez un rôle pour voir les permissions</span>
                    </div>
                `);
            }

            function animatePermissionBadges() {
                $('.permission-badge').each(function(index) {
                    const $badge = $(this);
                    setTimeout(() => {
                        $badge.animate({ opacity: 1 }, 300);
                    }, index * 100);
                });
            }
            
            function validatePasswords() {
                const password = $('#password').val();
                const confirm = $('#password-confirm').val();
                const indicator = $('#password-match-indicator');
                
                if (!password && !confirm) {
                    indicator.text('Les mots de passe doivent correspondre')
                             .removeClass('text-success text-danger')
                             .addClass('text-muted');
                    return true;
                }
                
                if (password && password.length < 6) {
                    indicator.text('Le mot de passe doit contenir au moins 6 caractères')
                             .removeClass('text-success text-muted')
                             .addClass('text-danger');
                    return false;
                }
                
                if (password && confirm && password !== confirm) {
                    indicator.text('✗ Les mots de passe ne correspondent pas')
                             .removeClass('text-success text-muted')
                             .addClass('text-danger');
                    return false;
                }
                
                if (password && confirm && password === confirm) {
                    indicator.text('✓ Les mots de passe correspondent')
                             .removeClass('text-danger text-muted')
                             .addClass('text-success');
                    return true;
                }
                
                return true;
            }
            
            function validateForm() {
                let isValid = true;
                const errors = [];
                
                // Vérifications obligatoires
                if (!$('#name').val().trim()) {
                    errors.push('Le nom est obligatoire');
                    $('#name').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#name').removeClass('is-invalid');
                }
                
                if (!$('#email').val().trim()) {
                    errors.push('L\'email est obligatoire');
                    $('#email').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#email').removeClass('is-invalid');
                }

                if (!$('#contact').val().trim()) {
                    errors.push('Le contact est obligatoire');
                    $('#contact').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#contact').removeClass('is-invalid');
                }

                if (!$('#type_user').val()) {
                    errors.push('Le type d\'agent est obligatoire');
                    $('#type_user').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#type_user').removeClass('is-invalid');
                }
                
                if (!$('#role').val()) {
                    errors.push('Le rôle est obligatoire');
                    $('#role').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#role').removeClass('is-invalid');
                }
                
                if (!validatePasswords()) {
                    errors.push('Les mots de passe ne sont pas valides');
                    isValid = false;
                }
                
                if (!isValid) {
                    showNotification('Veuillez corriger les erreurs:\n' + errors.join('\n'), 'error');
                }
                
                return isValid;
            }

            function syncUserTypeWithRole() {
                const typeUser = $('#type_user').val();
                const roleSelect = $('#role');
                
                if (!typeUser || !roleSelect.length) return;
                
                const typeToRoleMap = {
                    'admin': 'Admin',
                    'agent_comptoir': 'Agent Comptoir',
                    'commercial': 'Commercial'
                };
                
                const targetRoleName = typeToRoleMap[typeUser];
                if (targetRoleName) {
                    // Trouver l'option correspondante
                    roleSelect.find('option').each(function() {
                        if ($(this).text().trim() === targetRoleName) {
                            roleSelect.val($(this).val()).trigger('change');
                            return false; // break
                        }
                    });
                }
            }

            function previewPhoto(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        $('#preview-image').attr('src', e.target.result);
                        $('#photo-preview').show();
                    };
                    
                    reader.readAsDataURL(input.files[0]);
                } else {
                    $('#photo-preview').hide();
                }
            }
            
            function showNotification(message, type = 'info') {
                // Supprimer les notifications existantes
                $('.toast-notification').remove();
                
                const iconMap = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-triangle',
                    warning: 'fas fa-exclamation-circle',
                    info: 'fas fa-info-circle'
                };
                
                const colorMap = {
                    success: 'alert-success',
                    error: 'alert-danger',
                    warning: 'alert-warning',
                    info: 'alert-info'
                };
                
                const toast = $(`
                    <div class="toast-notification alert ${colorMap[type] || 'alert-info'} position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 300px;">
                        <div class="d-flex align-items-center">
                            <i class="${iconMap[type] || 'fas fa-info-circle'} me-2"></i>
                            <span class="flex-grow-1">${message}</span>
                            <button type="button" class="btn-close ms-2" onclick="$(this).closest('.toast-notification').remove()"></button>
                        </div>
                    </div>
                `);
                
                $('body').append(toast);
                
                // Auto-remove après 5 secondes
                setTimeout(() => toast.remove(), 5000);
            }

            // Message de confirmation lors de la soumission
            $('#createUserForm').on('submit', function() {
                showNotification('Création en cours...', 'info');
            });

            // Exposer les fonctions pour usage externe
            window.loadRolePermissions = loadRolePermissions;
            window.resetPermissionsDisplay = resetPermissionsDisplay;
        </script>
    @endpush
@endsection