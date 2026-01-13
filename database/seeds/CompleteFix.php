<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Categories;
use App\Models\Grades;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CompleteFix extends Seeder
{
    /**
     * SEEDER CORRECTIF COMPLET POUR PSI AFRICA
     * Ce seeder corrige TOUS les problèmes détectés
     *
     * @return void
     */
    public function run()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "🚀 CORRECTION COMPLÈTE PSI AFRICA - DÉBUT\n";
        echo str_repeat("=", 80) . "\n\n";

        try {
            // 0. Vérification préalable
            $this->verifyPrerequisites();

            // 1. Corriger la structure de la table users
            $this->fixUsersTableStructure();

            // 2. Nettoyer et recréer les rôles/permissions
            $this->cleanAndRecreateRolesPermissions();

            // 3. Créer/corriger les tables de support
            $this->createSupportTables();

            // 4. Corriger les utilisateurs existants
            $this->fixExistingUsers();

            // 5. Assigner les rôles correctement
            $this->assignRolesCorrectly();

            // 6. Vérifier la configuration finale
            $this->verifyFinalConfiguration();

            // 7. Nettoyer le cache
            $this->clearAllCaches();

            echo "\n" . str_repeat("=", 80) . "\n";
            echo "✅ CORRECTION COMPLÈTE TERMINÉE AVEC SUCCÈS !\n";
            echo str_repeat("=", 80) . "\n";
            echo "🎯 COMPTES DE TEST DISPONIBLES :\n";
            echo "   🔴 Super Admin : superadmin@psiafrica.ci / superadmin123\n";
            echo "   🟠 Admin       : admin@psiafrica.ci / admin123\n";
            echo "   🔵 Agent       : comptoir@psiafrica.ci / comptoir123\n";
            echo "   🟢 Commercial  : commercial@psiafrica.ci / commercial123\n";
            echo "\n🎉 VOTRE SITE PSI AFRICA EST MAINTENANT OPÉRATIONNEL !\n";
            echo str_repeat("=", 80) . "\n";

        } catch (\Exception $e) {
            echo "\n❌ ERREUR CRITIQUE : " . $e->getMessage() . "\n";
            echo "Stack trace : " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }

    /**
     * Vérifier les prérequis
     */
    private function verifyPrerequisites()
    {
        echo "🔍 VÉRIFICATION DES PRÉREQUIS\n";
        echo "----------------------------\n";

        // Vérifier si les tables existent
        $requiredTables = ['users', 'roles', 'permissions', 'model_has_roles', 'role_has_permissions'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                throw new \Exception("Table manquante : {$table}");
            }
            echo "  ✓ Table {$table} existe\n";
        }

        echo "  ✅ Tous les prérequis sont satisfaits\n\n";
    }

    /**
     * Corriger la structure de la table users
     */
    private function fixUsersTableStructure()
    {
        echo "🛠️ CORRECTION STRUCTURE TABLE USERS\n";
        echo "-----------------------------------\n";

        $columnsToAdd = [
            'matricule' => "VARCHAR(255) NULL UNIQUE",
            'contact' => "VARCHAR(255) NULL",
            'type_user' => "ENUM('admin','agent_comptoir','commercial','public') DEFAULT 'public'",
            'photo_user' => "VARCHAR(255) NULL DEFAULT 'NULL'",
            'etat' => "TINYINT(1) DEFAULT 1",
            'statut_emploi' => "ENUM('actif','suspendu','conge','demission') DEFAULT 'actif'",
            'ent1d' => "BIGINT UNSIGNED DEFAULT 1",
            'user1d' => "BIGINT UNSIGNED NULL",
            'update_user' => "BIGINT UNSIGNED NULL",
            'id_categorie' => "BIGINT UNSIGNED NULL",
            'id_grade' => "BIGINT UNSIGNED NULL",
            'date_embauche' => "DATE NULL",
            'salaire' => "DECIMAL(10,2) NULL",
            'adresse' => "TEXT NULL"
        ];

        $existingColumns = Schema::getColumnListing('users');
        
        foreach ($columnsToAdd as $column => $definition) {
            if (!in_array($column, $existingColumns)) {
                try {
                    DB::statement("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                    echo "  ✓ Colonne {$column} ajoutée\n";
                } catch (\Exception $e) {
                    echo "  ⚠️ Erreur ajout colonne {$column}: " . $e->getMessage() . "\n";
                }
            } else {
                echo "  ◦ Colonne {$column} existe déjà\n";
            }
        }

        // Corriger les valeurs NULL
        DB::table('users')->whereNull('type_user')->update(['type_user' => 'public']);
        DB::table('users')->whereNull('etat')->update(['etat' => 1]);
        DB::table('users')->whereNull('statut_emploi')->update(['statut_emploi' => 'actif']);
        DB::table('users')->whereNull('ent1d')->update(['ent1d' => 1]);
        DB::table('users')->where('photo_user', '')->update(['photo_user' => 'NULL']);

        echo "  ✅ Structure table users corrigée\n\n";
    }

    /**
     * Nettoyer et recréer les rôles/permissions
     */
    private function cleanAndRecreateRolesPermissions()
    {
        echo "🎭 NETTOYAGE ET RECRÉATION RÔLES/PERMISSIONS\n";
        echo "-------------------------------------------\n";

        // Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Nettoyer les assignations existantes
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();

        echo "  🧹 Assignations existantes nettoyées\n";

        // Créer les permissions essentielles
        $permissions = [
            // Admin
            'manage_user' => 'Gérer les utilisateurs',
            'manage_role' => 'Gérer les rôles',
            'manage_permission' => 'Gérer les permissions',
            'view_dashboard_admin' => 'Dashboard admin',
            'manage_agents' => 'Gérer les agents',

            // Commercial
            'manage_clients' => 'Gérer les clients',
            'view_clients' => 'Voir les clients',
            'manage_forfaits' => 'Gérer les forfaits',
            'view_forfaits' => 'Voir les forfaits',
            'view_dashboard_commercial' => 'Dashboard commercial',
            'manage_partenaires' => 'Gérer les partenaires',
            'manage_temoignages' => 'Gérer les témoignages',

            // Agent Comptoir
            'manage_profil_visa' => 'Gérer les profils visa',
            'view_profil_visa' => 'Voir les profils visa',
            'edit_profil_visa_status' => 'Modifier statut profils visa',
            'add_message_profil_visa' => 'Ajouter messages profils visa',
            'view_dashboard_comptoir' => 'Dashboard comptoir',
            'manage_rendez_vous' => 'Gérer les rendez-vous',

            // Communes
            'manage_own_profile' => 'Gérer son profil',
            'export_data' => 'Exporter les données',
            'view_reports' => 'Voir les rapports'
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            echo "  ✓ Permission: {$name}\n";
        }

        // Créer les rôles principaux
        $roles = [
            'Super Admin' => 'Super administrateur système',
            'Admin' => 'Administrateur',
            'Commercial' => 'Responsable commercial',
            'Agent Comptoir' => 'Agent de traitement'
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
            echo "  ✓ Rôle: {$name}\n";
        }

        // Assigner les permissions aux rôles
        $this->assignPermissionsToRoles();

        echo "  ✅ Rôles et permissions recréés\n\n";
    }

    /**
     * Assigner les permissions aux rôles
     */
    private function assignPermissionsToRoles()
    {
        // Super Admin : toutes les permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
            echo "  ✓ Super Admin: " . Permission::count() . " permissions\n";
        }

        // Admin : toutes les permissions
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
            echo "  ✓ Admin: " . Permission::count() . " permissions\n";
        }

        // Commercial : permissions spécifiques
        $commercial = Role::where('name', 'Commercial')->first();
        if ($commercial) {
            $commercialPerms = [
                'manage_clients', 'view_clients', 'manage_forfaits', 'view_forfaits',
                'view_dashboard_commercial', 'manage_partenaires', 'manage_temoignages',
                'manage_own_profile', 'export_data', 'view_reports', 'view_profil_visa'
            ];
            $permissions = Permission::whereIn('name', $commercialPerms)->get();
            $commercial->syncPermissions($permissions);
            echo "  ✓ Commercial: " . $permissions->count() . " permissions\n";
        }

        // Agent Comptoir : permissions spécifiques
        $agentComptoir = Role::where('name', 'Agent Comptoir')->first();
        if ($agentComptoir) {
            $agentPerms = [
                'manage_profil_visa', 'view_profil_visa', 'edit_profil_visa_status',
                'add_message_profil_visa', 'view_dashboard_comptoir', 'manage_rendez_vous',
                'manage_own_profile', 'export_data', 'view_reports'
            ];
            $permissions = Permission::whereIn('name', $agentPerms)->get();
            $agentComptoir->syncPermissions($permissions);
            echo "  ✓ Agent Comptoir: " . $permissions->count() . " permissions\n";
        }
    }

    /**
     * Créer les tables de support
     */
    private function createSupportTables()
    {
        echo "📊 CRÉATION TABLES DE SUPPORT\n";
        echo "-----------------------------\n";

        // Table categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function ($table) {
                $table->id();
                $table->string('libelle');
                $table->text('description')->nullable();
                $table->tinyInteger('etat')->default(1);
                $table->unsignedBigInteger('ent1d')->default(1);
                $table->unsignedBigInteger('user1d')->nullable();
                $table->timestamps();
            });
            echo "  ✓ Table categories créée\n";
        }

        // Table grades
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function ($table) {
                $table->id();
                $table->string('libelle');
                $table->text('description')->nullable();
                $table->tinyInteger('etat')->default(1);
                $table->unsignedBigInteger('ent1d')->default(1);
                $table->unsignedBigInteger('user1d')->nullable();
                $table->timestamps();
            });
            echo "  ✓ Table grades créée\n";
        }

        // Insérer des données par défaut
        $this->insertDefaultSupportData();

        echo "  ✅ Tables de support créées\n\n";
    }

    /**
     * Insérer les données par défaut
     */
    private function insertDefaultSupportData()
    {
        // Categories par défaut
        if (Categories::count() == 0) {
            $categories = [
                ['libelle' => 'Tourisme', 'description' => 'Voyages touristiques'],
                ['libelle' => 'Affaires', 'description' => 'Voyages d\'affaires'],
                ['libelle' => 'Étudiant', 'description' => 'Voyages d\'études'],
                ['libelle' => 'Famille', 'description' => 'Voyages familiaux'],
                ['libelle' => 'Transit', 'description' => 'Voyages de transit']
            ];

            foreach ($categories as $category) {
                Categories::create(array_merge($category, [
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
            echo "  ✓ 5 catégories par défaut insérées\n";
        }

        // Grades par défaut
        if (Grades::count() == 0) {
            $grades = [
                ['libelle' => 'Junior', 'description' => 'Niveau débutant'],
                ['libelle' => 'Senior', 'description' => 'Niveau expérimenté'],
                ['libelle' => 'Expert', 'description' => 'Niveau expert'],
                ['libelle' => 'Manager', 'description' => 'Niveau managérial'],
                ['libelle' => 'Directeur', 'description' => 'Niveau directorial']
            ];

            foreach ($grades as $grade) {
                Grades::create(array_merge($grade, [
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
            echo "  ✓ 5 grades par défaut insérés\n";
        }
    }

    /**
     * Corriger les utilisateurs existants
     */
    private function fixExistingUsers()
    {
        echo "👤 CORRECTION UTILISATEURS EXISTANTS\n";
        echo "-----------------------------------\n";

        // Créer/Mettre à jour les utilisateurs de test principaux
        $testUsers = [
            [
                'email' => 'superadmin@psiafrica.ci',
                'name' => 'Super Administrateur PSI',
                'type_user' => 'admin',
                'matricule' => 'SUP001',
                'password' => 'superadmin123',
                'role' => 'Super Admin'
            ],
            [
                'email' => 'admin@psiafrica.ci',
                'name' => 'Administrateur PSI',
                'type_user' => 'admin',
                'matricule' => 'ADM001',
                'password' => 'admin123',
                'role' => 'Admin'
            ],
            [
                'email' => 'comptoir@psiafrica.ci',
                'name' => 'Agent Comptoir Principal',
                'type_user' => 'agent_comptoir',
                'matricule' => 'CPT001',
                'password' => 'comptoir123',
                'role' => 'Agent Comptoir'
            ],
            [
                'email' => 'commercial@psiafrica.ci',
                'name' => 'Commercial Principal',
                'type_user' => 'commercial',
                'matricule' => 'COM001',
                'password' => 'commercial123',
                'role' => 'Commercial'
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'type_user' => $userData['type_user'],
                    'matricule' => $userData['matricule'],
                    'etat' => 1,
                    'statut_emploi' => 'actif',
                    'ent1d' => 1,
                    'contact' => '+225 00 00 00 00',
                    'photo_user' => 'NULL',
                    'date_embauche' => now(),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            echo "  ✓ Utilisateur {$userData['name']} créé/mis à jour\n";
        }

        // Générer des matricules pour les agents sans matricule
        $this->generateMissingMatricules();

        echo "  ✅ Utilisateurs corrigés\n\n";
    }

    /**
     * Générer des matricules manquants
     */
    private function generateMissingMatricules()
    {
        $agentsWithoutMatricule = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->where(function($q) {
                $q->whereNull('matricule')->orWhere('matricule', '');
            })
            ->get();

        foreach ($agentsWithoutMatricule as $agent) {
            $prefix = match($agent->type_user) {
                'admin' => 'ADM',
                'agent_comptoir' => 'CPT',
                'commercial' => 'COM',
                default => 'USR'
            };

            // Trouver le prochain numéro
            $lastNumber = User::where('matricule', 'like', $prefix . '%')
                ->orderBy('matricule', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastNumber) {
                $lastNum = (int) substr($lastNumber->matricule, 3);
                $nextNumber = $lastNum + 1;
            }

            $matricule = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $agent->update(['matricule' => $matricule]);

            echo "  ✓ Matricule {$matricule} généré pour {$agent->name}\n";
        }
    }

    /**
     * Assigner les rôles correctement
     */
    private function assignRolesCorrectly()
    {
        echo "🎯 ASSIGNATION CORRECTE DES RÔLES\n";
        echo "--------------------------------\n";

        // Récupérer tous les agents internes
        $agents = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->get();

        foreach ($agents as $agent) {
            // Déterminer le rôle selon le type
            $roleName = match($agent->type_user) {
                'admin' => $agent->email === 'superadmin@psiafrica.ci' ? 'Super Admin' : 'Admin',
                'agent_comptoir' => 'Agent Comptoir',
                'commercial' => 'Commercial',
                default => null
            };

            if ($roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    // Nettoyer les anciens rôles et assigner le nouveau
                    $agent->syncRoles([$role]);
                    echo "  ✓ {$agent->name}: Rôle '{$roleName}' assigné\n";
                }
            }
        }

        echo "  ✅ Rôles assignés correctement\n\n";
    }

    /**
     * Vérifier la configuration finale
     */
    private function verifyFinalConfiguration()
    {
        echo "🔍 VÉRIFICATION CONFIGURATION FINALE\n";
        echo "-----------------------------------\n";

        // Vérifier les utilisateurs de test
        $testEmails = [
            'superadmin@psiafrica.ci' => 'Super Admin',
            'admin@psiafrica.ci' => 'Admin',
            'comptoir@psiafrica.ci' => 'Agent Comptoir',
            'commercial@psiafrica.ci' => 'Commercial'
        ];

        $allOK = true;
        foreach ($testEmails as $email => $expectedRole) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                echo "  ❌ Utilisateur {$email} introuvable\n";
                $allOK = false;
                continue;
            }

            $hasRole = $user->hasRole($expectedRole);
            if (!$hasRole) {
                echo "  ❌ {$email}: Rôle '{$expectedRole}' manquant\n";
                $allOK = false;
            } else {
                echo "  ✅ {$email}: Rôle '{$expectedRole}' OK\n";
            }
        }

        // Vérifier les statistiques
        echo "\n📊 STATISTIQUES FINALES:\n";
        echo "  - Total utilisateurs: " . User::count() . "\n";
        echo "  - Agents internes: " . User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->count() . "\n";
        echo "  - Utilisateurs publics: " . User::where('type_user', 'public')->count() . "\n";
        echo "  - Total rôles: " . Role::count() . "\n";
        echo "  - Total permissions: " . Permission::count() . "\n";

        if ($allOK) {
            echo "  ✅ Configuration finale OK\n\n";
        } else {
            echo "  ⚠️ Des problèmes persistent\n\n";
        }
    }

    /**
     * Nettoyer tous les caches
     */
    private function clearAllCaches()
    {
        echo "🧹 NETTOYAGE DES CACHES\n";
        echo "----------------------\n";

        try {
            // Cache des permissions Spatie
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            echo "  ✓ Cache permissions Spatie nettoyé\n";

            // Cache Laravel
            try {
                \Artisan::call('cache:clear');
                echo "  ✓ Cache Laravel nettoyé\n";
            } catch (\Exception $e) {
                echo "  ⚠️ Erreur cache Laravel: " . $e->getMessage() . "\n";
            }

            // Cache config
            try {
                \Artisan::call('config:clear');
                echo "  ✓ Cache config nettoyé\n";
            } catch (\Exception $e) {
                echo "  ⚠️ Erreur cache config: " . $e->getMessage() . "\n";
            }

            echo "  ✅ Caches nettoyés\n\n";

        } catch (\Exception $e) {
            echo "  ⚠️ Erreur nettoyage caches: " . $e->getMessage() . "\n\n";
        }
    }
}