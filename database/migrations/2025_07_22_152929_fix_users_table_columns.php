<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUsersTableColumns extends Migration
{
    /**
     * Run the migrations - CORRECTION COLONNES MANQUANTES
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les colonnes manquantes si elles n'existent pas
            
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->unique()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'contact')) {
                $table->string('contact')->nullable()->after('matricule');
            }
            
            if (!Schema::hasColumn('users', 'type_user')) {
                $table->enum('type_user', ['admin', 'agent_comptoir', 'commercial', 'public'])
                      ->default('public')->after('contact');
            }
            
            if (!Schema::hasColumn('users', 'photo_user')) {
                $table->string('photo_user')->nullable()->default('NULL')->after('type_user');
            }
            
            if (!Schema::hasColumn('users', 'etat')) {
                $table->tinyInteger('etat')->default(1)->after('photo_user');
            }
            
            if (!Schema::hasColumn('users', 'statut_emploi')) {
                $table->enum('statut_emploi', ['actif', 'suspendu', 'conge', 'demission'])
                      ->default('actif')->after('etat');
            }
            
            if (!Schema::hasColumn('users', 'ent1d')) {
                $table->unsignedBigInteger('ent1d')->default(1)->after('statut_emploi');
            }
            
            if (!Schema::hasColumn('users', 'user1d')) {
                $table->unsignedBigInteger('user1d')->nullable()->after('ent1d');
            }
            
            if (!Schema::hasColumn('users', 'update_user')) {
                $table->unsignedBigInteger('update_user')->nullable()->after('user1d');
            }
            
            if (!Schema::hasColumn('users', 'id_categorie')) {
                $table->unsignedBigInteger('id_categorie')->nullable()->after('update_user');
            }
            
            if (!Schema::hasColumn('users', 'id_grade')) {
                $table->unsignedBigInteger('id_grade')->nullable()->after('id_categorie');
            }
            
            if (!Schema::hasColumn('users', 'date_embauche')) {
                $table->date('date_embauche')->nullable()->after('id_grade');
            }
            
            if (!Schema::hasColumn('users', 'salaire')) {
                $table->decimal('salaire', 10, 2)->nullable()->after('date_embauche');
            }
            
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->text('adresse')->nullable()->after('salaire');
            }
        });

        // Créer les tables de catégories et grades si elles n'existent pas
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('libelle');
                $table->text('description')->nullable();
                $table->tinyInteger('etat')->default(1);
                $table->unsignedBigInteger('ent1d')->default(1);
                $table->unsignedBigInteger('user1d')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->string('libelle');
                $table->text('description')->nullable();
                $table->tinyInteger('etat')->default(1);
                $table->unsignedBigInteger('ent1d')->default(1);
                $table->unsignedBigInteger('user1d')->nullable();
                $table->timestamps();
            });
        }

        // Insérer des données par défaut dans les nouvelles tables
        $this->insertDefaultData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées (optionnel car dangereux en production)
            $columnsToRemove = [
                'matricule', 'contact', 'type_user', 'photo_user', 'etat', 
                'statut_emploi', 'ent1d', 'user1d', 'update_user', 
                'id_categorie', 'id_grade', 'date_embauche', 'salaire', 'adresse'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Supprimer les tables créées (optionnel)
        Schema::dropIfExists('categories');
        Schema::dropIfExists('grades');
    }

    /**
     * Insérer des données par défaut
     */
    private function insertDefaultData()
    {
        // Insérer des catégories par défaut
        if (Schema::hasTable('categories') && \DB::table('categories')->count() == 0) {
            \DB::table('categories')->insert([
                [
                    'libelle' => 'Tourisme',
                    'description' => 'Voyages touristiques et de loisirs',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Affaires',
                    'description' => 'Voyages d\'affaires et professionnels',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Étudiant',
                    'description' => 'Voyages d\'études et éducatifs',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Famille',
                    'description' => 'Voyages familiaux et regroupement',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Transit',
                    'description' => 'Voyages de transit et escales',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        // Insérer des grades par défaut
        if (Schema::hasTable('grades') && \DB::table('grades')->count() == 0) {
            \DB::table('grades')->insert([
                [
                    'libelle' => 'Junior',
                    'description' => 'Niveau débutant - 0 à 2 ans d\'expérience',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Senior',
                    'description' => 'Niveau expérimenté - 2 à 5 ans d\'expérience',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Expert',
                    'description' => 'Niveau expert - 5 à 10 ans d\'expérience',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Manager',
                    'description' => 'Niveau managérial - Plus de 10 ans',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Directeur',
                    'description' => 'Niveau directorial - Leadership senior',
                    'etat' => 1,
                    'ent1d' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        // Mettre à jour les utilisateurs existants avec des valeurs par défaut
        \DB::table('users')->whereNull('type_user')->update([
            'type_user' => 'public',
            'etat' => 1,
            'statut_emploi' => 'actif',
            'ent1d' => 1,
            'photo_user' => 'NULL'
        ]);

        // Générer des matricules pour les agents qui n'en ont pas
        $this->generateMatricules();
    }

    /**
     * Générer des matricules pour les agents existants
     */
    private function generateMatricules()
    {
        try {
            $agents = \DB::table('users')
                ->whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
                ->whereNull('matricule')
                ->get();

            foreach ($agents as $agent) {
                $prefix = match($agent->type_user) {
                    'admin' => 'ADM',
                    'agent_comptoir' => 'CPT',
                    'commercial' => 'COM',
                    default => 'USR'
                };

                // Trouver le prochain numéro disponible pour ce type
                $lastMatricule = \DB::table('users')
                    ->where('matricule', 'like', $prefix . '%')
                    ->orderBy('matricule', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastMatricule) {
                    $lastNumber = (int) substr($lastMatricule->matricule, 3);
                    $nextNumber = $lastNumber + 1;
                }

                $matricule = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                \DB::table('users')
                    ->where('id', $agent->id)
                    ->update(['matricule' => $matricule]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur génération matricules: ' . $e->getMessage());
        }
    }
}