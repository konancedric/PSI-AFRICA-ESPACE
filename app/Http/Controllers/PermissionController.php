<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * AFFICHAGE DE LA PAGE PERMISSIONS
     */
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect('/dashboard')->with('error', 'Accès non autorisé - Réservé aux administrateurs');
            }

            Log::info('PermissionController index - Chargement page permissions');

            $roles = $this->getSafeRoles();
            $permissions = $this->getSafePermissions();

            Log::info('Permissions index - Données chargées:', [
                'roles_count' => $roles->count(),
                'permissions_count' => $permissions->count()
            ]);

            return view('permission', compact('roles', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Erreur PermissionController index: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Erreur lors du chargement des permissions: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION CRITIQUE : Créer les permissions de base du système
     */
    public function createBasePermissions()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            if (!Schema::hasTable('permissions')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table permissions manquante - Système de permissions non configuré'
                ]);
            }

            $basePermissions = $this->getBasePermissionsDefinitions();

            $created = 0;
            $existing = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($basePermissions as $category => $permissions) {
                    Log::info("Traitement catégorie: {$category}");
                    
                    foreach ($permissions as $name => $description) {
                        try {
                            $permission = Permission::firstOrCreate(
                                ['name' => $name, 'guard_name' => 'web'],
                                ['description' => $description]
                            );
                            
                            if ($permission->wasRecentlyCreated) {
                                $created++;
                                Log::info("Permission créée: {$name}");
                            } else {
                                $existing++;
                                Log::info("Permission existe déjà: {$name}");
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Erreur création permission {$name}: " . $e->getMessage();
                            Log::error("Erreur permission {$name}: " . $e->getMessage());
                        }
                    }
                }

                DB::commit();

                Log::info("✅ Permissions de base traitées", [
                    'created' => $created,
                    'existing' => $existing,
                    'errors_count' => count($errors),
                    'user' => $user->name
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Permissions de base créées avec succès !",
                    'details' => [
                        'created' => $created,
                        'existing' => $existing,
                        'total' => array_sum(array_map('count', $basePermissions)),
                        'errors' => $errors
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur createBasePermissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création des permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ CORRECTION CRITIQUE : Vérifier la santé du système de permissions
     */
    public function checkPermissionsSystemHealth()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $health = [
                'tables' => [
                    'permissions' => Schema::hasTable('permissions'),
                    'roles' => Schema::hasTable('roles'),
                    'role_has_permissions' => Schema::hasTable('role_has_permissions'),
                    'model_has_roles' => Schema::hasTable('model_has_roles'),
                    'model_has_permissions' => Schema::hasTable('model_has_permissions'),
                ],
                'data' => [
                    'permissions_count' => 0,
                    'roles_count' => 0,
                    'users_with_roles' => 0,
                    'permissions_assigned' => 0,
                ],
                'status' => 'unknown'
            ];

            // Vérifier les données
            try {
                if ($health['tables']['permissions']) {
                    $health['data']['permissions_count'] = Permission::count();
                }
                
                if ($health['tables']['roles']) {
                    $health['data']['roles_count'] = Role::count();
                }
                
                if ($health['tables']['model_has_roles']) {
                    $health['data']['users_with_roles'] = DB::table('model_has_roles')
                        ->where('model_type', 'App\\Models\\User')
                        ->distinct('model_id')
                        ->count();
                }
                
                if ($health['tables']['role_has_permissions']) {
                    $health['data']['permissions_assigned'] = DB::table('role_has_permissions')
                        ->distinct()
                        ->count();
                }
            } catch (\Exception $e) {
                Log::warning('Erreur collecte données santé: ' . $e->getMessage());
            }

            // Déterminer le statut global
            $allTablesExist = array_reduce($health['tables'], function($carry, $exists) {
                return $carry && $exists;
            }, true);

            $hasMinimalData = $health['data']['permissions_count'] > 0 && 
                             $health['data']['roles_count'] > 0;

            if ($allTablesExist && $hasMinimalData) {
                $health['status'] = 'healthy';
            } elseif ($allTablesExist) {
                $health['status'] = 'incomplete';
            } else {
                $health['status'] = 'critical';
            }

            // Recommandations
            $recommendations = [];
            if (!$health['tables']['permissions']) {
                $recommendations[] = 'Créer la table permissions';
            }
            if (!$health['tables']['roles']) {
                $recommendations[] = 'Créer la table roles';
            }
            if ($health['data']['permissions_count'] == 0) {
                $recommendations[] = 'Créer les permissions de base';
            }
            if ($health['data']['roles_count'] == 0) {
                $recommendations[] = 'Créer les rôles de base';
            }

            $health['recommendations'] = $recommendations;

            Log::info('Vérification santé système permissions:', [
                'status' => $health['status'],
                'tables' => $health['tables'],
                'data' => $health['data']
            ]);

            return response()->json([
                'success' => true,
                'health' => $health,
                'message' => $this->getHealthStatusMessage($health['status']),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur checkPermissionsSystemHealth:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du système',
                'health' => [
                    'status' => 'error',
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * FONCTION PRINCIPALE : Obtenir les permissions d'un rôle pour affichage badge
     */
    public function getPermissionBadgeByRole(Request $request)
    {
        try {
            $roleId = $request->input('role_id') ?? $request->input('id') ?? $request->route('id');
            
            Log::info('🔍 getPermissionBadgeByRole - Demande permissions:', [
                'role_id' => $roleId,
                'method' => $request->method(),
                'all_inputs' => $request->all()
            ]);
            
            if (!$roleId) {
                Log::warning('ID du rôle manquant');
                return response()->json([
                    'success' => false,
                    'message' => 'ID du rôle requis',
                    'badges' => $this->getNoPermissionsHtml()
                ]);
            }

            if (!Schema::hasTable('roles')) {
                Log::warning('Table roles manquante');
                return response()->json([
                    'success' => true,
                    'message' => 'Système de rôles non configuré',
                    'badges' => $this->getDefaultPermissionsByRoleId($roleId)
                ]);
            }

            $role = Role::find($roleId);
            
            if (!$role) {
                Log::warning('Rôle non trouvé:', ['role_id' => $roleId]);
                
                $defaultBadges = $this->getDefaultPermissionsByRoleId($roleId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Rôle non trouvé, permissions par défaut utilisées',
                    'badges' => $defaultBadges,
                    'permissions' => $defaultBadges
                ]);
            }

            $badges = $this->generatePermissionBadges($role);

            return response()->json([
                'success' => true,
                'badges' => $badges,
                'permissions' => $badges,
                'role_name' => $role->name,
                'role_id' => $role->id
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur getPermissionBadgeByRole:', [
                'error' => $e->getMessage(),
                'role_id' => $request->input('role_id') ?? $request->input('id') ?? 'N/A'
            ]);

            $roleId = $request->input('role_id') ?? $request->input('id') ?? 1;
            $fallbackBadges = $this->getDefaultPermissionsByRoleId($roleId);

            return response()->json([
                'success' => true,
                'message' => 'Permissions par défaut utilisées suite à une erreur',
                'badges' => $fallbackBadges,
                'permissions' => $fallbackBadges,
                'error_details' => $e->getMessage()
            ]);
        }
    }

    /**
     * CRÉER UNE NOUVELLE PERMISSION
     */
    public function create(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->back()->with('error', 'Accès non autorisé');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:permissions,name',
                'description' => 'nullable|string|max:500',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->errors()->first());
            }

            if (!Schema::hasTable('permissions')) {
                return redirect()->back()->with('error', 'Table permissions manquante');
            }

            DB::beginTransaction();

            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'description' => $request->description
            ]);

            if ($request->roles && is_array($request->roles)) {
                $roles = Role::whereIn('id', $request->roles)->get();
                foreach ($roles as $role) {
                    $role->givePermissionTo($permission);
                }
            }

            DB::commit();

            Log::info('Permission créée:', [
                'permission' => $permission->name,
                'user' => $user->name
            ]);

            return redirect()->back()->with('success', 'Permission créée avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création permission: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * METTRE À JOUR UNE PERMISSION
     */
    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->back()->with('error', 'Accès non autorisé');
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:permissions,id',
                'name' => 'required|string|max:255|unique:permissions,name,' . $request->id,
                'description' => 'nullable|string|max:500',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->errors()->first());
            }

            $permission = Permission::find($request->id);
            if (!$permission) {
                return redirect()->back()->with('error', 'Permission non trouvée');
            }

            DB::beginTransaction();

            $permission->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            // Synchroniser les rôles
            if ($request->roles && is_array($request->roles)) {
                $allRoles = Role::all();
                foreach ($allRoles as $role) {
                    $role->revokePermissionTo($permission);
                }

                $newRoles = Role::whereIn('id', $request->roles)->get();
                foreach ($newRoles as $role) {
                    $role->givePermissionTo($permission);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Permission mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur mise à jour permission: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * SUPPRIMER UNE PERMISSION
     */
    public function delete($id)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect()->back()->with('error', 'Accès non autorisé');
            }

            $permission = Permission::find($id);
            if (!$permission) {
                return redirect()->back()->with('error', 'Permission non trouvée');
            }

            $permissionName = $permission->name;
            $permission->delete();

            Log::info('Permission supprimée:', [
                'permission' => $permissionName,
                'user' => $user->name
            ]);

            return redirect()->back()->with('success', 'Permission supprimée avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur suppression permission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * API DATATABLES POUR LES PERMISSIONS
     */
    public function getPermissionList(Request $request)
    {
        try {
            if (!Schema::hasTable('permissions')) {
                return response()->json([
                    'draw' => intval($request->input('draw', 1)),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Table permissions manquante'
                ]);
            }

            $permissions = Permission::with('roles')->get();

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $permissions->count(),
                'recordsFiltered' => $permissions->count(),
                'data' => $permissions->map(function($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'description' => $permission->description ?? '',
                        'roles' => $permission->roles->pluck('name')->implode(', '),
                        'created_at' => $permission->created_at->format('d/m/Y H:i'),
                        'action' => $this->formatPermissionActions($permission)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getPermissionList: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * OBTENIR LES STATISTIQUES DES PERMISSIONS
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_permissions' => $this->getTotalPermissions(),
                'permissions_with_roles' => $this->getPermissionsWithRoles(),
                'permissions_without_roles' => $this->getPermissionsWithoutRoles(),
                'most_used_permission' => $this->getMostUsedPermission(),
                'system_health' => $this->getSystemHealthStatus()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur getStatistics permissions: ' . $e->getMessage());
            return response()->json([
                'total_permissions' => 0,
                'permissions_with_roles' => 0,
                'permissions_without_roles' => 0,
                'most_used_permission' => 'Aucune',
                'system_health' => 'Erreur',
                'error' => true
            ], 500);
        }
    }

    // ========== MÉTHODES UTILITAIRES CORRIGÉES ==========

    private function getSafeRoles()
    {
        try {
            if (Schema::hasTable('roles')) {
                return Role::orderBy('name', 'asc')->pluck('name', 'id');
            }
        } catch (\Exception $e) {
            Log::warning('Erreur récupération rôles: ' . $e->getMessage());
        }
        return collect();
    }

    private function getSafePermissions()
    {
        try {
            if (Schema::hasTable('permissions')) {
                return Permission::orderBy('name', 'asc')->get();
            }
        } catch (\Exception $e) {
            Log::warning('Erreur récupération permissions: ' . $e->getMessage());
        }
        return collect();
    }

    /**
     * ✅ DÉFINITIONS DES PERMISSIONS DE BASE - COMPLÈTE ET CORRIGÉE
     */
    private function getBasePermissionsDefinitions(): array
    {
        return [
            'système' => [
                'manage_users' => 'Gestion des utilisateurs',
                'manage_roles' => 'Gestion des rôles',
                'manage_permissions' => 'Gestion des permissions',
                'view_dashboard' => 'Voir le dashboard',
                'system_admin' => 'Administration système',
                'manage_settings' => 'Gestion des paramètres',
                'manage_system_config' => 'Configuration système',
                'view_logs' => 'Consulter les logs'
            ],
            'dashboard' => [
                'view_dashboard_admin' => 'Dashboard administrateur',
                'view_dashboard_comptoir' => 'Dashboard comptoir',
                'view_dashboard_commercial' => 'Dashboard commercial',
                'view_analytics' => 'Voir les analyses',
                'export_data' => 'Exporter des données',
                'import_data' => 'Importer des données'
            ],
            'profil_visa' => [
                'manage_profil_visa' => 'Gestion complète des profils visa',
                'view_profil_visa' => 'Consulter les profils visa',
                'create_profil_visa' => 'Créer des profils visa',
                'edit_profil_visa' => 'Modifier les profils visa',
                'delete_profil_visa' => 'Supprimer les profils visa',
                'edit_profil_visa_status' => 'Modifier les statuts',
                'add_message_profil_visa' => 'Ajouter des messages',
                'view_profil_visa_documents' => 'Consulter les documents',
                'manage_profil_visa_documents' => 'Gérer les documents',
                'export_profil_visa' => 'Exporter les profils visa',
                'approve_profil_visa' => 'Approuver les profils visa',
                'reject_profil_visa' => 'Rejeter les profils visa',
                'process_profil_visa' => 'Traiter les profils visa',
                'validate_profil_visa' => 'Valider les profils visa'
            ],
            'utilisateurs' => [
                'view_user' => 'Consulter les utilisateurs',
                'create_user' => 'Créer des utilisateurs',
                'edit_user' => 'Modifier les utilisateurs',
                'delete_user' => 'Supprimer les utilisateurs',
                'manage_user' => 'Gestion complète des utilisateurs',
                'view_clients' => 'Consulter les clients',
                'manage_clients' => 'Gérer les clients'
            ],
            'rôles_permissions' => [
                'view_role' => 'Consulter les rôles',
                'create_role' => 'Créer des rôles',
                'edit_role' => 'Modifier les rôles',
                'delete_role' => 'Supprimer les rôles',
                'manage_role' => 'Gestion complète des rôles',
                'view_permission' => 'Consulter les permissions',
                'create_permission' => 'Créer des permissions',
                'edit_permission' => 'Modifier les permissions',
                'delete_permission' => 'Supprimer les permissions',
                'manage_permission' => 'Gestion complète des permissions'
            ],
            'agents' => [
                'manage_agents' => 'Gestion des agents',
                'view_agents' => 'Consulter les agents',
                'create_agents' => 'Créer des agents',
                'edit_agents' => 'Modifier les agents',
                'delete_agents' => 'Supprimer les agents'
            ],
            'rendez_vous' => [
                'manage_rendez_vous' => 'Gestion des rendez-vous',
                'view_rendez_vous' => 'Consulter les rendez-vous',
                'create_rendez_vous' => 'Créer des rendez-vous',
                'edit_rendez_vous' => 'Modifier les rendez-vous',
                'delete_rendez_vous' => 'Supprimer les rendez-vous'
            ],
            'commercial' => [
                'manage_clients' => 'Gestion des clients',
                'view_clients' => 'Consulter les clients',
                'manage_forfaits' => 'Gestion des forfaits',
                'view_forfaits' => 'Consulter les forfaits',
                'manage_souscrire_forfaits' => 'Gestion des souscriptions',
                'view_souscrire_forfaits' => 'Consulter les souscriptions',
                'manage_partenaires' => 'Gestion des partenaires',
                'view_partenaires' => 'Consulter les partenaires',
                'manage_temoignages' => 'Gestion des témoignages',
                'view_temoignages' => 'Consulter les témoignages',
                'sales_tracking' => 'Suivi des ventes',
                'client_communication' => 'Communication client'
            ],
            'contenu' => [
                'manage_services' => 'Gestion des services',
                'view_services' => 'Consulter les services',
                'create_services' => 'Créer des services',
                'edit_services' => 'Modifier les services',
                'delete_services' => 'Supprimer les services',
                'manage_actualites' => 'Gestion des actualités',
                'view_actualites' => 'Consulter les actualités',
                'create_actualites' => 'Créer des actualités',
                'edit_actualites' => 'Modifier les actualités',
                'delete_actualites' => 'Supprimer les actualités',
                'manage_faqs' => 'Gestion des FAQs',
                'view_faqs' => 'Consulter les FAQs',
                'create_faqs' => 'Créer des FAQs',
                'edit_faqs' => 'Modifier les FAQs',
                'delete_faqs' => 'Supprimer les FAQs'
            ],
            'galerie' => [
                'manage_galerie_video' => 'Gestion galerie vidéo',
                'view_galerie_video' => 'Consulter galerie vidéo',
                'create_galerie_video' => 'Créer vidéos',
                'edit_galerie_video' => 'Modifier vidéos',
                'delete_galerie_video' => 'Supprimer vidéos',
                'moderate_galerie_video' => 'Modérer galerie vidéo',
                'manage_galerie_images' => 'Gestion galerie images',
                'view_galerie_images' => 'Consulter galerie images',
                'create_galerie_images' => 'Créer images',
                'edit_galerie_images' => 'Modifier images',
                'delete_galerie_images' => 'Supprimer images'
            ],
            'documents' => [
                'manage_documents_voyage' => 'Gestion documents voyage',
                'view_documents_voyage' => 'Consulter documents voyage',
                'create_documents_voyage' => 'Créer documents voyage',
                'edit_documents_voyage' => 'Modifier documents voyage',
                'delete_documents_voyage' => 'Supprimer documents voyage'
            ],
            'autres' => [
                'manage_categories' => 'Gestion des catégories',
                'view_categories' => 'Consulter les catégories',
                'create_categories' => 'Créer des catégories',
                'edit_categories' => 'Modifier les catégories',
                'delete_categories' => 'Supprimer les catégories',
                'manage_categories_images' => 'Gestion catégories images',
                'view_categories_images' => 'Consulter catégories images',
                'create_categories_images' => 'Créer catégories images',
                'edit_categories_images' => 'Modifier catégories images',
                'delete_categories_images' => 'Supprimer catégories images',
                'manage_sliders' => 'Gestion des sliders',
                'view_sliders' => 'Consulter les sliders',
                'create_sliders' => 'Créer des sliders',
                'edit_sliders' => 'Modifier les sliders',
                'delete_sliders' => 'Supprimer les sliders',
                'manage_statuts_etat' => 'Gestion statuts état',
                'view_statuts_etat' => 'Consulter statuts état',
                'create_statuts_etat' => 'Créer statuts état',
                'edit_statuts_etat' => 'Modifier statuts état',
                'delete_statuts_etat' => 'Supprimer statuts état',
                'manage_villes' => 'Gestion des villes',
                'view_villes' => 'Consulter les villes',
                'create_villes' => 'Créer des villes',
                'edit_villes' => 'Modifier les villes',
                'delete_villes' => 'Supprimer les villes',
                'manage_parrainages' => 'Gestion des parrainages',
                'view_parrainages' => 'Consulter les parrainages',
                'create_parrainages' => 'Créer des parrainages',
                'edit_parrainages' => 'Modifier des parrainages',
                'delete_parrainages' => 'Supprimer des parrainages'
            ],
            'réservations' => [
                'manage_reservation_achat' => 'Gestion réservations achat',
                'view_reservation_achat' => 'Consulter réservations achat',
                'create_reservation_achat' => 'Créer réservations achat',
                'edit_reservation_achat' => 'Modifier réservations achat',
                'delete_reservation_achat' => 'Supprimer réservations achat'
            ],
            'rapports' => [
                'view_reports' => 'Voir les rapports',
                'create_reports' => 'Créer des rapports',
                'export_reports' => 'Exporter des rapports'
            ],
            'sécurité' => [
                'financial_access' => 'Accès financier',
                'database_access' => 'Accès base de données',
                'server_admin' => 'Administration serveur',
                'security_admin' => 'Administration sécurité',
                'audit_logs' => 'Journaux d\'audit',
                'backup_restore' => 'Sauvegarde/Restauration'
            ]
        ];
    }

    private function getDefaultPermissionsByRoleId($roleId): string
    {
        $roleMapping = [
            1 => 'Super Admin',
            2 => 'Admin', 
            3 => 'Agent Comptoir',
            4 => 'Commercial'
        ];
        
        $roleName = $roleMapping[$roleId] ?? 'Utilisateur';
        
        return $this->getDefaultPermissionsByRoleName($roleName);
    }

    private function generatePermissionBadges($role): string
    {
        try {
            if ($role->name === 'Super Admin' || stripos($role->name, 'super') !== false) {
                return $this->getSuperAdminBadge();
            }

            $permissions = $this->getRolePermissions($role);

            if (empty($permissions)) {
                return $this->getDefaultPermissionsByRoleName($role->name);
            }

            return $this->formatPermissionsBadges($permissions, $role->name);

        } catch (\Exception $e) {
            Log::error('Erreur génération badges:', [
                'role_name' => $role->name,
                'error' => $e->getMessage()
            ]);
            
            return $this->getDefaultPermissionsByRoleName($role->name);
        }
    }

    private function getRolePermissions($role): array
    {
        try {
            if (!Schema::hasTable('permissions') || !Schema::hasTable('role_has_permissions')) {
                return [];
            }

            $rolePermissions = $role->permissions()->get(['name']);
            
            if ($rolePermissions->count() > 0) {
                return $rolePermissions->map(function($permission) {
                    return [
                        'name' => $permission->name,
                        'display_name' => $this->getPermissionDisplayName($permission->name)
                    ];
                })->toArray();
            }
            
            return [];
            
        } catch (\Exception $e) {
            Log::error('Erreur récupération permissions Spatie:', [
                'error' => $e->getMessage(),
                'role_name' => $role->name ?? 'Unknown'
            ]);
            
            return [];
        }
    }

    private function getDefaultPermissionsByRoleName($roleName): string
    {
        $defaultPermissions = [
            'Super Admin' => [
                ['name' => 'all_permissions', 'display_name' => 'Toutes les Permissions'],
                ['name' => 'system_admin', 'display_name' => 'Administration Système'],
                ['name' => 'manage_users', 'display_name' => 'Gestion Utilisateurs'],
                ['name' => 'manage_roles', 'display_name' => 'Gestion Rôles'],
                ['name' => 'manage_permissions', 'display_name' => 'Gestion Permissions'],
                ['name' => 'financial_access', 'display_name' => 'Accès Financier'],
                ['name' => 'database_access', 'display_name' => 'Accès Base de Données'],
                ['name' => 'security_admin', 'display_name' => 'Administration Sécurité']
            ],
            'Admin' => [
                ['name' => 'manage_users', 'display_name' => 'Gestion Utilisateurs'],
                ['name' => 'manage_roles', 'display_name' => 'Gestion Rôles'],
                ['name' => 'manage_profil_visa', 'display_name' => 'Gestion Profils Visa'],
                ['name' => 'view_dashboard_admin', 'display_name' => 'Dashboard Admin'],
                ['name' => 'financial_access', 'display_name' => 'Accès Financier'],
                ['name' => 'manage_agents', 'display_name' => 'Gestion Agents']
            ],
            'Agent Comptoir' => [
                ['name' => 'manage_profil_visa', 'display_name' => 'Gestion Profils Visa'],
                ['name' => 'view_profil_visa', 'display_name' => 'Consultation Profils'],
                ['name' => 'edit_profil_visa_status', 'display_name' => 'Modification Statuts'],
                ['name' => 'add_message_profil_visa', 'display_name' => 'Ajout Messages'],
                ['name' => 'view_dashboard_comptoir', 'display_name' => 'Dashboard Comptoir'],
                ['name' => 'manage_rendez_vous', 'display_name' => 'Gestion Rendez-vous']
            ],
            'Commercial' => [
                ['name' => 'manage_clients', 'display_name' => 'Gestion Clients'],
                ['name' => 'manage_forfaits', 'display_name' => 'Gestion Forfaits'],
                ['name' => 'view_dashboard_commercial', 'display_name' => 'Dashboard Commercial'],
                ['name' => 'manage_partenaires', 'display_name' => 'Gestion Partenaires'],
                ['name' => 'sales_tracking', 'display_name' => 'Suivi Ventes'],
                ['name' => 'client_communication', 'display_name' => 'Communication Client']
            ]
        ];

        $permissions = null;
        foreach ($defaultPermissions as $role => $perms) {
            if (strcasecmp($role, $roleName) === 0 || stripos($roleName, $role) !== false) {
                $permissions = $perms;
                break;
            }
        }

        if (!$permissions) {
            $permissions = [
                ['name' => 'basic_access', 'display_name' => 'Accès de Base'],
                ['name' => 'view_dashboard', 'display_name' => 'Voir Dashboard']
            ];
        }

        if (stripos($roleName, 'super') !== false || (stripos($roleName, 'admin') !== false && count($permissions) > 10)) {
            return $this->getSuperAdminBadge();
        }

        return $this->formatPermissionsBadges($permissions, $roleName);
    }

    private function formatPermissionsBadges($permissions, $roleName = ''): string
    {
        if (empty($permissions)) {
            return $this->getNoPermissionsHtml();
        }

        $html = '<div class="permissions-badges-container">';
        
        if ($roleName) {
            $html .= '<div class="role-title mb-2"><small class="text-muted">Permissions pour <strong>' . htmlspecialchars($roleName) . '</strong>:</small></div>';
        }
        
        $html .= '<div class="permissions-grid" style="display: flex; flex-wrap: wrap; gap: 5px;">';
        
        $count = 0;
        $maxDisplay = 8;
        
        foreach ($permissions as $permission) {
            if ($count >= $maxDisplay) {
                $remaining = count($permissions) - $maxDisplay;
                $html .= '<span class="badge bg-secondary permission-badge ms-1 mb-1" title="' . $remaining . ' autres permissions">' 
                       . '<i class="fas fa-plus me-1"></i>+' . $remaining . '</span>';
                break;
            }
            
            $name = is_array($permission) ? $permission['name'] : $permission;
            $displayName = is_array($permission) ? ($permission['display_name'] ?? $name) : $name;
            $badgeClass = $this->getPermissionBadgeClass($name);
            $truncatedName = $this->truncatePermissionName($displayName, 20);
            
            $html .= '<span class="badge ' . $badgeClass . ' permission-badge ms-1 mb-1" 
                           title="' . htmlspecialchars($displayName) . '"
                           style="font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 8px;">' 
                   . '<i class="fas fa-key me-1"></i>' . htmlspecialchars($truncatedName) . '</span>';
            
            $count++;
        }
        
        $html .= '</div></div>';
        
        return $html;
    }

    private function getSuperAdminBadge(): string
    {
        return '
            <div class="alert alert-warning super-admin-badge mb-0" style="border-radius: 8px; border: 2px solid #ffc107; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
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
        ';
    }

    private function getNoPermissionsHtml(): string
    {
        return '
            <div class="alert alert-warning mb-0" role="alert" style="border-radius: 8px;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Aucune permission</strong> assignée à ce rôle.
                <br><small class="text-muted mt-1">Sélectionnez un rôle pour voir les permissions associées.</small>
            </div>
        ';
    }

    private function getPermissionBadgeClass($permission): string
    {
        $normalizedPermission = strtolower($permission);
        
        if (strpos($normalizedPermission, 'manage') !== false || strpos($normalizedPermission, 'gestion') !== false) {
            return 'bg-primary text-white';
        } elseif (strpos($normalizedPermission, 'view') !== false || strpos($normalizedPermission, 'dashboard') !== false) {
            return 'bg-success text-white';
        } elseif (strpos($normalizedPermission, 'edit') !== false || strpos($normalizedPermission, 'modifier') !== false) {
            return 'bg-warning text-dark';
        } elseif (strpos($normalizedPermission, 'delete') !== false || strpos($normalizedPermission, 'supprimer') !== false) {
            return 'bg-danger text-white';
        } elseif (strpos($normalizedPermission, 'export') !== false || strpos($normalizedPermission, 'import') !== false) {
            return 'bg-info text-white';
        } elseif (strpos($normalizedPermission, 'create') !== false || strpos($normalizedPermission, 'add') !== false) {
            return 'bg-secondary text-white';
        } elseif (strpos($normalizedPermission, 'admin') !== false || strpos($normalizedPermission, 'system') !== false) {
            return 'bg-danger text-white';
        } else {
            return 'bg-dark text-white';
        }
    }

    private function getPermissionDisplayName($permission): string
    {
        $displayNames = [
            'manage_users' => 'Gestion Utilisateurs',
            'manage_roles' => 'Gestion Rôles',
            'manage_permissions' => 'Gestion Permissions',
            'system_admin' => 'Administration Système',
            'manage_profil_visa' => 'Gestion Profils Visa',
            'view_profil_visa' => 'Consultation Profils',
            'delete_profil_visa' => 'Suppression Profils',
            'edit_profil_visa_status' => 'Modification Statuts',
            'add_message_profil_visa' => 'Ajout Messages',
            'manage_rendez_vous' => 'Gestion Rendez-vous',
            'manage_clients' => 'Gestion Clients',
            'manage_forfaits' => 'Gestion Forfaits',
            'view_dashboard_admin' => 'Dashboard Admin',
            'view_dashboard_comptoir' => 'Dashboard Comptoir',
            'view_dashboard_commercial' => 'Dashboard Commercial',
            'financial_access' => 'Accès Financier',
            'export_data' => 'Export Données',
            'basic_access' => 'Accès de Base'
        ];

        return $displayNames[$permission] ?? ucwords(str_replace(['_', '-'], ' ', $permission));
    }

    private function truncatePermissionName($name, $length = 25): string
    {
        return strlen($name) > $length ? substr($name, 0, $length) . '...' : $name;
    }

    private function formatPermissionActions($permission): string
    {
        return "
            <div class='btn-group' role='group'>
                <button class='btn btn-sm btn-outline-primary' onclick='editPermission({$permission->id})' title='Modifier'>
                    <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-outline-info' onclick='viewPermission({$permission->id})' title='Voir détails'>
                    <i class='fas fa-eye'></i>
                </button>
                <button class='btn btn-sm btn-outline-danger' onclick='deletePermission({$permission->id})' title='Supprimer'>
                    <i class='fas fa-trash'></i>
                </button>
            </div>
        ";
    }

    private function getHealthStatusMessage($status): string
    {
        $messages = [
            'healthy' => 'Système de permissions opérationnel',
            'incomplete' => 'Système partiellement configuré',
            'critical' => 'Problèmes critiques détectés',
            'error' => 'Erreur lors de la vérification'
        ];

        return $messages[$status] ?? 'Statut inconnu';
    }

    // Méthodes de statistiques
    private function getTotalPermissions(): int
    {
        try {
            return Schema::hasTable('permissions') ? Permission::count() : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPermissionsWithRoles(): int
    {
        try {
            return Schema::hasTable('role_has_permissions') ? Permission::has('roles')->count() : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPermissionsWithoutRoles(): int
    {
        try {
            return Schema::hasTable('role_has_permissions') ? Permission::doesntHave('roles')->count() : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMostUsedPermission(): string
    {
        try {
            if (Schema::hasTable('role_has_permissions')) {
                $permission = Permission::withCount('roles')->orderBy('roles_count', 'desc')->first();
                return $permission ? $permission->name : 'Aucune';
            }
            return 'Aucune';
        } catch (\Exception $e) {
            return 'Aucune';
        }
    }

    private function getSystemHealthStatus(): string
    {
        try {
            $requiredTables = ['permissions', 'roles', 'role_has_permissions'];
            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    return 'Incomplet';
                }
            }
            return 'Opérationnel';
        } catch (\Exception $e) {
            return 'Erreur';
        }
    }
}