<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class CleanDuplicatesSeeder extends Seeder
{
    /**
     * ✅ SEEDER CORRECTIF : Nettoyer TOUS les doublons de rôles et permissions
     * 
     * @return void
     */
    public function run()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "🧹 NETTOYAGE COMPLET DES DOUBLONS PSI AFRICA\n";
        echo str_repeat("=", 80) . "\n\n";

        try {
            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // 1. Diagnostiquer les doublons
            $this->diagnoseDuplicates();

            // 2. Nettoyer les doublons de rôles
            $this->cleanRoleDuplicates();

            // 3. Nettoyer les doublons d'assignations rôles-utilisateurs
            $this->cleanUserRoleAssignments();

            // 4. Nettoyer les doublons de permissions
            $this->cleanPermissionDuplicates();

            // 5. Nettoyer les doublons d'assignations rôles-permissions
            $this->cleanRolePermissionAssignments();

            // 6. Vérifier l'intégrité finale
            $this->verifyIntegrity();

            // 7. Réassigner les rôles corrects
            $this->reassignCorrectRoles();

            // 8. Nettoyer le cache final
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            echo "\n" . str_repeat("=", 80) . "\n";
            echo "✅ NETTOYAGE TERMINÉ AVEC SUCCÈS !\n";
            echo "🎯 Plus aucun doublon de rôle ne devrait apparaître.\n";
            echo str_repeat("=", 80) . "\n";

        } catch (\Exception $e) {
            echo "\n❌ ERREUR CRITIQUE : " . $e->getMessage() . "\n";
            echo "Stack trace : " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    /**
     * 1. Diagnostiquer les doublons existants
     */
    private function diagnoseDuplicates()
    {
        echo "🔍 DIAGNOSTIC DES DOUBLONS\n";
        echo "-------------------------\n";

        try {
            // Diagnostic rôles dupliqués
            if (Schema::hasTable('roles')) {
                $duplicateRoles = DB::table('roles')
                    ->select('name', DB::raw('COUNT(*) as count'))
                    ->groupBy('name')
                    ->having('count', '>', 1)
                    ->get();

                echo "  📊 Rôles dupliqués trouvés: " . $duplicateRoles->count() . "\n";
                foreach ($duplicateRoles as $role) {
                    echo "    - {$role->name}: {$role->count} occurrences\n";
                }
            }

            // Diagnostic assignations utilisateur-rôle dupliquées
            if (Schema::hasTable('model_has_roles')) {
                $duplicateAssignments = DB::table('model_has_roles')
                    ->select('model_id', 'role_id', DB::raw('COUNT(*) as count'))
                    ->where('model_type', 'App\\Models\\User')
                    ->groupBy('model_id', 'role_id')
                    ->having('count', '>', 1)
                    ->get();

                echo "  📊 Assignations utilisateur-rôle dupliquées: " . $duplicateAssignments->count() . "\n";
            }

            // Diagnostic permissions dupliquées
            if (Schema::hasTable('permissions')) {
                $duplicatePermissions = DB::table('permissions')
                    ->select('name', DB::raw('COUNT(*) as count'))
                    ->groupBy('name')
                    ->having('count', '>', 1)
                    ->get();

                echo "  📊 Permissions dupliquées trouvées: " . $duplicatePermissions->count() . "\n";
                foreach ($duplicatePermissions as $permission) {
                    echo "    - {$permission->name}: {$permission->count} occurrences\n";
                }
            }

            echo "\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur diagnostic: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 2. Nettoyer les doublons de rôles
     */
    private function cleanRoleDuplicates()
    {
        echo "🧹 NETTOYAGE DES RÔLES DUPLIQUÉS\n";
        echo "-------------------------------\n";

        try {
            if (!Schema::hasTable('roles')) {
                echo "  ⚠️ Table roles manquante\n\n";
                return;
            }

            // Récupérer tous les rôles dupliqués
            $duplicateRoles = DB::table('roles')
                ->select('name')
                ->groupBy('name')
                ->having(DB::raw('COUNT(*)'), '>', 1)
                ->pluck('name');

            $cleanedCount = 0;

            foreach ($duplicateRoles as $roleName) {
                echo "  🔧 Nettoyage rôle: {$roleName}\n";

                // Récupérer tous les doublons de ce rôle
                $roles = DB::table('roles')->where('name', $roleName)->get();
                
                if ($roles->count() <= 1) continue;

                // Garder le premier (le plus ancien)
                $keepRole = $roles->first();
                $duplicatesToDelete = $roles->skip(1);

                echo "    - Garder: ID {$keepRole->id}\n";
                echo "    - Supprimer: " . $duplicatesToDelete->count() . " doublons\n";

                // Transférer toutes les assignations vers le rôle à garder
                foreach ($duplicatesToDelete as $duplicateRole) {
                    // Transférer les assignations utilisateurs
                    if (Schema::hasTable('model_has_roles')) {
                        DB::table('model_has_roles')
                            ->where('role_id', $duplicateRole->id)
                            ->update(['role_id' => $keepRole->id]);
                    }

                    // Transférer les assignations permissions
                    if (Schema::hasTable('role_has_permissions')) {
                        DB::table('role_has_permissions')
                            ->where('role_id', $duplicateRole->id)
                            ->update(['role_id' => $keepRole->id]);
                    }

                    // Supprimer le doublon
                    DB::table('roles')->where('id', $duplicateRole->id)->delete();
                    $cleanedCount++;
                }
            }

            echo "  ✅ {$cleanedCount} rôles dupliqués supprimés\n\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur nettoyage rôles: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * 3. Nettoyer les assignations utilisateur-rôle dupliquées
     */
    private function cleanUserRoleAssignments()
    {
        echo "🧹 NETTOYAGE ASSIGNATIONS UTILISATEUR-RÔLE\n";
        echo "-----------------------------------------\n";

        try {
            if (!Schema::hasTable('model_has_roles')) {
                echo "  ⚠️ Table model_has_roles manquante\n\n";
                return;
            }

            // Supprimer les doublons d'assignations
            $duplicatesRemoved = DB::statement("
                DELETE t1 FROM model_has_roles t1
                INNER JOIN model_has_roles t2 
                WHERE t1.id > t2.id 
                AND t1.model_id = t2.model_id 
                AND t1.role_id = t2.role_id 
                AND t1.model_type = t2.model_type
            ");

            echo "  ✅ Assignations dupliquées nettoyées\n";

            // Vérifier les utilisateurs avec multiple rôles du même type
            $usersWithMultipleRoles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->whereIn('users.type_user', ['admin', 'agent_comptoir', 'commercial'])
                ->select('model_has_roles.model_id', 'users.name', 'users.type_user', DB::raw('COUNT(*) as role_count'))
                ->groupBy('model_has_roles.model_id', 'users.name', 'users.type_user')
                ->having('role_count', '>', 1)
                ->get();

            echo "  📊 Utilisateurs avec multiples rôles: " . $usersWithMultipleRoles->count() . "\n";

            // Corriger les utilisateurs avec multiples rôles
            foreach ($usersWithMultipleRoles as $userInfo) {
                $this->fixUserMultipleRoles($userInfo->model_id, $userInfo->type_user, $userInfo->name);
            }

            echo "\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur nettoyage assignations: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * Corriger un utilisateur avec multiples rôles
     */
    private function fixUserMultipleRoles($userId, $typeUser, $userName)
    {
        try {
            echo "    🔧 Correction utilisateur: {$userName} (ID: {$userId})\n";

            // Déterminer le rôle correct selon le type_user
            $correctRoleName = match($typeUser) {
                'admin' => 'Admin',
                'agent_comptoir' => 'Agent Comptoir', 
                'commercial' => 'Commercial',
                default => null
            };

            if (!$correctRoleName) {
                echo "      ⚠️ Type utilisateur non reconnu: {$typeUser}\n";
                return;
            }

            // Récupérer l'ID du rôle correct
            $correctRole = DB::table('roles')->where('name', $correctRoleName)->first();
            if (!$correctRole) {
                echo "      ⚠️ Rôle {$correctRoleName} non trouvé\n";
                return;
            }

            // Supprimer tous les rôles actuels
            DB::table('model_has_roles')
                ->where('model_id', $userId)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            // Assigner le rôle correct unique
            DB::table('model_has_roles')->insert([
                'role_id' => $correctRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId
            ]);

            echo "      ✅ Rôle unique {$correctRoleName} assigné\n";

        } catch (\Exception $e) {
            echo "      ❌ Erreur correction utilisateur {$userId}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 4. Nettoyer les doublons de permissions
     */
    private function cleanPermissionDuplicates()
    {
        echo "🧹 NETTOYAGE DES PERMISSIONS DUPLIQUÉES\n";
        echo "-------------------------------------\n";

        try {
            if (!Schema::hasTable('permissions')) {
                echo "  ⚠️ Table permissions manquante\n\n";
                return;
            }

            // Récupérer toutes les permissions dupliquées
            $duplicatePermissions = DB::table('permissions')
                ->select('name')
                ->groupBy('name')
                ->having(DB::raw('COUNT(*)'), '>', 1)
                ->pluck('name');

            $cleanedCount = 0;

            foreach ($duplicatePermissions as $permissionName) {
                echo "  🔧 Nettoyage permission: {$permissionName}\n";

                // Récupérer tous les doublons de cette permission
                $permissions = DB::table('permissions')->where('name', $permissionName)->get();
                
                if ($permissions->count() <= 1) continue;

                // Garder la première (la plus ancienne)
                $keepPermission = $permissions->first();
                $duplicatesToDelete = $permissions->skip(1);

                // Transférer toutes les assignations vers la permission à garder
                foreach ($duplicatesToDelete as $duplicatePermission) {
                    // Transférer les assignations rôles-permissions
                    if (Schema::hasTable('role_has_permissions')) {
                        DB::table('role_has_permissions')
                            ->where('permission_id', $duplicatePermission->id)
                            ->update(['permission_id' => $keepPermission->id]);
                    }

                    // Transférer les assignations directes utilisateur-permission (si elles existent)
                    if (Schema::hasTable('model_has_permissions')) {
                        DB::table('model_has_permissions')
                            ->where('permission_id', $duplicatePermission->id)
                            ->update(['permission_id' => $keepPermission->id]);
                    }

                    // Supprimer le doublon
                    DB::table('permissions')->where('id', $duplicatePermission->id)->delete();
                    $cleanedCount++;
                }
            }

            echo "  ✅ {$cleanedCount} permissions dupliquées supprimées\n\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur nettoyage permissions: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * 5. Nettoyer les assignations rôle-permission dupliquées
     */
    private function cleanRolePermissionAssignments()
    {
        echo "🧹 NETTOYAGE ASSIGNATIONS RÔLE-PERMISSION\n";
        echo "----------------------------------------\n";

        try {
            if (!Schema::hasTable('role_has_permissions')) {
                echo "  ⚠️ Table role_has_permissions manquante\n\n";
                return;
            }

            // Supprimer les doublons d'assignations rôle-permission
            $beforeCount = DB::table('role_has_permissions')->count();
            
            DB::statement("
                DELETE t1 FROM role_has_permissions t1
                INNER JOIN role_has_permissions t2 
                WHERE t1.role_id = t2.role_id 
                AND t1.permission_id = t2.permission_id 
                AND t1.permission_id > t2.permission_id
            ");

            $afterCount = DB::table('role_has_permissions')->count();
            $removedCount = $beforeCount - $afterCount;

            echo "  ✅ {$removedCount} assignations rôle-permission dupliquées supprimées\n\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur nettoyage assignations rôle-permission: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * 6. Vérifier l'intégrité finale
     */
    private function verifyIntegrity()
    {
        echo "✅ VÉRIFICATION DE L'INTÉGRITÉ FINALE\n";
        echo "-----------------------------------\n";

        try {
            // Vérifier les rôles
            if (Schema::hasTable('roles')) {
                $totalRoles = DB::table('roles')->count();
                $uniqueRoles = DB::table('roles')->distinct('name')->count('name');
                echo "  📊 Rôles totaux: {$totalRoles}\n";
                echo "  📊 Rôles uniques: {$uniqueRoles}\n";
                
                if ($totalRoles == $uniqueRoles) {
                    echo "  ✅ Aucun doublon de rôle détecté\n";
                } else {
                    echo "  ⚠️ Des doublons de rôles persistent\n";
                }
            }

            // Vérifier les permissions
            if (Schema::hasTable('permissions')) {
                $totalPermissions = DB::table('permissions')->count();
                $uniquePermissions = DB::table('permissions')->distinct('name')->count('name');
                echo "  📊 Permissions totales: {$totalPermissions}\n";
                echo "  📊 Permissions uniques: {$uniquePermissions}\n";
                
                if ($totalPermissions == $uniquePermissions) {
                    echo "  ✅ Aucun doublon de permission détecté\n";
                } else {
                    echo "  ⚠️ Des doublons de permissions persistent\n";
                }
            }

            // Vérifier les assignations utilisateur-rôle
            if (Schema::hasTable('model_has_roles')) {
                $duplicateUserRoles = DB::table('model_has_roles')
                    ->select('model_id', 'role_id')
                    ->where('model_type', 'App\\Models\\User')
                    ->groupBy('model_id', 'role_id')
                    ->having(DB::raw('COUNT(*)'), '>', 1)
                    ->count();

                echo "  📊 Assignations utilisateur-rôle dupliquées: {$duplicateUserRoles}\n";
                
                if ($duplicateUserRoles == 0) {
                    echo "  ✅ Aucun doublon d'assignation utilisateur-rôle\n";
                } else {
                    echo "  ⚠️ Des doublons d'assignations persistent\n";
                }
            }

            echo "\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur vérification intégrité: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * 7. Réassigner les rôles corrects
     */
    private function reassignCorrectRoles()
    {
        echo "🎯 RÉASSIGNATION DES RÔLES CORRECTS\n";
        echo "---------------------------------\n";

        try {
            // Récupérer tous les agents internes sans rôle ou avec un rôle incorrect
            $agents = DB::table('users')
                ->whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
                ->where('ent1d', 1)
                ->get();

            $correctedCount = 0;

            foreach ($agents as $agent) {
                // Déterminer le rôle correct
                $correctRoleName = match($agent->type_user) {
                    'admin' => $agent->email === 'superadmin@psiafrica.ci' ? 'Super Admin' : 'Admin',
                    'agent_comptoir' => 'Agent Comptoir',
                    'commercial' => 'Commercial',
                    default => null
                };

                if (!$correctRoleName) continue;

                // Récupérer le rôle correct
                $correctRole = DB::table('roles')->where('name', $correctRoleName)->first();
                if (!$correctRole) {
                    echo "    ⚠️ Rôle {$correctRoleName} non trouvé\n";
                    continue;
                }

                // Vérifier si l'utilisateur a déjà le bon rôle
                $hasCorrectRole = DB::table('model_has_roles')
                    ->where('model_id', $agent->id)
                    ->where('role_id', $correctRole->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->exists();

                if (!$hasCorrectRole) {
                    // Supprimer tous les rôles actuels
                    DB::table('model_has_roles')
                        ->where('model_id', $agent->id)
                        ->where('model_type', 'App\\Models\\User')
                        ->delete();

                    // Assigner le rôle correct
                    DB::table('model_has_roles')->insert([
                        'role_id' => $correctRole->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $agent->id
                    ]);

                    echo "    ✅ {$agent->name}: Rôle {$correctRoleName} assigné\n";
                    $correctedCount++;
                } else {
                    echo "    ✓ {$agent->name}: Rôle {$correctRoleName} déjà correct\n";
                }
            }

            echo "  📊 Total corrections: {$correctedCount}\n\n";

        } catch (\Exception $e) {
            echo "  ❌ Erreur réassignation rôles: " . $e->getMessage() . "\n\n";
        }
    }
}