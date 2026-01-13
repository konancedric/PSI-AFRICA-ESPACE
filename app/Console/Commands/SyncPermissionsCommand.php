<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--user_id=} {--clear-cache} {--fix-commercial}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise les permissions et rôles PSI Africa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Début de la synchronisation des permissions PSI Africa...');

        // Clear cache si demandé
        if ($this->option('clear-cache')) {
            $this->info('🗑️ Vidage du cache des permissions...');
            Artisan::call('permission:cache-reset');
            Cache::flush();
            $this->info('✅ Cache vidé avec succès');
        }

        // Créer les permissions manquantes
        $this->createMissingPermissions();

        // Créer les rôles manquants
        $this->createMissingRoles();

        // Fix spécifique pour le commercial
        if ($this->option('fix-commercial')) {
            $this->fixCommercialPermissions();
        }

        // Synchroniser un utilisateur spécifique
        if ($userId = $this->option('user_id')) {
            $this->syncSpecificUser($userId);
        } else {
            // Synchroniser tous les utilisateurs
            $this->syncAllUsers();
        }

        // Diagnostic final
        $this->runDiagnostic();

        $this->info('✅ Synchronisation terminée avec succès !');
    }

    /**
     * Créer les permissions manquantes
     */
    private function createMissingPermissions()
    {
        $this->info('📝 Création des permissions manquantes...');

        $permissions = [
            // Permissions Commerciales
            'manage_clients' => 'Gérer les clients',
            'view_clients' => 'Voir les clients',
            'manage_forfaits' => 'Gérer les forfaits',
            'view_forfaits' => 'Voir les forfaits',
            'manage_souscrire_forfaits' => 'Gérer les souscriptions forfaits',
            'manage_partenaires' => 'Gérer les partenaires',
            'view_partenaires' => 'Voir les partenaires',
            'manage_temoignages' => 'Gérer les témoignages',
            'view_temoignages' => 'Voir les témoignages',
            'view_dashboard_commercial' => 'Accéder au dashboard commercial',
            'export_commercial_data' => 'Exporter les données commerciales',

            // Permissions Comptoir
            'manage_profil_visa' => 'Gérer les profils visa',
            'view_profil_visa' => 'Voir les profils visa',
            'edit_profil_visa_status' => 'Modifier le statut des profils visa',
            'add_message_profil_visa' => 'Ajouter des messages aux profils visa',
            'manage_rendez_vous' => 'Gérer les rendez-vous',
            'view_dashboard_comptoir' => 'Accéder au dashboard comptoir',

            // Permissions Admin
            'manage_users' => 'Gérer les utilisateurs',
            'view_users' => 'Voir les utilisateurs',
            'manage_roles' => 'Gérer les rôles',
            'manage_permissions' => 'Gérer les permissions',
            'view_dashboard_admin' => 'Accéder au dashboard admin',
            'manage_system_config' => 'Gérer la configuration système',

            // Permissions Générales
            'manage_own_profile' => 'Gérer son propre profil',
            'view_statistics' => 'Voir les statistiques',
            'export_data' => 'Exporter des données',
        ];

        $created = 0;
        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ]);

            if ($permission->wasRecentlyCreated) {
                $created++;
                $this->line("  ✅ Permission créée: {$name}");
            }
        }

        $this->info("📝 {$created} nouvelles permissions créées");
    }

    /**
     * Créer les rôles manquants
     */
    private function createMissingRoles()
    {
        $this->info('👥 Création des rôles manquants...');

        $roles = [
            'Super Admin' => [
                'description' => 'Super administrateur avec tous les droits',
                'permissions' => [] // Aura toutes les permissions via le code
            ],
            'Admin' => [
                'description' => 'Administrateur système',
                'permissions' => [
                    'manage_users', 'view_users', 'manage_roles', 'manage_permissions',
                    'view_dashboard_admin', 'manage_system_config', 'view_statistics',
                    'export_data', 'manage_own_profile'
                ]
            ],
            'Commercial' => [
                'description' => 'Agent commercial',
                'permissions' => [
                    'manage_clients', 'view_clients', 'manage_forfaits', 'view_forfaits',
                    'manage_souscrire_forfaits', 'manage_partenaires', 'view_partenaires',
                    'manage_temoignages', 'view_temoignages', 'view_dashboard_commercial',
                    'export_commercial_data', 'view_statistics', 'manage_own_profile'
                ]
            ],
            'Agent Comptoir' => [
                'description' => 'Agent comptoir pour gestion visa',
                'permissions' => [
                    'manage_profil_visa', 'view_profil_visa', 'edit_profil_visa_status',
                    'add_message_profil_visa', 'manage_rendez_vous', 'view_dashboard_comptoir',
                    'view_statistics', 'manage_own_profile'
                ]
            ]
        ];

        $created = 0;
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            if ($role->wasRecentlyCreated) {
                $created++;
                $this->line("  ✅ Rôle créé: {$roleName}");
            }

            // Assigner les permissions au rôle
            if (!empty($roleData['permissions'])) {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->syncPermissions($permissions);
                $this->line("  🔗 Permissions assignées au rôle {$roleName}: " . count($permissions));
            }
        }

        $this->info("👥 {$created} nouveaux rôles créés");
    }

    /**
     * Fix spécifique pour les commerciaux
     */
    private function fixCommercialPermissions()
    {
        $this->info('🔧 Correction spécifique des permissions commerciales...');

        // Trouver tous les commerciaux
        $commerciaux = User::where('type_user', 'commercial')
            ->where('ent1d', 1)
            ->get();

        $this->info("👥 {$commerciaux->count()} commerciaux trouvés");

        foreach ($commerciaux as $commercial) {
            // S'assurer qu'il a le rôle Commercial
            if (!$commercial->hasRole('Commercial')) {
                $commercial->assignRole('Commercial');
                $this->line("  ✅ Rôle 'Commercial' assigné à {$commercial->name}");
            }

            // Vérifier ses permissions
            $permissions = $commercial->getAllPermissions();
            $this->line("  📋 {$commercial->name}: {$permissions->count()} permissions");

            if ($permissions->count() === 0) {
                // Réassigner le rôle pour forcer les permissions
                $commercial->syncRoles(['Commercial']);
                $this->line("  🔄 Rôles resynchronisés pour {$commercial->name}");
            }
        }
    }

    /**
     * Synchroniser un utilisateur spécifique
     */
    private function syncSpecificUser($userId)
    {
        $this->info("🔄 Synchronisation de l'utilisateur #{$userId}...");

        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ Utilisateur #{$userId} non trouvé");
            return;
        }

        $this->info("👤 Synchronisation de: {$user->name} ({$user->email})");
        $this->info("📋 Type utilisateur: {$user->type_user}");

        // Assigner le rôle selon le type_user
        $this->assignRoleByType($user);

        // Diagnostic
        $permissions = $user->getAllPermissions();
        $roles = $user->getRoleNames();

        $this->info("✅ Synchronisation terminée:");
        $this->line("  - Rôles: " . $roles->implode(', '));
        $this->line("  - Permissions: {$permissions->count()}");
    }

    /**
     * Synchroniser tous les utilisateurs
     */
    private function syncAllUsers()
    {
        $this->info('🔄 Synchronisation de tous les utilisateurs...');

        $users = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->where('ent1d', 1)
            ->get();

        $this->info("👥 {$users->count()} utilisateurs internes trouvés");

        $fixed = 0;
        foreach ($users as $user) {
            $beforeRoles = $user->getRoleNames()->count();
            $beforePermissions = $user->getAllPermissions()->count();

            $this->assignRoleByType($user);

            $afterRoles = $user->fresh()->getRoleNames()->count();
            $afterPermissions = $user->fresh()->getAllPermissions()->count();

            if ($beforePermissions !== $afterPermissions || $beforeRoles !== $afterRoles) {
                $fixed++;
                $this->line("  ✅ {$user->name}: {$beforePermissions}→{$afterPermissions} permissions, {$beforeRoles}→{$afterRoles} rôles");
            }
        }

        $this->info("🔧 {$fixed} utilisateurs corrigés");
    }

    /**
     * Assigner le rôle selon le type_user
     */
    private function assignRoleByType(User $user)
    {
        $roleMapping = [
            'admin' => 'Admin',
            'agent_comptoir' => 'Agent Comptoir',
            'commercial' => 'Commercial'
        ];

        if (isset($roleMapping[$user->type_user])) {
            $expectedRole = $roleMapping[$user->type_user];
            
            if (!$user->hasRole($expectedRole)) {
                $user->assignRole($expectedRole);
                $this->line("  🔗 Rôle '{$expectedRole}' assigné à {$user->name}");
            }
        }
    }

    /**
     * Diagnostic final
     */
    private function runDiagnostic()
    {
        $this->info('🔍 Diagnostic final...');

        // Statistiques globales
        $totalUsers = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->where('ent1d', 1)->count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        $this->table(['Métrique', 'Valeur'], [
            ['Utilisateurs internes', $totalUsers],
            ['Rôles total', $totalRoles],
            ['Permissions total', $totalPermissions]
        ]);

        // Diagnostic par type d'utilisateur
        $diagnosticData = [];
        foreach (['admin', 'agent_comptoir', 'commercial'] as $type) {
            $users = User::where('type_user', $type)->where('ent1d', 1)->get();
            
            $withRoles = $users->filter(function($user) {
                return $user->getRoleNames()->count() > 0;
            })->count();
            
            $withPermissions = $users->filter(function($user) {
                return $user->getAllPermissions()->count() > 0;
            })->count();
            
            $diagnosticData[] = [
                ucfirst(str_replace('_', ' ', $type)),
                $users->count(),
                $withRoles,
                $withPermissions
            ];
        }

        $this->table(['Type', 'Total', 'Avec Rôles', 'Avec Permissions'], $diagnosticData);

        // Vérification des commerciaux spécifiquement
        $commerciaux = User::where('type_user', 'commercial')->where('ent1d', 1)->get();
        $commerciauxAvecPermissions = $commerciaux->filter(function($user) {
            return $user->getAllPermissions()->count() > 0;
        });

        if ($commerciaux->count() > 0) {
            $pourcentage = round(($commerciauxAvecPermissions->count() / $commerciaux->count()) * 100, 1);
            
            if ($pourcentage === 100.0) {
                $this->info("✅ COMMERCIAUX: {$pourcentage}% ont des permissions ({$commerciauxAvecPermissions->count()}/{$commerciaux->count()})");
            } else {
                $this->warn("⚠️ COMMERCIAUX: Seulement {$pourcentage}% ont des permissions ({$commerciauxAvecPermissions->count()}/{$commerciaux->count()})");
            }
        }
    }
}