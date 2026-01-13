<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Grades;
use App\Models\Categories;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;

class DiagnoseErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:diagnose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostiquer les erreurs du système';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Diagnostic du système PSI Africa...');
        $this->newLine();

        $errors = [];

        // 1. Vérifier les modèles
        $errors = array_merge($errors, $this->checkModels());

        // 2. Vérifier les tables
        $errors = array_merge($errors, $this->checkTables());

        // 3. Vérifier les permissions
        $errors = array_merge($errors, $this->checkPermissions());

        // 4. Vérifier les rôles
        $errors = array_merge($errors, $this->checkRoles());

        // 5. Vérifier les utilisateurs
        $errors = array_merge($errors, $this->checkUsers());

        // 6. Afficher le résumé
        $this->displaySummary($errors);

        return empty($errors) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Vérifier les modèles
     */
    private function checkModels(): array
    {
        $this->info('📁 Vérification des modèles...');
        $errors = [];

        $models = [
            'App\Models\User' => 'User',
            'App\Models\Grades' => 'Grades',
            'App\Models\Categories' => 'Categories',
            'App\Models\ProfilVisa' => 'ProfilVisa',
            'App\Models\StatutsEtat' => 'StatutsEtat',
            'App\Models\Entreprises' => 'Entreprises',
        ];

        foreach ($models as $class => $name) {
            if (class_exists($class)) {
                $this->line("  ✅ {$name} existe");
            } else {
                $errors[] = "❌ Modèle manquant: {$name} ({$class})";
                $this->error("  ❌ {$name} manquant");
            }
        }

        return $errors;
    }

    /**
     * Vérifier les tables
     */
    private function checkTables(): array
    {
        $this->info('🗄️  Vérification des tables...');
        $errors = [];

        $tables = [
            'users' => 'Utilisateurs',
            'grades' => 'Grades',
            'categories' => 'Catégories',
            'profil_visa' => 'Profils Visa',
            'statuts_etat' => 'Statuts État',
            'entreprises' => 'Entreprises',
            'roles' => 'Rôles',
            'permissions' => 'Permissions',
            'model_has_roles' => 'Assignation Rôles',
            'role_has_permissions' => 'Permissions Rôles',
        ];

        foreach ($tables as $table => $name) {
            if (Schema::hasTable($table)) {
                $count = \DB::table($table)->count();
                $this->line("  ✅ {$name} ({$count} enregistrements)");
            } else {
                $errors[] = "❌ Table manquante: {$name} ({$table})";
                $this->error("  ❌ {$name} manquante");
            }
        }

        return $errors;
    }

    /**
     * Vérifier les permissions
     */
    private function checkPermissions(): array
    {
        $this->info('🔐 Vérification des permissions...');
        $errors = [];

        try {
            $permissionsCount = Permission::count();
            $this->line("  ✅ {$permissionsCount} permission(s) trouvée(s)");

            // Vérifier les permissions critiques
            $criticalPermissions = [
                'manage_user',
                'manage_profil_visa',
                'view_dashboard'
            ];

            foreach ($criticalPermissions as $permission) {
                if (Permission::where('name', $permission)->exists()) {
                    $this->line("    ✅ {$permission}");
                } else {
                    $errors[] = "❌ Permission critique manquante: {$permission}";
                    $this->warn("    ⚠️  {$permission} manquante");
                }
            }

        } catch (\Exception $e) {
            $errors[] = "❌ Erreur système permissions: " . $e->getMessage();
            $this->error("  ❌ Erreur: " . $e->getMessage());
        }

        return $errors;
    }

    /**
     * Vérifier les rôles
     */
    private function checkRoles(): array
    {
        $this->info('👥 Vérification des rôles...');
        $errors = [];

        try {
            $rolesCount = Role::count();
            $this->line("  ✅ {$rolesCount} rôle(s) trouvé(s)");

            // Vérifier les rôles critiques
            $criticalRoles = [
                'Admin',
                'Agent Comptoir',
                'Commercial'
            ];

            foreach ($criticalRoles as $role) {
                if (Role::where('name', $role)->exists()) {
                    $this->line("    ✅ {$role}");
                } else {
                    $errors[] = "❌ Rôle critique manquant: {$role}";
                    $this->warn("    ⚠️  {$role} manquant");
                }
            }

        } catch (\Exception $e) {
            $errors[] = "❌ Erreur système rôles: " . $e->getMessage();
            $this->error("  ❌ Erreur: " . $e->getMessage());
        }

        return $errors;
    }

    /**
     * Vérifier les utilisateurs
     */
    private function checkUsers(): array
    {
        $this->info('👤 Vérification des utilisateurs...');
        $errors = [];

        try {
            $totalUsers = User::count();
            $this->line("  ✅ {$totalUsers} utilisateur(s) total");

            // Statistiques par type
            $admins = User::where('type_user', 'admin')->count();
            $agentsComptoir = User::where('type_user', 'agent_comptoir')->count();
            $commerciaux = User::where('type_user', 'commercial')->count();
            $publicUsers = User::where('type_user', 'public')->count();
            $withoutType = User::whereNull('type_user')->orWhere('type_user', '')->count();

            $this->line("    • Admins: {$admins}");
            $this->line("    • Agents comptoir: {$agentsComptoir}");
            $this->line("    • Commerciaux: {$commerciaux}");
            $this->line("    • Utilisateurs publics: {$publicUsers}");
            
            if ($withoutType > 0) {
                $errors[] = "⚠️  {$withoutType} utilisateur(s) sans type défini";
                $this->warn("    ⚠️  {$withoutType} sans type défini");
            }

            // Vérifier qu'il y a au moins un admin
            if ($admins === 0) {
                $errors[] = "❌ Aucun administrateur trouvé";
                $this->error("    ❌ Aucun administrateur");
            }

            // Vérifier les utilisateurs sans rôle
            $usersWithoutRoles = User::whereDoesntHave('roles')->count();
            if ($usersWithoutRoles > 0) {
                $errors[] = "⚠️  {$usersWithoutRoles} utilisateur(s) sans rôle";
                $this->warn("    ⚠️  {$usersWithoutRoles} sans rôle");
            }

        } catch (\Exception $e) {
            $errors[] = "❌ Erreur système utilisateurs: " . $e->getMessage();
            $this->error("  ❌ Erreur: " . $e->getMessage());
        }

        return $errors;
    }

    /**
     * Afficher le résumé
     */
    private function displaySummary(array $errors): void
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ DU DIAGNOSTIC');
        $this->line(str_repeat('=', 50));

        if (empty($errors)) {
            $this->info('✅ SYSTÈME EN BONNE SANTÉ');
            $this->line('Aucun problème critique détecté.');
        } else {
            $this->error('❌ PROBLÈMES DÉTECTÉS (' . count($errors) . ')');
            foreach ($errors as $error) {
                $this->line("  {$error}");
            }

            $this->newLine();
            $this->info('🔧 SOLUTIONS RECOMMANDÉES:');
            $this->line('1. Exécuter: php artisan migrate');
            $this->line('2. Exécuter: php artisan permissions:fix');
            $this->line('3. Exécuter: php artisan users:fix-types');
            $this->line('4. Créer le modèle Grades si manquant');
            $this->line('5. Vérifier les fichiers de modèles');
        }

        $this->newLine();
    }
}