<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionsSeeder extends Seeder
{
    /**
     * ✅ SEEDER CORRIGÉ AVEC PERMISSIONS STRICTES - SUPPRESSION LIMITÉE
     */
    public function run()
    {
        try {
            Log::info('🚀 Début du seeding des permissions PSI Africa AVEC RESTRICTIONS');

            // Désactiver les contraintes de clés étrangères temporairement
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // ==================== CRÉATION DES PERMISSIONS AVEC RESTRICTIONS ====================
            
            $permissions = [
                // ===== PERMISSIONS ADMIN & SYSTÈME =====
                'manage_user' => 'Gérer les utilisateurs et agents',
                'create_user' => 'Créer des utilisateurs',
                'edit_user' => 'Modifier des utilisateurs',
                'delete_user' => 'Supprimer des utilisateurs', // ADMIN SEULEMENT
                'view_user' => 'Consulter les utilisateurs',
                'manage_role' => 'Gérer les rôles du système',
                'create_role' => 'Créer des rôles',
                'edit_role' => 'Modifier des rôles',
                'delete_role' => 'Supprimer des rôles', // ADMIN SEULEMENT
                'view_role' => 'Consulter les rôles',
                'manage_permission' => 'Gérer les permissions',
                'system_maintenance' => 'Maintenance système',
                'database_access' => 'Accès base de données', // SUPER ADMIN SEULEMENT
                'developer_access' => 'Accès développeur', // SUPER ADMIN SEULEMENT

                // ===== PERMISSIONS PROFILS VISA (GRANULAIRES ET RESTRICTIVES) =====
                'manage_profil_visa' => 'Gérer tous les profils et demandes de visa', // ADMIN SEULEMENT
                'view_profil_visa' => 'Consulter les profils visa',
                'create_profil_visa' => 'Créer un profil visa',
                'edit_profil_visa' => 'Modifier un profil visa',
                'delete_profil_visa' => 'Supprimer un profil visa', // ❌ ADMIN/SUPER ADMIN SEULEMENT
                'edit_profil_visa_status' => 'Modifier le statut des demandes de visa',
                'add_message_profil_visa' => 'Ajouter des messages aux profils visa',
                'view_profil_visa_documents' => 'Consulter les documents des profils visa',
                'manage_profil_visa_documents' => 'Gérer les documents des profils visa',
                'upload_profil_visa_documents' => 'Uploader des documents pour profils visa',
                'download_profil_visa_documents' => 'Télécharger des documents de profils visa',
                'approve_profil_visa' => 'Approuver des demandes de visa',
                'reject_profil_visa' => 'Rejeter des demandes de visa',
                'process_profil_visa' => 'Traiter des demandes de visa',
                'validate_profil_visa' => 'Valider des profils visa',
                'export_profil_visa' => 'Exporter des profils visa',
                'duplicate_profil_visa' => 'Dupliquer des profils visa', // ADMIN SEULEMENT
                'archive_profil_visa' => 'Archiver des profils visa', // ADMIN SEULEMENT
                'restore_profil_visa' => 'Restaurer des profils visa archivés', // ADMIN SEULEMENT
                'priority_profil_visa' => 'Définir la priorité des profils visa',
                'assign_profil_visa' => 'Assigner des profils visa à des agents',
                'reassign_profil_visa' => 'Réassigner des profils visa',
                'send_notification_profil_visa' => 'Envoyer des notifications pour profils visa',
                'bulk_action_profil_visa' => 'Actions en masse sur profils visa', // ADMIN SEULEMENT
                'advanced_search_profil_visa' => 'Recherche avancée dans profils visa',

                // ===== PERMISSIONS COMPTOIR =====
                'manage_rendez_vous' => 'Gérer les rendez-vous clients',
                'create_rendez_vous' => 'Créer des rendez-vous',
                'edit_rendez_vous' => 'Modifier des rendez-vous',
                'delete_rendez_vous' => 'Supprimer des rendez-vous', // ADMIN SEULEMENT
                'view_rendez_vous' => 'Consulter les rendez-vous',
                'view_dashboard_comptoir' => 'Accéder au tableau de bord comptoir',
                'traitement_rapide_visa' => 'Effectuer un traitement rapide des visas',
                'manage_documentsvoyage' => 'Gérer les documents de voyage',
                'create_documentsvoyage' => 'Créer des documents de voyage',
                'edit_documentsvoyage' => 'Modifier des documents de voyage',
                'delete_documentsvoyage' => 'Supprimer des documents de voyage', // ADMIN SEULEMENT
                'view_documentsvoyage' => 'Consulter les documents de voyage',

                // ===== PERMISSIONS COMMERCIAL =====
                'manage_clients' => 'Gérer la base clients',
                'create_clients' => 'Créer des clients',
                'edit_clients' => 'Modifier des clients',
                'delete_clients' => 'Supprimer des clients', // ADMIN SEULEMENT
                'view_clients' => 'Consulter les clients',
                'export_clients' => 'Exporter la base clients',
                'manage_forfaits' => 'Gérer les forfaits et tarifs',
                'create_forfaits' => 'Créer des forfaits',
                'edit_forfaits' => 'Modifier des forfaits',
                'delete_forfaits' => 'Supprimer des forfaits', // ADMIN SEULEMENT
                'view_forfaits' => 'Consulter les forfaits',
                'publish_forfaits' => 'Publier des forfaits',
                'manage_souscrire_forfaits' => 'Gérer les souscriptions forfaits',
                'create_souscrire_forfaits' => 'Créer des souscriptions',
                'edit_souscrire_forfaits' => 'Modifier des souscriptions',
                'delete_souscrire_forfaits' => 'Supprimer des souscriptions', // ADMIN SEULEMENT
                'view_souscrire_forfaits' => 'Consulter les souscriptions',
                'approve_souscrire_forfaits' => 'Approuver des souscriptions',
                'view_dashboard_commercial' => 'Accéder au tableau de bord commercial',

                // ===== PERMISSIONS AVANCÉES ET CRITIQUES =====
                'view_analytics' => 'Consulter les analyses avancées',
                'manage_notifications' => 'Gérer les notifications',
                'send_bulk_notifications' => 'Envoyer des notifications en masse',
                'audit_access' => 'Accès aux journaux d\'audit', // ADMIN SEULEMENT
                'financial_access' => 'Accès aux données financières', // ADMIN SEULEMENT
                'api_access' => 'Accès aux APIs',

                // ===== PERMISSIONS UTILISATEUR PUBLIC =====
                'manage_own_profile' => 'Gérer son propre profil',
                'view_own_profil_visa' => 'Consulter ses propres profils visa',
                'edit_own_profil_visa' => 'Modifier ses propres profils visa',
                'view_own_messages' => 'Consulter ses propres messages',
                'create_own_messages' => 'Créer ses propres messages',
            ];

            Log::info('Création de ' . count($permissions) . ' permissions avec restrictions');

            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['name' => $name, 'guard_name' => 'web']
                );
            }

            // ==================== CRÉATION DES RÔLES ====================
            
            $roles = [
                'Super Admin' => 'Accès complet au système',
                'Admin' => 'Administrateur avec accès étendu',
                'Agent Comptoir' => 'Agent de traitement des dossiers visa',
                'Commercial' => 'Responsable commercial et relation client',
                'Modérateur' => 'Modérateur de contenu',
                'Superviseur' => 'Superviseur avec accès limité'
            ];

            foreach ($roles as $roleName => $description) {
                Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => 'web'],
                    ['name' => $roleName, 'guard_name' => 'web']
                );
            }

            // ==================== ASSIGNATION STRICTE DES PERMISSIONS ====================
            
            // SUPER ADMIN : Toutes les permissions
            $superAdmin = Role::where('name', 'Super Admin')->first();
            if ($superAdmin) {
                $superAdmin->syncPermissions(Permission::all());
                Log::info('Super Admin : TOUTES les permissions assignées');
            }

            // ADMIN : Toutes les permissions sauf développeur et base de données
            $admin = Role::where('name', 'Admin')->first();
            if ($admin) {
                $adminPermissions = Permission::whereNotIn('name', [
                    'database_access',
                    'developer_access'
                ])->get();
                $admin->syncPermissions($adminPermissions);
                Log::info('Admin : ' . $adminPermissions->count() . ' permissions assignées (SANS suppression libre)');
            }

            // ❌ AGENT COMPTOIR : SANS PERMISSIONS DE SUPPRESSION CRITIQUES
            $agentComptoir = Role::where('name', 'Agent Comptoir')->first();
            if ($agentComptoir) {
                $comptoirPermissions = [
                    // ✅ Profils visa - LECTURE ET TRAITEMENT SEULEMENT
                    'view_profil_visa', 'create_profil_visa', 'edit_profil_visa',
                    'edit_profil_visa_status', 'add_message_profil_visa', 
                    'view_profil_visa_documents', 'manage_profil_visa_documents',
                    'upload_profil_visa_documents', 'download_profil_visa_documents',
                    'process_profil_visa', 'validate_profil_visa', 'assign_profil_visa',
                    'priority_profil_visa', 'send_notification_profil_visa',
                    'advanced_search_profil_visa',
                    // ❌ PAS DE: delete_profil_visa, bulk_action_profil_visa, archive_profil_visa
                    
                    // ✅ Rendez-vous - GESTION COMPLÈTE SAUF SUPPRESSION
                    'view_rendez_vous', 'create_rendez_vous', 'edit_rendez_vous',
                    // ❌ PAS DE: delete_rendez_vous
                    
                    // ✅ Documents voyage - GESTION SAUF SUPPRESSION
                    'view_documentsvoyage', 'create_documentsvoyage', 'edit_documentsvoyage',
                    // ❌ PAS DE: delete_documentsvoyage
                    
                    // ✅ Dashboard et outils
                    'view_dashboard_comptoir', 'traitement_rapide_visa',
                    'manage_own_profile', 'export_profil_visa'
                ];
                
                $permissions = Permission::whereIn('name', $comptoirPermissions)->get();
                $agentComptoir->syncPermissions($permissions);
                Log::info('✅ Agent Comptoir : ' . $permissions->count() . ' permissions (SANS suppression)');
            }

            // ❌ COMMERCIAL : SANS PERMISSIONS DE SUPPRESSION CRITIQUES
            $commercial = Role::where('name', 'Commercial')->first();
            if ($commercial) {
                $commercialPermissions = [
                    // ✅ Clients - GESTION SAUF SUPPRESSION
                    'view_clients', 'create_clients', 'edit_clients', 'export_clients',
                    // ❌ PAS DE: delete_clients
                    
                    // ✅ Forfaits - GESTION SAUF SUPPRESSION
                    'view_forfaits', 'create_forfaits', 'edit_forfaits', 'publish_forfaits',
                    // ❌ PAS DE: delete_forfaits
                    
                    // ✅ Souscriptions - GESTION SAUF SUPPRESSION
                    'view_souscrire_forfaits', 'create_souscrire_forfaits', 'edit_souscrire_forfaits',
                    'approve_souscrire_forfaits',
                    // ❌ PAS DE: delete_souscrire_forfaits
                    
                    // ✅ Dashboard et statistiques
                    'view_dashboard_commercial',
                    'manage_own_profile', 'export_clients',
                    
                    // ✅ Profils visa - LECTURE SEULEMENT
                    'view_profil_visa', 'view_profil_visa_documents'
                    // ❌ PAS DE: delete_profil_visa, manage_profil_visa
                ];
                
                $permissions = Permission::whereIn('name', $commercialPermissions)->get();
                $commercial->syncPermissions($permissions);
                Log::info('✅ Commercial : ' . $permissions->count() . ' permissions (SANS suppression)');
            }

            // MODÉRATEUR : Permissions de modération seulement
            $moderateur = Role::where('name', 'Modérateur')->first();
            if ($moderateur) {
                $moderateurPermissions = [
                    'view_profil_visa', 'view_profil_visa_documents',
                    'manage_own_profile'
                ];
                
                $permissions = Permission::whereIn('name', $moderateurPermissions)->get();
                $moderateur->syncPermissions($permissions);
                Log::info('✅ Modérateur : ' . $permissions->count() . ' permissions (LECTURE SEULE)');
            }

            // SUPERVISEUR : Permissions de consultation uniquement
            $superviseur = Role::where('name', 'Superviseur')->first();
            if ($superviseur) {
                $superviseurPermissions = [
                    // Consultation uniquement - AUCUNE SUPPRESSION
                    'view_profil_visa', 'view_profil_visa_documents', 'view_clients', 
                    'view_forfaits', 'view_souscrire_forfaits', 'view_rendez_vous',
                    'view_documentsvoyage', 'view_dashboard_comptoir', 'view_dashboard_commercial',
                    'manage_own_profile', 'export_profil_visa'
                ];
                
                $permissions = Permission::whereIn('name', $superviseurPermissions)->get();
                $superviseur->syncPermissions($permissions);
                Log::info('✅ Superviseur : ' . $permissions->count() . ' permissions (CONSULTATION SEULE)');
            }

            // ==================== ASSIGNATION DES RÔLES AUX UTILISATEURS ====================
            $this->assignRolesToExistingUsers();

            // Réactiver les contraintes de clés étrangères
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            Log::info('✅ Seeding des permissions AVEC RESTRICTIONS terminé avec succès');
            Log::info('❌ SUPPRESSIONS LIMITÉES: Seuls Admin et Super Admin peuvent supprimer');

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors du seeding des permissions: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw $e;
        }
    }

    /**
     * ✅ ASSIGNATION STRICTE DES RÔLES
     */
    private function assignRolesToExistingUsers()
    {
        try {
            Log::info('🔄 Assignation des rôles aux utilisateurs existants...');
            
            $users = DB::table('users')->get();
            
            foreach ($users as $user) {
                // Supprimer tous les rôles existants d'abord
                DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $user->id)
                    ->delete();

                $roleToAssign = null;
                
                switch ($user->type_user) {
                    case 'admin':
                        $roleToAssign = 'Admin';
                        break;
                    case 'agent_comptoir':
                        $roleToAssign = 'Agent Comptoir';
                        break;
                    case 'commercial':
                        $roleToAssign = 'Commercial';
                        break;
                    case 'moderateur':
                        $roleToAssign = 'Modérateur';
                        break;
                    case 'superviseur':
                        $roleToAssign = 'Superviseur';
                        break;
                    case 'public':
                    default:
                        continue 2; // Pas de rôle pour les utilisateurs publics
                }

                if ($roleToAssign) {
                    $role = Role::where('name', $roleToAssign)->first();
                    if ($role) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $role->id,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $user->id
                        ]);
                        
                        Log::info("✅ Rôle {$roleToAssign} assigné à {$user->name} (SANS permission suppression)");
                    }
                }
            }

            // Assignation spéciale pour les super admins
            $superAdminEmails = [
                'admin@psiafrica.ci', 
                'superadmin@psiafrica.ci',
                'administrator@psiafrica.ci',
                'root@psiafrica.ci'
            ];
            
            foreach ($superAdminEmails as $email) {
                $superUser = DB::table('users')->where('email', $email)->first();
                if ($superUser) {
                    $superAdminRole = Role::where('name', 'Super Admin')->first();
                    if ($superAdminRole) {
                        // Supprimer les autres rôles d'abord
                        DB::table('model_has_roles')
                            ->where('model_type', 'App\\Models\\User')
                            ->where('model_id', $superUser->id)
                            ->delete();
                            
                        // Assigner Super Admin
                        DB::table('model_has_roles')->insert([
                            'role_id' => $superAdminRole->id,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $superUser->id
                        ]);
                        
                        Log::info("👑 Rôle Super Admin assigné à {$superUser->name} (TOUTES permissions)");
                    }
                }
            }

            Log::info('✅ Assignation des rôles terminée avec restrictions de suppression');

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'assignation des rôles: ' . $e->getMessage());
        }
    }
}