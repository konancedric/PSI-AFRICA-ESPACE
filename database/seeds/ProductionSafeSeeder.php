<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProductionSafeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== DÉMARRAGE DU SEEDER SÉCURISÉ ===\n";
        
        DB::beginTransaction();
        
        try {
            // 1. Créer les permissions si Spatie est installé
            if (class_exists('\Spatie\Permission\Models\Permission')) {
                $this->createPermissions();
                $this->createRoles();
                $this->assignPermissionsToRoles();
            } else {
                echo "Spatie Permission non détecté, ignoré\n";
            }
            
            // 2. Créer les utilisateurs de base si nécessaire
            $this->createBasicUsers();
            
            // 3. Autres données spécifiques
            $this->seedOtherData();
            
            DB::commit();
            echo "\n=== SEEDER TERMINÉ AVEC SUCCÈS ===\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\nERREUR LORS DU SEEDER : " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    private function createPermissions()
    {
        echo "\n1. Création des permissions...\n";
        
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'dashboard.view',
            'reports.view',
            'settings.view',
            'settings.edit',
        ];
        
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            echo "  