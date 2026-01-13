<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthHomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CommercialDashboardController;
use App\Http\Controllers\ComptoirDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentsController;
use App\Http\Controllers\PublicUsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionsDiagnosticController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoriesImagesController;
use App\Http\Controllers\GalerieVideoController;
use App\Http\Controllers\GalerieImageController;
use App\Http\Controllers\SousCategoriesController;
use App\Http\Controllers\EntreprisesController;
use App\Http\Controllers\VillesController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ActualitesController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\TemoignagesController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\PartenairesController;
use App\Http\Controllers\DocumentsVoyageController;
use App\Http\Controllers\ReservationAchatController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfilVisaController;
use App\Http\Controllers\ParrainagesController;
use App\Http\Controllers\ForfaitsController;
use App\Http\Controllers\StatutsController;
use App\Http\Controllers\StatutsEtatController;
use App\Http\Controllers\SlidersController;
use App\Http\Controllers\SouscrireForfaitsController;
use App\Http\Controllers\CRMDashboardController;
use App\Http\Controllers\CRMClientsController;
use App\Http\Controllers\CRMInvoicesController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\MessagerieController;
use App\Http\Controllers\ContractSignatureController;
use App\Http\Controllers\CaisseController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes - PSI Africa - ✅ CORRECTION COMPLÈTE TOUTES ROUTES
|--------------------------------------------------------------------------
| ✅ CORRECTION CRITIQUE : Ajout de la route manquante profil.visa.add.message
| ✅ TOUTES LES ROUTES MANQUANTES AJOUTÉES
| ✅ CORRECTION DES ERREURS 404 DE LA SIDEBAR
*/

// ==================== ROUTES PUBLIQUES ====================

// ✅ SIGNATURE DE CONTRAT - ROUTES PUBLIQUES (Sans authentification)
Route::prefix('signature')->name('signature.')->withoutMiddleware(['auth'])->group(function() {
    Route::get('/{token}', [ContractSignatureController::class, 'showSignaturePage'])->name('show');
    Route::post('/{token}', [ContractSignatureController::class, 'processSignature'])->name('process');
    Route::get('/{token}/status', [ContractSignatureController::class, 'checkTokenStatus'])->name('status');
});

// ✅ CONSULTATION DE CONTRAT - ROUTES PUBLIQUES (Sans authentification)
Route::prefix('contrats')->name('contracts.')->withoutMiddleware(['auth'])->group(function() {
    Route::get('/view/{token}', [CRMController::class, 'showContract'])->name('view');
    Route::get('/download-pdf/{token}', [CRMController::class, 'downloadContractPDF'])->name('download-pdf');
});

// ✅ FACTURATION - ROUTE PUBLIQUE (Sans authentification)
// ⚠️ IMPORTANT : Ces routes sont PUBLIQUES et ne doivent PAS nécessiter d'authentification
// Route de test
Route::get('/facturation-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Route publique OK - Pas besoin de connexion!',
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->name : 'Non connecté',
        'middlewares' => Route::current()->middleware()
    ]);
})->withoutMiddleware(['auth']);

Route::get('/facturation/{token}', [CRMInvoicesController::class, 'showPublic'])
    ->name('facturation.show')
    ->withoutMiddleware(['auth']);

// ✅ Route supprimée - Tout est maintenant unifié sous /facturation/{token}
// Le même lien /facturation fonctionne pour les tokens de client et les tokens de facture individuelle
// Route::get('/portail-client/{token}', [CRMClientsController::class, 'showPortal'])
//     ->name('client.portal')
//     ->withoutMiddleware(['auth']);

Route::post('/facturation/{token}/validate', [CRMInvoicesController::class, 'validateByClient'])
    ->name('facturation.validate')
    ->withoutMiddleware(['auth']);

Route::post('/facturation/{token}/sign-receipt', [CRMInvoicesController::class, 'signReceipt'])
    ->name('facturation.sign-receipt')
    ->withoutMiddleware(['auth']);

Route::get('/', function () {
    return redirect('/dashboard');
});

// Routes d'authentification
Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login'])->name('login.post');
Route::get('register', [RegisterController::class,'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class,'register'])->name('register.post');
Route::get('login-1', [LoginController::class,'showLoginForm'])->name('login.page');
Route::get('register/pro', [HomeController::class,'registerpro'])->name('register.pro');
Route::post('register/pro', [RegisterController::class,'storeregisterpro'])->name('register.pro.post');

// Routes de récupération de mot de passe
Route::get('password/forget', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.forget');
Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.update');

// Routes publiques additionnelles
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// ✅ CORRECTION CRITIQUE : Route FAQs publique qui manquait
Route::get('/faq', [FaqsController::class, 'publicIndex'])->name('faq.public');

// ==================== ROUTES RESERVATION (Documents) ====================
Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::post('/reservation/store', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/reservation/list', [ReservationController::class, 'getList'])->name('reservation.list');
Route::post('/reservation/generate-word', [ReservationController::class, 'generateWord'])->name('reservation.generateWord');
Route::post('/reservation/generate-pdf', [ReservationController::class, 'generatePDF'])->name('reservation.generatePDF');
Route::get('/reservation/{id}', [ReservationController::class, 'show'])->name('reservation.show');
Route::delete('/reservation/{id}', [ReservationController::class, 'destroy'])->name('reservation.destroy');

// ==================== ROUTES AUTHENTIFIÉES ====================
Route::group(['middleware' => 'auth'], function(){
    
    // Déconnexion et utilitaires
    Route::get('/logout', [LoginController::class,'logout'])->name('logout');
    Route::post('/logout', [LoginController::class,'logout'])->name('logout.post');
    
    // Clear cache - seulement pour les admins
    Route::group(['middleware' => ['permission:manage_system_config']], function(){
        Route::get('/clear-cache', [HomeController::class,'clearCache'])->name('clear.cache');
    });

    // ==================== DASHBOARDS PRINCIPAUX ====================
    
    // Dashboard principal avec redirection automatique
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/dashboard/realtime-stats', [DashboardController::class,'getRealtimeStats'])->name('dashboard.realtime');
    Route::get('/dashboard/detailed-stats', [DashboardController::class,'getDetailedStats'])->name('dashboard.detailed');
    Route::get('/dashboard/export/{format}', [DashboardController::class,'exportStats'])->name('dashboard.export');
    Route::get('/dashboard/health-check', [DashboardController::class,'getSystemHealth'])->name('dashboard.health');

    // ==================== CAISSE ====================
    // Toutes les routes de la caisse sont protégées par le middleware check.caisse.access
    Route::middleware(['check.caisse.access'])->group(function() {
        Route::get('/caisse', [CaisseController::class,'index'])->name('caisse.index');
        Route::get('/caisse/api/users', [CaisseController::class,'getUsers'])->name('caisse.api.users');

        // Routes API pour les données de caisse
        Route::prefix('caisse/api')->group(function() {
            // Entrées
            Route::get('/entrees', [CaisseController::class,'getEntrees'])->name('caisse.api.entrees.index');
            Route::get('/entrees/ref/{ref}', [CaisseController::class,'getEntreeByRef'])->name('caisse.api.entrees.byref');
            Route::get('/entrees/{uuid}', [CaisseController::class,'getEntree'])->name('caisse.api.entrees.show');
            Route::post('/entrees', [CaisseController::class,'storeEntree'])->name('caisse.api.entrees.store');
            Route::put('/entrees/{uuid}', [CaisseController::class,'updateEntree'])->name('caisse.api.entrees.update');
            Route::delete('/entrees/{uuid}', [CaisseController::class,'deleteEntree'])->name('caisse.api.entrees.delete');

            // Sorties
            Route::get('/sorties', [CaisseController::class,'getSorties'])->name('caisse.api.sorties.index');
            Route::post('/sorties', [CaisseController::class,'storeSortie'])->name('caisse.api.sorties.store');
            Route::put('/sorties/{uuid}', [CaisseController::class,'updateSortie'])->name('caisse.api.sorties.update');
            Route::delete('/sorties/{uuid}', [CaisseController::class,'deleteSortie'])->name('caisse.api.sorties.delete');

            // Statistiques
            Route::get('/stats', [CaisseController::class,'getStats'])->name('caisse.api.stats');

            // Mois disponibles pour le filtre
            Route::get('/mois-disponibles', [CaisseController::class,'getMoisDisponibles'])->name('caisse.api.mois-disponibles');

            // Permissions
            Route::get('/my-permissions', [CaisseController::class,'getMyPermissions'])->name('caisse.api.my-permissions');
            Route::put('/users/{userId}/permissions', [CaisseController::class,'updateUserPermissions'])->name('caisse.api.update-permissions');

            // Blocage/Déblocage utilisateurs
            Route::post('/users/{userId}/toggle-block', [CaisseController::class,'toggleUserBlock'])->name('caisse.api.toggle-block');

            // Clients CRM avec factures
            Route::get('/clients', [CaisseController::class,'getClients'])->name('caisse.api.clients');

            // Enregistrer un paiement CRM depuis la caisse
            Route::post('/invoices/{invoiceId}/payment', [CaisseController::class,'recordCRMPayment'])->name('caisse.api.crm-payment');

            // Clôture mensuelle
            Route::get('/cloture/check/{mois}', [CaisseController::class,'checkCloture'])->name('caisse.api.cloture.check');
            Route::post('/cloture/cloturer', [CaisseController::class,'cloturerMois'])->name('caisse.api.cloture.cloturer');
            Route::get('/cloture/donnees/{mois}', [CaisseController::class,'getDonneesCloturees'])->name('caisse.api.cloture.donnees');
        });

        // Vue des activités Caisse
        Route::get('/caisse/activities-view', function() {
            return view('caisse.activities');
        })->name('caisse.activities.view');

        // API des activités Caisse
        Route::get('/caisse/activities', [CaisseController::class, 'getActivities'])->name('caisse.activities');
    });

    // Routes pour utilisateurs publics
    Route::get('/mes-demandes', [AuthHomeController::class,'mesDemandes'])->name('mes.demandes');
    Route::get('/profile', [AuthHomeController::class,'profile'])->name('profile');
    
    Route::group(['middleware' => ['auth']], function(){
        Route::get('/mes-demandes/details/{id}', [AuthHomeController::class,'viewDemande'])->name('mes.demandes.details');
        Route::get('/mes-demandes/create', [AuthHomeController::class,'createDemande'])->name('mes.demandes.create');
        Route::get('/mes-demandes/stats', [AuthHomeController::class,'getRealtimeUserStats'])->name('mes.demandes.stats');
        Route::get('/profil-visa/details/{id}', [AuthHomeController::class,'viewDemande'])->name('profil.visa.details');

        // Routes pour la gestion des dossiers clients
        Route::get('/mes-dossiers', [\App\Http\Controllers\MesDossiersController::class, 'index'])->name('mes-dossiers');
        Route::post('/mes-dossiers/upload', [\App\Http\Controllers\MesDossiersController::class, 'upload'])->name('mes-dossiers.upload');
        Route::get('/mes-dossiers/download/{id}', [\App\Http\Controllers\MesDossiersController::class, 'download'])->name('mes-dossiers.download');
        Route::delete('/mes-dossiers/delete/{id}', [\App\Http\Controllers\MesDossiersController::class, 'delete'])->name('mes-dossiers.delete');

        // Routes pour les factures et paiements
        Route::get('/mes-factures', [\App\Http\Controllers\MesFacturesController::class, 'index'])->name('mes-factures');
        Route::get('/mes-factures/{id}', [\App\Http\Controllers\MesFacturesController::class, 'show'])->name('mes-factures.show');
        Route::get('/mes-factures/{id}/pdf', [\App\Http\Controllers\MesFacturesController::class, 'downloadPdf'])->name('mes-factures.pdf');
    });

    // Dashboard Admin
    Route::group(['middleware' => ['permission:view_dashboard_admin']], function(){
        Route::get('/admin/dashboard', [AdminDashboardController::class,'index'])->name('admin.dashboard');
        Route::get('/admin/dashboard/realtime-stats', [AdminDashboardController::class,'getRealtimeStats'])->name('admin.dashboard.realtime');
        Route::get('/admin/dashboard/detailed-stats', [AdminDashboardController::class,'getDetailedStats'])->name('admin.dashboard.detailed');
        Route::get('/admin/dashboard/export/{format}', [AdminDashboardController::class,'exportReport'])->name('admin.dashboard.export');
        Route::get('/admin/dashboard/health-check', [AdminDashboardController::class,'systemHealthCheck'])->name('admin.dashboard.health');
    });

    // Routes Admin - Gestion des Dossiers Clients
    Route::group(['prefix' => 'admin/dossiers-clients', 'middleware' => ['auth']], function(){
        Route::get('/', [\App\Http\Controllers\AdminDossiersController::class, 'index'])->name('admin.dossiers.index');
        Route::get('/client/{clientId}', [\App\Http\Controllers\AdminDossiersController::class, 'showClient'])->name('admin.dossiers.client');
        Route::post('/client/{clientId}/upload', [\App\Http\Controllers\AdminDossiersController::class, 'store'])->name('admin.dossiers.store');
        Route::get('/download/{id}', [\App\Http\Controllers\AdminDossiersController::class, 'download'])->name('admin.dossiers.download');
        Route::delete('/delete/{id}', [\App\Http\Controllers\AdminDossiersController::class, 'destroy'])->name('admin.dossiers.delete');
        Route::post('/mark-processed/{id}', [\App\Http\Controllers\AdminDossiersController::class, 'markAsProcessed'])->name('admin.dossiers.mark-processed');
        Route::get('/search-clients', [\App\Http\Controllers\AdminDossiersController::class, 'searchClients'])->name('admin.dossiers.search-clients');
    });

    // ==================== ✅ PROFILS VISA - ROUTES COMPLÈTES AVEC CORRECTION ====================
    Route::group(['prefix' => 'profil-visa', 'middleware' => ['auth']], function(){
        // Routes de base
        Route::get('/', [ProfilVisaController::class,'index'])->name('profil.visa.index');
        Route::post('/', [ProfilVisaController::class,'index'])->name('profil.visa.filter');
        Route::get('/view/{id}', [ProfilVisaController::class,'view'])->name('profil.visa.view');
        Route::get('/statistics', [ProfilVisaController::class,'getStatistics'])->name('profil.visa.statistics');
        
        // Création
        Route::get('/create', [ProfilVisaController::class,'createForm'])->name('profil.visa.create');
        Route::post('/create', [ProfilVisaController::class,'create'])->name('profil.visa.store');
        
        // ✅ CORRECTION PRINCIPALE : Routes pour ajouter un message avec LA BONNE ROUTE
        Route::post('/add-message', [ProfilVisaController::class,'getAddMessageProfilVisa'])->name('profil.visa.add.message');
        Route::post('/add-message-profil-visa', [ProfilVisaController::class,'getAddMessageProfilVisa'])->name('profil.visa.add.message.alt');
        
        // ✅ Routes pour changer le statut
        Route::post('/add-statuts-etat', [ProfilVisaController::class,'addStatutsEtat'])->name('profil.visa.add.statuts.etat');
        Route::post('/update-status', [ProfilVisaController::class,'addStatutsEtat'])->name('profil.visa.update.status');
        
        // Modification
        Route::group(['middleware' => ['permission:edit_profil_visa|manage_profil_visa']], function(){
            Route::get('/edit/{id}', [ProfilVisaController::class,'editForm'])->name('profil.visa.edit');
            Route::post('/update', [ProfilVisaController::class,'update'])->name('profil.visa.update');
        });
        
        // Suppression individuelle - Tous les agents avec la permission
Route::group(['middleware' => ['permission:delete_profil_visa']], function(){
    Route::post('/delete', [ProfilVisaController::class,'delete'])->name('profil.visa.delete');
    Route::get('/delete/{id}', [ProfilVisaController::class,'deleteOKOK'])->name('profil.visa.delete.confirm');
});

// Suppression multiple - UNIQUEMENT Admin et Super Admin avec permission
Route::group(['middleware' => ['role:Super Admin|Admin', 'permission:delete_profil_visa']], function(){
    Route::post('/mass-delete', [ProfilVisaController::class,'massDelete'])->name('profil.visa.mass.delete');
    Route::post('/mass-delete-confirm', [ProfilVisaController::class,'massDeleteConfirm'])->name('profil.visa.mass.delete.confirm');
});
    });

    // ==================== ✅ GESTION DES UTILISATEURS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_user|manage_user']], function(){
        Route::get('users', [UserController::class,'index'])->name('users.index');
        Route::get('agents', [AgentsController::class,'index'])->name('agents.index');
        
        // Routes datatable
        Route::get('/user/get-list', [UserController::class,'getUserList'])->name('users.datatable');
        Route::get('/users/list', [UserController::class,'getUserList'])->name('users.list');
        Route::get('/agents/list', [AgentsController::class,'getAgentsList'])->name('agents.list');
        Route::get('/agents/get-list', [AgentsController::class,'getAgentsList'])->name('agents.datatable');
        
        // Routes statistiques
        Route::get('/users/statistics', [UserController::class,'getStatistics'])->name('users.statistics');
        Route::get('/agents/statistics', [AgentsController::class,'getStatistics'])->name('agents.statistics');
        
        // Création
        Route::group(['middleware' => ['permission:create_user|manage_user']], function(){
            Route::get('/user/create', [UserController::class,'create'])->name('users.create');
            Route::get('/users/create', [UserController::class,'create'])->name('create-user');
            Route::get('/agents/create', [AgentsController::class,'createForm'])->name('agents.create');
            Route::post('/user/create', [UserController::class,'store'])->name('users.store');
            Route::post('/users/store', [UserController::class,'store'])->name('store-user');
            Route::post('/agents/store', [AgentsController::class,'create'])->name('agents.store');
        });
        
        // Modification
        Route::group(['middleware' => ['permission:edit_user|manage_user']], function(){
            Route::get('/user/{id}', [UserController::class,'edit'])->name('users.edit');
            Route::get('/user/edit/{id}', [UserController::class,'edit'])->name('edit-user');
            Route::get('/users/{id}/edit', [UserController::class,'edit'])->name('users.edit.show');
            Route::get('/agents/{id}/edit', [AgentsController::class,'editForm'])->name('agents.edit');
            Route::post('/user/update', [UserController::class,'update'])->name('users.update');
            Route::post('/users/update', [UserController::class,'update'])->name('update-user');
            Route::post('/agents/update', [AgentsController::class,'update'])->name('agents.update');
            Route::put('/users/{id}', [UserController::class,'update'])->name('users.update.put');
            Route::post('/user/edit-etat', [UserController::class,'editetat'])->name('users.toggle.status');
            Route::post('/users/edit-etat', [UserController::class,'editetat'])->name('users.edit.status');
            Route::post('/users/toggle-status', [UserController::class,'editetat'])->name('users.status.toggle');
            Route::post('/agents/toggle-status', [AgentsController::class,'toggleStatus'])->name('agents.toggle.status');
        });
        
        // Suppression
        Route::group(['middleware' => ['permission:delete_user|manage_user']], function(){
            Route::get('/user/delete/{id}', [UserController::class,'delete'])->name('users.delete');
            Route::delete('/user/{id}', [UserController::class,'delete'])->name('delete-user');
            Route::delete('/users/{id}', [UserController::class,'delete'])->name('users.destroy');
            Route::get('/agents/delete/{id}', [AgentsController::class,'delete'])->name('agents.delete');
        });
        
        // Routes API détails
        Route::get('/user/details/{id}', [UserController::class,'getUserDetails'])->name('users.details');
        Route::get('/users/{id}/details', [UserController::class,'getUserDetails'])->name('users.details.show');
        Route::get('/agents/{id}/details', [AgentsController::class,'getAgentDetails'])->name('agents.details');
        Route::post('/user/reset-password', [UserController::class,'resetPassword'])->name('users.reset.password');
        Route::post('/users/reset-password', [UserController::class,'resetPassword'])->name('users.password.reset');
        Route::post('/agents/reset-password', [AgentsController::class,'resetPassword'])->name('agents.reset.password');
        
        // Routes export
        Route::get('/users/export/{format}', [UserController::class,'export'])->name('users.export');
        Route::get('/agents/export/{format}', [AgentsController::class,'export'])->name('agents.export');
    });

    // ==================== ✅ CLIENTS PUBLICS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_clients|manage_clients']], function(){
        Route::get('/list-clients', [PublicUsersController::class,'clientsList'])->name('clients.list');
        Route::get('/public-users', [PublicUsersController::class,'index'])->name('public.users.index');
        Route::get('/clients', [PublicUsersController::class,'clientsDashboard'])->name('clients.dashboard');
        Route::get('/list-users', [UserController::class,'usersList'])->name('users.list');
        Route::get('/clients/get-list', [PublicUsersController::class,'getClientsList'])->name('clients.datatable');
        Route::get('/clients/statistics', [PublicUsersController::class,'getStatistics'])->name('clients.statistics');
        
        Route::group(['middleware' => ['permission:manage_clients']], function(){
            Route::post('/clients/update-status', [PublicUsersController::class,'updateStatus'])->name('clients.update.status');
            Route::get('/clients/{id}/details', [PublicUsersController::class,'getClientDetails'])->name('clients.details');
        });
    });

    // ==================== ✅ GESTION DES RÔLES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_role|manage_role']], function(){
        Route::get('/roles', [RolesController::class,'index'])->name('roles.index');
        Route::get('/role/get-list', [RolesController::class,'getRoleList'])->name('roles.datatable');
        Route::get('/roles/statistics', [RolesController::class,'getStatistics'])->name('roles.statistics');
        
        Route::group(['middleware' => ['permission:edit_role|manage_role']], function(){
            Route::get('/roles/{id}/edit', [RolesController::class,'edit'])->name('roles.edit');
            Route::get('/role/edit/{id}', [RolesController::class,'edit'])->name('role.edit');
            Route::post('/role/update', [RolesController::class,'update'])->name('roles.update');
            Route::post('/roles/update', [RolesController::class,'update'])->name('roles.update.post');
            Route::put('/roles/{id}', [RolesController::class,'update'])->name('roles.update.put');
        });
        
        Route::group(['middleware' => ['permission:create_role|manage_role']], function(){
            Route::post('/role/create', [RolesController::class,'create'])->name('roles.store');
            Route::post('/roles/create', [RolesController::class,'create'])->name('roles.create');
        });
        
        Route::group(['middleware' => ['permission:delete_role|manage_role']], function(){
            Route::get('/role/delete/{id}', [RolesController::class,'delete'])->name('roles.delete');
            Route::get('/roles/{id}/delete', [RolesController::class,'delete'])->name('roles.delete.confirm');
            Route::delete('/roles/{id}', [RolesController::class,'delete'])->name('roles.destroy');
        });
        
        // Routes détails
        Route::get('/role/details/{id}', [RolesController::class,'getRoleDetails'])->name('roles.details');
        Route::get('/roles/{id}/details', [RolesController::class,'getRoleDetails'])->name('roles.details.show');
    });

    // ==================== ✅ PERMISSIONS - ROUTES COMPLÈTES AVEC CORRECTIONS ====================
    Route::group(['middleware' => ['permission:view_permission|manage_permission']], function(){
        Route::get('/permission', [PermissionController::class,'index'])->name('permissions.index');
        Route::get('/permissions', [PermissionController::class,'index'])->name('permissions.list');
        Route::get('/permission/get-list', [PermissionController::class,'getPermissionList'])->name('permissions.datatable');
        Route::get('/permissions/statistics', [PermissionController::class,'getStatistics'])->name('permissions.statistics');
        
        // ✅ ROUTES MANQUANTES CRITIQUES AJOUTÉES
        Route::post('/permissions/create-base', [PermissionController::class,'createBasePermissions'])->name('permissions.create.base');
        Route::get('/permissions/health-check', [PermissionController::class,'checkPermissionsSystemHealth'])->name('permissions.health');
        
        Route::group(['middleware' => ['permission:create_permission|manage_permission']], function(){
            Route::post('/permission/create', [PermissionController::class,'create'])->name('permissions.store');
        });
        
        Route::group(['middleware' => ['permission:edit_permission|manage_permission']], function(){
            Route::post('/permission/update', [PermissionController::class,'update'])->name('permissions.update');
        });
        
        Route::group(['middleware' => ['permission:delete_permission|manage_permission']], function(){
            Route::get('/permission/delete/{id}', [PermissionController::class,'delete'])->name('permissions.delete');
        });
        
        Route::get('/permission/details/{id}', [PermissionController::class,'getPermissionDetails'])->name('permissions.details');
        
        // Routes critiques pour les permissions de rôles
        Route::get('/get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole'])->name('permissions.badge');
        Route::post('/get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole'])->name('permissions.badge.post');
        Route::get('/get-role-permissions', [PermissionController::class,'getPermissionBadgeByRole'])->name('permissions.role');
        Route::post('/get-role-permissions', [PermissionController::class,'getPermissionBadgeByRole'])->name('permissions.role.post');
        Route::get('/role/{id}/permissions', [PermissionController::class,'getPermissionBadgeByRole'])->name('role.permissions');
        Route::post('/role/{id}/permissions', [PermissionController::class,'getPermissionBadgeByRole'])->name('role.permissions.post');
    });

    // ==================== ✅ DASHBOARD COMMERCIAL - ROUTES COMPLÈTES ====================
    Route::group(['prefix' => 'commercial', 'middleware' => ['permission:view_dashboard_commercial']], function(){
        Route::get('/dashboard', [CommercialDashboardController::class,'index'])->name('commercial.dashboard');
        Route::post('/dashboard/filter', [CommercialDashboardController::class,'filterByPeriod'])->name('commercial.dashboard.filter');
        Route::get('/dashboard/realtime-stats', [CommercialDashboardController::class,'getRealtimeStats'])->name('commercial.dashboard.realtime');
        Route::get('/dashboard/export/{format}', [CommercialDashboardController::class,'exportFilteredData'])->name('commercial.dashboard.export');
        Route::get('/clients', [CommercialDashboardController::class,'clientsDashboard'])->name('commercial.clients');
        Route::get('/statistiques', [HomeController::class,'commercialStatistiques'])->name('commercial.statistiques');
        Route::get('/exports', function() { return view('commercial.exports'); })->name('commercial.exports');
    });
    
    // ==================== ✅ DASHBOARD COMPTOIR - ROUTES COMPLÈTES ====================
    Route::group(['prefix' => 'comptoir', 'middleware' => ['permission:view_dashboard_comptoir']], function(){
        Route::get('/dashboard', [ComptoirDashboardController::class,'index'])->name('comptoir.dashboard');
        Route::post('/dashboard/filter', [ComptoirDashboardController::class,'filterByPeriod'])->name('comptoir.dashboard.filter');
        Route::get('/dashboard/realtime-stats', [ComptoirDashboardController::class,'getRealtimeStats'])->name('comptoir.dashboard.realtime');
        Route::get('/dashboard/profils-a-traiter', [ComptoirDashboardController::class,'getProfilsATraiter'])->name('comptoir.dashboard.profils');
        Route::post('/dashboard/traitement-rapide', [ComptoirDashboardController::class,'traitementRapide'])->name('comptoir.dashboard.traitement');
        Route::get('/dashboard/performance-stats', [ComptoirDashboardController::class,'getPerformanceStats'])->name('comptoir.dashboard.performance');
        Route::get('/export/{format}', [ComptoirDashboardController::class,'exportPerformance'])->name('comptoir.export');
    });

    // ==================== ✅ SERVICES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_services|manage_services']], function(){
        Route::get('/services', [ServicesController::class,'index'])->name('services.index');
        Route::get('/services/get-list', [ServicesController::class,'getServicesList'])->name('services.datatable');
        Route::get('/services/statistics', [ServicesController::class,'getStatistics'])->name('services.statistics');
        
        Route::group(['middleware' => ['permission:create_services|manage_services']], function(){
            Route::get('/services/create', function() { return view('admin.services.create'); })->name('services.create');
            Route::post('/services/create', [ServicesController::class,'create'])->name('services.store');
        });
        
        Route::group(['middleware' => ['permission:edit_services|manage_services']], function(){
            Route::get('/services/{id}/edit', function($id) { return view('admin.services.edit', compact('id')); })->name('services.edit');
            Route::post('/services/update', [ServicesController::class,'update'])->name('services.update');
        });
        
        Route::group(['middleware' => ['permission:delete_services|manage_services']], function(){
            Route::get('/services/delete/{id}', [ServicesController::class,'deleteOK'])->name('services.delete');
        });
        
        // Routes manquantes services
        Route::get('/services/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('services.details');
        Route::get('/services/export/{format}', [ServicesController::class,'export'])->name('services.export');
    });

    // ==================== ✅ FORFAITS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_forfaits|manage_forfaits']], function(){
        Route::get('/forfaits', [ForfaitsController::class,'index'])->name('forfaits.index');
        Route::get('/forfaits/get-list', [ForfaitsController::class,'getForfaitsList'])->name('forfaits.datatable');
        Route::get('/forfaits/statistics', [ForfaitsController::class,'getStatistics'])->name('forfaits.statistics');
        Route::get('/forfaits/analytics', [ForfaitsController::class,'getAnalytics'])->name('forfaits.analytics');
        
        Route::group(['middleware' => ['permission:create_forfaits|manage_forfaits']], function(){
            Route::get('/forfaits/create', function() { return view('admin.forfaits.create'); })->name('forfaits.create');
            Route::post('/forfaits/create', [ForfaitsController::class,'create'])->name('forfaits.store');
        });
        
        Route::group(['middleware' => ['permission:edit_forfaits|manage_forfaits']], function(){
            Route::get('/forfaits/{id}/edit', function($id) { return view('admin.forfaits.edit', compact('id')); })->name('forfaits.edit');
            Route::post('/forfaits/update', [ForfaitsController::class,'update'])->name('forfaits.update');
        });
        
        Route::group(['middleware' => ['permission:delete_forfaits|manage_forfaits']], function(){
            Route::get('/forfaits/delete/{id}', [ForfaitsController::class,'deleteOK'])->name('forfaits.delete');
        });
        
        // Routes manquantes forfaits
        Route::get('/forfaits/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('forfaits.details');
        Route::get('/forfaits/export/{format}', [ForfaitsController::class,'export'])->name('forfaits.export');
    });

    // ==================== ✅ SOUSCRIPTIONS FORFAITS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_souscrire_forfaits|manage_souscrire_forfaits']], function(){
        Route::get('/souscrire-forfaits', [SouscrireForfaitsController::class,'index'])->name('souscrire.forfaits.index');
        Route::get('/souscrire-forfaits/get-list', [SouscrireForfaitsController::class,'getSouscrireForfaitsList'])->name('souscrire.forfaits.datatable');
        Route::get('/souscrire-forfaits/statistics', [SouscrireForfaitsController::class,'getStatistics'])->name('souscrire.forfaits.statistics');
        
        Route::group(['middleware' => ['permission:create_souscrire_forfaits|manage_souscrire_forfaits']], function(){
            Route::get('/souscrire-forfaits/create', function() { return view('admin.souscrire-forfaits.create'); })->name('souscrire.forfaits.create');
            Route::post('/souscrire-forfaits/create', [SouscrireForfaitsController::class,'create'])->name('souscrire.forfaits.store');
        });
        
        Route::group(['middleware' => ['permission:edit_souscrire_forfaits|manage_souscrire_forfaits']], function(){
            Route::post('/souscrire-forfaits/update', [SouscrireForfaitsController::class,'update'])->name('souscrire.forfaits.update');
        });
        
        Route::group(['middleware' => ['permission:delete_souscrire_forfaits|manage_souscrire_forfaits']], function(){
            Route::get('/souscrire-forfaits/delete/{id}', [SouscrireForfaitsController::class,'delete'])->name('souscrire.forfaits.delete');
        });
        
        Route::get('/souscrire-forfaits/{id}/details', [SouscrireForfaitsController::class,'getSouscriptionDetails'])->name('souscrire.forfaits.details');
        Route::get('/souscrire-forfaits/export/{format}', [SouscrireForfaitsController::class,'export'])->name('souscrire.forfaits.export');
    });

    // ==================== ✅ RENDEZ-VOUS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_rendez_vous|manage_rendez_vous']], function(){
        Route::get('/rendez-vous', [RendezVousController::class,'index'])->name('rendez.vous.index');
        Route::get('/rendez-vous/get-list', [RendezVousController::class,'getRendezVousList'])->name('rendez.vous.datatable');
        Route::get('/rendez-vous/statistics', [RendezVousController::class,'getStatistics'])->name('rendez.vous.statistics');
        Route::get('/rendez-vous/calendar', [RendezVousController::class,'getCalendarData'])->name('rendez.vous.calendar');
        
        Route::group(['middleware' => ['permission:create_rendez_vous|manage_rendez_vous']], function(){
            Route::get('/rendez-vous/create', function() { return view('admin.rendez-vous.create'); })->name('rendez.vous.create');
            Route::post('/rendez-vous/create', [RendezVousController::class,'create'])->name('rendez.vous.store');
        });
        
        Route::group(['middleware' => ['permission:edit_rendez_vous|manage_rendez_vous']], function(){
            Route::get('/rendez-vous/{id}/edit', function($id) { return view('admin.rendez-vous.edit', compact('id')); })->name('rendez.vous.edit');
            Route::post('/rendez-vous/update', [RendezVousController::class,'update'])->name('rendez.vous.update');
        });
        
        Route::group(['middleware' => ['permission:delete_rendez_vous|manage_rendez_vous']], function(){
            Route::get('/rendez-vous/delete/{id}', [RendezVousController::class,'delete'])->name('rendez.vous.delete');
        });
    });

    // ==================== ✅ DOCUMENTS VOYAGE - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_documents_voyage|manage_documents_voyage']], function(){
        Route::get('/documents-voyage', [DocumentsVoyageController::class,'index'])->name('documents.voyage.index');
        Route::get('/documents-voyage/get-list', [DocumentsVoyageController::class,'getDocumentsVoyageList'])->name('documents.voyage.datatable');
        Route::get('/documents-voyage/statistics', [DocumentsVoyageController::class,'getStatistics'])->name('documents.voyage.statistics');
        
        Route::group(['middleware' => ['permission:create_documents_voyage|manage_documents_voyage']], function(){
            Route::get('/documents-voyage/create', function() { return view('admin.documents-voyage.create'); })->name('documents.voyage.create');
            Route::post('/documents-voyage/create', [DocumentsVoyageController::class,'create'])->name('documents.voyage.store');
        });
        
        Route::group(['middleware' => ['permission:edit_documents_voyage|manage_documents_voyage']], function(){
            Route::post('/documents-voyage/update', [DocumentsVoyageController::class,'update'])->name('documents.voyage.update');
        });
        
        Route::group(['middleware' => ['permission:delete_documents_voyage|manage_documents_voyage']], function(){
            Route::get('/documents-voyage/delete/{id}', [DocumentsVoyageController::class,'delete'])->name('documents.voyage.delete');
        });
        
        Route::get('/documents-voyage/{id}/details', [DocumentsVoyageController::class,'getDocumentDetails'])->name('documents.voyage.details');
        Route::get('/documents-voyage/export/{format}', [DocumentsVoyageController::class,'export'])->name('documents.voyage.export');
    });

    // ==================== ✅ RÉSERVATIONS ACHAT - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_reservation_achat|manage_reservation_achat']], function(){
        Route::get('/reservation-achat', [ReservationAchatController::class,'index'])->name('reservation.achat.index');
        Route::get('/reservation-achat/get-list', [ReservationAchatController::class,'getReservationAchatList'])->name('reservation.achat.datatable');
        Route::get('/reservation-achat/statistics', [ReservationAchatController::class,'getStatistics'])->name('reservation.achat.statistics');
        
        Route::group(['middleware' => ['permission:create_reservation_achat|manage_reservation_achat']], function(){
            Route::get('/reservation-achat/create', function() { return view('admin.reservation-achat.create'); })->name('reservation.achat.create');
            Route::post('/reservation-achat/create', [ReservationAchatController::class,'create'])->name('reservation.achat.store');
        });
        
        Route::group(['middleware' => ['permission:edit_reservation_achat|manage_reservation_achat']], function(){
            Route::post('/reservation-achat/update', [ReservationAchatController::class,'update'])->name('reservation.achat.update');
        });
        
        Route::group(['middleware' => ['permission:delete_reservation_achat|manage_reservation_achat']], function(){
            Route::get('/reservation-achat/delete/{id}', [ReservationAchatController::class,'deleteOK'])->name('reservation.achat.delete');
        });
        
        Route::get('/reservation-achat/{id}/details', [ReservationAchatController::class,'getReservationDetails'])->name('reservation.achat.details');
        Route::get('/reservation-achat/export/{format}', [ReservationAchatController::class,'export'])->name('reservation.achat.export');
    });

    // ==================== ✅ RÉSERVATIONS (BILLETS & HÔTELS) - ROUTES COMPLÈTES ====================
    // Note: Routes principales définies en dehors du middleware auth pour accès public

    // ==================== ✅ PARTENAIRES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_partenaires|manage_partenaires']], function(){
        Route::get('/partenaires', [PartenairesController::class,'index'])->name('partenaires.index');
        Route::get('/partenaires/get-list', [PartenairesController::class,'getPartenairesList'])->name('partenaires.datatable');
        Route::get('/partenaires/statistics', [PartenairesController::class,'getStatistics'])->name('partenaires.statistics');
        
        Route::group(['middleware' => ['permission:create_partenaires|manage_partenaires']], function(){
            Route::get('/partenaires/create', function() { return view('admin.partenaires.create'); })->name('partenaires.create');
            Route::post('/partenaires/create', [PartenairesController::class,'create'])->name('partenaires.store');
        });
        
        Route::group(['middleware' => ['permission:edit_partenaires|manage_partenaires']], function(){
            Route::get('/partenaires/{id}/edit', function($id) { return view('admin.partenaires.edit', compact('id')); })->name('partenaires.edit');
            Route::post('/partenaires/update', [PartenairesController::class,'update'])->name('partenaires.update');
        });
        
        Route::group(['middleware' => ['permission:delete_partenaires|manage_partenaires']], function(){
            Route::get('/partenaires/delete/{id}', [PartenairesController::class,'deleteOP'])->name('partenaires.delete');
        });
        
        // Routes manquantes partenaires
        Route::get('/partenaires/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('partenaires.details');
        Route::get('/partenaires/export/{format}', [PartenairesController::class,'export'])->name('partenaires.export');
    });

    // ==================== ✅ TÉMOIGNAGES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_temoignages|manage_temoignages']], function(){
        Route::get('/temoignages', [TemoignagesController::class,'index'])->name('temoignages.index');
        Route::get('/temoignages/get-list', [TemoignagesController::class,'getTemoignagesList'])->name('temoignages.datatable');
        Route::get('/temoignages/statistics', [TemoignagesController::class,'getStatistics'])->name('temoignages.statistics');
        
        Route::group(['middleware' => ['permission:create_temoignages|manage_temoignages']], function(){
            Route::get('/temoignages/create', function() { return view('admin.temoignages.create'); })->name('temoignages.create');
            Route::post('/temoignages/create', [TemoignagesController::class,'create'])->name('temoignages.store');
        });
        
        Route::group(['middleware' => ['permission:edit_temoignages|manage_temoignages']], function(){
            Route::get('/temoignages/{id}/edit', function($id) { return view('admin.temoignages.edit', compact('id')); })->name('temoignages.edit');
            Route::post('/temoignages/update', [TemoignagesController::class,'update'])->name('temoignages.update');
        });
        
        Route::group(['middleware' => ['permission:delete_temoignages|manage_temoignages']], function(){
            Route::get('/temoignages/delete/{id}', [TemoignagesController::class,'deleteOK'])->name('temoignages.delete');
        });
        
        // Routes manquantes témoignages
        Route::get('/temoignages/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('temoignages.details');
        Route::get('/temoignages/export/{format}', [TemoignagesController::class,'export'])->name('temoignages.export');
    });

    // ==================== ✅ ACTUALITÉS - ROUTES COMPLÈTES CORRIGÉES ====================
    Route::group(['middleware' => ['permission:view_actualites|manage_actualites']], function(){
        Route::get('/actualites', [ActualitesController::class,'index'])->name('actualites.index');
        Route::get('/actualites/get-list', [ActualitesController::class,'getActualitesList'])->name('actualites.datatable');
        Route::get('/actualites/statistics', [ActualitesController::class,'getStatistics'])->name('actualites.statistics');
        
        Route::group(['middleware' => ['permission:create_actualites|manage_actualites']], function(){
            Route::get('/actualites/create', function() { return view('admin.actualites.create'); })->name('actualites.create');
            Route::post('/actualites/create', [ActualitesController::class,'create'])->name('actualites.store');
        });
        
        Route::group(['middleware' => ['permission:edit_actualites|manage_actualites']], function(){
            Route::get('/actualites/{id}/edit', function($id) { return view('admin.actualites.edit', compact('id')); })->name('actualites.edit');
            Route::post('/actualites/update', [ActualitesController::class,'update'])->name('actualites.update');
        });
        
        Route::group(['middleware' => ['permission:delete_actualites|manage_actualites']], function(){
            Route::get('/actualites/delete/{id}', [ActualitesController::class,'deleteOK'])->name('actualites.delete');
        });
        
        // ✅ Routes manquantes actualités
        Route::get('/actualites/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('actualites.details');
        Route::get('/actualites/export/{format}', [ActualitesController::class,'export'])->name('actualites.export');
        Route::get('/actualites/{id}/view', [ActualitesController::class,'view'])->name('actualites.view');
        Route::post('/actualites/toggle-status', [ActualitesController::class,'toggleStatus'])->name('actualites.toggle.status');
    });

    // ==================== ✅ GALERIE VIDÉO - ROUTES COMPLÈTES CORRIGÉES ====================
    Route::group(['middleware' => ['permission:view_galerie_video|manage_galerie_video']], function(){
        Route::get('/galerie-video', [GalerieVideoController::class,'index'])->name('galerie.video.index');
        Route::get('/galerie-video/get-list', [GalerieVideoController::class,'getGalerieVideoList'])->name('galerie.video.datatable');
        Route::get('/galerie-video/statistics', [GalerieVideoController::class,'getStatistics'])->name('galerie.video.statistics');
        
        Route::group(['middleware' => ['permission:create_galerie_video|manage_galerie_video']], function(){
            Route::get('/galerie-video/create', function() { return view('admin.galerie-video.create'); })->name('galerie.video.create');
            Route::post('/galerie-video/create', [GalerieVideoController::class,'create'])->name('galerie.video.store');
        });
        
        Route::group(['middleware' => ['permission:edit_galerie_video|manage_galerie_video']], function(){
            Route::get('/galerie-video/{id}/edit', function($id) { return view('admin.galerie-video.edit', compact('id')); })->name('galerie.video.edit');
            Route::post('/galerie-video/update', [GalerieVideoController::class,'update'])->name('galerie.video.update');
        });
        
        Route::group(['middleware' => ['permission:delete_galerie_video|manage_galerie_video']], function(){
            Route::post('/galerie-video/delete', [GalerieVideoController::class,'delete'])->name('galerie.video.delete');
        });
        
        Route::group(['middleware' => ['permission:moderate_galerie_video']], function(){
            Route::post('/galerie-video/moderate', [GalerieVideoController::class,'moderate'])->name('galerie.video.moderate');
        });
        
        // ✅ Routes export manquantes - CORRECTION PRINCIPALE
        Route::get('/galerie-video/export/{format}', [GalerieVideoController::class,'export'])->name('galerie.video.export');
        Route::get('/galerie-video/{id}/details', [GalerieVideoController::class,'getVideoDetails'])->name('galerie.video.details');
        Route::get('/galerie-video/{id}/view', [GalerieVideoController::class,'view'])->name('galerie.video.view');
        Route::post('/galerie-video/toggle-status', [GalerieVideoController::class,'toggleStatus'])->name('galerie.video.toggle.status');
    });

    // ==================== ✅ GALERIE IMAGE - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_galerie_images|manage_galerie_images']], function(){
        Route::get('/galerie-image', [GalerieImageController::class,'index'])->name('galerie.image.index');
        Route::get('/galerie-image/get-list', [GalerieImageController::class,'getGalerieImageList'])->name('galerie.image.datatable');
        Route::get('/galerie-image/statistics', [GalerieImageController::class,'getStatistics'])->name('galerie.image.statistics');
        
        Route::group(['middleware' => ['permission:create_galerie_images|manage_galerie_images']], function(){
            Route::get('/galerie-image/create', function() { return view('admin.galerie-image.create'); })->name('galerie.image.create');
            Route::post('/galerie-image/create', [GalerieImageController::class,'create'])->name('galerie.image.store');
        });
        
        Route::group(['middleware' => ['permission:edit_galerie_images|manage_galerie_images']], function(){
            Route::get('/galerie-image/{id}/edit', function($id) { return view('admin.galerie-image.edit', compact('id')); })->name('galerie.image.edit');
            Route::post('/galerie-image/update', [GalerieImageController::class,'update'])->name('galerie.image.update');
        });
        
        Route::group(['middleware' => ['permission:delete_galerie_images|manage_galerie_images']], function(){
            Route::post('/galerie-image/delete', [GalerieImageController::class,'delete'])->name('galerie.image.delete');
        });
        
        Route::get('/galerie-image/export/{format}', [GalerieImageController::class,'export'])->name('galerie.image.export');
        Route::get('/galerie-image/{id}/details', [GalerieImageController::class,'getImageDetails'])->name('galerie.image.details');
    });

    // ==================== ✅ CATÉGORIES IMAGES - ROUTES COMPLÈTES CORRIGÉES ====================
    Route::group(['middleware' => ['permission:view_categories_images|manage_categories_images']], function(){
        Route::get('/categories-images', [CategoriesImagesController::class,'index'])->name('categories.images.index');
        Route::get('/categories-images/get-list', [CategoriesImagesController::class,'getCategoriesImagesList'])->name('categories.images.datatable');
        Route::get('/categories-images/statistics', [CategoriesImagesController::class,'getStatistics'])->name('categories.images.statistics');
        
        Route::group(['middleware' => ['permission:create_categories_images|manage_categories_images']], function(){
            Route::get('/categories-images/create', function() { return view('admin.categories-images.create'); })->name('categories.images.create');
            Route::post('/categories-images/create', [CategoriesImagesController::class,'create'])->name('categories.images.store');
        });
        
        Route::group(['middleware' => ['permission:edit_categories_images|manage_categories_images']], function(){
            Route::get('/categories-images/{id}/edit', function($id) { return view('admin.categories-images.edit', compact('id')); })->name('categories.images.edit');
            Route::post('/categories-images/update', [CategoriesImagesController::class,'update'])->name('categories.images.update');
        });
        
        Route::group(['middleware' => ['permission:delete_categories_images|manage_categories_images']], function(){
            Route::post('/categories-images/delete', [CategoriesImagesController::class,'delete'])->name('categories.images.delete');
        });
        
        // ✅ Routes export et détails manquantes
        Route::get('/categories-images/export/{format}', [CategoriesImagesController::class,'export'])->name('categories.images.export');
        Route::get('/categories-images/{id}/details', [CategoriesImagesController::class,'getCategoryDetails'])->name('categories.images.details');
        Route::get('/categories-images/{id}/view', [CategoriesImagesController::class,'view'])->name('categories.images.view');
        Route::post('/categories-images/toggle-status', [CategoriesImagesController::class,'toggleStatus'])->name('categories.images.toggle.status');
    });

    // ==================== ✅ CATÉGORIES - ROUTES COMPLÈTES CORRIGÉES ====================
    Route::group(['middleware' => ['permission:view_categories|manage_categories']], function(){
        Route::get('/categories', [CategoriesController::class,'index'])->name('categories.index');
        Route::get('/categories/get-list', [CategoriesController::class,'getCategoriesList'])->name('categories.datatable');
        Route::get('/categories/statistics', [CategoriesController::class,'getStatistics'])->name('categories.statistics');
        
        Route::group(['middleware' => ['permission:create_categories|manage_categories']], function(){
            Route::get('/categories/create', function() { return view('admin.categories.create'); })->name('categories.create');
            Route::post('/categories/create', [CategoriesController::class,'create'])->name('categories.store');
        });
        
        Route::group(['middleware' => ['permission:edit_categories|manage_categories']], function(){
            Route::get('/categories/{id}/edit', function($id) { return view('admin.categories.edit', compact('id')); })->name('categories.edit');
            Route::post('/categories/update', [CategoriesController::class,'update'])->name('categories.update');
        });
        
        Route::group(['middleware' => ['permission:delete_categories|manage_categories']], function(){
            Route::get('/categories/delete/{id}', [CategoriesController::class,'deleteOP'])->name('categories.delete');
        });
        
        // ✅ Routes manquantes catégories
        Route::get('/categories/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('categories.details');
        Route::get('/categories/export/{format}', [CategoriesController::class,'export'])->name('categories.export');
        Route::get('/categories/{id}/view', [CategoriesController::class,'view'])->name('categories.view');
        Route::post('/categories/toggle-status', [CategoriesController::class,'toggleStatus'])->name('categories.toggle.status');
    });

    // ==================== ✅ FAQS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_faqs|manage_faqs']], function(){
        Route::get('/faqs', [FaqsController::class,'index'])->name('faqs.index');
        Route::get('/faqs/get-list', [FaqsController::class,'getFaqsList'])->name('faqs.datatable');
        Route::get('/faqs/statistics', [FaqsController::class,'getStatistics'])->name('faqs.statistics');
        
        Route::group(['middleware' => ['permission:create_faqs|manage_faqs']], function(){
            Route::get('/faqs/create', function() { return view('admin.faqs.create'); })->name('faqs.create');
            Route::post('/faqs/create', [FaqsController::class,'create'])->name('faqs.store');
        });
        
        Route::group(['middleware' => ['permission:edit_faqs|manage_faqs']], function(){
            Route::get('/faqs/{id}/edit', function($id) { return view('admin.faqs.edit', compact('id')); })->name('faqs.edit');
            Route::post('/faqs/update', [FaqsController::class,'update'])->name('faqs.update');
        });
        
        Route::group(['middleware' => ['permission:delete_faqs|manage_faqs']], function(){
            Route::get('/faqs/delete/{id}', [FaqsController::class,'deleteOK'])->name('faqs.delete');
        });
        
        Route::get('/faqs/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('faqs.details');
        Route::get('/faqs/export/{format}', [FaqsController::class,'export'])->name('faqs.export');
    });

    // ==================== ✅ SLIDERS - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_sliders|manage_sliders']], function(){
        Route::get('/sliders', [SlidersController::class,'index'])->name('sliders.index');
        Route::get('/sliders/get-list', [SlidersController::class,'getSlidersList'])->name('sliders.datatable');
        Route::get('/sliders/statistics', [SlidersController::class,'getStatistics'])->name('sliders.statistics');
        
        Route::group(['middleware' => ['permission:create_sliders|manage_sliders']], function(){
            Route::get('/sliders/create', function() { return view('admin.sliders.create'); })->name('sliders.create');
            Route::post('/sliders/create', [SlidersController::class,'create'])->name('sliders.store');
        });
        
        Route::group(['middleware' => ['permission:edit_sliders|manage_sliders']], function(){
            Route::get('/sliders/{id}/edit', function($id) { return view('admin.sliders.edit', compact('id')); })->name('sliders.edit');
            Route::post('/sliders/update', [SlidersController::class,'update'])->name('sliders.update');
        });
        
        Route::group(['middleware' => ['permission:delete_sliders|manage_sliders']], function(){
            Route::get('/sliders/delete/{id}', [SlidersController::class,'deleteOP'])->name('sliders.delete');
        });
        
        Route::get('/sliders/{id}/details', function($id) { return response()->json(['id' => $id]); })->name('sliders.details');
        Route::get('/sliders/export/{format}', [SlidersController::class,'export'])->name('sliders.export');
    });

    // ==================== ✅ STATUTS ÉTAT - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_statuts_etat|manage_statuts_etat']], function(){
        Route::get('/statuts-etat', [StatutsEtatController::class,'index'])->name('statuts.etat.index');
        Route::get('/statuts-etat/get-list', [StatutsEtatController::class,'getStatutsEtatList'])->name('statuts.etat.datatable');
        Route::get('/statuts-etat/statistics', [StatutsEtatController::class,'getStatistics'])->name('statuts.etat.statistics');
        
        Route::group(['middleware' => ['permission:create_statuts_etat|manage_statuts_etat']], function(){
            Route::post('/statuts-etat/create', [StatutsEtatController::class,'create'])->name('statuts.etat.store');
        });
        
        Route::group(['middleware' => ['permission:edit_statuts_etat|manage_statuts_etat']], function(){
            Route::post('/statuts-etat/update', [StatutsEtatController::class,'update'])->name('statuts.etat.update');
        });
        
        Route::group(['middleware' => ['permission:delete_statuts_etat|manage_statuts_etat']], function(){
            Route::get('/statuts-etat/delete/{id}', [StatutsEtatController::class,'delete'])->name('statuts.etat.delete');
        });
        
        Route::get('/statuts-etat/{id}/details', [StatutsEtatController::class,'getStatutDetails'])->name('statuts.etat.details');
        Route::get('/statuts-etat/export/{format}', [StatutsEtatController::class,'export'])->name('statuts.etat.export');
    });

    // ==================== ✅ PARRAINAGES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_parrainages|manage_parrainages']], function(){
        Route::get('/parrainages', [ParrainagesController::class,'index'])->name('parrainages.index');
        Route::get('/parrainages/get-list', [ParrainagesController::class,'getParrainagesList'])->name('parrainages.datatable');
        Route::get('/parrainages/statistics', [ParrainagesController::class,'getStatistics'])->name('parrainages.statistics');
        
        Route::group(['middleware' => ['permission:create_parrainages|manage_parrainages']], function(){
            Route::post('/parrainages/create', [ParrainagesController::class,'create'])->name('parrainages.store');
        });
        
        Route::group(['middleware' => ['permission:edit_parrainages|manage_parrainages']], function(){
            Route::post('/parrainages/update', [ParrainagesController::class,'update'])->name('parrainages.update');
        });
        
        Route::group(['middleware' => ['permission:delete_parrainages|manage_parrainages']], function(){
            Route::get('/parrainages/delete/{id}', [ParrainagesController::class,'deleteOK'])->name('parrainages.delete');
        });
        
        Route::get('/parrainages/{id}/details', [ParrainagesController::class,'getParrainageDetails'])->name('parrainages.details');
        Route::get('/parrainages/export/{format}', [ParrainagesController::class,'export'])->name('parrainages.export');
    });

    // ==================== ✅ VILLES - ROUTES COMPLÈTES ====================
    Route::group(['middleware' => ['permission:view_villes|manage_villes']], function(){
        Route::get('/villes', [VillesController::class,'index'])->name('villes.index');
        Route::get('/villes/get-list', [VillesController::class,'getVillesList'])->name('villes.datatable');
        Route::get('/villes/statistics', [VillesController::class,'getStatistics'])->name('villes.statistics');
        
        Route::group(['middleware' => ['permission:create_villes|manage_villes']], function(){
            Route::post('/villes/create', [VillesController::class,'create'])->name('villes.store');
        });
        
        Route::group(['middleware' => ['permission:edit_villes|manage_villes']], function(){
            Route::post('/villes/update', [VillesController::class,'update'])->name('villes.update');
        });
        
        Route::group(['middleware' => ['permission:delete_villes|manage_villes']], function(){
            Route::get('/villes/delete/{id}', [VillesController::class,'delete'])->name('villes.delete');
        });
        
        Route::get('/villes/{id}/details', [VillesController::class,'getVilleDetails'])->name('villes.details');
        Route::get('/villes/export/{format}', [VillesController::class,'export'])->name('villes.export');
    });

    // ==================== CONFIGURATION ET LOGS ====================
    
    // Configuration générale (admin seulement)
    Route::group(['middleware' => ['permission:manage_system_config']], function(){
        Route::get('configuration', [AuthHomeController::class,'Configuration'])->name('configuration.view');
        Route::post('configuration', [AuthHomeController::class,'updateConfiguration'])->name('configuration.post');
    });

    // Logs système (admin seulement)
    Route::group(['middleware' => ['permission:view_logs']], function(){
        Route::get('/log-stat', [AuthHomeController::class,'log_stat'])->name('logs.index');
    });

    // Routes legacy
    Route::get('/old-dashboard', [AuthHomeController::class,'index'])->name('old.dashboard');
});

// ==================== ✅ ROUTES API POUR AJAX - TOUTES LES API MANQUANTES ====================
Route::group(['prefix' => 'api', 'middleware' => ['auth']], function(){
    
    // APIs Dashboard Commercial
    Route::group(['prefix' => 'commercial', 'middleware' => ['permission:view_dashboard_commercial']], function(){
        Route::get('/dashboard-stats', [CommercialDashboardController::class,'getRealtimeStats']);
        Route::get('/monthly-performance', [CommercialDashboardController::class,'getMonthlyPerformance']);
        Route::get('/conversion-funnel', [CommercialDashboardController::class,'getConversionFunnel']);
        Route::get('/top-products', [CommercialDashboardController::class,'getTopProducts']);
        Route::get('/trends-analysis', [CommercialDashboardController::class,'getTrendsAnalysis']);
        Route::get('/predictive-analytics', [CommercialDashboardController::class,'getPredictiveAnalytics']);
        Route::get('/client-stats/{id}', [PublicUsersController::class,'getClientStats']);
        Route::get('/forfait-stats/{id}', [ForfaitsController::class,'getForfaitStats']);
    });
    
    // APIs Dashboard Comptoir
    Route::group(['prefix' => 'comptoir', 'middleware' => ['permission:view_dashboard_comptoir']], function(){
        Route::get('/dashboard-stats', [ComptoirDashboardController::class,'getRealtimeStats']);
        Route::get('/processing-time-stats', [ComptoirDashboardController::class,'getProcessingTimeStats']);
        Route::get('/profils-a-traiter', [ComptoirDashboardController::class,'getProfilsATraiter']);
        Route::post('/traitement-rapide', [ComptoirDashboardController::class,'traitementRapide']);
        Route::get('/performance-stats', [ComptoirDashboardController::class,'getPerformanceStats']);
        Route::get('/profil-visa-stats', [ProfilVisaController::class,'getStatistics']);
    });
    
    // APIs Dashboard Admin
    Route::group(['prefix' => 'admin', 'middleware' => ['permission:view_dashboard_admin']], function(){
        Route::get('/dashboard-stats', [AdminDashboardController::class,'getRealtimeStats']);
        Route::get('/system-performance', [AdminDashboardController::class,'getSystemPerformance']);
        Route::get('/users-analytics', [AdminDashboardController::class,'getUsersAnalytics']);
    });
    
    // APIs pour utilisateurs publics
    Route::group(['prefix' => 'user'], function(){
        Route::get('/realtime-stats', [AuthHomeController::class,'getRealtimeUserStats']);
        Route::get('/profils-visa', [AuthHomeController::class,'getUserProfilsVisaApi']);
        Route::get('/messages', [AuthHomeController::class,'getUserRecentMessagesApi']);
        Route::get('/appointments', [AuthHomeController::class,'getUserUpcomingAppointmentsApi']);
        Route::get('/system-health', [AuthHomeController::class,'checkSystemHealth']);
    });
    
    // API PERMISSIONS - ROUTES MULTIPLES POUR COMPATIBILITÉ
    Route::get('/roles/{id}/permissions', [PermissionController::class,'getPermissionBadgeByRole']);
    Route::post('/roles/{id}/permissions', [PermissionController::class,'getPermissionBadgeByRole']);
    Route::get('/permissions/role/{id}', [PermissionController::class,'getPermissionBadgeByRole']);
    Route::post('/permissions/role/{id}', [PermissionController::class,'getPermissionBadgeByRole']);
    Route::get('/get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);
    Route::post('/get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);
});

// ==================== ROUTES DE TEST ET DEBUG ====================
Route::group(['middleware' => 'auth', 'prefix' => 'debug'], function(){
    Route::get('/test-db', function() {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'success', 'message' => 'Database connection OK']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    });
    
    Route::get('/test-permissions', function() {
        $user = Auth::user();
        return response()->json([
            'user' => $user->name,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    });
});

// ==================== ✅ ROUTES SUPPLÉMENTAIRES MANQUANTES ====================

// Route spécifique pour les utilisateurs publics sans permissions
Route::get('/mes-rendez-vous', function() {
    return redirect('/rendez-vous');
})->middleware('auth')->name('mes.rendez.vous');

// Routes services publics (sans middleware permission pour les clients)
Route::get('/services-publics', function() {
    return redirect('/services');
})->name('services.publics');

// ==================== GESTION DES ERREURS ====================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
// ==================== CRM ROUTES ====================
Route::group(['prefix' => 'crm', 'middleware' => ['auth']], function(){
    
    // Dashboard CRM
    Route::get('/', [CRMDashboardController::class, 'index'])->name('crm.dashboard');
    Route::get('/stats', [CRMDashboardController::class, 'getRealtimeStats'])->name('crm.stats');
    
    // Clients CRM
    Route::get('/clients', [CRMClientsController::class, 'index'])->name('crm.clients.index');
    Route::post('/clients', [CRMClientsController::class, 'store'])->name('crm.clients.store');
    Route::put('/clients/{id}', [CRMClientsController::class, 'update'])->name('crm.clients.update');
    Route::delete('/clients/{id}', [CRMClientsController::class, 'destroy'])->name('crm.clients.destroy');
    Route::post('/clients/{id}/generate-portal-link', [CRMClientsController::class, 'generatePortalLink'])->name('crm.clients.generate-portal-link');
    Route::get('/clients/export/{format}', [CRMClientsController::class, 'export'])->name('crm.clients.export');
    
    // Factures CRM
    Route::get('/invoices', [CRMInvoicesController::class, 'index'])->name('crm.invoices.index');
    Route::get('/invoices/{id}', [CRMInvoicesController::class, 'show'])->name('crm.invoices.show');
    Route::post('/invoices', [CRMInvoicesController::class, 'store'])->name('crm.invoices.store');
    Route::put('/invoices/{id}', [CRMInvoicesController::class, 'update'])->name('crm.invoices.update');
    // Route::post('/invoices/{id}/payment', [CRMInvoicesController::class, 'recordPayment'])->name('crm.invoices.payment'); // DÉSACTIVÉ : Les paiements se font maintenant dans la caisse
    Route::post('/invoices/{id}/generate-link', [CRMInvoicesController::class, 'generateLink'])->name('crm.invoices.generate-link');
    Route::delete('/invoices/{id}', [CRMInvoicesController::class, 'destroy'])->name('crm.invoices.destroy');
});

// ==================== CRM ROUTES COMPLÈTES AVEC PERMISSIONS ====================
Route::group(['prefix' => 'crm', 'middleware' => ['auth']], function(){
    
    // Page principale - Dashboard
    Route::get('/', [CRMController::class, 'index'])->name('crm.index');

    // Vue des activités CRM
    Route::get('/activities-view', function() {
        return view('crm.activities');
    })->name('crm.activities.view');

    // Calendrier - Page
    Route::get('/calendrier', [App\Http\Controllers\CalendrierController::class, 'index'])->name('crm.calendrier');

    // Calendrier - API Routes
    Route::prefix('calendrier')->name('calendrier.')->group(function() {
        Route::get('/events', [App\Http\Controllers\CalendrierController::class, 'getEvents'])->name('events');
        Route::post('/events', [App\Http\Controllers\CalendrierController::class, 'store'])->name('store');
        Route::get('/events/{id}', [App\Http\Controllers\CalendrierController::class, 'show'])->name('show');
        Route::put('/events/{id}', [App\Http\Controllers\CalendrierController::class, 'update'])->name('update');
        Route::delete('/events/{id}', [App\Http\Controllers\CalendrierController::class, 'destroy'])->name('destroy');
        Route::get('/stats', [App\Http\Controllers\CalendrierController::class, 'getStats'])->name('stats');
        Route::get('/upcoming', [App\Http\Controllers\CalendrierController::class, 'getUpcoming'])->name('upcoming');
        Route::get('/alarms', [App\Http\Controllers\CalendrierController::class, 'getTodayAlarms'])->name('alarms');
    });

    // Dashboard stats
    Route::get('/stats', [CRMController::class, 'getStats'])
        ->middleware('check.crm.permission:dashboard')
        ->name('crm.stats');
    // ✅ NOUVELLE ROUTE : Vérifier les permissions
    Route::get('/user/check-permissions', [CRMController::class, 'checkUserPermissions'])
        ->name('crm.user.check.permissions');
        Route::get('/clients/{id}', [CRMController::class, 'getClient'])->name('crm.clients.show');
    Route::get('/clients/{id}/commentaires', [CRMController::class, 'getClientCommentaires'])->name('crm.clients.commentaires');
    Route::post('/clients/{id}/commentaire', [CRMController::class, 'addClientCommentaire'])->name('crm.clients.commentaire.add');

    // ✅ ROUTES CONTRATS - CORRECTION: Prefix 'contrats' au lieu de 'crm/contrats' car déjà dans groupe 'crm'
    Route::prefix('contrats')->name('contrats.')->group(function() {
        Route::get('/', [CRMController::class, 'getContracts'])->name('index');
        Route::post('/', [CRMController::class, 'storeContract'])->name('store');
        Route::get('/{id}', [CRMController::class, 'getContract'])->name('show');
        Route::put('/{id}', [CRMController::class, 'updateContract'])->name('update');
        Route::delete('/{id}', [CRMController::class, 'deleteContract'])->name('destroy');
        Route::post('/{id}/sign', [CRMController::class, 'signContract'])->name('sign');
        Route::post('/{id}/regenerate-token', [CRMController::class, 'regenerateToken'])->name('regenerate-token');
        Route::post('/{id}/send-link', [CRMController::class, 'sendContractLink'])->name('send-link');
        Route::get('/stats/dashboard', [CRMController::class, 'getContractsStats'])->name('stats');
    });

    // CLIENTS
    Route::group(['middleware' => 'check.crm.permission:clients'], function(){
        Route::get('/clients', [CRMController::class, 'getClients'])->name('crm.clients.index');
        Route::post('/clients', [CRMController::class, 'storeClient'])->name('crm.clients.store');
        Route::get('/clients/{id}', [CRMController::class, 'getClient'])->name('crm.clients.show'); // ✅ AJOUTÉ
        Route::put('/clients/{id}', [CRMController::class, 'updateClient'])->name('crm.clients.update');
        Route::delete('/clients/{id}', [CRMController::class, 'deleteClient'])->name('crm.clients.destroy');
    });
    
    // FACTURES (INVOICING)
    Route::group(['middleware' => 'check.crm.permission:invoicing'], function(){
        Route::get('/invoices', [CRMController::class, 'getInvoices'])->name('crm.invoices.index');
        Route::post('/invoices', [CRMController::class, 'storeInvoice'])->name('crm.invoices.store');
        Route::get('/invoices/{id}', [CRMController::class, 'viewInvoice'])->name('crm.invoices.view');
        Route::put('/invoices/{id}', [CRMController::class, 'updateInvoice'])->name('crm.invoices.update');
        Route::get('/invoices/{id}/print', [CRMController::class, 'printInvoice'])->name('crm.invoices.print');
        // Route::post('/invoices/{id}/payment', [CRMController::class, 'recordPayment'])->name('crm.invoices.payment'); // DÉSACTIVÉ : Les paiements se font maintenant dans la caisse
        Route::post('/invoices/{id}/reminder', [CRMController::class, 'sendInvoiceReminder'])->name('crm.invoices.reminder');
        Route::delete('/invoices/{id}', [CRMController::class, 'deleteInvoice'])->name('crm.invoices.destroy');
        Route::get('/payments/{id}', [CRMController::class, 'getPayment'])->name('crm.payments.show'); // ✅ AJOUTÉ
        Route::put('/payments/{id}', [CRMController::class, 'updatePayment'])->name('crm.payments.update'); // ✅ AJOUTÉ
        Route::delete('/payments/{id}', [CRMController::class, 'deletePayment'])->name('crm.payments.destroy'); 
    });
    
    // RECOUVREMENT (RECOVERY)
    Route::get('/recovery', [CRMController::class, 'getRecoveryData'])
        ->middleware('check.crm.permission:recovery')
        ->name('crm.recovery');
    
    // PERFORMANCE
    Route::get('/performance', [CRMController::class, 'performance'])
        ->middleware('check.crm.permission:performance')
        ->name('crm.performance');
    
    // ANALYTICS
    Route::get('/analytics', [CRMController::class, 'getAnalyticsData'])
        ->middleware('check.crm.permission:analytics')
        ->name('crm.analytics');
    
    // ADMINISTRATION
    Route::group(['middleware' => 'check.crm.permission:admin'], function(){
        Route::get('/admin', [CRMController::class, 'getAdminData'])->name('crm.admin');
        Route::get('/admin/users/{id}', [CRMController::class, 'getUserDetails'])->name('crm.admin.user');
        Route::put('/admin/users/{id}/permissions', [CRMController::class, 'updateUserPermissions'])->name('crm.admin.user.permissions');
        Route::put('/admin/users/{id}/toggle-status', [CRMController::class, 'toggleUserStatus'])->name('crm.admin.user.toggle');
        Route::put('/admin/users/{id}/reset-password', [CRMController::class, 'resetUserPassword'])->name('crm.admin.user.reset');
        Route::delete('/admin/users/{id}', [CRMController::class, 'deleteUser'])->name('crm.admin.user.delete');
    });
    
    // Permissions utilisateur (accessible à tous)
    Route::get('/user/permissions', [CRMController::class, 'getUserPermissions'])->name('crm.user.permissions');

    // Récupération des activités CRM (accessible aux utilisateurs avec permission dashboard)
    Route::get('/activities', [CRMController::class, 'getActivities'])->name('crm.activities');

    // Factures d'un client spécifique
    Route::get('/clients/{id}/invoices', [CRMController::class, 'getClientInvoices'])
        ->name('crm.clients.invoices');
     // RELANCES
    Route::group(['middleware' => 'check.crm.permission:clients'], function(){
        Route::get('/relances', [CRMController::class, 'getRelances'])->name('crm.relances.index');
        Route::post('/relances', [CRMController::class, 'storeRelance'])->name('crm.relances.store');
        Route::put('/relances/{id}', [CRMController::class, 'updateRelance'])->name('crm.relances.update');
        Route::get('/relances/client/{clientId}', [CRMController::class, 'getClientRelances'])->name('crm.relances.client');
        Route::get('/relances/stats', [CRMController::class, 'getRelancesStats'])->name('crm.relances.stats');
        Route::get('/relances/aujourd-hui', [CRMController::class, 'getRelancesAujourdhui'])
        ->name('crm.relances.today');
        // ✅ NOUVELLES ROUTES RELANCES AVEC TEMPLATES
    Route::group(['middleware' => 'check.crm.permission:clients'], function(){
        
        // Routes relances existantes
        Route::get('/relances', [CRMController::class, 'getRelances'])->name('crm.relances.index');
        Route::post('/relances', [CRMController::class, 'storeRelance'])->name('crm.relances.store');
        Route::put('/relances/{id}', [CRMController::class, 'updateRelance'])->name('crm.relances.update');
        Route::get('/relances/client/{clientId}', [CRMController::class, 'getClientRelances'])->name('crm.relances.client');
        Route::get('/relances/stats', [CRMController::class, 'getRelancesStats'])->name('crm.relances.stats');
        Route::get('/relances/aujourd-hui', [CRMController::class, 'getRelancesAujourdhui'])->name('crm.relances.today');
        
        // ✅ NOUVELLE ROUTE : Obtenir les templates de relances
        Route::get('/relances/templates', [CRMController::class, 'getRelanceTemplates'])->name('crm.relances.templates');
        Route::post('/clients/{id}/commentaire', [CRMController::class, 'addClientCommentaire'])->name('crm.clients.commentaire.add');
        Route::get('/clients/{id}/commentaires', [CRMController::class, 'getClientCommentaires'])->name('crm.clients.commentaires');
    });
    });
});

// ==================== ✅ MESSAGERIE INTERNE - ROUTES COMPLÈTES ====================
Route::group(['prefix' => 'messagerie', 'middleware' => ['auth']], function(){
    
    // Page principale de la messagerie
    Route::get('/', [MessagerieController::class, 'index'])->name('messagerie.index');
    
    // Accès direct (alias)
    Route::get('/chat', [MessagerieController::class, 'index'])->name('messagerie.chat');
    
    // Dashboard de la messagerie
    Route::get('/dashboard', [MessagerieController::class, 'index'])->name('messagerie.dashboard');
    
    // Routes API pour la synchronisation des messages
    Route::group(['prefix' => 'api'], function(){
        
        // Récupérer tous les messages
        Route::get('/messages', [MessagerieController::class, 'getMessages'])->name('messagerie.api.messages');
        
        // Sauvegarder un message
        Route::post('/messages', [MessagerieController::class, 'sendMessage'])->name('messagerie.api.send');
        
        // Récupérer les utilisateurs actifs
        Route::get('/users', [MessagerieController::class, 'getUsers'])->name('messagerie.api.users');
        
        // Statistiques en temps réel
        Route::get('/stats', [MessagerieController::class, 'getStats'])->name('messagerie.api.stats');
        
        // ✅ NOUVEAU : Routes pour les notifications et messages non lus
        Route::post('/mark-as-read', [MessagerieController::class, 'markAsRead'])->name('messagerie.api.mark.read');
        Route::get('/unread-count', [MessagerieController::class, 'getUnreadCount'])->name('messagerie.api.unread.count');
        
        // ✅ Routes pour l'appel vidéo 1-to-1 avec invitation
        Route::post('/start-video-call', [MessagerieController::class, 'startVideoCall'])->name('messagerie.api.start.call');
        Route::get('/check-video-call', [MessagerieController::class, 'checkVideoCallInvitations'])->name('messagerie.api.check.call');
        Route::post('/accept-video-call', [MessagerieController::class, 'acceptVideoCall'])->name('messagerie.api.accept.call');
        Route::post('/reject-video-call', [MessagerieController::class, 'rejectVideoCall'])->name('messagerie.api.reject.call');
        Route::get('/check-call-status', [MessagerieController::class, 'checkCallStatus'])->name('messagerie.api.call.status');
        
        // ✅ NOUVEAU : Routes pour l'appel vidéo de GROUPE
        Route::post('/start-group-video-call', [MessagerieController::class, 'startGroupVideoCall'])->name('messagerie.api.start.group.call');
        Route::post('/accept-group-video-call', [MessagerieController::class, 'acceptGroupVideoCall'])->name('messagerie.api.accept.group.call');
        Route::post('/leave-group-video-call', [MessagerieController::class, 'leaveGroupVideoCall'])->name('messagerie.api.leave.group.call');
        Route::get('/get-call-participants', [MessagerieController::class, 'getCallParticipants'])->name('messagerie.api.get.participants');
        
        // ✅ Routes pour WebRTC (échange SDP et ICE candidates)
        Route::post('/exchange-webrtc', [MessagerieController::class, 'exchangeWebRTC'])->name('messagerie.api.exchange.webrtc');
        Route::get('/get-webrtc-data', [MessagerieController::class, 'getWebRTCData'])->name('messagerie.api.get.webrtc');
    });
    
    // Routes pour les administrateurs uniquement
    Route::group(['middleware' => ['role:Super Admin|Admin']], function(){
        
        // Panneau d'administration de la messagerie
        Route::get('/admin', [MessagerieController::class, 'index'])->name('messagerie.admin');
        
        // Gérer les utilisateurs de la messagerie
        Route::get('/admin/users', [MessagerieController::class, 'adminUsers'])->name('messagerie.admin.users');
        
        // Créer un utilisateur pour la messagerie
        Route::post('/admin/users', [MessagerieController::class, 'createUser'])->name('messagerie.admin.users.create');
        
        // Modifier un utilisateur
        Route::put('/admin/users/{id}', [MessagerieController::class, 'updateUser'])->name('messagerie.admin.users.update');
        
        // Supprimer un utilisateur
        Route::delete('/admin/users/{id}', [MessagerieController::class, 'deleteUser'])->name('messagerie.admin.users.delete');
    });
});
