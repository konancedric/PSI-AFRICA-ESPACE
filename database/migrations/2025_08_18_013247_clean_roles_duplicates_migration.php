<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ✅ MIGRATION POUR NETTOYER LES DOUBLONS DE RÔLES - PSI AFRICA
 * Cette migration supprime tous les doublons existants et ajoute des contraintes
 */
class CleanRolesDuplicatesMigration extends Migration
{
    /**
     * Run the migrations - NETTOYAGE COMPLET DES DOUBLONS
     *
     * @return void
     */
    public function up()
    {
        try {
            Log::info('🧹 Début nettoyage complet des doublons de rôles - PSI Africa');
            
            // ✅ ÉTAPE 1 : Nettoyer les doublons dans la table roles
            $this->cleanDuplicateRoles();
            
            // ✅ ÉTAPE 2 : Nettoyer les doublons dans role_has_permissions
            $this->cleanDuplicateRolePermissions();
            
            // ✅ ÉTAPE 3 : Nettoyer les doublons dans model_has_roles
            $this->cleanDuplicateUserRoles();
            
            // ✅ ÉTAPE 4 : Ajouter des contraintes uniques pour éviter les futurs doublons
            $this->addUniqueConstraints();
            
            // ✅ ÉTAPE 5 : Optimiser les index pour les performances
            $this->optimizeIndexes();
            
            Log::info('✅ Nettoyage complet des doublons de rôles terminé avec succès');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur nettoyage doublons rôles: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ ÉTAPE 1 : Nettoyer les doublons dans la table roles
     */
    private function cleanDuplicateRoles()
    {
        try {
            Log::info('🔍 Nettoyage des doublons dans la table roles');
            
            // Identifier et supprimer les doublons de rôles
            $duplicates = DB::select("
                SELECT name, COUNT(*) as count, MIN(id) as keep_id, GROUP_CONCAT(id) as all_ids
                FROM roles 
                GROUP BY name 
                HAVING COUNT(*) > 1
            ");
            
            foreach ($duplicates as $duplicate) {
                Log::info("🔍 Doublon détecté pour le rôle: {$duplicate->name} ({$duplicate->count} entrées)");
                
                // Récupérer tous les IDs sauf le plus ancien
                $allIds = explode(',', $duplicate->all_ids);
                $duplicateIds = array_filter($allIds, function($id) use ($duplicate) {
                    return $id != $duplicate->keep_id;
                });
                
                if (!empty($duplicateIds)) {
                    // Transférer les relations vers le rôle principal avant suppression
                    $this->transferRoleRelations($duplicateIds, $duplicate->keep_id);
                    
                    // Supprimer les doublons
                    DB::table('roles')->whereIn('id', $duplicateIds)->delete();
                    
                    Log::info("✅ Supprimé " . count($duplicateIds) . " doublons pour le rôle: {$duplicate->name}");
                }
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur nettoyage doublons roles: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ Transférer les relations avant suppression des doublons
     */
    private function transferRoleRelations($duplicateIds, $keepId)
    {
        try {
            // Transférer les permissions
            foreach ($duplicateIds as $duplicateId) {
                // Récupérer les permissions du doublon
                $permissions = DB::table('role_has_permissions')
                    ->where('role_id', $duplicateId)
                    ->pluck('permission_id');
                
                foreach ($permissions as $permissionId) {
                    // Ajouter la permission au rôle principal si elle n'existe pas déjà
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'role_id' => $keepId,
                        'permission_id' => $permissionId
                    ]);
                }
                
                // Transférer les utilisateurs
                $users = DB::table('model_has_roles')
                    ->where('role_id', $duplicateId)
                    ->select('model_type', 'model_id')
                    ->get();
                
                foreach ($users as $user) {
                    // Ajouter l'utilisateur au rôle principal si il n'existe pas déjà
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $keepId,
                        'model_type' => $user->model_type,
                        'model_id' => $user->model_id
                    ]);
                }
                
                // Supprimer les relations du doublon
                DB::table('role_has_permissions')->where('role_id', $duplicateId)->delete();
                DB::table('model_has_roles')->where('role_id', $duplicateId)->delete();
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur transfert relations: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ ÉTAPE 2 : Nettoyer les doublons dans role_has_permissions
     */
    private function cleanDuplicateRolePermissions()
    {
        try {
            Log::info('🔍 Nettoyage des doublons dans role_has_permissions');
            
            // Compter les doublons avant nettoyage
            $duplicateCount = DB::select("
                SELECT COUNT(*) as count
                FROM (
                    SELECT role_id, permission_id, COUNT(*) as duplicate_count
                    FROM role_has_permissions 
                    GROUP BY role_id, permission_id 
                    HAVING COUNT(*) > 1
                ) as duplicates
            ");
            
            if ($duplicateCount[0]->count > 0) {
                Log::info("🔍 {$duplicateCount[0]->count} doublons détectés dans role_has_permissions");
                
                // Créer une table temporaire avec les enregistrements uniques
                DB::statement("
                    CREATE TEMPORARY TABLE temp_role_permissions AS
                    SELECT MIN(role_id) as role_id, permission_id
                    FROM role_has_permissions
                    GROUP BY role_id, permission_id
                ");
                
                // Vider la table originale
                DB::table('role_has_permissions')->truncate();
                
                // Réinsérer les enregistrements uniques
                DB::statement("
                    INSERT INTO role_has_permissions (role_id, permission_id)
                    SELECT role_id, permission_id FROM temp_role_permissions
                ");
                
                // Supprimer la table temporaire
                DB::statement("DROP TEMPORARY TABLE temp_role_permissions");
                
                Log::info('✅ Doublons role_has_permissions nettoyés');
            } else {
                Log::info('✅ Aucun doublon détecté dans role_has_permissions');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur nettoyage role_has_permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ ÉTAPE 3 : Nettoyer les doublons dans model_has_roles
     */
    private function cleanDuplicateUserRoles()
    {
        try {
            Log::info('🔍 Nettoyage des doublons dans model_has_roles');
            
            // Compter les doublons avant nettoyage
            $duplicateCount = DB::select("
                SELECT COUNT(*) as count
                FROM (
                    SELECT role_id, model_type, model_id, COUNT(*) as duplicate_count
                    FROM model_has_roles 
                    GROUP BY role_id, model_type, model_id 
                    HAVING COUNT(*) > 1
                ) as duplicates
            ");
            
            if ($duplicateCount[0]->count > 0) {
                Log::info("🔍 {$duplicateCount[0]->count} doublons détectés dans model_has_roles");
                
                // Créer une table temporaire avec les enregistrements uniques
                DB::statement("
                    CREATE TEMPORARY TABLE temp_model_roles AS
                    SELECT DISTINCT role_id, model_type, model_id
                    FROM model_has_roles
                ");
                
                // Vider la table originale
                DB::table('model_has_roles')->truncate();
                
                // Réinsérer les enregistrements uniques
                DB::statement("
                    INSERT INTO model_has_roles (role_id, model_type, model_id)
                    SELECT role_id, model_type, model_id FROM temp_model_roles
                ");
                
                // Supprimer la table temporaire
                DB::statement("DROP TEMPORARY TABLE temp_model_roles");
                
                Log::info('✅ Doublons model_has_roles nettoyés');
            } else {
                Log::info('✅ Aucun doublon détecté dans model_has_roles');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur nettoyage model_has_roles: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ ÉTAPE 4 : Ajouter des contraintes uniques pour éviter les futurs doublons
     */
    private function addUniqueConstraints()
    {
        try {
            Log::info('🔒 Ajout de contraintes uniques');
            
            // Contrainte unique sur le nom des rôles
            if (!$this->indexExists('roles', 'roles_name_unique')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->unique('name', 'roles_name_unique');
                });
                Log::info('✅ Contrainte unique ajoutée sur roles.name');
            }
            
            // Contrainte unique sur role_has_permissions
            if (!$this->indexExists('role_has_permissions', 'role_has_permissions_unique')) {
                Schema::table('role_has_permissions', function (Blueprint $table) {
                    $table->unique(['role_id', 'permission_id'], 'role_has_permissions_unique');
                });
                Log::info('✅ Contrainte unique ajoutée sur role_has_permissions');
            }
            
            // Contrainte unique sur model_has_roles
            if (!$this->indexExists('model_has_roles', 'model_has_roles_unique')) {
                Schema::table('model_has_roles', function (Blueprint $table) {
                    $table->unique(['role_id', 'model_type', 'model_id'], 'model_has_roles_unique');
                });
                Log::info('✅ Contrainte unique ajoutée sur model_has_roles');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur ajout contraintes: ' . $e->getMessage());
            // Ne pas faire échouer la migration pour les contraintes
        }
    }

    /**
     * ✅ ÉTAPE 5 : Optimiser les index pour les performances
     */
    private function optimizeIndexes()
    {
        try {
            Log::info('⚡ Optimisation des index');
            
            // Index sur roles
            if (!$this->indexExists('roles', 'roles_guard_name_index')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->index('guard_name', 'roles_guard_name_index');
                });
            }
            
            // Index sur role_has_permissions
            if (!$this->indexExists('role_has_permissions', 'role_has_permissions_role_id_index')) {
                Schema::table('role_has_permissions', function (Blueprint $table) {
                    $table->index('role_id', 'role_has_permissions_role_id_index');
                    $table->index('permission_id', 'role_has_permissions_permission_id_index');
                });
            }
            
            // Index sur model_has_roles
            if (!$this->indexExists('model_has_roles', 'model_has_roles_model_id_model_type_index')) {
                Schema::table('model_has_roles', function (Blueprint $table) {
                    $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                    $table->index('role_id', 'model_has_roles_role_id_index');
                });
            }
            
            Log::info('✅ Index optimisés');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur optimisation index: ' . $e->getMessage());
            // Ne pas faire échouer la migration pour les index
        }
    }

    /**
     * ✅ Vérifier si un index existe
     */
    private function indexExists($table, $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * ✅ Méthode de statistiques pour vérifier le nettoyage
     */
    private function displayCleaningStats()
    {
        try {
            $stats = [
                'total_roles' => DB::table('roles')->count(),
                'unique_role_names' => DB::table('roles')->distinct('name')->count(),
                'role_permissions' => DB::table('role_has_permissions')->count(),
                'user_roles' => DB::table('model_has_roles')->count(),
            ];
            
            Log::info('📊 Statistiques après nettoyage:', $stats);
            
            if ($stats['total_roles'] === $stats['unique_role_names']) {
                Log::info('✅ Aucun doublon de rôle détecté');
            } else {
                Log::warning('⚠️ Des doublons de rôles subsistent');
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur affichage statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Log::info('🔄 Rollback nettoyage doublons rôles');
            
            // Supprimer les contraintes uniques ajoutées
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_unique');
            });
            
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $table->dropUnique('role_has_permissions_unique');
            });
            
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropUnique('model_has_roles_unique');
            });
            
            Log::info('✅ Rollback terminé');
            
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur rollback (normal si contraintes n\'existaient pas): ' . $e->getMessage());
        }
    }

    /**
     * ✅ Méthode pour exécuter le nettoyage manuellement
     */
    public static function runCleanup()
    {
        $migration = new self();
        $migration->up();
    }
}