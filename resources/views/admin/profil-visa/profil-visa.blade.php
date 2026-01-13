@extends('layouts.main')
@section('title', 'Profil Visa')
@section('content')

<div class="container-fluid">
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="fa fa-folder bg-blue"></i>
                <div class="d-inline">
                    <h5>{{ __('Profil Visa')}}</h5>
                    <span>{{ __('Gestion des Profil Visa')}}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <nav class="breadcrumb-container" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{url('dashboard')}}"><i class="fa fa-folder"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">{{ __('Profil Visa')}}</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- Messages d'alerte -->
    @include('include.message')
    
    @auth
        @php
            $user = Auth::user();
            $isAgent = $user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial']) || 
                        in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial']);
        @endphp

        <!-- ✅ SECTION AGENTS : Filtres de recherche -->
        @if($isAgent)
            @can('manage_profil_visa')
                @if(view()->exists('admin.profil-visa.div-requetor-profil-visa'))
                    @include('admin.profil-visa.div-requetor-profil-visa')
                @endif
            @endcan
        @endif

        <!-- ✅ SECTION UTILISATEURS PUBLICS : Mes Statistiques -->
        @if(!$isAgent)
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">
                            <i class="fa fa-user"></i> Mes Statistiques
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="user-stats-row">
                            <div class="col-md-3">
                                <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                                    <div class="card-body text-center">
                                        <h4 id="total-demandes">
                                            @if(isset($statistiques) && is_array($statistiques))
                                                {{ $statistiques['total_demandes'] ?? 0 }}
                                            @elseif(isset($dataProfilVisa) && method_exists($dataProfilVisa, 'total'))
                                                {{ $dataProfilVisa->total() }}
                                            @elseif(isset($dataProfilVisa) && method_exists($dataProfilVisa, 'count'))
                                                {{ $dataProfilVisa->count() }}
                                            @else
                                                0
                                            @endif
                                        </h4>
                                        <p class="mb-0">Total Demandes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #fd7e14, #e8590c);">
                                    <div class="card-body text-center">
                                        <h4 id="demandes-attente">
                                            {{ (isset($statistiques) && is_array($statistiques)) ? ($statistiques['demandes_en_attente'] ?? 0) : 0 }}
                                        </h4>
                                        <p class="mb-0">En Attente</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                                    <div class="card-body text-center">
                                        <h4 id="demandes-approuvees">
                                            {{ (isset($statistiques) && is_array($statistiques)) ? ($statistiques['demandes_approuvees'] ?? 0) : 0 }}
                                        </h4>
                                        <p class="mb-0">Approuvées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                                    <div class="card-body text-center">
                                        <h4 id="demandes-mois">
                                            {{ (isset($statistiques) && is_array($statistiques)) ? ($statistiques['demandes_ce_mois'] ?? 0) : 0 }}
                                        </h4>
                                        <p class="mb-0">Ce Mois</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ✅ SECTION PRINCIPALE : Liste des profils -->
        <div class="col-md-12">
            <div class="card profil-visa-main-card">
                @if($isAgent)
                    <div class="card-header bg-dark text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>LISTE DES PROFILS VISA</span>
                            <div>
                                <button class="btn btn-primary btn-sm profil-visa-action-btn" onclick="window.location.reload()">
                                    <i class="fa fa-refresh"></i> Actualiser
                                </button>
                                @can('manage_profil_visa')
                                    <button class="btn btn-success btn-sm profil-visa-action-btn" onclick="createNewProfil()">
                                        <i class="fa fa-plus"></i> Nouveau Profil
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>MES DEMANDES DE VISA</span>
                            <div>
                                <button class="btn btn-light btn-sm profil-visa-action-btn" onclick="refreshData()">
                                    <i class="fa fa-refresh"></i> Actualiser
                                </button>
                                <a href="https://psiafrica.ci/je-definis-mon-profil-visa" class="btn btn-success btn-sm profil-visa-action-btn" target="_blank">
                                    <i class="fa fa-plus"></i> Nouvelle Demande
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="card-body">
                    @if(isset($dataProfilVisa) && $dataProfilVisa && (method_exists($dataProfilVisa, 'count') ? $dataProfilVisa->count() > 0 : count($dataProfilVisa) > 0))
                        @if(view()->exists('admin.profil-visa.list-profil-visa'))
                            @include('admin.profil-visa.list-profil-visa')
                        @else
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Le fichier de liste des profils visa est manquant.
                            </div>
                        @endif
                    @else
                        <!-- ✅ MESSAGE SI AUCUNE DONNÉE -->
                        <div class="profil-visa-empty-state">
                            <i class="fa fa-folder-open"></i>
                            @if($isAgent)
                                <h5>Aucun profil visa trouvé</h5>
                                <p>Utilisez les filtres ci-dessus pour rechercher des profils spécifiques.</p>
                            @else
                                <h5>Vous n'avez aucune demande de visa</h5>
                                <p>Cliquez sur "Nouvelle Demande" pour créer votre première demande de visa.</p>
                                <a href="https://psiafrica.ci/je-definis-mon-profil-visa" class="btn btn-primary profil-visa-action-btn" target="_blank">
                                    <i class="fa fa-plus"></i> Créer ma première demande
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- ✅ PAGINATION -->
                @if(isset($dataProfilVisa) && $dataProfilVisa && method_exists($dataProfilVisa, 'links'))
                    <div class="card-footer">
                        {{ $dataProfilVisa->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- ✅ SECTION NON AUTHENTIFIÉ -->
        <div class="col-md-12">
            <div class="alert alert-warning text-center">
                <i class="fa fa-lock fa-2x mb-3"></i>
                <h5>Accès restreint</h5>
                <p>Vous devez être connecté pour voir vos profils visa.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fa fa-sign-in"></i> Se connecter
                </a>
            </div>
        </div>
    @endauth
</div>
</div>

@push('script')
<script>
// ✅ CORRECTION CRITIQUE : Variable globale pour éviter la réinitialisation DataTable
let profilVisaDataTable = null;
let dataTableInitialized = false;

$(document).ready(function() {
console.log('🟢 Profil Visa page loaded');

// ✅ CORRECTION PRINCIPALE : Attendre que le DOM soit complètement chargé
setTimeout(function() {
    initializeProfilVisaDataTableSafe();
}, 500); // Délai pour s'assurer que tout est chargé

// ✅ Actualiser les statistiques pour les utilisateurs publics
@if(isset($isAgent) && !$isAgent)
    updateUserStats();
    setInterval(updateUserStats, 30000); // Actualiser toutes les 30 secondes
@endif

// ✅ Gestion des alertes Bootstrap
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
});

// ✅ FONCTION CORRIGÉE : Initialisation DataTable sécurisée SANS réinitialisation
function initializeProfilVisaDataTableSafe() {
try {
    console.log('🔄 Tentative d\'initialisation DataTable...');
    
    // ✅ CORRECTION CRITIQUE : Vérifier si déjà initialisé
    if (dataTableInitialized) {
        console.log('⚠️ DataTable déjà initialisé, ignorer...');
        return;
    }
    
    // Vérifier si DataTable est disponible et si la table existe
    if (typeof $.fn.DataTable === 'undefined') {
        console.log('❌ DataTable non disponible');
        return;
    }
    
    // Chercher la table dans l'ordre de priorité
    let $table = null;
    if ($('#data_table').length) {
        $table = $('#data_table');
    } else if ($('#profil_visa_data_table').length) {
        $table = $('#profil_visa_data_table');
    } else if $('.table').first().length) {
        $table = $('.table').first();
    }
    
    if (!$table || $table.length === 0) {
        console.log('❌ Aucune table trouvée pour DataTable');
        return;
    }
    
    console.log('✅ Table trouvée:', $table.attr('id') || 'table sans ID');
    
    // ✅ CORRECTION CRITIQUE : Vérifier si la table est déjà un DataTable
    if ($.fn.DataTable.isDataTable($table[0])) {
        console.log('⚠️ Table déjà initialisée comme DataTable, destruction...');
        $table.DataTable().destroy();
        $table.empty(); // Vider le contenu si nécessaire
    }
    
    // ✅ Attendre un moment pour éviter les conflits
    setTimeout(function() {
        try {
            // Initialiser le nouveau DataTable avec configuration sécurisée
            profilVisaDataTable = $table.DataTable({
                "destroy": true, // ✅ IMPORTANT : Permet la destruction automatique
                "retrieve": false, // ✅ Ne pas récupérer l'instance existante
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json",
                    "emptyTable": "Aucune donnée disponible dans le tableau",
                    "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
                    "infoFiltered": "(filtré à partir de _MAX_ entrées au total)",
                    "lengthMenu": "Afficher _MENU_ entrées",
                    "loadingRecords": "Chargement...",
                    "processing": "Traitement...",
                    "search": "Rechercher:",
                    "zeroRecords": "Aucune donnée correspondante trouvée",
                    "paginate": {
                        "first": "Premier",
                        "last": "Dernier",
                        "next": "Suivant",
                        "previous": "Précédent"
                    }
                },
                "responsive": true,
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tout"]],
                "order": [[ 0, "desc" ]], // Tri par première colonne (date) décroissante
                "columnDefs": [
                    { 
                        "orderable": false, 
                        "targets": -1, // Dernière colonne (Actions)
                        "className": "text-center"
                    },
                    {
                        "className": "text-center",
                        "targets": [1, 2, 3] // Centrer certaines colonnes
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "processing": true,
                "deferRender": true,
                "stateSave": true, // Sauvegarder l'état
                "stateDuration": 60 * 60 * 24, // 24 heures
                "initComplete": function(settings, json) {
                    console.log('✅ DataTable Profil Visa initialisé avec succès');
                    dataTableInitialized = true;
                    
                    // Attacher les événements après initialisation
                    attachProfilVisaEvents();
                },
                "drawCallback": function(settings) {
                    console.log('🔄 DataTable redessiné');
                    // Réattacher les événements après chaque redraw
                    attachProfilVisaEvents();
                },
                "error": function(xhr, error, thrown) {
                    console.error('❌ Erreur DataTable:', error);
                    dataTableInitialized = false;
                }
            });
            
        } catch (initError) {
            console.error('❌ Erreur lors de l\'initialisation DataTable:', initError);
            dataTableInitialized = false;
            
            // Fallback : fonctionnement sans DataTable
            console.log('📋 Fonctionnement en mode table simple');
            attachProfilVisaEvents();
        }
    }, 200); // Petit délai pour éviter les conflits
    
} catch (error) {
    console.error('❌ Erreur critique initialisation DataTable:', error);
    dataTableInitialized = false;
    // Continuer sans DataTable
    attachProfilVisaEvents();
}
}

// ✅ FONCTION : Attacher les événements aux boutons de manière sécurisée
function attachProfilVisaEvents() {
try {
    console.log('🔗 Attachement des événements...');
    
    // Événements pour les boutons d'action avec gestion des erreurs
    $(document).off('click', '.btn[onclick*="confirmDelete"]').on('click', '.btn[onclick*="confirmDelete"]', function(e) {
        e.preventDefault();
        try {
            let onclick = $(this).attr('onclick');
            if (onclick) {
                eval(onclick);
            }
        } catch (evalError) {
            console.error('Erreur exécution onclick:', evalError);
        }
    });
    
    // Événements pour les autres boutons d'action
    $(document).off('click', '.action-btn').on('click', '.action-btn', function(e) {
        e.preventDefault();
        try {
            let action = $(this).data('action');
            let id = $(this).data('id');
            
            if (action && id) {
                window[action](id);
            }
        } catch (actionError) {
            console.error('Erreur exécution action:', actionError);
        }
    });
    
    // Tooltips Bootstrap si disponible
    if (typeof $().tooltip === 'function') {
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    }
    
    console.log('✅ Événements attachés avec succès');
    
} catch (error) {
    console.error('❌ Erreur attachement événements:', error);
}
}

// ✅ FONCTION : Mettre à jour les statistiques utilisateur
function updateUserStats() {
@if(isset($isAgent) && !$isAgent)
    $.ajax({
        url: '/api/user/realtime-stats',
        method: 'GET',
        timeout: 10000, // 10 secondes timeout
        success: function(response) {
            try {
                if (response.success && response.stats) {
                    $('#total-demandes').text(response.stats.total_demandes || 0);
                    $('#demandes-attente').text(response.stats.demandes_en_attente || 0);
                    $('#demandes-approuvees').text(response.stats.demandes_approuvees || 0);
                    $('#demandes-mois').text(response.stats.demandes_ce_mois || 0);
                    
                    console.log('📊 Statistiques mises à jour:', response.stats);
                }
            } catch (parseError) {
                console.warn('⚠️ Erreur parsing stats:', parseError);
            }
        },
        error: function(xhr, status, error) {
            console.warn('⚠️ Erreur mise à jour stats:', error);
            // Ne pas afficher d'alerte pour éviter de déranger l'utilisateur
        }
    });
@endif
}

// ✅ FONCTION : Actualiser les données de manière sécurisée
function refreshData() {
try {
    showNotification('info', 'Actualisation en cours...');
    
    // Si DataTable existe et est initialisé, le recharger
    if (profilVisaDataTable && dataTableInitialized && $.fn.DataTable.isDataTable(profilVisaDataTable)) {
        profilVisaDataTable.ajax.reload(null, false);
    } else {
        // Sinon, recharger la page
        window.location.reload();
    }
} catch (error) {
    console.error('Erreur refresh:', error);
    window.location.reload();
}
}

// ✅ FONCTION : Créer un nouveau profil (pour les agents)
function createNewProfil() {
window.open('https://psiafrica.ci/je-definis-mon-profil-visa', '_blank');
}

// ✅ FONCTION : Voir les détails d'un profil
function viewProfilDetails(profilId) {
if (profilId) {
    window.location.href = '/profil-visa/view/' + profilId;
}
}

// ✅ FONCTION : Confirmer la suppression
function confirmDelete(profilId, numeroProfilVisa) {
if (!profilId) {
    showNotification('error', 'ID du profil manquant');
    return;
}

if (confirm('Êtes-vous sûr de vouloir supprimer le profil visa "' + (numeroProfilVisa || 'N/A') + '" ?\n\nCette action est irréversible !')) {
    try {
        // Créer et soumettre le formulaire de suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profil.visa.delete") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = profilId;
        
        form.appendChild(csrfToken);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    } catch (error) {
        console.error('Erreur suppression:', error);
        showNotification('error', 'Erreur lors de la suppression');
    }
}
}

// ✅ FONCTION : Afficher des notifications toast
function showNotification(type, message, callback) {
try {
    let alertClass = type === 'success' ? 'alert-success' : 
                    type === 'error' ? 'alert-danger' : 
                    type === 'warning' ? 'alert-warning' : 'alert-info';
    
    let iconClass = type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show notification-toast" role="alert" 
                style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
            <i class="fas ${iconClass} me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-hide après 5 secondes
    setTimeout(function() {
        notification.fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Callback si fourni
    if (callback && typeof callback === 'function') {
        notification.on('click', callback);
    }
} catch (error) {
    console.error('Erreur notification:', error);
    // Fallback simple
    alert(message);
}
}

// ✅ FONCTION: Actualiser DataTable de manière sécurisée
function refreshProfilVisaDataTable() {
try {
    if (profilVisaDataTable && dataTableInitialized && $.fn.DataTable.isDataTable(profilVisaDataTable)) {
        profilVisaDataTable.ajax.reload(null, false); // Recharger sans réinitialiser la pagination
    } else {
        console.log('DataTable non disponible, rechargement de la page...');
        window.location.reload();
    }
} catch (error) {
    console.error('Erreur refresh DataTable:', error);
    window.location.reload();
}
}

// ✅ GESTION DES ERREURS AJAX GLOBALES
$(document).ajaxError(function(event, xhr, settings, thrownError) {
if (xhr.status === 401) {
    showNotification('error', 'Votre session a expiré. Vous allez être redirigé vers la page de connexion.');
    setTimeout(() => window.location.href = '/login', 3000);
} else if (xhr.status === 403) {
    showNotification('error', 'Vous n\'avez pas l\'autorisation d\'effectuer cette action.');
} else if (xhr.status >= 500) {
    console.error('Erreur serveur:', xhr.status, thrownError);
    showNotification('error', 'Une erreur serveur est survenue. Veuillez réessayer plus tard.');
}
});

// ✅ GESTION DES MESSAGES FLASH LARAVEL
@if(session('success'))
showNotification('success', '{{ session("success") }}');
@endif

@if(session('error'))
showNotification('error', '{{ session("error") }}');
@endif

@if(session('warning'))
showNotification('warning', '{{ session("warning") }}');
@endif

@if(session('info'))
showNotification('info', '{{ session("info") }}');
@endif

// ✅ NETTOYAGE LORS DE LA FERMETURE DE LA PAGE
$(window).on('beforeunload', function() {
if (profilVisaDataTable && dataTableInitialized) {
    try {
        profilVisaDataTable.destroy();
        dataTableInitialized = false;
    } catch (error) {
        console.log('Nettoyage DataTable lors de la fermeture');
    }
}
});

console.log('✅ Profil Visa page fully initialized with DataTable protection');
</script>
@endpush

<!-- ✅ STYLES CSS ADDITIONNELS -->
@push('style')
<style>
.notification-toast {
box-shadow: 0 4px 8px rgba(0,0,0,0.1);
border-radius: 8px;
border: none;
}

.page-header-title i {
margin-right: 10px;
}

.card-body {
padding: 1.5rem;
}

.badge {
font-size: 0.85em;
}

.card {
transition: all 0.3s ease;
border-radius: 8px;
box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Amélioration des cartes de statistiques */
.card h4 {
font-size: 2rem;
font-weight: bold;
}

.card p {
font-size: 0.9rem;
opacity: 0.9;
}

/* Style pour les boutons */
.btn {
border-radius: 20px;
padding: 8px 16px;
font-weight: 500;
}

.btn-sm {
padding: 6px 12px;
font-size: 0.85rem;
}

/* Table responsive */
@media (max-width: 768px) {
.table-responsive {
    font-size: 0.9rem;
}

.btn-group .btn {
    padding: 4px 8px;
    font-size: 0.8rem;
}

.card h4 {
    font-size: 1.5rem;
}
}

/* Animation des cartes de statistiques */
.card {
animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
from {
    opacity: 0;
    transform: translateY(20px);
}
to {
    opacity: 1;
    transform: translateY(0);
}
}

/* Style pour le header du tableau */
.card-header {
border-radius: 8px 8px 0 0;
font-weight: 600;
}

/* Style pour les icônes */
.fa {
margin-right: 5px;
}

/* Progress bar pour les statistiques */
.progress {
height: 8px;
border-radius: 4px;
margin-top: 10px;
}

/* Style pour les badges de statut */
.badge {
padding: 6px 12px;
border-radius: 15px;
font-weight: 500;
}

/* Amélioration de l'affichage des colonnes */
.table th,
.table td {
vertical-align: middle;
padding: 12px 8px;
}

.table th {
background-color: #f8f9fa;
border-top: none;
font-weight: 600;
font-size: 0.9rem;
}

/* Hover effect sur les lignes du tableau */
.table tbody tr:hover {
background-color: rgba(0,123,255,0.05);
}

/* Style pour les boutons d'action */
.btn-group .btn {
margin-right: 3px;
}

.btn-group .btn:last-child {
margin-right: 0;
}

/* ✅ STYLES DATATABLE PERSONNALISÉS AMÉLIORÉS */
.dataTables_wrapper {
padding: 15px 0;
}

.dataTables_wrapper .dataTables_filter {
float: right;
margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter input {
margin-left: 0.5rem;
padding: 5px 10px;
border: 1px solid #ddd;
border-radius: 4px;
}

.dataTables_wrapper .dataTables_length {
float: left;
margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_length select {
margin: 0 0.5rem;
padding: 5px;
border: 1px solid #ddd;
border-radius: 4px;
}

.table.dataTable {
border-collapse: separate;
border-spacing: 0;
width: 100% !important;
}

.table.dataTable thead th {
border-bottom: 2px solid #dee2e6;
background-color: #f8f9fa;
font-weight: 600;
}

.table.dataTable tbody tr:hover {
background-color: rgba(0,123,255,0.05);
}

/* ✅ Animation de chargement améliorée */
.dataTables_processing {
position: absolute;
top: 50%;
left: 50%;
width: 250px;
margin-left: -125px;
margin-top: -30px;
text-align: center;
padding: 15px 20px;
background: rgba(255, 255, 255, 0.95);
border: 1px solid #ddd;
border-radius: 8px;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
font-weight: 500;
color: #495057;
}

.dataTables_processing:before {
content: '';
display: inline-block;
width: 20px;
height: 20px;
margin-right: 10px;
border: 2px solid #f3f3f3;
border-top: 2px solid #007bff;
border-radius: 50%;
animation: spin 1s linear infinite;
vertical-align: middle;
}

@keyframes spin {
0% { transform: rotate(0deg); }
100% { transform: rotate(360deg); }
}

/* Style pour les messages d'erreur DataTable */
.dataTables_empty {
text-align: center;
font-style: italic;
color: #6c757d;
padding: 3rem;
background-color: #f8f9fa;
}

/* ✅ Style pour la pagination */
.dataTables_wrapper .dataTables_paginate {
float: right;
margin-top: 1rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
padding: 6px 12px;
margin-left: 2px;
border: 1px solid #ddd;
border-radius: 4px;
background: white;
color: #495057;
cursor: pointer;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
background-color: #e9ecef;
border-color: #adb5bd;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
background-color: #007bff;
border-color: #007bff;
color: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
color: #6c757d;
cursor: not-allowed;
background-color: #f8f9fa;
}

/* ✅ Info DataTable */
.dataTables_wrapper .dataTables_info {
float: left;
margin-top: 1rem;
color: #6c757d;
font-size: 0.9rem;
}

/* Style responsive pour DataTable */
@media (max-width: 768px) {
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    float: none;
    text-align: center;
    margin: 0.5rem 0;
}

.table.dataTable {
    font-size: 0.85rem;
}
}
</style>
@endpush
@endsection