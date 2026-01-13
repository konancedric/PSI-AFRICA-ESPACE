<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger les permissions et résoudre l\'erreur 403';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔧 Correction des permissions...');
        
        try {
            // 1. Créer les permissions manquantes
            $this->createPermissions();
            
            // 2. Créer les rôles manquants
            $this->createRoles();
            
            // 3. Assigner les permissions aux rôles
            $this->assignPermissionsToRoles();
            
            // 4. Assigner les rôles aux utilisateurs
            $this->assignRolesToUsers();
            
            // 5. Créer un super admin si nécessaire
            $this->createSuperAdmin();
            
            $this->info('✅ Permissions corrigées avec succès !');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la correction: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Créer les permissions nécessaires
     */
    private function createPermissions()
    {
        $this->info('📝 Création des permissions...');

        $permissions = [
            // Gestion utilisateurs
            'manage_user' => 'Gérer les utilisateurs',
            'view_user' => 'Voir les utilisateurs',
            'create_user' => 'Créer des utilisateurs',
            'edit_user' => 'Modifier les utilisateurs',
            'delete_user' => 'Supprimer les utilisateurs',
            
            // Gestion agents internes
            'manage_agents' => 'Gérer les agents internes',
            'view_agents' => 'Voir les agents internes',
            'create_agents' => 'Créer des agents',
            'edit_agents' => 'Modifier les agents',
            
            // Gestion utilisateurs publics
            'manage_public_users' => 'Gérer les utilisateurs publics',
            'view_public_users' => 'Voir les utilisateurs publics',
            
            // Gestion rôles et permissions
            'manage_role' => 'Gérer les rôles',
            'manage_permission' => 'Gérer les permissions',
            
            // Gestion profils visa
            'manage_profil_visa' => 'Gérer les profils visa',
            'view_profil_visa' => 'Voir les profils visa',
            'manage_user_profil_visa' => 'Gérer ses propres profils visa',
            
            // Dashboard et rapports
            'view_dashboard' => 'Voir le dashboard',
            'view_admin_dashboard' => 'Voir le dashboard admin',
            'view_statistics' => 'Voir les statistiques',
            
            // Autres modules
            'manage_categories' => 'Gérer les catégories',
            'manage_services' => 'Gérer les services',
            'manage_actualites' => 'Gérer les actualités',
            'manage_forfaits' => 'Gérer les forfaits',
            'manage_faqs' => 'Gérer les FAQs',
            'manage_temoignages' => 'Gérer les témoignages',
            'manage_rendez_vous' => 'Gérer les rendez-vous',
            'manage_souscrire_forfaits' => 'Gérer les souscriptions',
            'manage_documentsvoyage' => 'Gérer les documents de voyage',
            'manage_reservation_achat' => 'Gérer les réservations',
            'manage_partenaires' => 'Gérer les partenaires',
            'manage_sliders' => 'Gérer les sliders',
            'manage_parrainages' => 'Gérer les parrainages',
            'manage_statuts' => 'Gérer les statuts',
            'manage_statuts_etat' => 'Gérer les statuts d\'état',
            'manage_galerie_images' => 'Gérer la galerie d\'images',
            'manage_config_ent' => 'Gérer la configuration entreprise',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $this->line("✅ Permission créée: {$name}");
        }
    }

    /**
     * Créer les rôles nécessaires
     */
    private function createRoles()
    {
        $this->info('👥 Création des rôles...');

        $roles = [
            'Super Admin' => 'Administrateur suprême avec tous les droits',
            'Admin' => 'Administrateur système',
            'Agent Comptoir' => 'Agent de comptoir',
            'Commercial' => 'Agent commercial',
            'User' => 'Utilisateur standard'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $this->line("✅ Rôle créé: {$name}");
        }
    }

    /**
     * Assigner les permissions aux rôles
     */
    private function assignPermissionsToRoles()
    {
        $this->info('🔐 Attribution des permissions aux rôles...');

        // Super Admin - Toutes les permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
            $this->line("✅ Toutes les permissions assignées au Super Admin");
        }

        // Admin - Presque toutes les permissions
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereNotIn('name', [])->get(); // Toutes sauf exceptions
            $admin->syncPermissions($adminPermissions);
            $this->line("✅ Permissions Admin assignées");
        }

        // Agent Comptoir
        $agentComptoir = Role::where('name', 'Agent Comptoir')->first();
        if ($agentComptoir) {
            $comptoirPermissions = [
                'view_dashboard',
                'manage_profil_visa',
                'view_profil_visa',
                'manage_rendez_vous',
                'view_statistics',
                'manage_documentsvoyage',
                'view_user',
                'view_public_users'
            ];
            $agentComptoir->syncPermissions($comptoirPermissions);
            $this->line("✅ Permissions Agent Comptoir assignées");
        }

        // Commercial
        $commercial = Role::where('name', 'Commercial')->first();
        if ($commercial) {
            $commercialPermissions = [
                'view_dashboard',
                'manage_forfaits',
                'manage_souscrire_forfaits',
                'manage_partenaires',
                'manage_temoignages',
                'view_statistics',
                'view_user',
                'view_public_users'
            ];
            $commercial->syncPermissions($commercialPermissions);
            $this->line("✅ Permissions Commercial assignées");
        }

        // User
        $user = Role::where('name', 'User')->first();
        if ($user) {
            $userPermissions = [
                'manage_user_profil_visa',
                'view_dashboard'
            ];
            $user->syncPermissions($userPermissions);
            $this->line("✅ Permissions User assignées");
        }
    }

    /**
     * Assigner les rôles aux utilisateurs selon leur type
     */
    private function assignRolesToUsers()
    {
        $this->info('👤 Attribution des rôles aux utilisateurs...');

        // Administrateurs
        $admins = User::where('type_user', 'admin')->get();
        foreach ($admins as $admin) {
            if (!$admin->hasRole('Admin') && !$admin->hasRole('Super Admin')) {
                $admin->assignRole('Admin');
                $this->line("✅ Rôle Admin assigné à: {$admin->name}");
            }
        }

        // Agents comptoir
        $agentsComptoir = User::where('type_user', 'agent_comptoir')->get();
        foreach ($agentsComptoir as $agent) {
            if (!$agent->hasRole('Agent Comptoir')) {
                $agent->assignRole('Agent Comptoir');
                $this->line("✅ Rôle Agent Comptoir assigné à: {$agent->name}");
            }
        }

        // Commerciaux
        $commerciaux = User::where('type_user', 'commercial')->get();
        foreach ($commerciaux as $commercial) {
            if (!$commercial->hasRole('Commercial')) {
                $commercial->assignRole('Commercial');
                $this->line("✅ Rôle Commercial assigné à: {$commercial->name}");
            }
        }

        // Utilisateurs publics
        $publicUsers = User::where('type_user', 'public')->get();
        foreach ($publicUsers as $user) {
            if (!$user->hasAnyRole()) {
                $user->assignRole('User');
                $this->line("✅ Rôle User assigné à: {$user->name}");
            }
        }
    }

    /**
     * Créer un super admin si nécessaire
     */
    private function createSuperAdmin()
    {
        $this->info('👑 Vérification du Super Admin...');

        $superAdmin = User::whereHas('roles', function($query) {
            $query->where('name', 'Super Admin');
        })->first();

        if (!$superAdmin) {
            // Chercher un admin existant pour le promouvoir
            $admin = User::where('type_user', 'admin')
                         ->where('email', 'like', '%admin%')
                         ->first();

            if ($admin) {
                $admin->assignRole('Super Admin');
                $this->line("✅ Super Admin créé: {$admin->name}");
            } else {
                $this->warn("⚠️ Aucun utilisateur trouvé pour devenir Super Admin");
                $this->info("💡 Vous pouvez assigner manuellement le rôle Super Admin à un utilisateur:");
                $this->info("   User::find(ID)->assignRole('Super Admin');");
            }
        } else {
            $this->line("✅ Super Admin existe déjà: {$superAdmin->name}");
        }
    }
}