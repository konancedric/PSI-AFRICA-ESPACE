<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupCRMPermissions extends Command
{
    protected $signature = 'crm:setup-permissions';
    protected $description = 'Configure les permissions CRM pour tous les rôles';

    public function handle()
    {
        $this->info('Création des permissions CRM...');

        $permissions = [
            'crm_dashboard',
            'crm_clients',
            'crm_invoicing',
            'crm_recovery',
            'crm_performance',
            'crm_analytics',
            'crm_admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("✓ Permission créée : {$permission}");
        }

        // Super Admin et Admin : toutes les permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($permissions);
            $this->info('✓ Permissions assignées à Super Admin');
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions($permissions);
            $this->info('✓ Permissions assignées à Admin');
        }

        // Manager : permissions limitées
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions(['crm_dashboard', 'crm_clients', 'crm_invoicing', 'crm_recovery', 'crm_performance']);
        $this->info('✓ Permissions assignées à Manager');

        // Commercial
        $commercial = Role::firstOrCreate(['name' => 'Commercial']);
        $commercial->syncPermissions(['crm_dashboard', 'crm_clients', 'crm_invoicing']);
        $this->info('✓ Permissions assignées à Commercial');

        // Agent Comptoir
        $comptoir = Role::firstOrCreate(['name' => 'Agent Comptoir']);
        $comptoir->syncPermissions(['crm_dashboard', 'crm_clients', 'crm_invoicing', 'crm_recovery']);
        $this->info('✓ Permissions assignées à Agent Comptoir');

        $this->info('');
        $this->info('✅ Configuration CRM terminée avec succès!');
        
        return 0;
    }
}