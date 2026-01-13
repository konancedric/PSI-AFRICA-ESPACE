@extends('layouts.main')

@section('title', 'Administration - Dashboard PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête du Dashboard Admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="admin-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="admin-title mb-2" style="font-weight: 700; font-size: 2.2rem;">
                            <i class="fas fa-crown me-3" style="color: #ffd700;"></i>
                            Administration PSI Africa
                        </h1>
                        <p class="admin-subtitle mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                            Gestion avancée des administrateurs et surveillance du système
                        </p>
                    </div>
                    <div class="admin-actions">
                        <button class="btn btn-outline-light btn-lg me-3" onclick="refreshDashboard()" style="border-radius: 12px;">
                            <i class="fas fa-sync-alt me-2"></i> Actualiser
                        </button>
                        <button class="btn btn-light btn-lg" onclick="window.print()" style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-download me-2"></i> Rapport
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Principales - FOCUS UTILISATEURS ADMIN -->
    <div class="row mb-4">
        <!-- Total Utilisateurs Admin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-icon mb-3">
                            <i class="fas fa-user-shield fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <h3 class="stats-number mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ number_format($totalUsersAdmin ?? 8) }}</h3>
                        <p class="stats-label mb-2" style="font-size: 0.95rem; opacity: 0.9;">Utilisateurs Admin</p>
                        <div class="stats-trend">
                            <span class="badge bg-white text-primary px-3 py-1" style="border-radius: 12px; font-weight: 600;">
                                <i class="fas fa-arrow-up me-1"></i> +{{ $newUsersAdminThisMonth ?? 3 }}
                            </span>
                            <small style="opacity: 0.8; margin-left: 0.5rem;">ce mois</small>
                        </div>
                    </div>
                    <div class="stats-chart" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Internes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(32, 201, 151, 0.3); transition: all 0.3s ease;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-icon mb-3">
                            <i class="fas fa-user-tie fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <h3 class="stats-number mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ number_format($totalAgents ?? 22) }}</h3>
                        <p class="stats-label mb-2" style="font-size: 0.95rem; opacity: 0.9;">Agents Internes</p>
                        <div class="stats-trend">
                            <span class="badge bg-white text-success px-3 py-1" style="border-radius: 12px; font-weight: 600;">
                                <i class="fas fa-user-check me-1"></i> {{ $agentsActifs ?? 20 }} actifs
                            </span>
                        </div>
                    </div>
                    <div class="stats-chart" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users-cog fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profils Visa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(253, 126, 20, 0.3); transition: all 0.3s ease;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-icon mb-3">
                            <i class="fas fa-passport fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <h3 class="stats-number mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ number_format($totalProfilVisa ?? 5126) }}</h3>
                        <p class="stats-label mb-2" style="font-size: 0.95rem; opacity: 0.9;">Profils Visa</p>
                        <div class="stats-trend">
                            <span class="badge bg-white text-warning px-3 py-1" style="border-radius: 12px; font-weight: 600;">
                                <i class="fas fa-calendar-day me-1"></i> {{ $profilsVisaAujourdhui ?? 12 }}
                            </span>
                            <small style="opacity: 0.8; margin-left: 0.5rem;">aujourd'hui</small>
                        </div>
                    </div>
                    <div class="stats-chart" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-pie fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Système -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(111, 66, 193, 0.3); transition: all 0.3s ease;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-icon mb-3">
                            <i class="fas fa-tachometer-alt fa-2x" style="opacity: 0.8;"></i>
                        </div>
                        <h3 class="stats-number mb-1" style="font-size: 2.5rem; font-weight: 700;">{{ $successRate ?? 87.5 }}%</h3>
                        <p class="stats-label mb-2" style="font-size: 0.95rem; opacity: 0.9;">Performance</p>
                        <div class="stats-trend">
                            <span class="badge bg-white text-purple px-3 py-1" style="border-radius: 12px; font-weight: 600; color: #6f42c1 !important;">
                                <i class="fas fa-rocket me-1"></i> Excellent
                            </span>
                        </div>
                    </div>
                    <div class="stats-chart" style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-award fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau de Contrôle Principal -->
    <div class="row mb-4">
        <!-- Actions Rapides -->
        <div class="col-xl-8 mb-4">
            <div class="control-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #f0f0f0;">
                <h5 class="panel-title mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Centre de Contrôle Admin
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ url('/users') }}" class="action-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                            <div class="d-flex align-items-center">
                                <div class="action-icon me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users-cog fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600;">Gérer les Agents</h6>
                                    <small style="opacity: 0.9;">Administration complète</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <a href="{{ url('/user/create') }}" class="action-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); color: white; border-radius: 15px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(32, 201, 151, 0.3);">
                            <div class="d-flex align-items-center">
                                <div class="action-icon me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-plus fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600;">Créer un Utilisateur</h6>
                                    <small style="opacity: 0.9;">Nouvel administrateur</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <a href="{{ url('/dashboard') }}" class="action-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%); color: white; border-radius: 15px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);">
                            <div class="d-flex align-items-center">
                                <div class="action-icon me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-area fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600;">Dashboard Principal</h6>
                                    <small style="opacity: 0.9;">Statistiques complètes</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <a href="{{ url('/roles') }}" class="action-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; border-radius: 15px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);">
                            <div class="d-flex align-items-center">
                                <div class="action-icon me-3" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-shield-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600;">Rôles & Permissions</h6>
                                    <small style="opacity: 0.9;">Sécurité système</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations Admin -->
        <div class="col-xl-4 mb-4">
            <div class="admin-profile" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #f0f0f0; height: 100%;">
                <h5 class="profile-title mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-user-shield me-2 text-primary"></i>
                    Profil Administrateur
                </h5>
                
                <div class="admin-avatar text-center mb-4">
                    @if(Auth::user()->photo_user && Auth::user()->photo_user != 'NULL')
                        <img src="{{ asset('upload/users/' . Auth::user()->photo_user) }}" 
                             class="rounded-circle mb-3" 
                             style="width: 80px; height: 80px; object-fit: cover; border: 4px solid #667eea;" 
                             alt="Photo Admin">
                    @else
                        <div class="avatar-placeholder mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: 700; margin: 0 auto;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                
                <div class="profile-info">
                    <div class="info-item mb-3" style="padding: 0.75rem; background: #f8f9fa; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #6c757d; font-weight: 500;">Nom</span>
                            <span style="color: #2d3436; font-weight: 600;">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                    
                    <div class="info-item mb-3" style="padding: 0.75rem; background: #f8f9fa; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #6c757d; font-weight: 500;">Email</span>
                            <span style="color: #2d3436; font-weight: 600; font-size: 0.9rem;">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    
                    <div class="info-item mb-3" style="padding: 0.75rem; background: #f8f9fa; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #6c757d; font-weight: 500;">Rôles</span>
                            <div>
                                @foreach(Auth::user()->getRoleNames() as $role)
                                    <span class="badge bg-primary me-1" style="border-radius: 8px;">{{ $role }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item" style="padding: 0.75rem; background: #f8f9fa; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #6c757d; font-weight: 500;">Statut</span>
                            <span class="badge bg-success" style="border-radius: 8px; padding: 0.5rem 1rem;">
                                <i class="fas fa-check-circle me-1"></i> Actif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Utilisateurs Admin et Système -->
    <div class="row mb-4">
        <!-- Utilisateurs Admin Récents - SECTION CORRIGÉE -->
        <div class="col-xl-8 mb-4">
            <div class="admins-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #f0f0f0;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="panel-title mb-0" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                        <i class="fas fa-user-shield me-2 text-info"></i>
                        Utilisateurs Admin Récents
                        @if(isset($usersAdminRecents) && $usersAdminRecents->count() > 0)
                            <span class="badge bg-info ms-2">{{ $usersAdminRecents->count() }}</span>
                        @endif
                    </h5>
                    <a href="{{ url('/users') }}" class="btn btn-outline-primary" style="border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-eye me-1"></i> Voir tout
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" style="border-radius: 12px; overflow: hidden;">
                        <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <tr>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Utilisateur Admin</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Email</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Rôles</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Statut</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($usersAdminRecents) && $usersAdminRecents->count() > 0)
                                @foreach($usersAdminRecents as $admin)
                                <tr style="transition: all 0.3s ease;" class="admin-row">
                                    <td style="border: none; padding: 1rem;">
                                        <div class="d-flex align-items-center">
                                            @if(isset($admin->photo_user) && $admin->photo_user && $admin->photo_user != 'NULL')
                                                <img src="{{ asset('upload/users/' . $admin->photo_user) }}" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;" 
                                                     alt="Photo {{ $admin->name ?? 'Admin' }}">
                                            @else
                                                <div class="admin-avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                                    {{ strtoupper(substr($admin->name ?? 'AD', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: #2d3436;">{{ $admin->name ?? 'Nom non défini' }}</div>
                                                <small style="color: #6c757d;">{{ $admin->matricule ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="border: none; padding: 1rem;">
                                        <span style="color: #495057;">{{ $admin->email ?? 'Email non défini' }}</span>
                                    </td>
                                    <td style="border: none; padding: 1rem;">
                                        @if(isset($admin->roles) && $admin->roles && $admin->roles->count() > 0)
                                            @foreach($admin->roles as $role)
                                                <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.4rem 0.8rem; border-radius: 12px; font-weight: 500; margin-right: 0.25rem;">
                                                    {{ $role->name ?? 'Rôle' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-warning text-dark" style="padding: 0.4rem 0.8rem; border-radius: 12px;">
                                                {{ $admin->type_user_label ?? 'Aucun rôle' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="border: none; padding: 1rem;">
                                        @if(($admin->etat ?? 1) == 1)
                                            <span class="badge bg-success" style="padding: 0.5rem 1rem; border-radius: 12px; font-weight: 500;">
                                                <i class="fas fa-check-circle me-1"></i> Actif
                                            </span>
                                        @else
                                            <span class="badge bg-danger" style="padding: 0.5rem 1rem; border-radius: 12px; font-weight: 500;">
                                                <i class="fas fa-times-circle me-1"></i> Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td style="border: none; padding: 1rem;">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editAdmin({{ $admin->id ?? 0 }})" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="viewAdmin({{ $admin->id ?? 0 }})" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if(($admin->etat ?? 1) == 1)
                                                <button class="btn btn-sm btn-outline-warning" onclick="deactivateAdmin({{ $admin->id ?? 0 }})" title="Désactiver">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-success" onclick="activateAdmin({{ $admin->id ?? 0 }})" title="Activer">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center py-5" style="border: none;">
                                    <div style="color: #6c757d;">
                                        <i class="fas fa-user-shield fa-3x mb-3" style="opacity: 0.5;"></i>
                                        <br>
                                        <strong>Aucun utilisateur admin récent</strong>
                                        <br>
                                        <small>Créez votre premier administrateur maintenant</small>
                                        <br>
                                        <a href="{{ url('/user/create') }}" class="btn btn-primary mt-3" style="border-radius: 12px;">
                                            <i class="fas fa-plus me-1"></i> Créer un Admin
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination simple si nécessaire -->
                @if(isset($usersAdminRecents) && $usersAdminRecents->count() >= 10)
                <div class="d-flex justify-content-center mt-3">
                    <small class="text-muted">Affichage des 10 derniers utilisateurs admin</small>
                </div>
                @endif
            </div>
        </div>

        <!-- État du Système -->
        <div class="col-xl-4 mb-4">
            <div class="system-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #f0f0f0; height: 100%;">
                <h5 class="panel-title mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-cogs me-2 text-success"></i>
                    État du Système
                </h5>
                
                <div class="system-checks">
                    <div class="check-item mb-3" style="padding: 1rem; background: #f8f9fa; border-radius: 12px; border-left: 4px solid {{ ($systemStatus['database_connection'] ?? true) ? '#28a745' : '#dc3545' }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-database me-3 text-{{ ($systemStatus['database_connection'] ?? true) ? 'success' : 'danger' }}"></i>
                                <span style="font-weight: 600; color: #2d3436;">Base de données</span>
                            </div>
                            <span class="badge bg-{{ ($systemStatus['database_connection'] ?? true) ? 'success' : 'danger' }}" style="border-radius: 8px; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-{{ ($systemStatus['database_connection'] ?? true) ? 'check' : 'times' }} me-1"></i> 
                                {{ ($systemStatus['database_connection'] ?? true) ? 'OK' : 'ERREUR' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="check-item mb-3" style="padding: 1rem; background: #f8f9fa; border-radius: 12px; border-left: 4px solid {{ ($systemStatus['permissions_system'] ?? true) ? '#28a745' : '#dc3545' }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt me-3 text-{{ ($systemStatus['permissions_system'] ?? true) ? 'success' : 'danger' }}"></i>
                                <span style="font-weight: 600; color: #2d3436;">Permissions</span>
                            </div>
                            <span class="badge bg-{{ ($systemStatus['permissions_system'] ?? true) ? 'success' : 'danger' }}" style="border-radius: 8px; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-{{ ($systemStatus['permissions_system'] ?? true) ? 'check' : 'times' }} me-1"></i> 
                                {{ ($systemStatus['permissions_system'] ?? true) ? 'OK' : 'ERREUR' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="check-item mb-3" style="padding: 1rem; background: #f8f9fa; border-radius: 12px; border-left: 4px solid {{ ($systemStatus['agents_system'] ?? true) ? '#28a745' : '#dc3545' }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users-cog me-3 text-{{ ($systemStatus['agents_system'] ?? true) ? 'success' : 'danger' }}"></i>
                                <span style="font-weight: 600; color: #2d3436;">Système Agents</span>
                            </div>
                            <span class="badge bg-{{ ($systemStatus['agents_system'] ?? true) ? 'success' : 'danger' }}" style="border-radius: 8px; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-{{ ($systemStatus['agents_system'] ?? true) ? 'check' : 'times' }} me-1"></i> 
                                {{ ($systemStatus['agents_system'] ?? true) ? 'OK' : 'ERREUR' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="check-item" style="padding: 1rem; background: #f8f9fa; border-radius: 12px; border-left: 4px solid {{ ($systemStatus['roles_configured'] ?? true) ? '#28a745' : '#dc3545' }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-server me-3 text-{{ ($systemStatus['roles_configured'] ?? true) ? 'success' : 'danger' }}"></i>
                                <span style="font-weight: 600; color: #2d3436;">Configuration</span>
                            </div>
                            <span class="badge bg-{{ ($systemStatus['roles_configured'] ?? true) ? 'success' : 'danger' }}" style="border-radius: 8px; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-{{ ($systemStatus['roles_configured'] ?? true) ? 'check' : 'times' }} me-1"></i> 
                                {{ ($systemStatus['roles_configured'] ?? true) ? 'OK' : 'ERREUR' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="system-summary mt-4 pt-4" style="border-top: 2px solid #e9ecef;">
                    <div class="text-center">
                        @php
                            $allOk = collect($systemStatus ?? [])->every(function($status) { return $status === true; });
                        @endphp
                        <div class="system-status-icon mb-3">
                            <i class="fas fa-{{ $allOk ? 'check-circle' : 'exclamation-triangle' }} fa-3x text-{{ $allOk ? 'success' : 'warning' }}"></i>
                        </div>
                        <h6 style="color: {{ $allOk ? '#28a745' : '#ffc107' }}; font-weight: 700; margin-bottom: 0.5rem;">
                            {{ $allOk ? 'Système Opérationnel' : 'Attention Requise' }}
                        </h6>
                        <small style="color: #6c757d;">
                            {{ $allOk ? 'Tous les services fonctionnent correctement' : 'Certains services nécessitent votre attention' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Rapide -->
    <div class="row">
        <div class="col-12">
            <div class="quick-nav" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #f0f0f0;">
                <h5 class="nav-title mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-compass me-2 text-warning"></i>
                    Navigation Rapide
                </h5>
                
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ url('/users') }}" class="nav-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; text-decoration: none; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="text-center">
                                <i class="fas fa-users-cog fa-2x mb-3 text-primary"></i>
                                <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 0.5rem;">Gestion Agents</h6>
                                <small style="color: #6c757d;">Administration complète</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ url('/profil-visa') }}" class="nav-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; text-decoration: none; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="text-center">
                                <i class="fas fa-passport fa-2x mb-3 text-success"></i>
                                <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 0.5rem;">Profils Visa</h6>
                                <small style="color: #6c757d;">Gestion des demandes</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ url('/dashboard') }}" class="nav-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; text-decoration: none; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="text-center">
                                <i class="fas fa-chart-area fa-2x mb-3 text-info"></i>
                                <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 0.5rem;">Dashboard Principal</h6>
                                <small style="color: #6c757d;">Statistiques PSI Africa</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ url('/roles') }}" class="nav-card" style="display: block; padding: 1.5rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; text-decoration: none; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="text-center">
                                <i class="fas fa-shield-alt fa-2x mb-3 text-warning"></i>
                                <h6 style="color: #2d3436; font-weight: 600; margin-bottom: 0.5rem;">Rôles & Permissions</h6>
                                <small style="color: #6c757d;">Sécurité système</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- CSS intégré pour les animations et effets -->
<style>
/* Animations et transitions */
.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2) !important;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3) !important;
}

.nav-card:hover {
    transform: translateY(-3px);
    border-color: #667eea !important;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
}

.admin-row:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px);
}

/* Animations d'entrée */
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

.stats-card, .control-panel, .admin-profile, .admins-panel, .system-panel, .quick-nav {
    animation: slideInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-header {
        padding: 1.5rem !important;
    }
    
    .admin-title {
        font-size: 1.8rem !important;
    }
    
    .admin-actions {
        margin-top: 1rem;
    }
    
    .admin-actions .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .stats-number {
        font-size: 2rem !important;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}

/* Effet de pulsation pour les éléments importants */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
    }
}

.admin-header {
    animation: pulse 2s infinite;
}

/* Personnalisation des scrollbars */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* Spinner de chargement */
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

/* Badge personnalisé */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Table améliorée */
.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05) !important;
}
</style>

<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard Admin PSI Africa chargé');
    
    // Animation d'entrée séquencielle
    const elements = document.querySelectorAll('.stats-card, .control-panel, .admin-profile, .admins-panel, .system-panel, .quick-nav');
    
    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Mise à jour en temps réel des statistiques admin
    function updateAdminStats() {
        const statsElements = document.querySelectorAll('.stats-number');
        
        // Ajouter un indicateur de chargement
        statsElements.forEach(el => {
            const originalText = el.textContent;
            el.dataset.originalText = originalText;
            el.innerHTML = '<span class="loading-spinner"></span>';
        });
        
        fetch('/admin/dashboard/realtime-stats')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 Statistiques admin mises à jour:', data);
                
                // Mettre à jour les statistiques
                if (data.total_users_admin !== undefined) {
                    const adminCountEl = document.querySelector('.stats-number');
                    if (adminCountEl) {
                        adminCountEl.textContent = new Intl.NumberFormat().format(data.total_users_admin);
                    }
                }
                
                // Afficher une notification de succès
                showNotification('Statistiques mises à jour avec succès', 'success');
            })
            .catch(error => {
                console.error('❌ Erreur mise à jour stats:', error);
                
                // Restaurer les valeurs originales en cas d'erreur
                statsElements.forEach(el => {
                    if (el.dataset.originalText) {
                        el.textContent = el.dataset.originalText;
                    }
                });
                
                showNotification('Erreur lors de la mise à jour des statistiques', 'error');
            });
    }

    // Actualiser toutes les 5 minutes
    setInterval(updateAdminStats, 300000);
    
    // Effet de survol pour les cartes d'action
    document.querySelectorAll('.action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Vérifier périodiquement l'état du système
    function checkSystemHealth() {
        fetch('/admin/dashboard/health-check')
            .then(response => response.json())
            .then(data => {
                console.log('🔍 Santé du système vérifiée:', data);
                
                // Mettre à jour les indicateurs de santé
                Object.keys(data.checks || {}).forEach(check => {
                    const indicator = document.querySelector(`[data-check="${check}"]`);
                    if (indicator) {
                        indicator.className = data.checks[check] ? 'fas fa-check text-success' : 'fas fa-times text-danger';
                    }
                });
            })
            .catch(error => {
                console.error('❌ Erreur vérification santé:', error);
            });
    }
    
    // Vérifier la santé du système toutes les 10 minutes
    setInterval(checkSystemHealth, 600000);
});

// Fonctions pour les actions sur les utilisateurs admin
function editAdmin(adminId) {
    if (adminId && adminId > 0) {
        window.location.href = `/user/edit/${adminId}`;
    } else {
        showNotification('ID administrateur invalide', 'error');
    }
}

function viewAdmin(adminId) {
    if (!adminId || adminId <= 0) {
        showNotification('ID administrateur invalide', 'error');
        return;
    }
    
    fetch(`/user/details/${adminId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('👤 Détails admin:', data.user);
                showUserDetailsModal(data.user);
            } else {
                showNotification(data.error || 'Erreur lors du chargement des détails', 'error');
            }
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
}

function activateAdmin(adminId) {
    if (!adminId || adminId <= 0) {
        showNotification('ID administrateur invalide', 'error');
        return;
    }
    
    if (!confirm('Êtes-vous sûr de vouloir activer cet administrateur ?')) {
        return;
    }
    
    // Obtenir le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Token CSRF manquant', 'error');
        return;
    }
    
    fetch('/user/edit-etat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: adminId,
            etat: 1
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Administrateur activé avec succès', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.error || 'Erreur lors de l\'activation', 'error');
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

function deactivateAdmin(adminId) {
    if (!adminId || adminId <= 0) {
        showNotification('ID administrateur invalide', 'error');
        return;
    }
    
    if (!confirm('Êtes-vous sûr de vouloir désactiver cet administrateur ?')) {
        return;
    }
    
    // Obtenir le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('Token CSRF manquant', 'error');
        return;
    }
    
    fetch('/user/edit-etat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: adminId,
            etat: 0
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Administrateur désactivé avec succès', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.error || 'Erreur lors de la désactivation', 'error');
        }
    })
    .catch(error => {
        console.error('❌ Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

// Fonction pour rafraîchir les données
function refreshDashboard() {
    showNotification('Actualisation en cours...', 'info');
    location.reload();
}

// Notification toast pour les actions
function showNotification(message, type = 'success') {
    // Supprimer les notifications existantes
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    // Créer une notification toast moderne
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

// Fonction pour afficher les détails d'un utilisateur
function showUserDetailsModal(user) {
    // Créer un modal simple pour afficher les détails
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.style.cssText = 'z-index: 10000;';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-shield me-2"></i>
                        Détails de l'administrateur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="this.closest('.modal').remove()"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            ${user.photo_url ? 
                                `<img src="${user.photo_url}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;" alt="Photo">` :
                                `<div class="rounded-circle mb-3 mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold;">${user.name.substring(0, 2).toUpperCase()}</div>`
                            }
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr><td><strong>Nom:</strong></td><td>${user.name}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${user.email}</td></tr>
                                <tr><td><strong>Matricule:</strong></td><td>${user.matricule || 'N/A'}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>${user.type_user_label || user.type_user}</td></tr>
                                <tr><td><strong>Statut:</strong></td><td>
                                    <span class="badge bg-${user.etat == 1 ? 'success' : 'danger'}">
                                        ${user.etat == 1 ? 'Actif' : 'Inactif'}
                                    </span>
                                </td></tr>
                                <tr><td><strong>Rôles:</strong></td><td>
                                    ${(user.roles || []).map(role => `<span class="badge bg-primary me-1">${role}</span>`).join('')}
                                </td></tr>
                                <tr><td><strong>Créé le:</strong></td><td>${new Date(user.created_at).toLocaleDateString('fr-FR')}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="editAdmin(${user.id})">Modifier</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Afficher le modal
    setTimeout(() => {
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }, 100);
}

// CSS pour les animations
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
</script>

<!-- Ajouter le meta token CSRF si pas déjà présent -->
@if(!isset($csrfToken))
<meta name="csrf-token" content="{{ csrf_token() }}">
@endif

@endsection