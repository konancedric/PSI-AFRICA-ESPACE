@extends('layouts.main')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête Super Admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="admin-header" style="background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%); color: white; padding: 2rem; border-radius: 10px; position: relative;">
                <div class="position-absolute" style="top: 20px; right: 20px;">
                    <span class="badge bg-danger fs-6">SUPER ADMIN</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="dashboard-title mb-2">
                            <i class="fas fa-shield-alt"></i>
                            Dashboard Super Administrateur
                        </h1>
                        <p class="dashboard-subtitle mb-0">
                            Contrôle total du système PSI Africa - Gestion avancée
                        </p>
                    </div>
                    <div class="dashboard-actions">
                        <button class="btn btn-outline-light btn-sm me-2" onclick="refreshAdminStats()">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                        <button class="btn btn-light btn-sm me-2" onclick="exportAdminReport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="openSystemMaintenance()">
                            <i class="fas fa-cogs"></i> Maintenance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Système -->
    @if(isset($systemStatus))
    <div class="row mb-4">
        <div class="col-12">
            @php
                $allHealthy = array_reduce($systemStatus, function($carry, $status) { return $carry && $status; }, true);
            @endphp
            <div class="alert alert-{{ $allHealthy ? 'success' : 'warning' }} alert-dismissible fade show" role="alert">
                <h5>
                    <i class="fas fa-{{ $allHealthy ? 'check-circle' : 'exclamation-triangle' }}"></i> 
                    État du Système
                </h5>
                <div class="row">
                    <div class="col-md-3">
                        <span class="badge bg-{{ $systemStatus['database_connection'] ? 'success' : 'danger' }}">
                            Base de données: {{ $systemStatus['database_connection'] ? 'OK' : 'Erreur' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-{{ $systemStatus['permissions_system'] ? 'success' : 'danger' }}">
                            Permissions: {{ $systemStatus['permissions_system'] ? 'OK' : 'Erreur' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-{{ $systemStatus['agents_system'] ? 'success' : 'danger' }}">
                            Agents: {{ $systemStatus['agents_system'] ? 'OK' : 'Erreur' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-{{ $systemStatus['roles_configured'] ? 'success' : 'danger' }}">
                            Rôles: {{ $systemStatus['roles_configured'] ? 'OK' : 'Erreur' }}
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Cartes de Statistiques Administratives -->
    <div class="row mb-4">
        <!-- Utilisateurs Admin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #dc3545;">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($totalUsersAdmin ?? 0) }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">Administrateurs</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-arrow-up"></i> +{{ $newUsersAdminToday ?? 0 }}
                                </span>
                                <small class="text-muted">aujourd'hui</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs Public -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6f42c1 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #6f42c1;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($totalPublicUsers ?? 0) }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">Utilisateurs Public</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-chart-line"></i> {{ number_format($totalPublicUsers ?? 0) }}
                                </span>
                                <small class="text-muted">total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Internes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #20c997 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #20c997;">
                                <i class="fas fa-user-tie fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($totalAgents ?? 0) }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">Agents Internes</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check"></i> {{ $agentsActifs ?? 0 }}
                                </span>
                                <small class="text-muted">actifs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chiffre d'Affaires -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #fd7e14 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #fd7e14;">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($chiffreAffairesMois ?? 0, 0, ',', ' ') }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">CA du Mois (FCFA)</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-shopping-cart"></i> {{ $souscriptionsCeMois ?? 0 }}
                                </span>
                                <small class="text-muted">souscriptions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de Performance -->
    <div class="row mb-4">
        <!-- Profils Visa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #0dcaf0;">
                                <i class="fas fa-passport fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($totalProfilVisa ?? 0) }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">Profils Visa Total</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-plus"></i> {{ $profilsVisaAujourdhui ?? 0 }}
                                </span>
                                <small class="text-muted">aujourd'hui</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux de Réussite -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #198754;">
                                <i class="fas fa-chart-pie fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ $successRate ?? 87.5 }}%</h3>
                            <p class="text-muted mb-1 small text-uppercase">Taux de Réussite</p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $successRate ?? 87.5 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profils en Attente -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #ffc107;">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ number_format($profilsEnAttente ?? 0) }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">En Attente</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $profilsUrgents ?? 0 }}
                                </span>
                                <small class="text-muted">urgents</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Temps Moyen -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6610f2 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3 text-white" style="background: #6610f2;">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1 fw-bold">{{ $avgProcessingTime ?? 3.2 }}</h3>
                            <p class="text-muted mb-1 small text-uppercase">Jours Moyen</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ ($avgProcessingTime ?? 0) <= 7 ? 'success' : 'warning' }} me-2">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <small class="text-muted">traitement</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Administratifs -->
    <div class="row mb-4">
        <!-- Évolution des Admins -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area text-danger"></i>
                        Évolution Administrative (6 mois)
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="adminEvolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des Agents -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-success"></i>
                        Répartition des Agents
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="agentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables de Données Administratives -->
    <div class="row mb-4">
        <!-- Administrateurs Récents -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-shield text-danger"></i>
                        Administrateurs Récents
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="manageAdmins()">
                        <i class="fas fa-cogs"></i> Gérer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Administrateur</th>
                                    <th>Rôle</th>
                                    <th class="text-center">État</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($usersAdminRecents) && count($usersAdminRecents) > 0)
                                    @foreach($usersAdminRecents->take(5) as $admin)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($admin->photo_user && $admin->photo_user != 'NULL')
                                                    <img src="{{ asset('upload/users/' . $admin->photo_user) }}" 
                                                         alt="Photo" class="rounded-circle me-2" 
                                                         style="width: 35px; height: 35px; object-fit: cover;">
                                                @else
                                                    <div class="bg-danger text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 35px; height: 35px; font-size: 0.8rem; font-weight: bold;">
                                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $admin->name ?? 'N/A' }}</strong>
                                                    <br><small class="text-muted">{{ $admin->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(isset($admin->roles) && count($admin->roles) > 0)
                                                @foreach($admin->roles as $role)
                                                    <span class="badge bg-{{ $role->name == 'Super Admin' ? 'danger' : 'warning' }}">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">{{ $admin->type_user_label ?? 'Admin' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ ($admin->etat ?? 1) == 1 ? 'success' : 'danger' }}">
                                                {{ ($admin->etat ?? 1) == 1 ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewAdmin({{ $admin->id }})" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" onclick="editAdmin({{ $admin->id }})" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Aucun administrateur récent
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités Système -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-info"></i>
                        Activités Récentes du Système
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-list" style="max-height: 400px; overflow-y: auto;">
                        @if(isset($activitesRecentes) && count($activitesRecentes) > 0)
                            @foreach($activitesRecentes as $activity)
                            <div class="activity-item d-flex align-items-center py-2 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle p-2 text-white" style="background-color: 
                                        @switch($activity['color'])
                                            @case('success') #198754 @break
                                            @case('info') #0dcaf0 @break
                                            @case('warning') #ffc107 @break
                                            @case('danger') #dc3545 @break
                                            @default #6c757d
                                        @endswitch
                                        ; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 fw-semibold">{{ $activity['message'] }}</p>
                                    <small class="text-muted">
                                        {{ isset($activity['date']) ? \Carbon\Carbon::parse($activity['date'])->diffForHumans() : 'Date inconnue' }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>Aucune activité récente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides Super Admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-warning"></i>
                        Actions Rapides Super Admin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-danger w-100" onclick="createAdmin()">
                                <i class="fas fa-user-plus"></i>
                                <br><small>Créer Admin</small>
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-warning w-100" onclick="manageRoles()">
                                <i class="fas fa-key"></i>
                                <br><small>Gérer Rôles</small>
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-info w-100" onclick="systemBackup()">
                                <i class="fas fa-database"></i>
                                <br><small>Sauvegarde</small>
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-success w-100" onclick="viewSystemLogs()">
                                <i class="fas fa-file-alt"></i>
                                <br><small>Logs Système</small>
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="configureSystem()">
                                <i class="fas fa-cogs"></i>
                                <br><small>Configuration</small>
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button class="btn btn-outline-dark w-100" onclick="securityAudit()">
                                <i class="fas fa-shield-alt"></i>
                                <br><small>Audit Sécurité</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chargement de Chart.js depuis CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<!-- Données pour JavaScript -->
<script>
// Transmission des données PHP vers JavaScript
window.adminDashboardData = {
    evolutionMensuelle: @json($evolutionMensuelle ?? []),
    agentsStats: {
        admins: {{ $admins ?? 0 }},
        agentsComptoir: {{ $agentsComptoir ?? 0 }},
        commerciaux: {{ $commerciaux ?? 0 }}
    },
    systemStatus: @json($systemStatus ?? [])
};

console.log('🔥 Données Dashboard Super Admin chargées:', window.adminDashboardData);
</script>

<!-- Script des graphiques pour Super Admin -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Initialisation du dashboard Super Admin...');
    
    // Vérifier Chart.js
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js non chargé');
        return;
    }
    
    console.log('✅ Chart.js disponible pour Super Admin');
    
    // Configuration globale
    Chart.defaults.font.family = 'Segoe UI, system-ui, sans-serif';
    Chart.defaults.color = '#6c757d';
    
    // Couleurs Super Admin - Rouge/Violet theme
    const adminColors = {
        primary: '#dc3545',      // Rouge Super Admin
        secondary: '#6f42c1',    // Violet PSI
        success: '#20c997',      // Vert PSI
        info: '#0dcaf0',         // Bleu PSI
        warning: '#ffc107',      // Jaune admin
        danger: '#dc3545',       // Rouge danger
        dark: '#343a40'          // Gris foncé
    };
    
    // ============== GRAPHIQUE ÉVOLUTION ADMINISTRATIVE ==============
    const evolutionCtx = document.getElementById('adminEvolutionChart');
    if (evolutionCtx) {
        const evolutionData = window.adminDashboardData.evolutionMensuelle || [];
        console.log('📈 Création graphique évolution admin avec', evolutionData.length, 'éléments');
        
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: evolutionData.map(item => item.month || 'N/A'),
                datasets: [{
                    label: 'Administrateurs',
                    data: evolutionData.map(item => item.admins || 0),
                    borderColor: adminColors.primary,
                    backgroundColor: adminColors.primary + '20',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: adminColors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6
                }, {
                    label: 'Agents Internes',
                    data: evolutionData.map(item => item.agents || 0),
                    borderColor: adminColors.success,
                    backgroundColor: adminColors.success + '20',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: adminColors.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Profils Visa',
                    data: evolutionData.map(item => item.profil_visa || 0),
                    borderColor: adminColors.info,
                    backgroundColor: adminColors.info + '20',
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: adminColors.info,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // ============== GRAPHIQUE RÉPARTITION AGENTS ==============
    const agentsCtx = document.getElementById('agentsChart');
    if (agentsCtx) {
        const agentsData = window.adminDashboardData.agentsStats || {};
        console.log('🥧 Création graphique agents avec données:', agentsData);
        
        new Chart(agentsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Administrateurs', 'Agents Comptoir', 'Commerciaux'],
                datasets: [{
                    data: [
                        agentsData.admins || 0,
                        agentsData.agentsComptoir || 0,
                        agentsData.commerciaux || 0
                    ],
                    backgroundColor: [
                        adminColors.primary,   // Rouge pour Admin
                        adminColors.info,      // Bleu pour Agent Comptoir
                        adminColors.success    // Vert pour Commercial
                    ],
                    borderWidth: 4,
                    borderColor: '#fff',
                    hoverOffset: 15,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
    
    console.log('✅ Dashboard Super Admin initialisé avec succès !');
});

// ============== FONCTIONS ADMINISTRATIVES ==============

function refreshAdminStats() {
    console.log('🔄 Actualisation stats admin...');
    fetch('/admin/dashboard/realtime-stats')
        .then(response => response.json())
        .then(data => {
            console.log('📊 Stats actualisées:', data);
            // Actualiser les valeurs des cartes
            location.reload(); // Simple reload pour le moment
        })
        .catch(error => {
            console.error('❌ Erreur actualisation:', error);
        });
}

function exportAdminReport() {
    console.log('📥 Export rapport admin...');
    window.open('/admin/dashboard/export/pdf', '_blank');
}

function openSystemMaintenance() {
    console.log('🔧 Ouverture maintenance système...');
    if(confirm('⚠️ Attention: Activer le mode maintenance va rendre le site inaccessible pour tous les utilisateurs (sauf Super Admin).\n\nContinuer?')) {
        fetch('/admin/maintenance/toggle', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Mode maintenance modifié');
        });
    }
}

function createAdmin() {
    console.log('👤 Créer nouvel admin...');
    window.location.href = '/users/create';
}

function manageRoles() {
    console.log('🔑 Gestion des rôles...');
    window.location.href = '/roles';
}

function systemBackup() {
    console.log('💾 Sauvegarde système...');
    if(confirm('Lancer une sauvegarde complète du système?')) {
        fetch('/admin/backup/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Sauvegarde initiée');
        });
    }
}

function viewSystemLogs() {
    console.log('📄 Logs système...');
    window.location.href = '/log-stat';
}

function configureSystem() {
    console.log('⚙️ Configuration système...');
    window.location.href = '/configuration';
}

function securityAudit() {
    console.log('🔒 Audit sécurité...');
    if(confirm('Lancer un audit de sécurité complet?')) {
        fetch('/admin/security/audit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Audit de sécurité lancé');
        });
    }
}

function viewAdmin(id) {
    console.log('👁️ Voir admin ID:', id);
    window.location.href = '/users/' + id + '/edit';
}

function editAdmin(id) {
    console.log('✏️ Modifier admin ID:', id);
    window.location.href = '/users/' + id + '/edit';
}

function manageAdmins() {
    console.log('👥 Gestion des admins...');
    window.location.href = '/users';
}

// Auto-refresh des stats toutes les 30 secondes
setInterval(function() {
    fetch('/admin/dashboard/realtime-stats')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour silencieusement certaines valeurs
            console.log('🔄 Stats auto-actualisées');
        })
        .catch(error => {
            console.error('❌ Erreur auto-refresh:', error);
        });
}, 30000);

</script>

@endsection