<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesController extends Controller
{
    /**
     * ✅ CORRECTION FINALE : Afficher la liste des rôles SANS DOUBLONS
     */
    public function index(Request $request)
    {
        try {
            // Vérifier les permissions
            if (!Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect('/')->with('error', 'Accès non autorisé - Réservé aux administrateurs');
            }

            // Récupérer les permissions de manière sécurisée
            $permissions = $this->getSafePermissions();

            Log::info('RolesController index - Permissions chargées:', ['count' => $permissions->count()]);

            return view('roles', compact('permissions'));
        } catch (\Exception $e) {
            Log::error('Erreur RolesController index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du chargement des rôles');
        }
    }

    /**
     * ✅ CORRECTION MAJEURE : API DataTables SANS DOUBLONS
     */
    public function getRoleList(Request $request)
    {
        try {
            Log::info('🚀 getRoleList: Début requête DataTables - CORRECTION DOUBLONS');
            
            // Vérifications de base
            $user = Auth::user();
            if (!$user || !$user->hasAnyRole(['Admin', 'Super Admin'])) {
                return $this->errorResponse('Accès refusé', $request);
            }

            // ✅ CORRECTION : Query avec DISTINCT pour éviter les doublons
            $query = Role::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
                ->distinct()
                ->orderBy('name', 'asc');
            
            $data = $query->get();
            
            Log::info("✅ getRoleList: {$data->count()} rôles récupérés SANS DOUBLONS");

            return Datatables::of($data)
                ->addColumn('permissions', function ($role) {
                    return $this->formatPermissionsWithoutDuplicates($role);
                })
                ->addColumn('users_count', function ($role) {
                    return $this->formatUsersCountSafe($role);
                })
                ->addColumn('created_at', function ($role) {
                    return $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('action', function ($role) {
                    return $this->formatActionsSafe($role);
                })
                ->rawColumns(['permissions', 'users_count', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('❌ getRoleList error: ' . $e->getMessage());
            return $this->errorResponse('Erreur serveur: ' . $e->getMessage(), $request);
        }
    }

    /**
     * ✅ CORRECTION : Formater les permissions SANS DOUBLONS
     */
    private function formatPermissionsWithoutDuplicates($role): string
    {
        try {
            if ($role->name == 'Super Admin') {
                return '<span class="badge bg-success m-1">
                    <i class="fas fa-crown me-1"></i>Toutes les permissions
                </span>';
            }
            
            // ✅ RÉCUPÉRATION SÉCURISÉE DES PERMISSIONS UNIQUES
            $permissions = $this->getRolePermissionsUnique($role);
            
            if (empty($permissions)) {
                return '<span class="badge bg-warning m-1">
                    <i class="fas fa-exclamation-triangle me-1"></i>Aucune permission
                </span>';
            }
            
            $badges = '';
            $count = 0;
            $maxDisplay = 4; // Limiter l'affichage
            
            // ✅ AFFICHAGE SANS DOUBLONS
            foreach ($permissions as $permission) {
                if ($count < $maxDisplay) {
                    $badges .= '<span class="badge bg-dark m-1">' . e($permission) . '</span>';
                    $count++;
                }
            }
            
            if (count($permissions) > $maxDisplay) {
                $remaining = count($permissions) - $maxDisplay;
                $badges .= '<span class="badge bg-secondary m-1">+' . $remaining . '</span>';
            }

            return $badges;

        } catch (\Exception $e) {
            Log::error('Erreur formatage permissions: ' . $e->getMessage());
            return '<span class="badge bg-danger m-1">Erreur</span>';
        }
    }

    /**
     * ✅ NOUVEAU : Récupérer les permissions uniques d'un rôle
     */
    private function getRolePermissionsUnique($role): array
    {
        try {
            if (!Schema::hasTable('role_has_permissions') || !Schema::hasTable('permissions')) {
                return [];
            }

            // ✅ QUERY AVEC DISTINCT POUR ÉVITER LES DOUBLONS
            $permissions = DB::table('role_has_permissions')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_has_permissions.role_id', $role->id)
                ->distinct()
                ->pluck('permissions.name')
                ->toArray();

            // ✅ SUPPRESSION FINALE DES DOUBLONS AVEC array_unique
            return array_values(array_unique($permissions));

        } catch (\Exception $e) {
            Log::warning('Erreur récupération permissions uniques: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ CORRECTION : Compter les utilisateurs de manière sécurisée
     */
    private function formatUsersCountSafe($role): string
    {
        try {
            $usersCount = $this->getRoleUsersCountSafe($role);
            $color = $usersCount > 0 ? 'info' : 'secondary';
            return '<span class="badge bg-' . $color . '">' . $usersCount . ' utilisateur(s)</span>';
        } catch (\Exception $e) {
            Log::error('Erreur formatage compteur utilisateurs: ' . $e->getMessage());
            return '<span class="badge bg-secondary">0 utilisateur(s)</span>';
        }
    }

    /**
     * ✅ NOUVEAU : Compter les utilisateurs sans doublons
     */
    private function getRoleUsersCountSafe($role): int
    {
        try {
            if (!Schema::hasTable('model_has_roles')) {
                return 0;
            }

            // ✅ COMPTER AVEC DISTINCT POUR ÉVITER LES DOUBLONS
            return DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', 'App\\Models\\User')
                ->distinct('model_id')
                ->count('model_id');

        } catch (\Exception $e) {
            Log::warning('Erreur comptage utilisateurs: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ CORRECTION : Actions sécurisées
     */
    private function formatActionsSafe($role): string
    {
        $hasManageRoles = Auth::user()->hasAnyRole(['Admin', 'Super Admin']);
        
        if (!$hasManageRoles) {
            return '';
        }

        if ($role->name == 'Super Admin') {
            return '<span class="text-muted small">
                <i class="fas fa-shield-alt"></i> Protégé
            </span>';
        }

        $usersCount = $this->getRoleUsersCountSafe($role);
        
        $output = '<div class="btn-group btn-group-sm" role="group">
            <a href="' . url('roles/' . $role->id . '/edit') . '" 
               class="btn btn-outline-primary" 
               title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            <button class="btn btn-outline-info" 
                    onclick="viewRole(' . $role->id . ')" 
                    title="Voir détails">
                <i class="fas fa-eye"></i>
            </button>';
        
        // Permettre suppression seulement si pas d'utilisateurs
        if ($usersCount == 0) {
            $output .= '<a href="' . url('roles/' . $role->id . '/delete') . '" 
                          class="btn btn-outline-danger" 
                          onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce rôle ?\')" 
                          title="Supprimer">
                <i class="fas fa-trash"></i>
            </a>';
        } else {
            $output .= '<button class="btn btn-outline-secondary" 
                               disabled 
                               title="Impossible de supprimer - ' . $usersCount . ' utilisateur(s) assigné(s)">
                <i class="fas fa-ban"></i>
            </button>';
        }
        
        $output .= '</div>';

        return $output;
    }

    /**
     * ✅ CORRECTION : Créer un rôle SANS DOUBLONS
     */
    public function create(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            // ✅ VÉRIFIER LES DOUBLONS AVANT CRÉATION
            $existingRole = Role::where('name', $request->name)->first();
            if ($existingRole) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Un rôle avec ce nom existe déjà');
            }

            // Créer le rôle
            $role = Role::create([
                'name' => $request->name, 
                'guard_name' => 'web'
            ]);
            
            // ✅ ASSIGNER LES PERMISSIONS SANS DOUBLONS
            if ($request->permissions && is_array($request->permissions)) {
                $this->assignPermissionsUniqueToRole($role, $request->permissions);
            }

            DB::commit();

            Log::info('Nouveau rôle créé SANS DOUBLONS:', [
                'role_id' => $role->id,
                'name' => $role->name,
                'permissions_count' => count($request->permissions ?? [])
            ]);
            
            return redirect('roles')->with('success', 'Rôle créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur RolesController create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du rôle: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Assigner des permissions uniques à un rôle
     */
    private function assignPermissionsUniqueToRole($role, $permissionIds)
    {
        try {
            if (!Schema::hasTable('permissions') || !Schema::hasTable('role_has_permissions')) {
                return;
            }

            // ✅ NETTOYER D'ABORD LES DOUBLONS EXISTANTS
            DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->delete();

            // ✅ RÉCUPÉRER LES PERMISSIONS UNIQUES
            $uniquePermissionIds = array_unique($permissionIds);
            $permissions = Permission::whereIn('id', $uniquePermissionIds)->get();
            
            // ✅ SYNCHRONISER (ÉVITE LES DOUBLONS AUTOMATIQUEMENT)
            $role->syncPermissions($permissions);

            Log::info("Permissions uniques assignées au rôle {$role->name}: " . count($permissions));

        } catch (\Exception $e) {
            Log::error('Erreur assignation permissions uniques: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION : Mettre à jour un rôle SANS DOUBLONS
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            $role = Role::find($request->id);

            if (!$role) {
                return redirect('roles')->with('error', 'Rôle non trouvé');
            }

            // Protéger le rôle Super Admin
            if ($role->name == 'Super Admin') {
                return redirect('roles')->with('error', 'Le rôle Super Admin ne peut pas être modifié');
            }

            // ✅ VÉRIFIER L'UNICITÉ DU NOM
            if ($role->name !== $request->name) {
                $existingRole = Role::where('name', $request->name)
                    ->where('id', '!=', $request->id)
                    ->first();
                if ($existingRole) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Ce nom de rôle existe déjà');
                }
            }

            DB::beginTransaction();

            // Mettre à jour le nom du rôle
            $role->update(['name' => $request->name]);

            // ✅ SYNCHRONISER LES PERMISSIONS SANS DOUBLONS
            $this->syncRolePermissionsUnique($role, $request->permissions ?? []);

            DB::commit();

            Log::info('Rôle mis à jour SANS DOUBLONS:', [
                'role_id' => $role->id,
                'name' => $role->name,
                'permissions_count' => count($request->permissions ?? [])
            ]);

            return redirect('roles')->with('success', 'Informations du rôle mises à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur RolesController update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du rôle: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Synchroniser les permissions sans doublons
     */
    private function syncRolePermissionsUnique($role, $permissionIds)
    {
        try {
            if (!Schema::hasTable('permissions') || !Schema::hasTable('role_has_permissions')) {
                return;
            }

            // ✅ NETTOYER TOUS LES DOUBLONS EXISTANTS
            DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->delete();

            if (empty($permissionIds)) {
                // Aucune permission à assigner
                Log::info("Toutes les permissions supprimées pour le rôle {$role->name}");
                return;
            }

            // ✅ ASSIGNER UNIQUEMENT LES PERMISSIONS UNIQUES
            $uniquePermissionIds = array_unique($permissionIds);
            $permissions = Permission::whereIn('id', $uniquePermissionIds)->get();
            
            // Utiliser syncPermissions qui évite automatiquement les doublons
            $role->syncPermissions($permissions);

            Log::info("Permissions synchronisées pour le rôle {$role->name}: " . count($permissions));

        } catch (\Exception $e) {
            Log::error('Erreur synchronisation permissions uniques: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION : Supprimer un rôle (inchangé mais sécurisé)
     */
    public function delete($id): RedirectResponse
    {
        try {
            $role = Role::find($id);
            
            if (!$role) {
                return redirect('roles')->with('error', 'Rôle non trouvé');
            }

            // Protéger le rôle Super Admin
            if ($role->name == 'Super Admin') {
                return redirect('roles')->with('error', 'Le rôle Super Admin ne peut pas être supprimé');
            }

            // Vérifier si le rôle a des utilisateurs assignés
            $usersCount = $this->getRoleUsersCountSafe($role);
            if ($usersCount > 0) {
                return redirect('roles')->with('error', "Impossible de supprimer ce rôle car il est assigné à {$usersCount} utilisateur(s)");
            }

            DB::beginTransaction();

            // ✅ NETTOYER TOUTES LES RELATIONS AVANT SUPPRESSION
            $this->cleanRoleRelations($role);
            
            $roleName = $role->name;
            $role->delete();

            DB::commit();

            Log::info('Rôle supprimé:', ['role_name' => $roleName]);

            return redirect('roles')->with('success', 'Rôle supprimé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur RolesController delete: ' . $e->getMessage());
            return redirect('roles')->with('error', 'Erreur lors de la suppression du rôle: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Nettoyer toutes les relations d'un rôle
     */
    private function cleanRoleRelations($role)
    {
        try {
            // Supprimer les permissions
            if (Schema::hasTable('role_has_permissions')) {
                DB::table('role_has_permissions')
                    ->where('role_id', $role->id)
                    ->delete();
            }

            // Supprimer les assignations utilisateurs
            if (Schema::hasTable('model_has_roles')) {
                DB::table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->delete();
            }

            Log::info("Relations nettoyées pour le rôle {$role->name}");

        } catch (\Exception $e) {
            Log::error('Erreur nettoyage relations: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION : Obtenir les détails d'un rôle pour API
     */
    public function getRoleDetails($id)
    {
        try {
            $role = Role::find($id);
            
            if (!$role) {
                return response()->json(['success' => false, 'error' => 'Rôle non trouvé'], 404);
            }

            // Récupérer les données du rôle de manière sécurisée
            $roleData = [
                'id' => $role->id,
                'name' => $role->name,
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ];

            // ✅ RÉCUPÉRER LES PERMISSIONS SANS DOUBLONS
            $permissions = $this->getRolePermissionsUnique($role);
            
            // ✅ RÉCUPÉRER LES UTILISATEURS SANS DOUBLONS
            $users = $this->getRoleUsersUnique($role);

            return response()->json([
                'success' => true,
                'role' => $roleData,
                'permissions' => $permissions,
                'permissions_count' => count($permissions),
                'users_count' => count($users),
                'users' => array_slice($users, 0, 5) // Limiter à 5 utilisateurs pour l'affichage
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur RolesController getRoleDetails: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Récupérer les utilisateurs uniques d'un rôle
     */
    private function getRoleUsersUnique($role): array
    {
        try {
            if (!Schema::hasTable('model_has_roles') || !Schema::hasTable('users')) {
                return [];
            }

            // ✅ QUERY AVEC DISTINCT POUR ÉVITER LES DOUBLONS
            $users = DB::table('model_has_roles')
                ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.role_id', $role->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->select('users.id', 'users.name', 'users.email', 'users.type_user')
                ->distinct()
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type_user' => $user->type_user ?? 'public'
                    ];
                })
                ->toArray();

            return array_values($users); // Reindexer le tableau

        } catch (\Exception $e) {
            Log::warning('Erreur récupération utilisateurs uniques: ' . $e->getMessage());
            return [];
        }
    }

    // ==================== MÉTHODES UTILITAIRES CONSERVÉES ====================

    /**
     * Récupérer les permissions de manière sécurisée
     */
    private function getSafePermissions()
    {
        try {
            if (Schema::hasTable('permissions')) {
                return Permission::orderBy('name', 'asc')->pluck('name', 'id');
            }
        } catch (\Exception $e) {
            Log::warning('Erreur récupération permissions: ' . $e->getMessage());
        }
        return collect();
    }

    /**
     * ✅ CORRECTION : Éditer un rôle (route qui manquait)
     */
    public function edit($id)
    {
        try {
            // Vérifier les permissions
            if (!Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
                return redirect('/')->with('error', 'Accès non autorisé');
            }

            $role = Role::find($id);

            if (!$role) {
                return redirect('roles')->with('error', 'Rôle non trouvé');
            }

            // Protéger le rôle Super Admin
            if ($role->name == 'Super Admin') {
                return redirect('roles')->with('error', 'Le rôle Super Admin ne peut pas être modifié');
            }

            // ✅ RÉCUPÉRER LES PERMISSIONS DU RÔLE SANS DOUBLONS
            $role_permission = $this->getRolePermissionIds($role);
            
            // Récupérer toutes les permissions disponibles
            $permissions = $this->getSafePermissions();

            Log::info('Edition rôle SANS DOUBLONS:', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($role_permission),
                'total_permissions' => $permissions->count()
            ]);

            return view('edit-roles', compact('role', 'role_permission', 'permissions'));

        } catch (\Exception $e) {
            Log::error('Erreur RolesController edit: ' . $e->getMessage());
            return redirect('roles')->with('error', 'Erreur lors du chargement du rôle: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVEAU : Récupérer les IDs des permissions d'un rôle sans doublons
     */
    private function getRolePermissionIds($role): array
    {
        try {
            if (!Schema::hasTable('role_has_permissions')) {
                return [];
            }

            // ✅ RÉCUPÉRER LES IDS UNIQUES
            $permissionIds = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->distinct()
                ->pluck('permission_id')
                ->toArray();

            return array_values(array_unique($permissionIds));

        } catch (\Exception $e) {
            Log::warning('Erreur récupération IDs permissions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ CORRECTION : Statistiques des rôles
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_roles' => $this->getTotalRolesUnique(),
                'roles_with_users' => $this->getRolesWithUsersUnique(),
                'roles_without_users' => $this->getRolesWithoutUsersUnique(),
                'total_permissions' => $this->getTotalPermissions(),
                'most_used_role' => $this->getMostUsedRoleUnique(),
                'roles_by_type' => $this->getRolesByTypeUnique()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur RolesController getStatistics: ' . $e->getMessage());
            return response()->json([
                'total_roles' => 0,
                'roles_with_users' => 0,
                'roles_without_users' => 0,
                'total_permissions' => 0,
                'most_used_role' => 'Aucun',
                'roles_by_type' => [],
                'error' => true
            ], 500);
        }
    }

    // ==================== MÉTHODES DE STATISTIQUES CORRIGÉES ====================

    private function getTotalRolesUnique(): int
    {
        try {
            return Role::distinct('name')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRolesWithUsersUnique(): int
    {
        try {
            if (Schema::hasTable('model_has_roles')) {
                return Role::whereHas('users')->distinct()->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRolesWithoutUsersUnique(): int
    {
        try {
            if (Schema::hasTable('model_has_roles')) {
                return Role::doesntHave('users')->distinct()->count();
            }
            return $this->getTotalRolesUnique();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalPermissions(): int
    {
        try {
            if (Schema::hasTable('permissions')) {
                return Permission::distinct('name')->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMostUsedRoleUnique(): string
    {
        try {
            if (Schema::hasTable('model_has_roles')) {
                $role = Role::withCount('users')
                    ->orderBy('users_count', 'desc')
                    ->first();
                return $role ? $role->name : 'Aucun';
            }
            return 'Aucun';
        } catch (\Exception $e) {
            return 'Aucun';
        }
    }

    private function getRolesByTypeUnique(): array
    {
        try {
            if (Schema::hasTable('model_has_roles') && Schema::hasTable('role_has_permissions')) {
                return Role::withCount('users', 'permissions')
                    ->distinct()
                    ->get()
                    ->map(function($role) {
                        return [
                            'name' => $role->name,
                            'users_count' => $role->users_count ?? 0,
                            'permissions_count' => $role->permissions_count ?? 0
                        ];
                    })->toArray();
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Réponse d'erreur pour DataTables
     */
    private function errorResponse(string $message, Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $message
        ]);
    }
}