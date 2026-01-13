<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsDiagnosticController extends Controller
{
    /**
     * Diagnostic complet des permissions d'un utilisateur
     */
    public function diagnoseUser(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Diagnostic détaillé
            $diagnostic = [
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type_user' => $user->type_user,
                    'etat' => $user->etat,
                    'matricule' => $user->matricule
                ],
                'roles_info' => [
                    'count' => $user->roles()->count(),
                    'list' => $user->getRoleNames()->toArray(),
                    'has_commercial_role' => $user->hasRole('Commercial'),
                    'roles_with_permissions' => $user->roles->map(function($role) {
                        return [
                            'name' => $role->name,
                            'permissions_count' => $role->permissions()->count(),
                            'permissions' => $role->permissions->pluck('name')->toArray()
                        ];
                    })
                ],
                'permissions_info' => [
                    'direct_count' => $user->permissions()->count(),
                    'total_count' => $user->getAllPermissions()->count(),
                    'list' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'critical_permissions' => $this->checkCriticalPermissions($user)
                ],
                'access_checks' => [
                    'can_access_commercial_dashboard' => $this->canAccessCommercialDashboard($user),
                    'can_manage_clients' => $user->hasPermissionTo('manage_clients'),
                    'can_manage_forfaits' => $user->hasPermissionTo('manage_forfaits'),
                    'can_view_dashboard_commercial' => $user->hasPermissionTo('view_dashboard_commercial')
                ],
                'system_info' => [
                    'spatie_permission_loaded' => class_exists('Spatie\Permission\Models\Permission'),
                    'total_roles_in_system' => Role::count(),
                    'total_permissions_in_system' => Permission::count(),
                    'cache_status' => $this->checkCacheStatus()
                ]
            ];

            return response()->json([
                'success' => true,
                'diagnostic' => $diagnostic,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur diagnostic permissions:', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du diagnostic: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Réparer les permissions d'un utilisateur
     */
    public function repairUserPermissions(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('Début réparation permissions', ['user_id' => $user->id]);

            $results = [];

            // 1. Vérifier et assigner le rôle selon type_user
            if ($user->type_user === 'commercial' && !$user->hasRole('Commercial')) {
                $user->assignRole('Commercial');
                $results[] = 'Rôle Commercial assigné';
                Log::info('Rôle Commercial assigné', ['user_id' => $user->id]);
            }

            // 2. Synchroniser les permissions du rôle
            if ($user->hasRole('Commercial')) {
                $commercialRole = Role::where('name', 'Commercial')->first();
                if ($commercialRole) {
                    // Forcer la resynchronisation
                    $user->syncRoles(['Commercial']);
                    $results[] = 'Rôles resynchronisés';
                    Log::info('Rôles resynchronisés', ['user_id' => $user->id]);
                }
            }

            // 3. Vider le cache des permissions
            Artisan::call('permission:cache-reset');
            $results[] = 'Cache des permissions vidé';

            // 4. Diagnostic post-réparation
            $user->refresh();
            $permissionsApres = $user->getAllPermissions()->count();
            $rolesApres = $user->getRoleNames()->count();

            $results[] = "Permissions après réparation: {$permissionsApres}";
            $results[] = "Rôles après réparation: {$rolesApres}";

            Log::info('Réparation terminée', [
                'user_id' => $user->id,
                'permissions_count' => $permissionsApres,
                'roles_count' => $rolesApres,
                'results' => $results
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réparation effectuée avec succès',
                'results' => $results,
                'new_permissions_count' => $permissionsApres,
                'new_roles_count' => $rolesApres
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur réparation permissions:', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la réparation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser tous les commerciaux
     */
    public function syncAllCommercials(Request $request)
    {
        try {
            // Vérifier que l'utilisateur peut faire cette action
            if (!Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
                return response()->json(['error' => 'Action non autorisée'], 403);
            }

            $commerciaux = User::where('type_user', 'commercial')
                ->where('ent1d', 1)
                ->get();

            $results = [];
            $fixed = 0;

            foreach ($commerciaux as $commercial) {
                $beforePermissions = $commercial->getAllPermissions()->count();
                
                // Assigner le rôle Commercial s'il ne l'a pas
                if (!$commercial->hasRole('Commercial')) {
                    $commercial->assignRole('Commercial');
                    $fixed++;
                    $results[] = "Rôle Commercial assigné à {$commercial->name}";
                }

                $afterPermissions = $commercial->fresh()->getAllPermissions()->count();
                
                if ($beforePermissions !== $afterPermissions) {
                    $results[] = "{$commercial->name}: {$beforePermissions} → {$afterPermissions} permissions";
                }
            }

            // Vider le cache
            Artisan::call('permission:cache-reset');
            $results[] = 'Cache des permissions vidé';

            Log::info('Synchronisation commerciaux terminée', [
                'total_commerciaux' => $commerciaux->count(),
                'commerciaux_fixes' => $fixed,
                'admin_user' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => "Synchronisation terminée: {$fixed} commerciaux corrigés sur {$commerciaux->count()}",
                'results' => $results,
                'stats' => [
                    'total_commerciaux' => $commerciaux->count(),
                    'commerciaux_fixes' => $fixed
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur synchronisation commerciaux:', [
                'admin_user' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des permissions
     */
    public function getPermissionsStats()
    {
        try {
            $stats = [
                'system' => [
                    'total_roles' => Role::count(),
                    'total_permissions' => Permission::count(),
                    'total_users' => User::where('ent1d', 1)->count()
                ],
                'by_type' => [
                    'admin' => [
                        'count' => User::where('type_user', 'admin')->where('ent1d', 1)->count(),
                        'with_permissions' => User::where('type_user', 'admin')
                            ->where('ent1d', 1)
                            ->get()
                            ->filter(function($user) {
                                return $user->getAllPermissions()->count() > 0;
                            })->count()
                    ],
                    'agent_comptoir' => [
                        'count' => User::where('type_user', 'agent_comptoir')->where('ent1d', 1)->count(),
                        'with_permissions' => User::where('type_user', 'agent_comptoir')
                            ->where('ent1d', 1)
                            ->get()
                            ->filter(function($user) {
                                return $user->getAllPermissions()->count() > 0;
                            })->count()
                    ],
                    'commercial' => [
                        'count' => User::where('type_user', 'commercial')->where('ent1d', 1)->count(),
                        'with_permissions' => User::where('type_user', 'commercial')
                            ->where('ent1d', 1)
                            ->get()
                            ->filter(function($user) {
                                return $user->getAllPermissions()->count() > 0;
                            })->count()
                    ]
                ],
                'roles_distribution' => Role::withCount('users')->get()->map(function($role) {
                    return [
                        'name' => $role->name,
                        'users_count' => $role->users_count,
                        'permissions_count' => $role->permissions()->count()
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Méthodes privées
     */
    private function checkCriticalPermissions($user): array
    {
        $criticalPermissions = [
            'manage_clients',
            'manage_forfaits', 
            'manage_partenaires',
            'view_dashboard_commercial',
            'manage_souscrire_forfaits'
        ];

        $result = [];
        foreach ($criticalPermissions as $permission) {
            $result[$permission] = $user->hasPermissionTo($permission);
        }

        return $result;
    }

    private function canAccessCommercialDashboard($user): bool
    {
        return $user->hasRole('Commercial') || 
               $user->type_user === 'commercial' ||
               $user->hasAnyRole(['Super Admin', 'Admin']) ||
               $user->hasPermissionTo('view_dashboard_commercial');
    }

    private function checkCacheStatus(): array
    {
        try {
            // Tester si le cache fonctionne
            cache()->put('permission_test', 'ok', 60);
            $cacheWorks = cache()->get('permission_test') === 'ok';
            
            return [
                'cache_working' => $cacheWorks,
                'driver' => config('cache.default'),
                'permission_cache_exists' => cache()->has('spatie.permission.cache')
            ];
        } catch (\Exception $e) {
            return [
                'cache_working' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}