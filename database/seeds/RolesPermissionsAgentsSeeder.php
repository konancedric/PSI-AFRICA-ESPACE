<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class RolesPermissionsAgentsSeeder extends Seeder
{
    /**
     * Run the database seeds - VERSION COMPLÈTEMENT CORRIGÉE
     *
     * @return void
     */
    public function run()
    {
        // Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "🚀 Début du seeder des rôles et permissions PSI Africa...\n";
        
        // ✅ CORRECTION CRITIQUE : Vérifier d'abord si la colonne type_user est bien VARCHAR
        $this->fixUserTableStructure();
        
        // Nettoyer les assignations existantes pour éviter les doublons
        DB::table('model_has_roles')->where('model_type', 'App\\Models\\User')->delete();
        
        echo "🧹 Nettoyage des assignations existantes...\n";
        
        // Créer les permissions spécifiques aux agents
        $permissions = [
            // Permissions pour agents comptoir
            'manage_profil_visa' => 'Gérer les profils visa',
            'view_profil_visa' => 'Voir les profils visa',
            'edit_profil_visa_status' => 'Modifier le statut des profils visa',
            'add_message_profil_visa' => 'Ajouter des messages aux profils visa',
            'view_dashboard_comptoir' => 'Accéder au tableau de bord comptoir',
            'manage_rendez_vous' => 'Gérer les rendez-vous',
            'view_statistiques_comptoir' => 'Voir les statistiques comptoir',
            
            // Permissions pour commerciaux
            'manage_clients' => 'Gérer les clients',
            'view_clients' => 'Voir les clients',
            'manage_forfaits' => 'Gérer les forfaits',
            'view_forfaits' => 'Voir les forfaits',
            'manage_souscrire_forfaits' => 'Gérer les souscriptions forfaits',
            'view_dashboard_commercial' => 'Accéder au tableau de bord commercial',
            'manage_partenaires' => 'Gérer les partenaires',
            'view_statistiques_commercial' => 'Voir les statistiques commerciales',
            'manage_temoignages' => 'Gérer les témoignages',
            'manage_services' => 'Gérer les services',
            'manage_actualites' => 'Gérer les actualités',
            'manage_faqs' => 'Gérer les FAQs',
            
            // Permissions communes
            'view_users_list' => 'Voir la liste des utilisateurs',
            'export_data' => 'Exporter les données',
            'view_reports' => 'Voir les rapports',
            'manage_own_profile' => 'Gérer son propre profil',
            
            // Permissions administrateur
            'manage_user' => 'Gérer les utilisateurs',
            'manage_role' => 'Gérer les rôles',
            'manage_permission' => 'Gérer les permissions',
            'manage_agents' => 'Gérer les agents internes',
            'view_all_statistics' => 'Voir toutes les statistiques',
            'manage_system_config' => 'Gérer la configuration système',
            'manage_categories' => 'Gérer les catégories',
            'manage_galerie_images' => 'Gérer la galerie d\'images',
            'manage_config_ent' => 'Gérer la configuration entreprise',
            'manage_documentsvoyage' => 'Gérer les documents de voyage',
            'manage_reservation_achat' => 'Gérer les réservations d\'achat',
            'manage_parrainages' => 'Gérer les parrainages',
            'manage_statuts' => 'Gérer les statuts',
            'manage_statuts_etat' => 'Gérer les statuts d\'état',
            'manage_sliders' => 'Gérer les sliders',
            'manage_user_profil_visa' => 'Gérer son profil visa utilisateur',
            'manage_sales' => 'Gérer les ventes',
            'manage_projects' => 'Gérer les projets',
        ];

        echo "📝 Création des permissions...\n";
        
        // Créer les permissions si elles n'existent pas
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
            echo "  ✓ Permission : {$name}\n";
        }

        echo "🎭 Création des rôles...\n";
        
        // Créer les rôles principaux
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin'], ['guard_name' => 'web']);
        $agentComptoirRole = Role::firstOrCreate(['name' => 'Agent Comptoir'], ['guard_name' => 'web']);
        $commercialRole = Role::firstOrCreate(['name' => 'Commercial'], ['guard_name' => 'web']);

        echo "  ✓ Rôle : Super Admin\n";
        echo "  ✓ Rôle : Admin\n";
        echo "  ✓ Rôle : Agent Comptoir\n";
        echo "  ✓ Rôle : Commercial\n";

        echo "🔗 Attribution des permissions aux rôles...\n";

        // SUPER ADMIN : Toutes les permissions
        $superAdminRole->syncPermissions(Permission::all());
        echo "  ✓ Super Admin : " . Permission::count() . " permissions\n";

        // ADMIN : Toutes les permissions (pour éviter les erreurs 403)
        $adminRole->syncPermissions(Permission::all());
        echo "  ✓ Admin : " . Permission::count() . " permissions\n";

        // AGENT COMPTOIR : Permissions spécifiques
        $agentComptoirPermissions = [
            'manage_profil_visa',
            'view_profil_visa', 
            'edit_profil_visa_status',
            'add_message_profil_visa',
            'view_dashboard_comptoir',
            'manage_rendez_vous',
            'view_statistiques_comptoir',
            'view_users_list',
            'export_data',
            'view_reports',
            'manage_own_profile',
        ];
        $agentComptoirRole->syncPermissions($agentComptoirPermissions);
        echo "  ✓ Agent Comptoir : " . count($agentComptoirPermissions) . " permissions\n";

        // COMMERCIAL : Permissions spécifiques étendues
        $commercialPermissions = [
            'manage_clients',
            'view_clients',
            'manage_forfaits',
            'view_forfaits',
            'manage_souscrire_forfaits',
            'view_dashboard_commercial',
            'manage_partenaires',
            'view_statistiques_commercial',
            'manage_temoignages',
            'manage_services',
            'manage_actualites',
            'manage_faqs',
            'view_users_list',
            'export_data',
            'view_reports',
            'manage_own_profile',
            'view_profil_visa', // Pour consulter les profils des clients
        ];
        $commercialRole->syncPermissions($commercialPermissions);
        echo "  ✓ Commercial : " . count($commercialPermissions) . " permissions\n";

        echo "👥 Création/Mise à jour des utilisateurs...\n";

        // ✅ CORRECTION CRITIQUE : S'assurer que tous les utilisateurs ont type_user STRING
        
        // Super Admin
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@psiafrica.ci'],
            [
                'name' => 'Super Administrateur PSI Africa',
                'password' => Hash::make('superadmin123'),
                'matricule' => 'SUP001',
                'type_user' => 'admin', // ✅ STRING au lieu d'INTEGER
                'etat' => 1,
                'ent1d' => 1,
                'statut_emploi' => 'actif',
                'contact' => '+225 00 00 00 00',
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->syncRoles([$superAdminRole]);
        echo "  ✓ Super Admin mis à jour : superadmin@psiafrica.ci\n";

        // Admin principal
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@psiafrica.ci'],
            [
                'name' => 'Administrateur PSI Africa',
                'password' => Hash::make('admin123'),
                'matricule' => 'ADM001',
                'type_user' => 'admin', // ✅ STRING au lieu d'INTEGER
                'etat' => 1,
                'ent1d' => 1,
                'statut_emploi' => 'actif',
                'contact' => '+225 01 02 03 04',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles([$adminRole]);
        echo "  ✓ Admin mis à jour : admin@psiafrica.ci\n";

        // Agent comptoir principal
        $agentComptoir = User::updateOrCreate(
            ['email' => 'comptoir@psiafrica.ci'],
            [
                'name' => 'Agent Comptoir Principal',
                'password' => Hash::make('comptoir123'),
                'matricule' => 'CPT001',
                'type_user' => 'agent_comptoir', // ✅ STRING au lieu d'INTEGER
                'etat' => 1,
                'ent1d' => 1,
                'statut_emploi' => 'actif',
                'date_embauche' => now(),
                'contact' => '+225 01 02 03 04',
                'email_verified_at' => now(),
            ]
        );
        $agentComptoir->syncRoles([$agentComptoirRole]);
        echo "  ✓ Agent Comptoir mis à jour : comptoir@psiafrica.ci\n";

        // Commercial principal - CORRECTION CRITIQUE
        $commercial = User::updateOrCreate(
            ['email' => 'commercial@psiafrica.ci'],
            [
                'name' => 'Commercial Principal',
                'password' => Hash::make('commercial123'),
                'matricule' => 'COM001',
                'type_user' => 'commercial', // ✅ CORRECTION CRITIQUE : STRING au lieu d'INTEGER
                'etat' => 1,
                'ent1d' => 1,
                'statut_emploi' => 'actif',
                'date_embauche' => now(),
                'contact' => '+225 05 06 07 08',
                'email_verified_at' => now(),
            ]
        );
        $commercial->syncRoles([$commercialRole]);
        echo "  ✅ Commercial mis à jour : commercial@psiafrica.ci\n";

        // Créer des agents supplémentaires pour les tests
        echo "📊 Création d'agents supplémentaires...\n";
        
        // Agents comptoir supplémentaires
        for ($i = 2; $i <= 3; $i++) {
            $agentExtra = User::updateOrCreate(
                ['email' => "comptoir{$i}@psiafrica.ci"],
                [
                    'name' => "Agent Comptoir {$i}",
                    'password' => Hash::make('password123'),
                    'matricule' => "CPT00{$i}",
                    'type_user' => 'agent_comptoir', // ✅ STRING
                    'etat' => 1,
                    'ent1d' => 1,
                    'statut_emploi' => 'actif',
                    'date_embauche' => now()->subDays(rand(30, 365)),
                    'contact' => '+225 0' . rand(1, 9) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'email_verified_at' => now(),
                ]
            );
            $agentExtra->syncRoles([$agentComptoirRole]);
        }

        // Commerciaux supplémentaires
        for ($i = 2; $i <= 3; $i++) {
            $commercialExtra = User::updateOrCreate(
                ['email' => "commercial{$i}@psiafrica.ci"],
                [
                    'name' => "Commercial {$i}",
                    'password' => Hash::make('password123'),
                    'matricule' => "COM00{$i}",
                    'type_user' => 'commercial', // ✅ STRING
                    'etat' => 1,
                    'ent1d' => 1,
                    'statut_emploi' => 'actif',
                    'date_embauche' => now()->subDays(rand(30, 365)),
                    'contact' => '+225 0' . rand(1, 9) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'email_verified_at' => now(),
                ]
            );
            $commercialExtra->syncRoles([$commercialRole]);
        }

        echo "  ✓ 2 agents comptoir supplémentaires créés\n";
        echo "  ✓ 2 commerciaux supplémentaires créés\n";

        // ✅ CORRECTION CRITIQUE : Nettoyer tous les utilisateurs avec type_user INTEGER
        $this->convertIntegerTypeUsersToString();

        // VERIFICATION CRITIQUE : S'assurer que les rôles sont bien assignés
        echo "🔧 Vérification finale des assignations...\n";
        
        // Réassigner tous les rôles pour être sûr
        $allUsers = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->get();
        
        foreach ($allUsers as $user) {
            switch ($user->type_user) {
                case 'admin':
                    if ($user->email === 'superadmin@psiafrica.ci') {
                        $user->syncRoles([$superAdminRole]);
                    } else {
                        $user->syncRoles([$adminRole]);
                    }
                    break;
                case 'agent_comptoir':
                    $user->syncRoles([$agentComptoirRole]);
                    break;
                case 'commercial':
                    $user->syncRoles([$commercialRole]);
                    break;
            }
        }

        // Verification finale
        $verificationCommercial = User::where('email', 'commercial@psiafrica.ci')->first();
        if ($verificationCommercial) {
            echo "  🔍 Vérification Commercial Principal :\n";
            echo "    - Email: " . $verificationCommercial->email . "\n";
            echo "    - Type: " . $verificationCommercial->type_user . "\n";
            echo "    - Rôles: " . implode(', ', $verificationCommercial->getRoleNames()->toArray()) . "\n";
            echo "    - A le rôle Commercial: " . ($verificationCommercial->hasRole('Commercial') ? 'OUI ✅' : 'NON ❌') . "\n";
            echo "    - Etat: " . ($verificationCommercial->etat ? 'Actif ✅' : 'Inactif ❌') . "\n";
            echo "    - Statut emploi: " . $verificationCommercial->statut_emploi . "\n";
        }

        // Vider le cache à la fin
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "\n✅ CONFIGURATION TERMINÉE AVEC SUCCÈS!\n";
        echo "🎯 COMPTES DE TEST DISPONIBLES :\n";
        echo "   🔴 Super Admin: superadmin@psiafrica.ci / superadmin123\n";
        echo "   🟠 Admin: admin@psiafrica.ci / admin123\n";
        echo "   🔵 Agent Comptoir: comptoir@psiafrica.ci / comptoir123\n";
        echo "   🟢 Commercial: commercial@psiafrica.ci / commercial123\n";
        echo "   📋 + 4 autres agents de test\n";
        echo "\n🚀 Connectez-vous avec commercial@psiafrica.ci pour tester le dashboard commercial!\n";
        echo "🎨 Les rôles et permissions sont maintenant correctement configurés.\n";
    }

    /**
     * ✅ CORRECTION CRITIQUE : Vérifier et corriger la structure de la table users
     */
    private function fixUserTableStructure()
    {
        try {
            echo "🔧 Vérification structure table users...\n";
            
            // Vérifier les colonnes de la table users
            $columns = Schema::getColumnListing('users');
            echo "   Colonnes détectées: " . implode(', ', $columns) . "\n";
            
            // Vérifier si type_user existe et son type
            $columnInfo = DB::select("SHOW COLUMNS FROM users WHERE Field = 'type_user'");
            
            if (empty($columnInfo)) {
                echo "   ⚠️ Colonne type_user n'existe pas, ajout...\n";
                Schema::table('users', function ($table) {
                    $table->string('type_user', 50)->default('public')->after('email');
                });
            } else {
                $columnType = $columnInfo[0]->Type;
                echo "   Type actuel de type_user: " . $columnType . "\n";
                
                // Si c'est un INTEGER, le convertir en VARCHAR
                if (strpos(strtolower($columnType), 'int') !== false) {
                    echo "   🔄 Conversion type_user INTEGER vers VARCHAR...\n";
                    
                    // D'abord convertir les données existantes
                    DB::statement("UPDATE users SET type_user = CASE 
                        WHEN type_user = 1 OR type_user = '1' THEN 'admin'
                        WHEN type_user = 2 OR type_user = '2' THEN 'agent_comptoir'
                        WHEN type_user = 3 OR type_user = '3' THEN 'commercial'
                        ELSE 'public'
                    END");
                    
                    // Puis changer le type de colonne
                    Schema::table('users', function ($table) {
                        $table->string('type_user', 50)->default('public')->change();
                    });
                    
                    echo "   ✅ Conversion terminée\n";
                }
            }
            
            // Ajouter les autres colonnes manquantes
            Schema::table('users', function ($table) use ($columns) {
                if (!in_array('matricule', $columns)) {
                    $table->string('matricule', 20)->nullable()->unique()->after('email');
                }
                if (!in_array('contact', $columns)) {
                    $table->string('contact', 20)->nullable()->after('matricule');
                }
                if (!in_array('ent1d', $columns)) {
                    $table->unsignedBigInteger('ent1d')->default(1)->after('contact');
                }
                if (!in_array('etat', $columns)) {
                    $table->boolean('etat')->default(1)->after('ent1d');
                }
                if (!in_array('statut_emploi', $columns)) {
                    $table->enum('statut_emploi', ['actif', 'suspendu', 'conge', 'demission'])->default('actif')->after('etat');
                }
                if (!in_array('user1d', $columns)) {
                    $table->unsignedBigInteger('user1d')->nullable()->after('statut_emploi');
                }
                if (!in_array('update_user', $columns)) {
                    $table->unsignedBigInteger('update_user')->nullable()->after('user1d');
                }
                if (!in_array('photo_user', $columns)) {
                    $table->string('photo_user')->default('NULL')->after('update_user');
                }
                if (!in_array('date_embauche', $columns)) {
                    $table->date('date_embauche')->nullable()->after('photo_user');
                }
                if (!in_array('salaire', $columns)) {
                    $table->decimal('salaire', 12, 2)->nullable()->after('date_embauche');
                }
                if (!in_array('adresse', $columns)) {
                    $table->text('adresse')->nullable()->after('salaire');
                }
                if (!in_array('id_categorie', $columns)) {
                    $table->unsignedBigInteger('id_categorie')->nullable()->after('adresse');
                }
                if (!in_array('id_grade', $columns)) {
                    $table->unsignedBigInteger('id_grade')->nullable()->after('id_categorie');
                }
            });
            
            echo "   ✅ Structure table users vérifiée et corrigée\n";
            
        } catch (\Exception $e) {
            echo "   ❌ Erreur lors de la correction de la structure: " . $e->getMessage() . "\n";
            Log::error('Erreur fixUserTableStructure: ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION : Convertir tous les type_user INTEGER vers STRING
     */
    private function convertIntegerTypeUsersToString()
    {
        try {
            echo "🔄 Conversion des type_user INTEGER vers STRING...\n";
            
            $updated = DB::table('users')
                ->whereIn('type_user', ['1', '2', '3', '4', 1, 2, 3, 4])
                ->update([
                    'type_user' => DB::raw("CASE 
                        WHEN type_user IN ('1', 1) THEN 'admin'
                        WHEN type_user IN ('2', 2) THEN 'agent_comptoir'
                        WHEN type_user IN ('3', 3) THEN 'commercial'
                        ELSE 'public'
                    END")
                ]);
            
            echo "   ✅ {$updated} utilisateurs convertis\n";
            
            // Corriger aussi les utilisateurs sans type_user
            $fixedNull = DB::table('users')
                ->whereNull('type_user')
                ->orWhere('type_user', '')
                ->update(['type_user' => 'public']);
            
            echo "   ✅ {$fixedNull} utilisateurs sans type corrigés\n";
            
        } catch (\Exception $e) {
            echo "   ❌ Erreur conversion: " . $e->getMessage() . "\n";
            Log::error('Erreur convertIntegerTypeUsersToString: ' . $e->getMessage());
        }
    }
}