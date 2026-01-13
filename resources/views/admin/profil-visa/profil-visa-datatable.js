/**
 * ✅ GESTIONNAIRE DATATABLE PROFIL VISA - VERSION CORRIGÉE
 * Fichier séparé pour éviter les conflits de réinitialisation DataTable
 * 
 * Utilisation : Inclure ce fichier après jQuery et DataTables
 * <script src="{{ asset('js/profil-visa-datatable.js') }}"></script>
 */

class ProfilVisaDataTable {
    constructor() {
        this.table = null;
        this.isInitialized = false;
        this.tableSelector = null;
        this.config = {
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            responsive: true,
            pageLength: 25,
            order: [[0, "desc"]], // Tri par date décroissante
            columnDefs: [
                { orderable: false, targets: -1 }, // Pas de tri sur la colonne Action
                { searchable: false, targets: [-1] }
            ],
            processing: false,
            serverSide: false,
            autoWidth: false,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: () => this.attachEvents(),
            initComplete: (settings, json) => {
                console.log('✅ DataTable Profil Visa initialisé avec succès');
                this.isInitialized = true;
                this.attachEvents();
            },
            errorCallback: (xhr, error, thrown) => {
                console.error('❌ Erreur DataTable:', error);
                this.isInitialized = false;
            }
        };
        
        this.init();
    }

    /**
     * Initialisation principale
     */
    init() {
        // Attendre que le DOM soit prêt
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initTable());
        } else {
            this.initTable();
        }
    }

    /**
     * Trouver et initialiser la table
     */
    initTable() {
        try {
            // Chercher la table appropriée
            this.tableSelector = this.findTable();
            
            if (!this.tableSelector) {
                console.warn('⚠️ Aucune table trouvée pour DataTable');
                return;
            }

            // Vérifier si jQuery et DataTables sont disponibles
            if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
                console.warn('⚠️ jQuery ou DataTables non disponible');
                return;
            }

            // Attendre un peu pour éviter les conflits
            setTimeout(() => this.createDataTable(), 300);

        } catch (error) {
            console.error('❌ Erreur initialisation table:', error);
        }
    }

    /**
     * Rechercher la table appropriée
     */
    findTable() {
        const selectors = [
            '#profil_visa_data_table',
            '#data_table',
            'table.dataTable:first',
            '.card-body table:first'
        ];

        for (let selector of selectors) {
            const $table = $(selector);
            if ($table.length > 0 && $table.find('tbody tr').length > 0) {
                console.log(`🎯 Table trouvée: ${selector}`);
                return selector;
            }
        }

        return null;
    }

    /**
     * Créer DataTable
     */
    createDataTable() {
        try {
            const $table = $(this.tableSelector);

            // Vérifier si DataTable est déjà initialisé
            if ($.fn.DataTable.isDataTable(this.tableSelector)) {
                console.log('⚠️ DataTable déjà initialisé, destruction...');
                $table.DataTable().destroy();
                
                // Petit délai après destruction
                setTimeout(() => this.initializeNewTable($table), 100);
            } else {
                this.initializeNewTable($table);
            }

        } catch (error) {
            console.error('❌ Erreur création DataTable:', error);
        }
    }

    /**
     * Initialiser nouvelle instance DataTable
     */
    initializeNewTable($table) {
        try {
            this.table = $table.DataTable(this.config);
            this.isInitialized = true;
            console.log('✅ DataTable créé avec succès');

        } catch (error) {
            console.error('❌ Erreur initialisation DataTable:', error);
            this.isInitialized = false;
            
            // Fallback : continuer sans DataTable
            this.attachEvents();
        }
    }

    /**
     * Attacher les événements aux boutons
     */
    attachEvents() {
        try {
            // Utiliser la délégation d'événements pour les boutons dynamiques
            $(document).off('click.profilVisa', '[data-action]')
                       .on('click.profilVisa', '[data-action]', (e) => {
                           e.preventDefault();
                           this.handleAction(e.currentTarget);
                       });

            // Gestion des boutons avec onclick (legacy)
            $(document).off('click.profilVisa', '[onclick*="confirmDelete"]')
                       .on('click.profilVisa', '[onclick*="confirmDelete"]', (e) => {
                           e.preventDefault();
                           const onclick = e.currentTarget.getAttribute('onclick');
                           if (onclick) {
                               try {
                                   eval(onclick);
                               } catch (evalError) {
                                   console.error('Erreur onclick:', evalError);
                               }
                           }
                       });

            console.log('✅ Événements attachés');

        } catch (error) {
            console.error('❌ Erreur attachement événements:', error);
        }
    }

    /**
     * Gérer les actions des boutons
     */
    handleAction(button) {
        const action = button.getAttribute('data-action');
        const profilId = button.getAttribute('data-profil-id');

        if (!profilId) {
            console.warn('ID profil manquant');
            return;
        }

        switch (action) {
            case 'approve':
                this.approveVisa(profilId);
                break;
            case 'reject':
                this.rejectVisa(profilId);
                break;
            case 'duplicate':
                this.duplicateVisa(profilId);
                break;
            case 'archive':
                this.archiveVisa(profilId);
                break;
            case 'priority':
                this.setPriority(profilId);
                break;
            case 'assign':
                this.assignAgent(profilId);
                break;
            default:
                console.warn('Action inconnue:', action);
        }
    }

    /**
     * Actions spécialisées
     */
    approveVisa(profilId) {
        if (confirm('Êtes-vous sûr de vouloir approuver ce visa ?')) {
            this.submitForm('/profil-visa/approve', { id: profilId });
        }
    }

    rejectVisa(profilId) {
        const reason = prompt('Raison du rejet (obligatoire) :');
        if (reason && reason.trim() !== '') {
            this.submitForm('/profil-visa/reject', { 
                id: profilId, 
                rejection_reason: reason 
            });
        } else if (reason !== null) {
            alert('La raison du rejet est obligatoire.');
        }
    }

    duplicateVisa(profilId) {
        if (confirm('Êtes-vous sûr de vouloir dupliquer ce profil visa ?')) {
            this.submitForm('/profil-visa/duplicate', { id: profilId });
        }
    }

    archiveVisa(profilId) {
        const reason = prompt('Raison de l\'archivage (optionnel) :');
        if (confirm('Êtes-vous sûr de vouloir archiver ce profil visa ?')) {
            const data = { id: profilId };
            if (reason) {
                data.archive_reason = reason;
            }
            this.submitForm('/profil-visa/archive', data);
        }
    }

    setPriority(profilId) {
        const priority = prompt('Niveau de priorité (normal, urgent, tres_urgent) :');
        if (priority && ['normal', 'urgent', 'tres_urgent'].includes(priority)) {
            this.submitForm('/profil-visa/priority', { 
                id: profilId, 
                priority_level: priority 
            });
        } else if (priority !== null) {
            alert('Priorité invalide. Utilisez: normal, urgent, ou tres_urgent');
        }
    }

    assignAgent(profilId) {
        window.location.href = `/profil-visa/assign/${profilId}`;
    }

    /**
     * Soumettre un formulaire
     */
    submitForm(action, data) {
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;

            // Token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = $('meta[name="csrf-token"]').attr('content') || 
                             document.querySelector('input[name="_token"]')?.value;

            form.appendChild(csrfToken);

            // Ajouter les données
            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();

        } catch (error) {
            console.error('Erreur soumission formulaire:', error);
            alert('Erreur lors de l\'envoi de la demande');
        }
    }

    /**
     * Recharger les données DataTable
     */
    reload() {
        if (this.table && this.isInitialized) {
            try {
                this.table.ajax.reload(null, false);
            } catch (error) {
                console.warn('Erreur rechargement DataTable, rechargement page:', error);
                window.location.reload();
            }
        } else {
            window.location.reload();
        }
    }

    /**
     * Détruire DataTable
     */
    destroy() {
        if (this.table && this.isInitialized) {
            try {
                this.table.destroy();
                this.isInitialized = false;
                this.table = null;
                console.log('🧹 DataTable détruit');
            } catch (error) {
                console.warn('Erreur destruction DataTable:', error);
            }
        }

        // Nettoyer les événements
        $(document).off('.profilVisa');
    }

    /**
     * Redessiner DataTable
     */
    redraw() {
        if (this.table && this.isInitialized) {
            try {
                this.table.draw();
            } catch (error) {
                console.warn('Erreur redraw DataTable:', error);
            }
        }
    }

    /**
     * Obtenir l'instance DataTable
     */
    getInstance() {
        return this.table;
    }

    /**
     * Vérifier si DataTable est initialisé
     */
    isReady() {
        return this.isInitialized && this.table !== null;
    }
}

/**
 * Fonction globale d'initialisation
 */
function initProfilVisaDataTable() {
    try {
        // Éviter les multiples instances
        if (window.profilVisaTableInstance) {
            window.profilVisaTableInstance.destroy();
        }
        
        window.profilVisaTableInstance = new ProfilVisaDataTable();
        
        // Fonctions globales pour compatibilité
        window.refreshProfilVisaDataTable = () => {
            if (window.profilVisaTableInstance) {
                window.profilVisaTableInstance.reload();
            }
        };
        
        window.destroyProfilVisaDataTable = () => {
            if (window.profilVisaTableInstance) {
                window.profilVisaTableInstance.destroy();
                window.profilVisaTableInstance = null;
            }
        };
        
    } catch (error) {
        console.error('❌ Erreur initialisation ProfilVisaDataTable:', error);
    }
}

/**
 * Fonction de nettoyage à la fermeture
 */
function cleanupProfilVisaDataTable() {
    if (window.profilVisaTableInstance) {
        window.profilVisaTableInstance.destroy();
        window.profilVisaTableInstance = null;
    }
}

// Auto-initialisation quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProfilVisaDataTable);
} else {
    initProfilVisaDataTable();
}

// Nettoyage à la fermeture
window.addEventListener('beforeunload', cleanupProfilVisaDataTable);

// Export pour utilisation modulaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProfilVisaDataTable;
}