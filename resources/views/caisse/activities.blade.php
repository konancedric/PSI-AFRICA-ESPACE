@extends('layouts.main')

@section('title', 'Activités Caisse - PSI AFRICA')

@section('content')
<div class="container-fluid p-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">💰 Activités Caisse</h2>
            <p class="text-muted mb-0">Historique complet de toutes les activités de la caisse</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="refreshActivities()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filtres de recherche</h5>
            <div class="row">
                <div class="col-md-3">
                    <label>Recherche</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Action, détails, utilisateur...">
                </div>
                <div class="col-md-2">
                    <label>Type d'action</label>
                    <select id="actionFilter" class="form-control">
                        <option value="">Toutes les actions</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Utilisateur</label>
                    <select id="userFilter" class="form-control">
                        <option value="">Tous les utilisateurs</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Date début</label>
                    <input type="date" id="dateFromFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Date fin</label>
                    <input type="date" id="dateToFilter" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des activités -->
    <div class="card">
        <div class="card-body">
            <div id="activitiesTableContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-4">
                <div id="paginationInfo"></div>
                <nav>
                    <ul class="pagination mb-0" id="paginationControls"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    .activity-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .activity-row {
        transition: background-color 0.2s;
    }

    .activity-row:hover {
        background-color: #f8f9fa;
    }

    .activity-details {
        max-width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-timestamp {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

@push('script')
<script>
    let currentPage = 1;
    let perPage = 50;
    let filters = {
        search: '',
        action: '',
        user_id: '',
        date_from: '',
        date_to: ''
    };

    // Charger les activités au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        loadActivities();

        // Événements pour les filtres
        document.getElementById('searchInput').addEventListener('input', debounce(function() {
            filters.search = this.value;
            currentPage = 1;
            loadActivities();
        }, 500));

        document.getElementById('actionFilter').addEventListener('change', function() {
            filters.action = this.value;
            currentPage = 1;
            loadActivities();
        });

        document.getElementById('userFilter').addEventListener('change', function() {
            filters.user_id = this.value;
            currentPage = 1;
            loadActivities();
        });

        document.getElementById('dateFromFilter').addEventListener('change', function() {
            filters.date_from = this.value;
            currentPage = 1;
            loadActivities();
        });

        document.getElementById('dateToFilter').addEventListener('change', function() {
            filters.date_to = this.value;
            currentPage = 1;
            loadActivities();
        });
    });

    function loadActivities() {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPage,
            ...filters
        });

        console.log('Chargement des activités caisse...', {
            url: `/caisse/activities?${params}`,
            params: Object.fromEntries(params)
        });

        fetch(`/caisse/activities?${params}`)
            .then(response => {
                console.log('Réponse reçue:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data);
                if (data.success) {
                    console.log(`${data.activities.length} activités trouvées`);
                    renderActivities(data.activities);
                    renderPagination(data.pagination);
                    populateFilters(data.filters);
                } else {
                    console.error('Erreur dans les données:', data.message);
                    showError(data.message || 'Erreur lors du chargement des activités');
                }
            })
            .catch(error => {
                console.error('Erreur complète:', error);
                showError(`Erreur de connexion: ${error.message}`);
            });
    }

    function renderActivities(activities) {
        const container = document.getElementById('activitiesTableContainer');

        if (activities.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune activité trouvée</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 15%">Date/Heure</th>
                            <th style="width: 15%">Action</th>
                            <th style="width: 40%">Détails</th>
                            <th style="width: 15%">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        activities.forEach(activity => {
            const date = new Date(activity.created_at);
            const formattedDate = date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const formattedTime = date.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const actionColor = getActionColor(activity.action);
            const userName = activity.user ? activity.user.name : activity.user_name;
            const userMatricule = activity.user ? activity.user.matricule : '';

            html += `
                <tr class="activity-row">
                    <td class="activity-timestamp">
                        <div>${formattedDate}</div>
                        <div class="text-muted">${formattedTime}</div>
                    </td>
                    <td>
                        <span class="activity-badge" style="background-color: ${actionColor}; color: white;">
                            ${escapeHtml(activity.action)}
                        </span>
                    </td>
                    <td class="activity-details">${escapeHtml(activity.details || '-')}</td>
                    <td>
                        <div>${escapeHtml(userName)}</div>
                        ${userMatricule ? `<div class="text-muted small">${escapeHtml(userMatricule)}</div>` : ''}
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function renderPagination(pagination) {
        const infoContainer = document.getElementById('paginationInfo');
        const controlsContainer = document.getElementById('paginationControls');

        // Informations de pagination
        infoContainer.innerHTML = `
            Affichage de ${pagination.from || 0} à ${pagination.to || 0} sur ${pagination.total} activités
        `;

        // Contrôles de pagination
        let controls = '';

        // Bouton Précédent
        controls += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${pagination.current_page - 1}); return false;">
                    Précédent
                </a>
            </li>
        `;

        // Numéros de pages
        const maxPages = 5;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
        let endPage = Math.min(pagination.last_page, startPage + maxPages - 1);

        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        if (startPage > 1) {
            controls += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="changePage(1); return false;">1</a>
                </li>
            `;
            if (startPage > 2) {
                controls += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            controls += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        }

        if (endPage < pagination.last_page) {
            if (endPage < pagination.last_page - 1) {
                controls += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            controls += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="changePage(${pagination.last_page}); return false;">${pagination.last_page}</a>
                </li>
            `;
        }

        // Bouton Suivant
        controls += `
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${pagination.current_page + 1}); return false;">
                    Suivant
                </a>
            </li>
        `;

        controlsContainer.innerHTML = controls;
    }

    function populateFilters(filtersData) {
        const actionFilter = document.getElementById('actionFilter');
        const userFilter = document.getElementById('userFilter');

        // Peupler les types d'actions
        if (filtersData.action_types) {
            let actionOptions = '<option value="">Toutes les actions</option>';
            filtersData.action_types.forEach(action => {
                actionOptions += `<option value="${escapeHtml(action)}">${escapeHtml(action)}</option>`;
            });
            actionFilter.innerHTML = actionOptions;
        }

        // Peupler les utilisateurs
        if (filtersData.users) {
            let userOptions = '<option value="">Tous les utilisateurs</option>';
            filtersData.users.forEach(user => {
                const label = user.matricule ? `${user.name} (${user.matricule})` : user.name;
                userOptions += `<option value="${user.id}">${escapeHtml(label)}</option>`;
            });
            userFilter.innerHTML = userOptions;
        }
    }

    function changePage(page) {
        currentPage = page;
        loadActivities();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function refreshActivities() {
        currentPage = 1;
        loadActivities();
    }

    function clearFilters() {
        filters = {
            search: '',
            action: '',
            user_id: '',
            date_from: '',
            date_to: ''
        };
        document.getElementById('searchInput').value = '';
        document.getElementById('actionFilter').value = '';
        document.getElementById('userFilter').value = '';
        document.getElementById('dateFromFilter').value = '';
        document.getElementById('dateToFilter').value = '';
        currentPage = 1;
        loadActivities();
    }

    function getActionColor(action) {
        const colors = {
            'Entrée Créée': '#28a745',
            'Entrée Modifiée': '#ffc107',
            'Entrée Supprimée': '#dc3545',
            'Sortie Créée': '#17a2b8',
            'Sortie Modifiée': '#fd7e14',
            'Sortie Supprimée': '#dc3545',
            'Clôture Mensuelle': '#6f42c1'
        };

        return colors[action] || '#6c757d';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showError(message) {
        const container = document.getElementById('activitiesTableContainer');
        container.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `;
    }
</script>
@endpush
@endsection
