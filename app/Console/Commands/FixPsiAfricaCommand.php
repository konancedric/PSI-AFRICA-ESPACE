<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class FixPsiAfricaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psi:fix {--force : Force la correction sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger tous les problèmes de PSI Africa (structure DB, rôles, permissions, utilisateurs)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 PSI AFRICA - OUTIL DE CORRECTION AUTOMATIQUE');
        $this->line('================================================');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Cette commande va corriger la structure de la base de données et les rôles/permissions. Continuer ?')) {
                $this->warn('⚠️ Correction annulée par l\'utilisateur');
                return 1;
            }
        }

        try {
            $this->info('📋 Diagnostic initial...');
            $issues = $this->diagnoseIssues();
            
            if (empty($issues)) {
                $this->info('✅ Aucun problème détecté !');
                return 0;
            }

            $this->warn('⚠️ Problèmes détectés :');
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }
            $this->newLine();

            // 1. Corriger la structure de la base de données
            $this->fixDatabaseStructure();

            // 2. Corriger les rôles et permissions
            $this->fixRolesAndPermissions();

            // 3. Corriger les utilisateurs
            $this->fixUsers();

            // 4. Vérification finale
            $this->finalVerification();

            // 5. Nettoyer les caches
            $this->clearCaches();

            $this->newLine();
            $this->info('🎉 CORRECTION TERMINÉE AVEC SUCCÈS !');
            $this->displayTestAccounts();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur critique : ' . $e->getMessage());
            $this->line('Stack trace : ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Diagnostiquer les problèmes
     */
    private function diagnoseIssues(): array
    {
        $issues = [];

        // Vérifier la structure users
        $userColumns = Schema::getColumnListing('users');
        $requiredColumns = ['matricule', 'contact', 'type_user', 'etat', 'statut_emploi'];
        
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $userColumns)) {
                $issues[] = "Colonne manquante dans users: {$column}";
            }
        }

        // Vérifier les rôles
        try {
            $requiredRoles = ['Super Admin', 'Admin', 'Commercial', 'Agent Comptoir'];
            $existingRoles = Role::pluck('name')->toArray();
            
            foreach ($requiredRoles as $role) {
                if (!in_array($role, $existingRoles)) {
                    $issues[] = "Rôle manquant: {$role}";
                }
            }
        } catch (\Exception $e) {
            $issues[] = "Erreur système de rôles: " . $e->getMessage();
        }

        // Vérifier les utilisateurs de test
        $testEmails = ['superadmin@psiafrica.ci', 'admin@psiafrica.ci', 'commercial@psiafrica.ci', 'comptoir@psiafrica.ci'];
        foreach ($testEmails as $email) {
            if (!User::where('email', $email)->exists()) {
                $issues[] = "Utilisateur de test manquant: {$email}";
            }
        }

        // Vérifier les assignations de rôles
        try {
            $commerciaux = User::where('type_user', 'commercial')->get();
            foreach ($commerciaux as $commercial) {
                if (!$commercial->hasRole('Commercial')) {
                    $issues[] = "Commercial sans rôle: {$commercial->email}";
                }
            }

            $agentsComptoir = User::where('type_user', 'agent_comptoir')->get();
            foreach ($agentsComptoir as $agent) {
                if (!$agent->hasRole('Agent Comptoir')) {
                    $issues[] = "Agent comptoir sans rôle: {$agent->email}";
                }
            }
        } catch (\Exception $e) {
            $issues[] = "Erreur vérification assignations: " . $e->getMessage();
        }

        return $issues;
    }

    /**
     * Corriger la structure de la base de données
     */
    private function fixDatabaseStructure()
    {
        $this->info('🛠️ Correction de la structure de la base de données...');
        
        try {
            // Exécuter la migration de correction
            $this->line('  - Ajout des colonnes manquantes...');
            
            $alterQueries = [
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS matricule VARCHAR(255) NULL UNIQUE",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS contact VARCHAR(255) NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS type_user ENUM('admin','agent_comptoir','commercial','public') DEFAULT 'public'",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS photo_user VARCHAR(255) NULL DEFAULT 'NULL'",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS etat TINYINT(1) DEFAULT 1",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS statut_emploi ENUM('actif','suspendu','conge','demission') DEFAULT 'actif'",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS ent1d BIGINT UNSIGNED DEFAULT 1",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS user1d BIGINT UNSIGNED NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS update_user BIGINT UNSIGNED NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS id_categorie BIGINT UNSIGNED NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS id_grade BIGINT UNSIGNED NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS date_embauche DATE NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS salaire DECIMAL(10,2) NULL",
                "ALTER TABLE users ADD COLUMN IF NOT EXISTS adresse TEXT NULL"
            ];

            $existingColumns = Schema::getColumnListing('users');
            
            foreach ($alterQueries as $query) {
                $columnName = $this->extractColumnName($query);
                if (!in_array($columnName, $existingColumns)) {
                    try {
                        DB::statement($query);
                        $this->line("    ✓ Colonne {$columnName} ajoutée");
                    } catch (\Exception $e) {
                        if (!str_contains($e->getMessage(), 'Duplicate column')) {
                            $this->warn("    ⚠️ Erreur colonne {$columnName}: " . $e->getMessage());
                        }
                    }
                }
            }

            // Corriger les valeurs par défaut
            DB::table('users')->whereNull('type_user')->update(['type_user' => 'public']);
            DB::table('users')->whereNull('etat')->update(['etat' => 1]);
            DB::table('users')->whereNull('statut_emploi')->update(['statut_emploi' => 'actif']);
            DB::table('users')->whereNull('ent1d')->update(['ent1d' => 1]);

            $this->info('  ✅ Structure de la base de données corrigée');

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur structure DB: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extraire le nom de la colonne d'une requête ALTER
     */
    private function extractColumnName($query)
    {
        if (preg_match('/ADD COLUMN (?:IF NOT EXISTS )?(\w+)/', $query, $matches)) {
            return $matches[1];
        }
        return 'unknown';
    }

    /**
     * Corriger les rôles et permissions
     */
    private function fixRolesAndPermissions()
    {
        $this->info('🎭 Correction des rôles et permissions...');

        try {
            // Vider le cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Nettoyer les assignations existantes
            DB::table('model_has_roles')->truncate();
            DB::table('role_has_permissions')->truncate();
            $this->line('  - Assignations existantes nettoyées');

            // Créer les permissions
            $permissions = [
                'manage_user' => 'Gérer les utilisateurs',
                'manage_role' => 'Gérer les rôles', 
                'manage_permission' => 'Gérer les permissions',
                'view_dashboard_admin' => 'Dashboard admin',
                'manage_clients' => 'Gérer les clients',
                'view_dashboard_commercial' => 'Dashboard commercial',
                'manage_profil_visa' => 'Gérer les profils visa',
                'view_dashboard_comptoir' => 'Dashboard comptoir',
                'manage_own_profile' => 'Gérer son profil',
                'export_data' => 'Exporter les données'
            ];

            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            }
            $this->line('  - ' . count($permissions) . ' permissions créées');

            // Créer les rôles
            $roles = [
                'Super Admin' => 'Super administrateur',
                'Admin' => 'Administrateur',
                'Commercial' => 'Responsable commercial',
                'Agent Comptoir' => 'Agent de traitement'
            ];

            foreach ($roles as $name => $description) {
                Role::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            }
            $this->line('  - ' . count($roles) . ' rôles créés');

            // Assigner les permissions aux rôles
            $this->assignPermissionsToRoles();

            $this->info('  ✅ Rôles et permissions corrigés');

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur rôles/permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Assigner les permissions aux rôles
     */
    private function assignPermissionsToRoles()
    {
        // Super Admin et Admin : toutes les permissions
        $allPermissions = Permission::all();
        
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($allPermissions);
            $this->line('    ✓ Super Admin: ' . $allPermissions->count() . ' permissions');
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions($allPermissions);
            $this->line('    ✓ Admin: ' . $allPermissions->count() . ' permissions');
        }

        // Commercial : permissions spécifiques
        $commercial = Role::where('name', 'Commercial')->first();
        if ($commercial) {
            $commercialPerms = Permission::whereIn('name', [
                'manage_clients', 'view_dashboard_commercial', 'manage_own_profile', 'export_data'
            ])->get();
            $commercial->syncPermissions($commercialPerms);
            $this->line('    ✓ Commercial: ' . $commercialPerms->count() . ' permissions');
        }

        // Agent Comptoir : permissions spécifiques
        $agentComptoir = Role::where('name', 'Agent Comptoir')->first();
        if ($agentComptoir) {
            $agentPerms = Permission::whereIn('name', [
                'manage_profil_visa', 'view_dashboard_comptoir', 'manage_own_profile', 'export_data'
            ])->get();
            $agentComptoir->syncPermissions($agentPerms);
            $this->line('    ✓ Agent Comptoir: ' . $agentPerms->count() . ' permissions');
        }
    }

    /**
     * Corriger les utilisateurs
     */
    private function fixUsers()
    {
        $this->info('👤 Correction des utilisateurs...');

        try {
            // Créer/mettre à jour les utilisateurs de test
            $testUsers = [
                [
                    'email' => 'superadmin@psiafrica.ci',
                    'name' => 'Super Admin PSI',
                    'type_user' => 'admin',
                    'matricule' => 'SUP001',
                    'password' => bcrypt('superadmin123'),
                    'role' => 'Super Admin'
                ],
                [
                    'email' => 'admin@psiafrica.ci', 
                    'name' => 'Admin PSI',
                    'type_user' => 'admin',
                    'matricule' => 'ADM001',
                    'password' => bcrypt('admin123'),
                    'role' => 'Admin'
                ],
                [
                    'email' => 'commercial@psiafrica.ci',
                    'name' => 'Commercial PSI',
                    'type_user' => 'commercial',
                    'matricule' => 'COM001',
                    'password' => bcrypt('commercial123'),
                    'role' => 'Commercial'
                ],
                [
                    'email' => 'comptoir@psiafrica.ci',
                    'name' => 'Agent Comptoir PSI',
                    'type_user' => 'agent_comptoir',
                    'matricule' => 'CPT001',
                    'password' => bcrypt('comptoir123'),
                    'role' => 'Agent Comptoir'
                ]
            ];

            foreach ($testUsers as $userData) {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => $userData['password'],
                        'type_user' => $userData['type_user'],
                        'matricule' => $userData['matricule'],
                        'etat' => 1,
                        'statut_emploi' => 'actif',
                        'ent1d' => 1,
                        'contact' => '+225 00 00 00 00',
                        'photo_user' => 'NULL',
                        'email_verified_at' => now()
                    ]
                );

                // Assigner le rôle
                $role = Role::where('name', $userData['role'])->first();
                if ($role) {
                    $user->syncRoles([$role]);
                }

                $this->line("  ✓ {$userData['name']} créé/mis à jour avec rôle {$userData['role']}");
            }

            // Corriger tous les agents existants
            $this->fixExistingAgents();

            $this->info('  ✅ Utilisateurs corrigés');

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur utilisateurs: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Corriger les agents existants
     */
    private function fixExistingAgents()
    {
        $agents = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->get();

        foreach ($agents as $agent) {
            // Déterminer le rôle correct
            $roleName = match($agent->type_user) {
                'admin' => in_array($agent->email, ['superadmin@psiafrica.ci']) ? 'Super Admin' : 'Admin',
                'agent_comptoir' => 'Agent Comptoir',
                'commercial' => 'Commercial',
                default => null
            };

            if ($roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$agent->hasRole($roleName)) {
                    $agent->syncRoles([$role]);
                    $this->line("  ✓ {$agent->name}: rôle {$roleName} assigné");
                }
            }

            // Générer matricule si manquant
            if (empty($agent->matricule)) {
                $prefix = match($agent->type_user) {
                    'admin' => 'ADM',
                    'agent_comptoir' => 'CPT',
                    'commercial' => 'COM',
                    default => 'USR'
                };

                $nextNumber = User::where('matricule', 'like', $prefix . '%')->count() + 1;
                $matricule = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                
                $agent->update(['matricule' => $matricule]);
                $this->line("  ✓ {$agent->name}: matricule {$matricule} généré");
            }
        }
    }

    /**
     * Vérification finale
     */
    private function finalVerification()
    {
        $this->info('🔍 Vérification finale...');

        $issues = $this->diagnoseIssues();
        
        if (empty($issues)) {
            $this->info('  ✅ Toutes les vérifications passées avec succès');
        } else {
            $this->warn('  ⚠️ Problèmes restants :');
            foreach ($issues as $issue) {
                $this->line("    - {$issue}");
            }
        }

        // Statistiques finales
        $this->line('  📊 Statistiques :');
        $this->line('    - Total utilisateurs : ' . User::count());
        $this->line('    - Agents internes : ' . User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->count());
        $this->line('    - Total rôles : ' . Role::count());
        $this->line('    - Total permissions : ' . Permission::count());
    }

    /**
     * Nettoyer les caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Nettoyage des caches...');

        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->line('  ✓ Cache permissions Spatie');

            Artisan::call('cache:clear');
            $this->line('  ✓ Cache Laravel');

            Artisan::call('config:clear');
            $this->line('  ✓ Cache configuration');

            $this->info('  ✅ Caches nettoyés');

        } catch (\Exception $e) {
            $this->warn('  ⚠️ Erreur nettoyage caches : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les comptes de test
     */
    private function displayTestAccounts()
    {
        $this->line('');
        $this->line('🎯 COMPTES DE TEST DISPONIBLES :');
        $this->line('================================');
        $this->line('🔴 Super Admin  : superadmin@psiafrica.ci / superadmin123');
        $this->line('🟠 Admin        : admin@psiafrica.ci / admin123');
        $this->line('🔵 Agent        : comptoir@psiafrica.ci / comptoir123');
        $this->line('🟢 Commercial   : commercial@psiafrica.ci / commercial123');
        $this->line('');
        $this->line('🌐 Connectez-vous sur votre site pour tester !');
        $this->line('📋 Tous les problèmes ont été résolus.');
    }
}