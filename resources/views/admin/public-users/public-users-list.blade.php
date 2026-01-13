@extends('layouts.main')

@section('title', 'Utilisateurs Publics - PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(23, 162, 184, 0.3);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title mb-2" style="font-weight: 700; font-size: 2rem;">
                            <i class="fas fa-users me-3"></i>
                            Utilisateurs Publics
                        </h1>
                        <p class="page-subtitle mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                            Gestion des utilisateurs clients de PSI Africa
                        </p>
                    </div>
                    <div class="page-actions">
                        <button class="btn btn-outline-light btn-lg me-3" onclick="refreshData()" style="border-radius: 12px;">
                            <i class="fas fa-sync-alt me-2"></i> Actualiser
                        </button>
                        <button class="btn btn-light btn-lg" onclick="exportUsers()" style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-download me-2"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border-radius: 20px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="stat-number mb-1" id="totalPublicUsers" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stat-label mb-0" style="font-size: 0.9rem; opacity: 0.9;">Total Utilisateurs</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 20px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="stat-number mb-1" id="activePublicUsers" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stat-label mb-0" style="font-size: 0.9rem; opacity: 0.9;">Utilisateurs Actifs</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #fd7e14 0%, #e0651e 100%); border-radius: 20px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(253, 126, 20, 0.3);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-passport fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="stat-number mb-1" id="usersWithProfiles" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stat-label mb-0" style="font-size: 0.9rem; opacity: 0.9;">Avec Profils Visa</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1 0%, #5a3391 100%); border-radius: 20px; padding: 1.5rem; color: white; box-shadow: 0 6px 20px rgba(111, 66, 193, 0.3);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-day fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="stat-number mb-1" id="newUsersToday" style="font-size: 2rem; font-weight: 700;">-</h3>
                        <p class="stat-label mb-0" style="font-size: 0.9rem; opacity: 0.9;">Nouveaux Aujourd'hui</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filters-card" style="background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="search-box">
                            <label class="form-label fw-semibold">Rechercher un utilisateur</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Nom, email ou téléphone..." style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-primary" type="button" onclick="searchUsers()" style="border-radius: 0 10px 10px 0;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select class="form-select" id="statusFilter" onchange="filterUsers()" style="border-radius: 10px;">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actifs</option>
                            <option value="0">Inactifs</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Profils Visa</label>
                        <select class="form-select" id="profilesFilter" onchange="filterUsers()" style="border-radius: 10px;">
                            <option value="">Tous</option>
                            <option value="with">Avec profils</option>
                            <option value="without">Sans profils</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary" onclick="resetFilters()" style="border-radius: 10px;">
                                <i class="fas fa-times me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des utilisateurs publics -->
    <div class="row">
        <div class="col-12">
            <div class="table-card" style="background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="table-header mb-4">
                    <h5 class="table-title" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                        <i class="fas fa-list me-2 text-info"></i>
                        Liste des Utilisateurs Publics
                    </h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="publicUsersTable" style="border-radius: 12px; overflow: hidden;">
                        <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <tr>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Photo</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Utilisateur</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Contact</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Type</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Profils Visa</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Statut</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Inscription</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Dernière activité</th>
                                <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Actions</th>
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

<!-- Modal pour voir les détails d'un utilisateur -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Détails de l'utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<style>
/* Animations et transitions */
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

.table-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15) !important;
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

.stat-card, .filters-card, .table-card {
    animation: slideInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem !important;
    }
    
    .page-title {
        font-size: 1.5rem !important;
    }
    
    .page-actions {
        margin-top: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem !important;
    }
}

/* Personnalisation DataTables */
.dataTables_wrapper .dataTables_filter input {
    border-radius: 10px !important;
    border: 1px solid #ddd !important;
}

.dataTables_wrapper .dataTables_length select {
    border-radius: 10px !important;
    border: 1px solid #ddd !important;
}

.table tbody tr:hover {
    background-color: rgba(23, 162, 184, 0.05) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser DataTables
    initializeDataTable();
    
    // Charger les statistiques
    loadStatistics();
    
    // Animation d'entrée des cartes
    animateCards();
});

function initializeDataTable() {
    if ($.fn.DataTable.isDataTable('#publicUsersTable')) {
        $('#publicUsersTable').DataTable().destroy();
    }
    
    $('#publicUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('public.users.datatable') }}",
            type: 'GET'
        },
        columns: [
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'contact_info', name: 'contact_info', orderable: false, searchable: false },
            { data: 'user_type', name: 'user_type', orderable: false, searchable: false },
            { data: 'profils_visa_count', name: 'profils_visa_count', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'registration', name: 'registration', orderable: false, searchable: false },
            { data: 'last_activity', name: 'last_activity', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']],
        drawCallback: function(settings) {
            // Réappliquer les tooltips après chaque redraw
            $('[title]').tooltip();
        }
    });
}

function loadStatistics() {
    fetch("{{ route('public.users.statistics') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalPublicUsers').textContent = new Intl.NumberFormat().format(data.total_users_public || 0);
            document.getElementById('activePublicUsers').textContent = new Intl.NumberFormat().format(data.users_actifs || 0);
            document.getElementById('usersWithProfiles').textContent = new Intl.NumberFormat().format(data.avec_profils_visa || 0);
            document.getElementById('newUsersToday').textContent = new Intl.NumberFormat().format(data.nouveaux_aujourd_hui || 0);
        })
        .catch(error => {
            console.error('Erreur chargement statistiques:', error);
        });
}

function animateCards() {
    const cards = document.querySelectorAll('.stat-card, .filters-card, .table-card');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
}

function viewPublicUser(id) {
    fetch(`{{ url('/public-users/details') }}/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                const stats = data.statistics;
                
                let content = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            ${user.photo_user && user.photo_user !== 'NULL' ? 
                                `<img src="${data.photo_url}" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">` :
                                `<div class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; font-size: 2rem; font-weight: bold;">${user.name.substring(0, 2).toUpperCase()}</div>`
                            }
                            <h5>${user.name}</h5>
                            <span class="badge bg-primary">Utilisateur Public</span>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <strong>Informations de contact :</strong>
                                    <ul class="list-unstyled mt-2">
                                        <li><i class="fas fa-envelope text-muted me-2"></i> ${user.email}</li>
                                        ${user.contact ? `<li><i class="fas fa-phone text-muted me-2"></i> ${user.contact}</li>` : ''}
                                    </ul>
                                </div>
                                <div class="col-12 mb-3">
                                    <strong>Statistiques :</strong>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <h6 class="mb-0">${stats.total_profils}</h6>
                                                <small class="text-muted">Profils Visa</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <h6 class="mb-0">${stats.profils_approuves}</h6>
                                                <small class="text-muted">Approuvés</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <strong>Informations système :</strong>
                                    <ul class="list-unstyled mt-2">
                                        <li><i class="fas fa-calendar text-muted me-2"></i> Inscrit le : ${new Date(user.created_at).toLocaleDateString('fr-FR')}</li>
                                        <li><i class="fas fa-clock text-muted me-2"></i> Dernière mise à jour : ${new Date(user.updated_at).toLocaleDateString('fr-FR')}</li>
                                        ${stats.derniere_demande ? `<li><i class="fas fa-passport text-muted me-2"></i> Dernière demande : ${stats.derniere_demande}</li>` : ''}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('userDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
            } else {
                showNotification('Erreur lors du chargement des détails', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
}

function viewUserProfiles(id) {
    // Rediriger vers la page des profils visa avec filtre sur cet utilisateur
    window.location.href = `{{ url('/profil-visa') }}?user_id=${id}`;
}

function toggleUserStatus(id) {
    const newStatus = confirm('Voulez-vous changer le statut de cet utilisateur ?') ? 1 : 0;
    
    fetch("{{ route('public.users.toggle.status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: id, etat: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.success, 'success');
            $('#publicUsersTable').DataTable().ajax.reload();
            loadStatistics();
        } else {
            showNotification(data.error || 'Erreur lors du changement de statut', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', 'error');
    });
}

function deletePublicUser(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        window.location.href = `{{ url('/public-users/delete') }}/${id}`;
    }
}

function searchUsers() {
    const searchTerm = document.getElementById('searchInput').value;
    $('#publicUsersTable').DataTable().search(searchTerm).draw();
}

function filterUsers() {
    // Logique de filtrage personnalisée
    $('#publicUsersTable').DataTable().ajax.reload();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('profilesFilter').value = '';
    $('#publicUsersTable').DataTable().search('').draw();
}

function refreshData() {
    $('#publicUsersTable').DataTable().ajax.reload();
    loadStatistics();
    showNotification('Données actualisées', 'success');
}

function exportUsers() {
    window.location.href = "{{ route('public.users.export') }}?format=excel";
}

function showNotification(message, type = 'success') {
    // Créer une notification toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        border-radius: 12px; 
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        min-width: 300px;
        animation: slideInRight 0.5s ease;
    `;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove après 5 secondes
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// CSS pour l'animation slideInRight
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
`;
document.head.appendChild(style);

// Actualisation automatique toutes les 2 minutes
setInterval(() => {
    loadStatistics();
}, 120000);
</script>
@endsection