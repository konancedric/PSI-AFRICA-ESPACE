/**
 * Gestion des Agents Internes PSI Africa
 * Version corrigée avec gestion d'erreurs améliorée
 * 
 * @author PSI Africa Dev Team
 * @version 2.0
 */

// Configuration globale
const AgentsManager = {
    // Configuration
    config: {
        debug: true,
        timeout: 30000,
        retryAttempts: 3,
        retryDelay: 1000
    },

    // Variables globales
    agentsTable: null,
    currentAgentId: null,
    ajaxRequests: new Map(),

    // URLs des routes
    routes: {
        datatable: '/users/get-list',
        details: '/user/details',
        update: '/user/update',
        delete: '/user/delete',
        toggleStatus: '/user/edit-etat',
        resetPassword: '/user/reset-password',
        statistics: '/users/statistics'
    },

    /**
     * Initialisation du gestionnaire
     */
    init() {
        this.log('🚀 Initialisation du gestionnaire des agents');
        
        // Configuration CSRF
        this.setupCSRF();
        
        // Initialisation DataTables
        this.initializeDataTable();
        
        // Chargement des statistiques
        this.loadStatistics();
        
        // Initialisation des événements
        this.initializeEvents();
        
        // Actualisation périodique
        this.setupPeriodicRefresh();
        
        // Gestion des erreurs globales
        this.setupErrorHandling();
    },

    /**
     * Configuration du token CSRF
     */
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content')
                }
            });
            this.log('✅ Token CSRF configuré');
        } else {
            this.error('❌ Token CSRF manquant');
        }
    },

    /**
     * Initialisation de DataTables avec gestion d'erreurs robuste
     */
    initializeDataTable() {
        this.log('📊 Initialisation DataTable');
        
        try {
            this.agentsTable = $('#agents_table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true, // Permet de réinitialiser si nécessaire
                ajax: {
                    url: this.routes.datatable,
                    type: 'GET',
                    timeout: this.config.timeout,
                    data: (d) => {
                        // Ajouter les filtres
                        d.type_filter = $('#filterType').val() || '';
                        d.status_filter = $('#filterStatus').val() || '';
                        d.role_filter = $('#filterRole').val() || '';
                        
                        this.log('📤 Paramètres DataTable:', d);
                        return d;
                    },
                    beforeSend: () => {
                        this.showTableLoading(true);
                    },
                    complete: () => {
                        this.showTableLoading(false);
                    },
                    error: (xhr, error, thrown) => {
                        this.handleDataTableError(xhr, error, thrown);
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'name',
                        title: 'Agent',
                        render: (data, type, row) => {
                            return this.renderAgentColumn(data, type, row);
                        }
                    },
                    {
                        data: 'contact_info',
                        name: 'contact',
                        title: 'Contact',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type_user_badge',
                        name: 'type_user',
                        title: 'Type',
                        orderable: false
                    },
                    {
                        data: 'roles',
                        name: 'roles',
                        title: 'Rôles',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => {
                            return this.renderRolesColumn(data, type, row);
                        }
                    },
                    {
                        data: 'grade_categorie',
                        name: 'grade_categorie',
                        title: 'Grade/Catégorie',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'etat',
                        title: 'Statut',
                        orderable: false
                    },
                    {
                        data: 'permissions',
                        name: 'permissions',
                        title: 'Permissions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_info',
                        name: 'created_at',
                        title: 'Créé le'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[7, 'desc']], // Trier par date de création
                pageLength: 25,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                    processing: '<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2"></div>Chargement des agents...</div>',
                    emptyTable: 'Aucun agent trouvé',
                    zeroRecords: 'Aucun agent correspondant aux critères de recherche',
                    error: 'Erreur lors du chargement des données'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                drawCallback: (settings) => {
                    this.onTableDrawComplete(settings);
                },
                errorCallback: (xhr, error, thrown) => {
                    this.handleDataTableError(xhr, error, thrown);
                }
            });

            // Gestionnaires de filtres
            $('#filterType, #filterStatus, #filterRole').on('change', () => {
                this.log('🔍 Application des filtres');
                this.agentsTable.ajax.reload();
            });

            this.log('✅ DataTable initialisé avec succès');

        } catch (error) {
            this.error('❌ Erreur initialisation DataTable:', error);
            this.showTableError('Erreur d\'initialisation du tableau');
        }
    },

    /**
     * Rendu de la colonne Agent
     */
    renderAgentColumn(data, type, row) {
        try {
            let avatar = '';
            if (row.photo && row.photo !== 'NULL' && row.photo !== null) {
                avatar = `<img src="${row.photo}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Photo">`;
            } else {
                const initials = data.name ? data.name.substring(0, 2).toUpperCase() : 'NA';
                avatar = `<div class="rounded-circle bg-primary text-white text-center me-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: bold;">${initials}</div>`;
            }
            
            return `<div class="d-flex align-items-center">
                ${avatar}
                <div>
                    <div class="fw-bold">${data.name || 'Nom non défini'}</div>
                    <small class="text-muted">${data.email || ''}</small>
                </div>
            </div>`;
        } catch (error) {
            this.error('Erreur rendu colonne agent:', error);
            return '<div class="text-danger">Erreur d\'affichage</div>';
        }
    },

    /**
     * Rendu de la colonne Rôles avec fallback
     */
    renderRolesColumn(data, type, row) {
        try {
            // Si les données de rôles sont vides ou nulles
            if (!data || data.trim() === '' || data === 'null' || data === null) {
                // Fallback basé sur le type d'utilisateur
                const typeLabels = {
                    'admin': 'Administrateur',
                    'agent_comptoir': 'Agent Comptoir',
                    'commercial': 'Commercial'
                };
                const typeColors = {
                    'admin': 'danger',
                    'agent_comptoir': 'info',
                    'commercial': 'success'
                };
                const label = typeLabels[row.type_user] || 'Non défini';
                const color = typeColors[row.type_user] || 'secondary';
                return `<span class="badge badge-${color} m-1">${label}</span>`;
            }
            return data;
        } catch (error) {
            this.error('Erreur rendu colonne rôles:', error);
            return '<span class="badge badge-secondary">Erreur</span>';
        }
    },

    /**
     * Gestion des erreurs DataTables
     */
    handleDataTableError(xhr, error, thrown) {
        this.error('❌ Erreur DataTable:', {
            status: xhr.status,
            error: error,
            thrown: thrown,
            responseText: xhr.responseText
        });

        let errorMessage = 'Erreur lors du chargement des données';
        
        if (xhr.status === 0) {
            errorMessage = 'Pas de connexion réseau';
        } else if (xhr.status === 404) {
            errorMessage = 'Endpoint non trouvé (404)';
        } else if (xhr.status === 500) {
            errorMessage = 'Erreur serveur interne (500)';
        } else if (xhr.status === 403) {
            errorMessage = 'Accès refusé (403)';
        } else if (error === 'timeout') {
            errorMessage = 'Timeout de la requête';
        }

        this.showTableError(errorMessage);
        this.showNotification(errorMessage, 'error');
    },

    /**
     * Afficher une erreur dans le tableau
     */
    showTableError(message) {
        const tbody = $('#agents_table tbody');
        tbody.html(`
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>Erreur de chargement</h5>
                        <p>${message}</p>
                        <button class="btn btn-primary" onclick="AgentsManager.refreshAgentsList()">
                            <i class="fas fa-sync-alt me-1"></i> Réessayer
                        </button>
                    </div>
                </td>
            </tr>
        `);
    },

    /**
     * Callback après rechargement du tableau
     */
    onTableDrawComplete(settings) {
        try {
            // Réinitialiser les tooltips
            $('[title]').tooltip();
            
            // Log des informations
            if (settings.json) {
                this.log('📊 Tableau rechargé:', {
                    total: settings.json.recordsTotal,
                    filtered: settings.json.recordsFiltered,
                    displayed: settings.json.data ? settings.json.data.length : 0
                });
            }
        } catch (error) {
            this.error('Erreur callback tableau:', error);
        }
    },

    /**
     * Afficher/masquer le loading du tableau
     */
    showTableLoading(show) {
        const loadingOverlay = $('#table-loading');
        if (show) {
            loadingOverlay.show();
        } else {
            loadingOverlay.hide();
        }
    },

    /**
     * Charger les statistiques avec retry
     */
    async loadStatistics() {
        this.log('📊 Chargement des statistiques');
        
        try {
            const response = await this.makeAjaxRequest('GET', this.routes.statistics);
            
            if (response) {
                this.updateStatistics(response);
                this.log('✅ Statistiques chargées:', response);
            }
        } catch (error) {
            this.error('❌ Erreur chargement statistiques:', error);
            this.updateStatisticsError();
        }
    },

    /**
     * Mettre à jour les statistiques
     */
    updateStatistics(data) {
        this.updateStatElement('#total-agents', data.total_agents || 0);
        this.updateStatElement('#total-admins', data.admins || 0);
        this.updateStatElement('#total-comptoir', data.agents_comptoir || 0);
        this.updateStatElement('#total-commercial', data.commerciaux || 0);
    },

    /**
     * Gérer l'erreur de statistiques
     */
    updateStatisticsError() {
        this.updateStatElement('#total-agents', '?');
        this.updateStatElement('#total-admins', '?');
        this.updateStatElement('#total-comptoir', '?');
        this.updateStatElement('#total-commercial', '?');
    },

    /**
     * Mettre à jour un élément de statistique
     */
    updateStatElement(selector, value) {
        const element = $(selector);
        const spinner = element.find('.spinner-border');
        
        if (spinner.length > 0) {
            spinner.fadeOut(300, function() {
                element.text(value);
                element.fadeIn(300);
            });
        } else {
            element.text(value);
        }
    },

    /**
     * Initialiser les événements
     */
    initializeEvents() {
        this.log('🔧 Initialisation des événements');

        // Formulaires
        $('#editAgentForm').on('submit', (e) => {
            e.preventDefault();
            this.submitEditForm();
        });

        $('#resetPasswordForm').on('submit', (e) => {
            e.preventDefault();
            this.submitResetPassword();
        });

        // Validation mot de passe
        $('#new_password, #confirm_password').on('input', () => {
            this.validatePasswordReset();
        });

        // Bouton édition depuis la vue
        $('#editFromView').on('click', () => {
            if (this.currentAgentId) {
                $('#viewAgentModal').modal('hide');
                setTimeout(() => this.editAgent(this.currentAgentId), 300);
            }
        });
    },

    /**
     * Actions sur les agents
     */
    async editAgent(agentId) {
        this.currentAgentId = agentId;
        this.log('✏️ Édition agent:', agentId);
        
        try {
            const response = await this.makeAjaxRequest('GET', `${this.routes.details}/${agentId}`);
            
            if (response && response.success) {
                this.populateEditForm(response.user);
                $('#editAgentModal').modal('show');
            } else {
                this.showNotification(response?.error || 'Erreur lors du chargement', 'error');
            }
        } catch (error) {
            this.error('Erreur édition agent:', error);
            this.showNotification('Erreur de connexion', 'error');
        }
    },

    async viewAgent(agentId) {
        this.currentAgentId = agentId;
        this.log('👁️ Vue agent:', agentId);
        
        try {
            const response = await this.makeAjaxRequest('GET', `${this.routes.details}/${agentId}`);
            
            if (response && response.success) {
                this.populateViewModal(response.user);
                $('#viewAgentModal').modal('show');
            } else {
                this.showNotification(response?.error || 'Erreur lors du chargement', 'error');
            }
        } catch (error) {
            this.error('Erreur vue agent:', error);
            this.showNotification('Erreur de connexion', 'error');
        }
    },

    async toggleStatus(agentId, newStatus) {
        const action = newStatus == 1 ? 'activer' : 'désactiver';
        
        if (!confirm(`Êtes-vous sûr de vouloir ${action} cet agent ?`)) {
            return;
        }

        try {
            const response = await this.makeAjaxRequest('POST', this.routes.toggleStatus, {
                id: agentId,
                etat: newStatus
            });

            if (response && response.success) {
                this.showNotification(`Agent ${action} avec succès`, 'success');
                this.agentsTable.ajax.reload(null, false);
                this.loadStatistics();
            } else {
                this.showNotification(response?.error || `Erreur lors de l'${action}`, 'error');
            }
        } catch (error) {
            this.error('Erreur toggle status:', error);
            this.showNotification('Erreur de connexion', 'error');
        }
    },

    resetPassword(agentId) {
        this.currentAgentId = agentId;
        $('#reset_user_id').val(agentId);
        $('#resetPasswordForm')[0].reset();
        $('#password_match_indicator')
            .text('Les mots de passe doivent correspondre')
            .removeClass('text-success text-danger')
            .addClass('text-muted');
        $('#resetPasswordModal').modal('show');
    },

    deleteAgent(agentId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet agent ? Cette action est irréversible.')) {
            return;
        }
        window.location.href = `${this.routes.delete}/${agentId}`;
    },

    /**
     * Fonctions utilitaires
     */
    refreshAgentsList() {
        this.log('🔄 Actualisation liste agents');
        if (this.agentsTable) {
            this.agentsTable.ajax.reload();
        }
        this.loadStatistics();
        this.showNotification('Liste actualisée', 'info');
    },

    clearFilters() {
        $('#filterType, #filterStatus, #filterRole').val('').trigger('change');
        this.showNotification('Filtres effacés', 'info');
    },

    exportAgents() {
        this.showNotification('Fonction d\'export en cours de développement', 'info');
    },

    /**
     * Validation mot de passe
     */
    validatePasswordReset() {
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
    },

    /**
     * Soumission des formulaires
     */
    async submitEditForm() {
        try {
            const formData = new FormData($('#editAgentForm')[0]);
            const response = await this.makeAjaxRequest('POST', this.routes.update, formData);

            if (response && response.success) {
                $('#editAgentModal').modal('hide');
                this.agentsTable.ajax.reload(null, false);
                this.loadStatistics();
                this.showNotification('Agent mis à jour avec succès', 'success');
            } else {
                this.showNotification(response?.error || 'Erreur lors de la mise à jour', 'error');
            }
        } catch (error) {
            this.error('Erreur soumission formulaire:', error);
            this.showNotification('Erreur lors de la mise à jour', 'error');
        }
    },

    async submitResetPassword() {
        if (!this.validatePasswordReset()) {
            return;
        }

        try {
            const formData = new FormData($('#resetPasswordForm')[0]);
            const response = await this.makeAjaxRequest('POST', this.routes.resetPassword, formData);

            if (response && response.success) {
                $('#resetPasswordModal').modal('hide');
                this.showNotification('Mot de passe réinitialisé avec succès', 'success');
            } else {
                this.showNotification(response?.error || 'Erreur lors de la réinitialisation', 'error');
            }
        } catch (error) {
            this.error('Erreur reset password:', error);
            this.showNotification('Erreur de connexion', 'error');
        }
    },

    /**
     * Utilitaire AJAX avec retry et timeout
     */
    async makeAjaxRequest(method, url, data = null, options = {}) {
        const requestId = Date.now() + Math.random();
        
        // Annuler la requête précédente si elle existe
        if (this.ajaxRequests.has(url)) {
            this.ajaxRequests.get(url).abort();
        }

        const defaultOptions = {
            method: method,
            url: url,
            timeout: this.config.timeout,
            ...options
        };

        if (data) {
            if (data instanceof FormData) {
                defaultOptions.data = data;
                defaultOptions.processData = false;
                defaultOptions.contentType = false;
            } else {
                defaultOptions.data = JSON.stringify(data);
                defaultOptions.contentType = 'application/json';
            }
        }

        return new Promise((resolve, reject) => {
            const xhr = $.ajax(defaultOptions)
                .done((response) => {
                    this.ajaxRequests.delete(url);
                    resolve(response);
                })
                .fail((xhr, status, error) => {
                    this.ajaxRequests.delete(url);
                    
                    if (status !== 'abort') {
                        this.error('Requête AJAX échouée:', {
                            url: url,
                            status: xhr.status,
                            error: error,
                            response: xhr.responseText
                        });
                        reject(new Error(`${error} (${xhr.status})`));
                    }
                });

            this.ajaxRequests.set(url, xhr);
        });
    },

    /**
     * Peuplement des modaux
     */
    populateEditForm(user) {
        $('#edit_user_id').val(user.id);
        
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nom & Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="${user.name || ''}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="${user.email || ''}" required>
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
        
        // Gérer l'affichage des champs de mot de passe
        $('#changePassword').on('change', function() {
            $('#passwordFields').toggle(this.checked);
            if (!this.checked) {
                $('#passwordFields input').val('');
            }
        });
    },

    populateViewModal(user) {
        const rolesBadges = (user.roles || []).map(role => 
            `<span class="badge bg-primary me-1">${role}</span>`
        ).join('');
        
        const content = `
            <div class="row">
                <div class="col-md-4 text-center">
                    ${user.photo_url ? 
                        `<img src="${user.photo_url}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="Photo">` :
                        `<div class="rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center text-white" style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 2.5rem; font-weight: bold;">${(user.name || 'NA').substring(0, 2).toUpperCase()}</div>`
                    }
                    <h5>${user.name || 'Nom non défini'}</h5>
                    <p class="text-muted">${user.type_user_label || 'Type non défini'}</p>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><td><strong>Email:</strong></td><td>${user.email || 'N/A'}</td></tr>
                        <tr><td><strong>Matricule:</strong></td><td>${user.matricule || 'N/A'}</td></tr>
                        <tr><td><strong>Contact:</strong></td><td>${user.contact || 'N/A'}</td></tr>
                        <tr><td><strong>Statut:</strong></td><td>
                            <span class="badge bg-${user.etat == 1 ? 'success' : 'danger'}">
                                ${user.etat == 1 ? 'Actif' : 'Inactif'}
                            </span>
                        </td></tr>
                        <tr><td><strong>Créé le:</strong></td><td>${user.created_at || 'N/A'}</td></tr>
                        <tr><td><strong>Rôles:</strong></td><td>${rolesBadges || '<span class="text-muted">Aucun rôle</span>'}</td></tr>
                    </table>
                </div>
            </div>
        `;
        
        $('#viewAgentContent').html(content);
    },

    /**
     * Système de notification
     */
    showNotification(message, type = 'success') {
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
    },

    /**
     * Configuration de l'actualisation périodique
     */
    setupPeriodicRefresh() {
        // Actualiser les statistiques toutes les 5 minutes
        setInterval(() => {
            this.loadStatistics();
        }, 300000);

        this.log('⏰ Actualisation périodique configurée (5 min)');
    },

    /**
     * Gestion des erreurs globales
     */
    setupErrorHandling() {
        // Gestion des erreurs AJAX globales
        $(document).ajaxError((event, xhr, settings, error) => {
            if (xhr.status === 419) { // Token CSRF expiré
                this.showNotification('Session expirée. Veuillez actualiser la page.', 'warning');
                setTimeout(() => location.reload(), 3000);
            }
        });

        // Gestion des erreurs JavaScript globales
        window.addEventListener('error', (event) => {
            this.error('Erreur JavaScript globale:', event.error);
        });

        this.log('🛡️ Gestion d\'erreurs globales configurée');
    },

    /**
     * Utilitaires de logging
     */
    log(...args) {
        if (this.config.debug) {
            console.log('[AgentsManager]', ...args);
        }
    },

    error(...args) {
        console.error('[AgentsManager ERROR]', ...args);
    }
};

// Fonctions globales pour compatibilité
window.editAgent = (id) => AgentsManager.editAgent(id);
window.viewAgent = (id) => AgentsManager.viewAgent(id);
window.toggleStatus = (id, status) => AgentsManager.toggleStatus(id, status);
window.resetPassword = (id) => AgentsManager.resetPassword(id);
window.deleteAgent = (id) => AgentsManager.deleteAgent(id);
window.refreshAgentsList = () => AgentsManager.refreshAgentsList();
window.clearFilters = () => AgentsManager.clearFilters();
window.exportAgents = () => AgentsManager.exportAgents();

// Initialisation automatique
$(document).ready(() => {
    AgentsManager.init();
});

// CSS pour les animations
if (!document.getElementById('agents-animations')) {
    const style = document.createElement('style');
    style.id = 'agents-animations';
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
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
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
            border-radius: 8px;
        }
    `;
    document.head.appendChild(style);
}