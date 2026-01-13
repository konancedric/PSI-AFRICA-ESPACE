<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * ✅ COMMANDE ARTISAN POUR NETTOYER LES DOUBLONS DE RÔLES - PSI AFRICA
 * Usage: php artisan roles:clean-duplicates
 */
class CleanRolesDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:clean-duplicates 
                            {--dry-run : Afficher ce qui serait fait sans l\'exécuter}
                            {--force : Forcer l\'exécution sans demander confirmation}
                            {--verbose : Afficher plus de détails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie tous les doublons de rôles et permissions dans PSI Africa';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 PSI Africa - Nettoyage des Doublons de Rôles');
        $this->info('================================================');
        
        try {
            // Analyser d'abord l'état actuel
            $this->analyzeCurrentState();
            
            if ($this->option('dry-run')) {
                $this->warn('🔍 MODE DRY-RUN : Simulation uniquement');
                $this->simulateCleanup();
                return 0;
            }
            
            // Demander confirmation si pas de --force
            if (!$this->option('force')) {
                if (!$this->confirm('Voulez-vous vraiment nettoyer les doublons ?')) {
                    $this->info('❌ Opération annulée');
                    return 0;
                }
            }
            
            // Exécuter le nettoyage
            $this->performCleanup();
            
            // Vérifier le résultat
            $this->verifyCleanup();
            
            $this->info('✅ Nettoyage terminé avec succès !');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur durant le nettoyage: ' . $e->getMessage());
            Log::error('Erreur CleanRolesDuplicatesCommand: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * ✅ Analyser l'état actuel de la base de données
     */
    private function analyzeCurrentState()
    {
        $this->info('🔍 Analyse de l\'état actuel...');
        
        // Analyser les rôles
        $totalRoles = DB::table('roles')->count();
        $uniqueRoleNames = DB::table('roles')->distinct('name')->count();
        $roleDuplicates = $totalRoles - $uniqueRoleNames;
        
        // Analyser les permissions de rôles
        $totalRolePermissions = DB::table('role_has_permissions')->count();
        $uniqueRolePermissions = DB::table('role_has_permissions')
            ->select('role_id', 'permission_id')
            ->distinct()
            ->count();
        $rolePermissionDuplicates = $totalRolePermissions - $uniqueRolePermissions;
        
        // Analyser les assignations utilisateur-rôle
        $totalUserRoles = DB::table('model_has_roles')->count();
        $uniqueUserRoles = DB::table('model_has_roles')
            ->select('role_id', 'model_type', 'model_id')
            ->distinct()
            ->count();
        $userRoleDuplicates = $totalUserRoles - $uniqueUserRoles;
        
        // Afficher les résultats
        $this->table(
            ['Table', 'Total', 'Uniques', 'Doublons'],
            [
                ['Rôles', $totalRoles, $uniqueRoleNames, $roleDuplicates],
                ['Permissions de Rôles', $totalRolePermissions, $uniqueRolePermissions, $rolePermissionDuplicates],
                ['Assignations Utilisateurs', $totalUserRoles, $uniqueUserRoles, $userRoleDuplicates],
            ]
        );
        
        if ($roleDuplicates === 0 && $rolePermissionDuplicates === 0 && $userRoleDuplicates === 0) {
            $this->info('✅ Aucun doublon détecté !');
            return 0;
        }
        
        $this->warn("⚠️ Doublons détectés : {$roleDuplicates} rôles, {$rolePermissionDuplicates} permissions, {$userRoleDuplicates} assignations");
    }

    /**
     * ✅ Simuler le nettoyage (dry-run)
     */
    private function simulateCleanup()
    {
        $this->info('🎭 Simulation du nettoyage...');
        
        // Simuler le nettoyage des rôles
        $rolesDuplicates = $this->findRoleDuplicates();
        if (!empty($rolesDuplicates)) {
            $this->warn('🔍 Rôles en doublon qui seraient supprimés :');
            foreach ($rolesDuplicates as $duplicate) {
                $ids = explode(',', $duplicate->all_ids);
                $keepId = $duplicate->keep_id;
                $deleteIds = array_filter($ids, fn($id) => $id != $keepId);
                
                $this->line("   - Rôle '{$duplicate->name}' : garder ID {$keepId}, supprimer IDs [" . implode(', ', $deleteIds) . "]");
            }
        }
        
        $this->info('✅ Simulation terminée - Utilisez sans --dry-run pour exécuter');
    }

    /**
     * ✅ Exécuter le nettoyage
     */
    private function performCleanup()
    {
        $this->info('🧹 Démarrage du nettoyage...');
        
        DB::beginTransaction();
        
        try {
            // Étape 1 : Nettoyer les doublons de rôles
            $this->cleanRoleDuplicates();
            
            // Étape 2 : Nettoyer les doublons de permissions
            $this->cleanRolePermissionDuplicates();
            
            // Étape 3 : Nettoyer les doublons d'assignations utilisateur
            $this->cleanUserRoleDuplicates();
            
            // Étape 4 : Ajouter les contraintes uniques
            $this->addUniqueConstraints();
            
            DB::commit();
            $this->info('✅ Transaction validée avec succès');
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * ✅ Nettoyer les doublons de rôles
     */
    private function cleanRoleDuplicates()
    {
        $duplicates = $this->findRoleDuplicates();
        
        if (empty($duplicates)) {
            $this->info('✅ Aucun doublon de rôle trouvé');
            return;
        }
        
        $this->info("🔧 Nettoyage de " . count($duplicates) . " rôles en doublon...");
        
        $progress = $this->output->createProgressBar(count($duplicates));
        $progress->start();
        
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($ids, fn($id) => $id != $keepId);
            
            if ($this->option('verbose')) {
                $this->line("\n   🔄 Rôle '{$duplicate->name}' : transfert vers ID {$keepId}");
            }
            
            // Transférer les relations
            $this->transferRoleRelations($deleteIds, $keepId);
            
            // Supprimer les doublons
            DB::table('roles')->whereIn('id', $deleteIds)->delete();
            
            $progress->advance();
        }
        
        $progress->finish();
        $this->line('');
        $this->info('✅ Doublons de rôles nettoyés');
    }

    /**
     * ✅ Trouver les doublons de rôles
     */
    private function findRoleDuplicates(): array
    {
        return DB::select("
            SELECT name, COUNT(*) as count, MIN(id) as keep_id, GROUP_CONCAT(id) as all_ids
            FROM roles 
            GROUP BY name 
            HAVING COUNT(*) > 1
        ");
    }

    /**
     * ✅ Transférer les relations d'un rôle vers un autre
     */
    private function transferRoleRelations($fromIds, $toId)
    {
        foreach ($fromIds as $fromId) {
            // Transférer les permissions
            $permissions = DB::table('role_has_permissions')
                ->where('role_id', $fromId)
                ->pluck('permission_id');
            
            foreach ($permissions as $permissionId) {
                DB::table('role_has_permissions')->insertIgnore([
                    'role_id' => $toId,
                    'permission_id' => $permissionId
                ]);
            }
            
            // Transférer les utilisateurs
            $users = DB::table('model_has_roles')
                ->where('role_id', $fromId)
                ->select('model_type', 'model_id')
                ->get();
            
            foreach ($users as $user) {
                DB::table('model_has_roles')->insertIgnore([
                    'role_id' => $toId,
                    'model_type' => $user->model_type,
                    'model_id' => $user->model_id
                ]);
            }
            
            // Supprimer les anciennes relations
            DB::table('role_has_permissions')->where('role_id', $fromId)->delete();
            DB::table('model_has_roles')->where('role_id', $fromId)->delete();
        }
    }

    /**
     * ✅ Nettoyer les doublons de permissions de rôles
     */
    private function cleanRolePermissionDuplicates()
    {
        $this->info('🔧 Nettoyage des doublons de permissions...');
        
        $duplicateCount = DB::select("
            SELECT COUNT(*) as count
            FROM (
                SELECT role_id, permission_id, COUNT(*) as duplicate_count
                FROM role_has_permissions 
                GROUP BY role_id, permission_id 
                HAVING COUNT(*) > 1
            ) as duplicates
        ")[0]->count;
        
        if ($duplicateCount == 0) {
            $this->info('✅ Aucun doublon de permission trouvé');
            return;
        }
        
        $this->warn("⚠️ {$duplicateCount} doublons de permissions détectés");
        
        // Utiliser une approche sécurisée pour nettoyer
        DB::statement("
            DELETE rp1 FROM role_has_permissions rp1
            INNER JOIN role_has_permissions rp2 
            WHERE rp1.role_id = rp2.role_id 
            AND rp1.permission_id = rp2.permission_id
            AND rp1.role_id > rp2.role_id
        ");
        
        $this->info('✅ Doublons de permissions nettoyés');
    }

    /**
     * ✅ Nettoyer les doublons d'assignations utilisateur-rôle
     */
    private function cleanUserRoleDuplicates()
    {
        $this->info('🔧 Nettoyage des assignations utilisateur...');
        
        $duplicateCount = DB::select("
            SELECT COUNT(*) as count
            FROM (
                SELECT role_id, model_type, model_id, COUNT(*) as duplicate_count
                FROM model_has_roles 
                GROUP BY role_id, model_type, model_id 
                HAVING COUNT(*) > 1
            ) as duplicates
        ")[0]->count;
        
        if ($duplicateCount == 0) {
            $this->info('✅ Aucun doublon d\'assignation trouvé');
            return;
        }
        
        $this->warn("⚠️ {$duplicateCount} doublons d'assignations détectés");
        
        // Nettoyer les doublons
        DB::statement("
            DELETE mhr1 FROM model_has_roles mhr1
            INNER JOIN model_has_roles mhr2 
            WHERE mhr1.role_id = mhr2.role_id 
            AND mhr1.model_type = mhr2.model_type 
            AND mhr1.model_id = mhr2.model_id
            AND mhr1.role_id > mhr2.role_id
        ");
        
        $this->info('✅ Doublons d\'assignations nettoyés');
    }

    /**
     * ✅ Ajouter les contraintes uniques
     */
    private function addUniqueConstraints()
    {
        $this->info('🔒 Ajout des contraintes uniques...');
        
        try {
            // Contrainte sur le nom des rôles
            if (!$this->indexExists('roles', 'roles_name_unique')) {
                DB::statement('ALTER TABLE roles ADD UNIQUE INDEX roles_name_unique (name)');
                $this->info('   ✅ Contrainte unique ajoutée sur roles.name');
            }
            
            // Contrainte sur role_has_permissions
            if (!$this->indexExists('role_has_permissions', 'role_has_permissions_unique')) {
                DB::statement('ALTER TABLE role_has_permissions ADD UNIQUE INDEX role_has_permissions_unique (role_id, permission_id)');
                $this->info('   ✅ Contrainte unique ajoutée sur role_has_permissions');
            }
            
            // Contrainte sur model_has_roles
            if (!$this->indexExists('model_has_roles', 'model_has_roles_unique')) {
                DB::statement('ALTER TABLE model_has_roles ADD UNIQUE INDEX model_has_roles_unique (role_id, model_type, model_id)');
                $this->info('   ✅ Contrainte unique ajoutée sur model_has_roles');
            }
            
        } catch (\Exception $e) {
            $this->warn('⚠️ Erreur ajout contraintes (non critique): ' . $e->getMessage());
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
     * ✅ Vérifier le résultat du nettoyage
     */
    private function verifyCleanup()
    {
        $this->info('🔍 Vérification du nettoyage...');
        
        $totalRoles = DB::table('roles')->count();
        $uniqueRoleNames = DB::table('roles')->distinct('name')->count();
        
        if ($totalRoles === $uniqueRoleNames) {
            $this->info('✅ Aucun doublon de rôle restant');
        } else {
            $this->error('❌ Des doublons de rôles subsistent');
        }
        
        // Afficher les statistiques finales
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Rôles total', $totalRoles],
                ['Noms uniques', $uniqueRoleNames],
                ['Permissions de rôles', DB::table('role_has_permissions')->count()],
                ['Assignations utilisateurs', DB::table('model_has_roles')->count()],
            ]
        );
    }

    /**
     * ✅ Commandes utilitaires supplémentaires
     */
    public function listDuplicates()
    {
        $this->info('📋 Liste des doublons détectés :');
        
        $duplicates = $this->findRoleDuplicates();
        if (empty($duplicates)) {
            $this->info('✅ Aucun doublon trouvé');
            return;
        }
        
        foreach ($duplicates as $duplicate) {
            $this->line("🔍 Rôle '{$duplicate->name}' : {$duplicate->count} occurrences (IDs: {$duplicate->all_ids})");
        }
    }
}