<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiagnosePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psi:diagnose-permissions {--fix : Corriger automatiquement les problèmes détectés}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostiquer et corriger les problèmes de permissions PSI Africa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Diagnostic des permissions PSI Africa');
        $this->info('==========================================');
        
        $fix = $this->option('fix');
        
        try {
            // 1. Vérifier l'état des tables
            $this->checkTables();
            
            // 2. Vérifier les rôles
            $this->checkRoles($fix);
            
            // 3. Vérifier les permissions
            $this->checkPermissions($fix);
            
            // 4. Vérifier les assignations
            $this->checkAssignments($fix);
            
            // 5. Vérifier les utilisateurs commerciaux
            $this->checkCommercialUsers($fix);
            
            // 6. Résumé final
            $this->showSummary();
            
            if (!$fix) {
                $this->warn('💡 Pour corriger automatiquement les problèmes, utilisez --fix');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du diagnostic: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Vérifier l'état des tables de permissions
     */
    private function checkTables()
    {
        $this->info('📋 Vérification des tables...');
        
        $tables = ['permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions'];
        
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->line("  ✅ Table {$table}: {$count} enregistrements");
            } catch (\Exception $e) {
                $this->error("  ❌ Table {$table}: Erreur - {$e->getMessage()}");
            }
        }
    }

    /**
     * Vérifier les rôles requis
     */
    private function checkRoles($fix = false)
    {
        $this->info('👥 Vérification des rôles...');
        
        $requiredRoles = ['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'];
        $missingRoles = [];
        
        foreach ($requiredRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $this->line("  ✅ Rôle '{$roleName}': Présent");
            } else {
                $this->warn("  ⚠️  Rôle '{$roleName}': MANQUANT");
                $missingRoles[] = $roleName;
            }
        }
        
        if ($fix && !empty($missingRoles)) {
            foreach ($missingRoles as $roleName) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->info("  ✅ Rôle '{$roleName}' créé");
            }
        }
    }

    /**
     * Vérifier les permissions essentielles
     */
    private function checkPermissions($fix = false)
    {
        $this->info('🔑 Vérification des permissions...');
        
        $essentialPermissions = [
            'manage_clients',
            'view_clients', 
            'manage_forfaits',
            'view_forfaits',
            'view_dashboard_commercial',
            'manage_profil_visa',
            'view_profil_visa',
            'view_dashboard_comptoir'
        ];
        
        $missingPermissions = [];
        
        foreach ($essentialPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $this->line("  ✅ Permission '{$permissionName}': Présente");
            } else {
                $this->warn("  ⚠️  Permission '{$permissionName}': MANQUANTE");
                $missingPermissions[] = $permissionName;
            }
        }
        
        if ($fix && !empty($missingPermissions)) {
            foreach ($missingPermissions as $permissionName) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                $this->info("  ✅ Permission '{$permissionName}' créée");
            }
        }
    }

    /**
     * Vérifier les assignations rôles-permissions
     */
    private function checkAssignments($fix = false)
    {
        $this->info('🔗 Vérification des assignations...');
        
        // Vérifier Commercial
        $commercial = Role::where('name', 'Commercial')->first();
        if ($commercial) {
            $commercialPermissions = ['manage_clients', 'view_clients', 'manage_forfaits', 'view_dashboard_commercial'];
            $assignedPermissions = $commercial->permissions()->pluck('name')->toArray();
            
            $this->line("  📊 Commercial a " . count($assignedPermissions) . " permissions");
            
            foreach ($commercialPermissions as $perm) {
                if (in_array($perm, $assignedPermissions)) {
                    $this->line("    ✅ {$perm}");
                } else {
                    $this->warn("    ⚠️  {$perm}: MANQUANTE");
                    
                    if ($fix) {
                        $permission = Permission::where('name', $perm)->first();
                        if ($permission) {
                            $commercial->givePermissionTo($permission);
                            $this->info("    ✅ Permission '{$perm}' assignée au Commercial");
                        }
                    }
                }
            }
        }
        
        // Vérifier Agent Comptoir
        $agentComptoir = Role::where('name', 'Agent Comptoir')->first();
        if ($agentComptoir) {
            $comptoirPermissions = ['manage_profil_visa', 'view_profil_visa', 'view_dashboard_comptoir'];
            $assignedPermissions = $agentComptoir->permissions()->pluck('name')->toArray();
            
            $this->line("  📊 Agent Comptoir a " . count($assignedPermissions) . " permissions");
            
            foreach ($comptoirPermissions as $perm) {
                if (in_array($perm, $assignedPermissions)) {
                    $this->line("    ✅ {$perm}");
                } else {
                    $this->warn("    ⚠️  {$perm}: MANQUANTE");
                    
                    if ($fix) {
                        $permission = Permission::where('name', $perm)->first();
                        if ($permission) {
                            $agentComptoir->givePermissionTo($permission);
                            $this->info("    ✅ Permission '{$perm}' assignée à l'Agent Comptoir");
                        }
                    }
                }
            }
        }
    }

    /**
     * Vérifier spécifiquement les utilisateurs commerciaux
     */
    private function checkCommercialUsers($fix = false)
    {
        $this->info('💼 Vérification des utilisateurs commerciaux...');
        
        // Utilisateurs avec type_user = commercial
        $commercialUsers = User::where('type_user', 'commercial')->get();
        $this->line("  📊 Trouvé " . $commercialUsers->count() . " utilisateurs commerciaux");
        
        foreach ($commercialUsers as $user) {
            $roles = $user->getRoleNames()->toArray();
            $hasCommercialRole = in_array('Commercial', $roles);
            
            if ($hasCommercialRole) {
                $this->line("  ✅ {$user->name}: A le rôle Commercial");
            } else {
                $this->warn("  ⚠️  {$user->name}: N'a PAS le rôle Commercial");
                
                if ($fix) {
                    $commercialRole = Role::where('name', 'Commercial')->first();
                    if ($commercialRole) {
                        $user->assignRole($commercialRole);
                        $this->info("  ✅ Rôle Commercial assigné à {$user->name}");
                    }
                }
            }
            
            // Vérifier les permissions directes
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            $this->line("    📋 {$user->name} a " . count($permissions) . " permissions totales");
        }
        
        // Utilisateurs avec le rôle Commercial
        $roleCommercial = Role::where('name', 'Commercial')->first();
        if ($roleCommercial) {
            $usersWithRole = $roleCommercial->users()->get();
            $this->line("  📊 " . $usersWithRole->count() . " utilisateurs ont le rôle Commercial");
            
            foreach ($usersWithRole as $user) {
                $typeMatch = ($user->type_user === 'commercial');
                if ($typeMatch) {
                    $this->line("  ✅ {$user->name}: Type et rôle cohérents");
                } else {
                    $this->warn("  ⚠️  {$user->name}: Rôle Commercial mais type_user = '{$user->type_user}'");
                    
                    if ($fix && $user->type_user !== 'admin') { // Ne pas changer les admins
                        $user->update(['type_user' => 'commercial']);
                        $this->info("  ✅ Type_user de {$user->name} mis à jour vers 'commercial'");
                    }
                }
            }
        }
    }

    /**
     * Afficher un résumé final
     */
    private function showSummary()
    {
        $this->info('📋 Résumé final...');
        
        // Compter les éléments
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalUsers = User::count();
        $commercialUsers = User::where('type_user', 'commercial')->count();
        $comptoirUsers = User::where('type_user', 'agent_comptoir')->count();
        
        $this->table(['Élément', 'Total'], [
            ['Rôles', $totalRoles],
            ['Permissions', $totalPermissions],
            ['Utilisateurs total', $totalUsers],
            ['Commerciaux', $commercialUsers],
            ['Agents Comptoir', $comptoirUsers],
        ]);
        
        // Vérifier le cache
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info('✅ Cache des permissions vidé');
        } catch (\Exception $e) {
            $this->warn('⚠️  Erreur lors du vidage du cache: ' . $e->getMessage());
        }
        
        $this->info('🎉 Diagnostic terminé !');
    }
}