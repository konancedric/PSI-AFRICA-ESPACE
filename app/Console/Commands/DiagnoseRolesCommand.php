<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class DiagnosticRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:diagnostic {--fix : Corriger automatiquement les problèmes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostiquer et corriger les problèmes de rôles et permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 DIAGNOSTIC DES RÔLES ET PERMISSIONS PSI AFRICA');
        $this->info('=' . str_repeat('=', 60));
        
        $problems = [];
        
        // 1. Vérifier les rôles
        $this->info("\n📋 1. VÉRIFICATION DES RÔLES");
        $this->checkRoles($problems);
        
        // 2. Vérifier les permissions
        $this->info("\n🔐 2. VÉRIFICATION DES PERMISSIONS");
        $this->checkPermissions($problems);
        
        // 3. Vérifier les utilisateurs
        $this->info("\n👥 3. VÉRIFICATION DES UTILISATEURS");
        $this->checkUsers($problems);
        
        // 4. Vérifier l'utilisateur commercial spécifique
        $this->info("\n🎯 4. DIAGNOSTIC DE L'UTILISATEUR COMMERCIAL");
        $this->checkCommercialUser($problems);
        
        // 5. Test d'accès
        $this->info("\n🚪 5. TEST D'ACCÈS AU DASHBOARD COMMERCIAL");
        $this->testCommercialAccess($problems);
        
        // 6. Résumé des problèmes
        $this->info("\n📊 RÉSUMÉ DU DIAGNOSTIC");
        if (empty($problems)) {
            $this->info('✅ Aucun problème détecté !');
        } else {
            $this->error('❌ ' . count($problems) . ' problème(s) détecté(s) :');
            foreach ($problems as $i => $problem) {
                $this->warn('  ' . ($i + 1) . '. ' . $problem);
            }
        }
        
        // 7. Correction automatique si demandée
        if ($this->option('fix') && !empty($problems)) {
            $this->info("\n🔧 CORRECTION AUTOMATIQUE");
            $this->fixProblems();
        } elseif (!empty($problems)) {
            $this->info("\n💡 Pour corriger automatiquement, lancez :");
            $this->comment("php artisan roles:diagnostic --fix");
        }
        
        return 0;
    }
    
    private function checkRoles(&$problems)
    {
        $requiredRoles = ['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'];
        
        foreach ($requiredRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                $problems[] = "Rôle manquant : {$roleName}";
                $this->error("  ❌ Rôle manquant : {$roleName}");
            } else {
                $this->info("  ✅ Rôle présent : {$roleName} (ID: {$role->id})");
            }
        }
        
        $this->info("  📊 Total des rôles : " . Role::count());
    }
    
    private function checkPermissions(&$problems)
    {
        $commercialPermissions = [
            'manage_clients',
            'view_dashboard_commercial',
            'manage_forfaits',
            'manage_services'
        ];
        
        foreach ($commercialPermissions as $permName) {
            $permission = Permission::where('name', $permName)->first();
            if (!$permission) {
                $problems[] = "Permission manquante : {$permName}";
                $this->error("  ❌ Permission manquante : {$permName}");
            } else {
                $this->info("  ✅ Permission présente : {$permName}");
            }
        }
        
        $this->info("  📊 Total des permissions : " . Permission::count());
    }
    
    private function checkUsers(&$problems)
    {
        // Vérifier les utilisateurs agents
        $agentsCount = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->count();
        $this->info("  📊 Total agents internes : {$agentsCount}");
        
        // Vérifier les utilisateurs sans rôles
        $usersWithoutRoles = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->whereDoesntHave('roles')
            ->get();
            
        if ($usersWithoutRoles->count() > 0) {
            $problems[] = $usersWithoutRoles->count() . " agent(s) sans rôle assigné";
            $this->error("  ❌ {$usersWithoutRoles->count()} agent(s) sans rôle :");
            foreach ($usersWithoutRoles as $user) {
                $this->warn("    - {$user->name} ({$user->email})");
            }
        } else {
            $this->info("  ✅ Tous les agents ont des rôles assignés");
        }
    }
    
    private function checkCommercialUser(&$problems)
    {
        $commercial = User::where('email', 'commercial@psiafrica.ci')->first();
        
        if (!$commercial) {
            $problems[] = "Utilisateur commercial de test introuvable";
            $this->error("  ❌ Utilisateur commercial@psiafrica.ci introuvable");
            return;
        }
        
        $this->info("  📋 Utilisateur Commercial Principal :");
        $this->info("    - ID : {$commercial->id}");
        $this->info("    - Nom : {$commercial->name}");
        $this->info("    - Email : {$commercial->email}");
        $this->info("    - Type : {$commercial->type_user}");
        $this->info("    - Etat : " . ($commercial->etat ? 'Actif' : 'Inactif'));
        $this->info("    - Statut emploi : {$commercial->statut_emploi}");
        $this->info("    - Matricule : {$commercial->matricule}");
        
        // Vérifier les rôles
        $roles = $commercial->getRoleNames()->toArray();
        if (empty($roles)) {
            $problems[] = "L'utilisateur commercial n'a aucun rôle";
            $this->error("  ❌ Aucun rôle assigné");
        } else {
            $this->info("    - Rôles : " . implode(', ', $roles));
            if (!$commercial->hasRole('Commercial')) {
                $problems[] = "L'utilisateur commercial n'a pas le rôle 'Commercial'";
                $this->error("  ❌ Rôle 'Commercial' manquant");
            } else {
                $this->info("  ✅ Rôle 'Commercial' correctement assigné");
            }
        }
        
        // Vérifier les permissions
        $permissions = $commercial->getAllPermissions()->pluck('name')->toArray();
        $this->info("    - Permissions : " . count($permissions) . " au total");
        
        $requiredPerms = ['view_dashboard_commercial', 'manage_clients'];
        foreach ($requiredPerms as $perm) {
            if (!$commercial->can($perm)) {
                $problems[] = "Permission manquante pour le commercial : {$perm}";
                $this->error("  ❌ Permission manquante : {$perm}");
            } else {
                $this->info("  ✅ Permission présente : {$perm}");
            }
        }
    }
    
    private function testCommercialAccess(&$problems)
    {
        $commercial = User::where('email', 'commercial@psiafrica.ci')->first();
        
        if (!$commercial) {
            $this->error("  ❌ Impossible de tester : utilisateur commercial introuvable");
            return;
        }
        
        // Test 1 : Vérification hasRole
        $hasRoleCommercial = $commercial->hasRole('Commercial');
        $this->info("  🧪 Test hasRole('Commercial') : " . ($hasRoleCommercial ? '✅ PASS' : '❌ FAIL'));
        if (!$hasRoleCommercial) {
            $problems[] = "Test hasRole('Commercial') échoue";
        }
        
        // Test 2 : Vérification hasRole avec tableau
        $hasRoleArray = $commercial->hasRole(['Commercial', 'Admin', 'Super Admin']);
        $this->info("  🧪 Test hasRole(array) : " . ($hasRoleArray ? '✅ PASS' : '❌ FAIL'));
        if (!$hasRoleArray) {
            $problems[] = "Test hasRole avec tableau échoue";
        }
        
        // Test 3 : Vérification type_user
        $hasTypeUser = ($commercial->type_user === 'commercial');
        $this->info("  🧪 Test type_user : " . ($hasTypeUser ? '✅ PASS' : '❌ FAIL'));
        if (!$hasTypeUser) {
            $problems[] = "Type user incorrect pour le commercial";
        }
        
        // Test 4 : Test de la condition d'accès complète
        $hasAccess = ($commercial->hasRole(['Commercial', 'Admin', 'Super Admin']) || $commercial->type_user === 'commercial');
        $this->info("  🧪 Test condition complète : " . ($hasAccess ? '✅ PASS' : '❌ FAIL'));
        if (!$hasAccess) {
            $problems[] = "La condition d'accès complète échoue";
        }
        
        // Test 5 : Vérifier les assignations dans la base
        $modelHasRoles = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $commercial->id)
            ->count();
        $this->info("  🧪 Assignations DB : {$modelHasRoles} enregistrement(s)");
        if ($modelHasRoles == 0) {
            $problems[] = "Aucune assignation de rôle dans la base de données";
        }
    }
    
    private function fixProblems()
    {
        $this->info("🔧 Début de la correction automatique...");
        
        // 1. Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("  ✅ Cache des permissions vidé");
        
        // 2. Créer les rôles manquants
        $requiredRoles = ['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'];
        foreach ($requiredRoles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);
            $this->info("  ✅ Rôle vérifié/créé : {$roleName}");
        }
        
        // 3. Créer les permissions manquantes
        $permissions = [
            'manage_clients' => 'Gérer les clients',
            'view_dashboard_commercial' => 'Accéder au tableau de bord commercial',
            'manage_forfaits' => 'Gérer les forfaits',
            'manage_services' => 'Gérer les services',
            'view_clients' => 'Voir les clients',
            'manage_partenaires' => 'Gérer les partenaires',
            'manage_temoignages' => 'Gérer les témoignages',
        ];
        
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            $this->info("  ✅ Permission vérifiée/créée : {$name}");
        }
        
        // 4. Assigner les permissions au rôle Commercial
        $commercialRole = Role::where('name', 'Commercial')->first();
        if ($commercialRole) {
            $commercialPermissions = [
                'manage_clients',
                'view_clients',
                'view_dashboard_commercial',
                'manage_forfaits',
                'manage_services',
                'manage_partenaires',
                'manage_temoignages',
            ];
            $commercialRole->syncPermissions($commercialPermissions);
            $this->info("  ✅ Permissions assignées au rôle Commercial");
        }
        
        // 5. Corriger l'utilisateur commercial
        $commercial = User::where('email', 'commercial@psiafrica.ci')->first();
        if ($commercial) {
            // S'assurer que les champs sont corrects
            $commercial->update([
                'type_user' => 'commercial',
                'etat' => 1,
                'statut_emploi' => 'actif',
            ]);
            
            // Réassigner le rôle
            $commercial->syncRoles(['Commercial']);
            $this->info("  ✅ Utilisateur commercial corrigé et rôle réassigné");
        } else {
            $this->error("  ❌ Utilisateur commercial introuvable pour correction");
        }
        
        // 6. Corriger tous les utilisateurs sans rôles
        $usersWithoutRoles = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->whereDoesntHave('roles')
            ->get();
            
        foreach ($usersWithoutRoles as $user) {
            switch ($user->type_user) {
                case 'admin':
                    $user->assignRole('Admin');
                    break;
                case 'agent_comptoir':
                    $user->assignRole('Agent Comptoir');
                    break;
                case 'commercial':
                    $user->assignRole('Commercial');
                    break;
            }
            $this->info("  ✅ Rôle assigné à {$user->name}");
        }
        
        // 7. Vider le cache à nouveau
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("  ✅ Cache final vidé");
        
        $this->info("\n🎉 Correction terminée ! Relancez le diagnostic pour vérifier.");
    }
}