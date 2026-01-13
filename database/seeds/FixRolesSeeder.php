<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixRolesSeeder extends Seeder
{
    /**
     * Diagnostic et correction des rôles - SEEDER CORRECTIF
     *
     * @return void
     */
    public function run()
    {
        echo "🔧 DIAGNOSTIC ET CORRECTION DES RÔLES PSI AFRICA\n";
        echo "================================================\n\n";

        // Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DIAGNOSTIC INITIAL
        echo "📊 DIAGNOSTIC INITIAL:\n";
        echo "---------------------\n";
        
        $totalUsers = User::count();
        $commerciaux = User::where('type_user', 'commercial')->get();
        $agentsComptoir = User::where('type_user', 'agent_comptoir')->get();
        $admins = User::where('type_user', 'admin')->get();
        
        echo "Total utilisateurs: {$totalUsers}\n";
        echo "Commerciaux (type_user): {$commerciaux->count()}\n";
        echo "Agents comptoir (type_user): {$agentsComptoir->count()}\n";
        echo "Admins (type_user): {$admins->count()}\n\n";

        // Vérifier les rôles existants
        echo "🎭 VÉRIFICATION DES RÔLES:\n";
        echo "-------------------------\n";
        
        $roles = Role::all();
        foreach ($roles as $role) {
            $usersCount = $role->users()->count();
            echo "- {$role->name}: {$usersCount} utilisateur(s)\n";
        }
        echo "\n";

        // 2. CRÉER LES RÔLES MANQUANTS
        echo "🔨 CRÉATION DES RÔLES MANQUANTS:\n";
        echo "-------------------------------\n";
        
        $rolesToCreate = [
            'Super Admin' => 'Super administrateur avec tous les droits',
            'Admin' => 'Administrateur du système',
            'Commercial' => 'Responsable commercial et ventes',
            'Agent Comptoir' => 'Agent de traitement des dossiers'
        ];

        foreach ($rolesToCreate as $roleName => $description) {
            $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);
            echo "✓ Rôle '{$roleName}' vérifié/créé\n";
        }
        echo "\n";

        // 3. CRÉER LES PERMISSIONS ESSENTIELLES
        echo "🔑 VÉRIFICATION DES PERMISSIONS:\n";
        echo "-------------------------------\n";
        
        $permissions = [
            // Permissions commerciales
            'manage_clients' => 'Gérer les clients',
            'view_clients' => 'Voir les clients',
            'manage_forfaits' => 'Gérer les forfaits',
            'view_forfaits' => 'Voir les forfaits',
            'view_dashboard_commercial' => 'Accéder au tableau de bord commercial',
            'manage_temoignages' => 'Gérer les témoignages',
            'manage_partenaires' => 'Gérer les partenaires',
            
            // Permissions agents comptoir
            'manage_profil_visa' => 'Gérer les profils visa',
            'view_profil_visa' => 'Voir les profils visa',
            'edit_profil_visa_status' => 'Modifier le statut des profils visa',
            'add_message_profil_visa' => 'Ajouter des messages aux profils visa',
            'view_dashboard_comptoir' => 'Accéder au tableau de bord comptoir',
            'manage_rendez_vous' => 'Gérer les rendez-vous',
            
            // Permissions admin
            'manage_user' => 'Gérer les utilisateurs',
            'manage_role' => 'Gérer les rôles',
            'manage_permission' => 'Gérer les permissions',
            'manage_agents' => 'Gérer les agents internes',
            'view_all_statistics' => 'Voir toutes les statistiques',
            'manage_system_config' => 'Gérer la configuration système',
            
            // Permissions communes
            'manage_own_profile' => 'Gérer son propre profil',
            'export_data' => 'Exporter les données',
            'view_reports' => 'Voir les rapports',
            
            // AJOUT DES PERMISSIONS MANQUANTES IMPORTANTES
            'manage_sales' => 'Gérer les ventes',
            'manage_projects' => 'Gérer les projets',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            echo "✓ Permission '{$name}' vérifiée/créée\n";
        }
        echo "\n";

        // 4. CORRIGER LES ASSIGNATIONS DE RÔLES
        echo "🎯 CORRECTION DES ASSIGNATIONS DE RÔLES:\n";
        echo "---------------------------------------\n";

        // Récupérer les rôles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $commercialRole = Role::where('name', 'Commercial')->first();
        $agentComptoirRole = Role::where('name', 'Agent Comptoir')->first();

        // CORRECTION COMMERCIAUX
        echo "🛠️ Correction des commerciaux:\n";
        $commerciauxSansRole = 0;
        $commerciauxCorriges = 0;
        
        foreach ($commerciaux as $commercial) {
            try {
                $hasCommercialRole = $commercial->hasRole('Commercial');
                
                if (!$hasCommercialRole) {
                    $commerciauxSansRole++;
                    echo "  ⚠️ {$commercial->name} (ID: {$commercial->id}) - type_user: {$commercial->type_user}, rôles actuels: " . implode(', ', $commercial->getRoleNames()->toArray()) . "\n";
                    
                    // Assigner le rôle Commercial
                    if ($commercialRole) {
                        $commercial->assignRole($commercialRole);
                        $commerciauxCorriges++;
                        echo "    ✅ Rôle 'Commercial' assigné à {$commercial->name}\n";
                    }
                } else {
                    echo "  ✓ {$commercial->name} a déjà le rôle Commercial\n";
                }
            } catch (\Exception $e) {
                echo "  ❌ Erreur pour {$commercial->name}: " . $e->getMessage() . "\n";
                Log::error("Erreur assignation rôle commercial", [
                    'user_id' => $commercial->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        echo "Commerciaux sans rôle trouvés: {$commerciauxSansRole}\n";
        echo "Commerciaux corrigés: {$commerciauxCorriges}\n\n";

        // CORRECTION AGENTS COMPTOIR
        echo "🛠️ Correction des agents comptoir:\n";
        $agentsComptoirSansRole = 0;
        $agentsComptoirCorriges = 0;
        
        foreach ($agentsComptoir as $agent) {
            try {
                $hasAgentRole = $agent->hasRole('Agent Comptoir');
                
                if (!$hasAgentRole) {
                    $agentsComptoirSansRole++;
                    echo "  ⚠️ {$agent->name} (ID: {$agent->id}) - type_user: {$agent->type_user}, rôles actuels: " . implode(', ', $agent->getRoleNames()->toArray()) . "\n";
                    
                    // Assigner le rôle Agent Comptoir
                    if ($agentComptoirRole) {
                        $agent->assignRole($agentComptoirRole);
                        $agentsComptoirCorriges++;
                        echo "    ✅ Rôle 'Agent Comptoir' assigné à {$agent->name}\n";
                    }
                } else {
                    echo "  ✓ {$agent->name} a déjà le rôle Agent Comptoir\n";
                }
            } catch (\Exception $e) {
                echo "  ❌ Erreur pour {$agent->name}: " . $e->getMessage() . "\n";
                Log::error("Erreur assignation rôle agent comptoir", [
                    'user_id' => $agent->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        echo "Agents comptoir sans rôle trouvés: {$agentsComptoirSansRole}\n";
        echo "Agents comptoir corrigés: {$agentsComptoirCorriges}\n\n";

        // CORRECTION ADMINS
        echo "🛠️ Correction des admins:\n";
        $adminsSansRole = 0;
        $adminsCorriges = 0;
        
        foreach ($admins as $admin) {
            try {
                $hasAdminRole = $admin->hasAnyRole(['Admin', 'Super Admin']);
                
                if (!$hasAdminRole) {
                    $adminsSansRole++;
                    echo "  ⚠️ {$admin->name} (ID: {$admin->id}) - type_user: {$admin->type_user}, rôles actuels: " . implode(', ', $admin->getRoleNames()->toArray()) . "\n";
                    
                    // Assigner le rôle Admin (ou Super Admin si c'est le premier)
                    $roleToAssign = ($admin->email === 'admin@psiafrica.ci' || $admin->email === 'superadmin@psiafrica.ci') ? $superAdminRole : $adminRole;
                    
                    if ($roleToAssign) {
                        $admin->assignRole($roleToAssign);
                        $adminsCorriges++;
                        echo "    ✅ Rôle '{$roleToAssign->name}' assigné à {$admin->name}\n";
                    }
                } else {
                    echo "  ✓ {$admin->name} a déjà un rôle admin\n";
                }
            } catch (\Exception $e) {
                echo "  ❌ Erreur pour {$admin->name}: " . $e->getMessage() . "\n";
                Log::error("Erreur assignation rôle admin", [
                    'user_id' => $admin->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        echo "Admins sans rôle trouvés: {$adminsSansRole}\n";
        echo "Admins corrigés: {$adminsCorriges}\n\n";

        // 5. ASSIGNER LES PERMISSIONS AUX RÔLES
        echo "🔗 ASSIGNATION DES PERMISSIONS AUX RÔLES:\n";
        echo "----------------------------------------\n";

        // Super Admin : toutes les permissions
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
            echo "✓ Super Admin: " . Permission::count() . " permissions assignées\n";
        }

        // Admin : toutes les permissions aussi (pour éviter les erreurs)
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
            echo "✓ Admin: " . Permission::count() . " permissions assignées\n";
        }

        // Commercial : permissions spécifiques
        if ($commercialRole) {
            $commercialPermissions = [
                'manage_clients', 'view_clients', 'manage_forfaits', 'view_forfaits',
                'view_dashboard_commercial', 'manage_temoignages', 'manage_partenaires',
                'manage_own_profile', 'export_data', 'view_reports', 'view_profil_visa'
            ];
            $commercialRole->syncPermissions($commercialPermissions);
            echo "✓ Commercial: " . count($commercialPermissions) . " permissions assignées\n";
        }

        // Agent Comptoir : permissions spécifiques
        if ($agentComptoirRole) {
            $agentComptoirPermissions = [
                'manage_profil_visa', 'view_profil_visa', 'edit_profil_visa_status',
                'add_message_profil_visa', 'view_dashboard_comptoir', 'manage_rendez_vous',
                'manage_own_profile', 'export_data', 'view_reports'
            ];
            $agentComptoirRole->syncPermissions($agentComptoirPermissions);
            echo "✓ Agent Comptoir: " . count($agentComptoirPermissions) . " permissions assignées\n";
        }

        // 6. VÉRIFICATION FINALE
        echo "\n📋 VÉRIFICATION FINALE:\n";
        echo "----------------------\n";
        
        // Vérifier chaque commercial
        $commerciauxOK = 0;
        $commerciauxKO = 0;
        foreach ($commerciaux as $commercial) {
            $hasRole = $commercial->hasRole('Commercial');
            if ($hasRole) {
                $commerciauxOK++;
                echo "✅ {$commercial->name}: OK (rôle Commercial assigné)\n";
            } else {
                $commerciauxKO++;
                echo "❌ {$commercial->name}: KO (pas de rôle Commercial)\n";
            }
        }

        // Vérifier chaque agent comptoir
        $agentsOK = 0;
        $agentsKO = 0;
        foreach ($agentsComptoir as $agent) {
            $hasRole = $agent->hasRole('Agent Comptoir');
            if ($hasRole) {
                $agentsOK++;
                echo "✅ {$agent->name}: OK (rôle Agent Comptoir assigné)\n";
            } else {
                $agentsKO++;
                echo "❌ {$agent->name}: KO (pas de rôle Agent Comptoir)\n";
            }
        }

        // 7. NETTOYER LE CACHE
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 8. RÉSUMÉ FINAL
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 RÉSUMÉ DE LA CORRECTION:\n";
        echo str_repeat("=", 50) . "\n";
        echo "Commerciaux vérifiés: {$commerciaux->count()}\n";
        echo "Commerciaux OK: {$commerciauxOK}\n";
        echo "Commerciaux corrigés: {$commerciauxCorriges}\n";
        echo "\nAgents comptoir vérifiés: {$agentsComptoir->count()}\n";
        echo "Agents comptoir OK: {$agentsOK}\n";
        echo "Agents comptoir corrigés: {$agentsComptoirCorriges}\n";
        echo "\nAdmins vérifiés: {$admins->count()}\n";
        echo "Admins corrigés: {$adminsCorriges}\n";

        if ($commerciauxKO == 0 && $agentsKO == 0) {
            echo "\n🎉 TOUS LES RÔLES SONT CORRECTEMENT ASSIGNÉS !\n";
            echo "Les commerciaux peuvent maintenant accéder à leur dashboard.\n";
        } else {
            echo "\n⚠️ CERTAINS PROBLÈMES PERSISTENT\n";
            echo "Veuillez vérifier manuellement les utilisateurs marqués KO.\n";
        }

        echo "\n✅ CORRECTION TERMINÉE\n";
    }
}