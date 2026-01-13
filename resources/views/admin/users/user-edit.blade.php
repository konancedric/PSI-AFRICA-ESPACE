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
                max-height: 200px;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 1rem;
                background-color: #f8f9fa;
            }
            .permission-badge {
                font-size: 0.7rem;
                margin: 2px;
                padding: 0.25rem 0.5rem;
                border-radius: 12px;
            }
            .permissions-loading {
                text-align: center;
                padding: 2rem;
                color: #6c757d;
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
                                            {{-- ✅ CORRECTION: Gestion flexible des rôles (objet ou collection) --}}
                                            @if(isset($roles) && $roles->count() > 0)
                                                @foreach($roles as $role_item)
                                                    {{-- Vérifier si c'est un objet ou une clé-valeur --}}
                                                    @if(is_object($role_item))
                                                        {{-- Format objet Role --}}
                                                        <option value="{{ $role_item->id }}" {{ old('role') == $role_item->id ? 'selected' : '' }}>
                                                            {{ $role_item->name }}
                                                        </option>
                                                    @else
                                                        {{-- Format collection pluck (clé => valeur) --}}
                                                        <option value="{{ $loop->index }}" {{ old('role') == $loop->index ? 'selected' : '' }}>
                                                            {{ $role_item }}
                                                        </option>
                                                    @endif
                                                @endforeach
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
                console.log('🔧 Initialisation création agent - Version corrigée');
                
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

                // ✅ MISE À JOUR DES PERMISSIONS SELON LE RÔLE SÉLECTIONNÉ
                $('#role').on('change', function() {
                    const roleId = $(this).val();
                    if (roleId) {
                        updateRolePermissions(roleId);
                    } else {
                        clearPermissions();
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
                    clearPermissions();
                    $('#password-match-indicator').text('Les mots de passe doivent correspondre')
                                                   .removeClass('text-success text-danger')
                                                   .addClass('text-muted');
                });
            });
            
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

            // ✅ FONCTION CORRIGÉE POUR METTRE À JOUR LES PERMISSIONS
            function updateRolePermissions(roleId) {
                console.log('🔄 Mise à jour des permissions pour le rôle:', roleId);
                
                // Afficher le loading
                $('#permission').html(`
                    <div class="permissions-loading">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <span>Chargement des permissions...</span>
                    </div>
                `);
                
                // Appel AJAX pour récupérer les permissions
                $.ajax({
                    url: '{{ route("permissions.badge") }}',
                    type: 'GET',
                    data: { 
                        role_id: roleId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('✅ Réponse permissions:', response);
                        
                        if (response.success && response.badges) {
                            $('#permission').html(response.badges);
                        } else if (response.permissions) {
                            $('#permission').html(response.permissions);
                        } else {
                            $('#permission').html(`
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucune permission trouvée pour ce rôle
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Erreur AJAX permissions:', error);
                        $('#permission').html(`
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Erreur lors du chargement des permissions
                            </div>
                        `);
                    }
                });
            }

            function clearPermissions() {
                $('#permission').html(`
                    <div class="permissions-loading">
                        <i class="fas fa-info-circle me-1"></i>
                        <span>Sélectionnez un rôle pour voir les permissions</span>
                    </div>
                `);
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
        </script>
    @endpush
@endsection