<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Créer la table grades si elle n'existe pas
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->string('libelle')->comment('Nom du grade');
                $table->text('description')->nullable()->comment('Description du grade');
                $table->integer('niveau')->default(1)->comment('Niveau hiérarchique du grade');
                $table->decimal('salaire_min', 10, 2)->nullable()->comment('Salaire minimum pour ce grade');
                $table->decimal('salaire_max', 10, 2)->nullable()->comment('Salaire maximum pour ce grade');
                $table->boolean('etat')->default(1)->comment('Statut actif/inactif');
                $table->unsignedBigInteger('ent1d')->default(1)->comment('ID de l\'entreprise');
                $table->unsignedBigInteger('user1d')->nullable()->comment('ID utilisateur créateur');
                $table->unsignedBigInteger('update_user')->nullable()->comment('ID utilisateur modificateur');
                $table->timestamps();

                // Index pour améliorer les performances
                $table->index(['etat', 'ent1d']);
                $table->index('niveau');
                $table->unique(['libelle', 'ent1d'], 'unique_grade_per_company');

                // Clés étrangères
                $table->foreign('ent1d')->references('id')->on('entreprises')->onDelete('cascade');
                $table->foreign('user1d')->references('id')->on('users')->onDelete('set null');
                $table->foreign('update_user')->references('id')->on('users')->onDelete('set null');
            });
        } else {
            // Si la table existe, vérifier et ajouter les colonnes manquantes
            Schema::table('grades', function (Blueprint $table) {
                if (!Schema::hasColumn('grades', 'niveau')) {
                    $table->integer('niveau')->default(1)->after('description');
                }
                if (!Schema::hasColumn('grades', 'salaire_min')) {
                    $table->decimal('salaire_min', 10, 2)->nullable()->after('niveau');
                }
                if (!Schema::hasColumn('grades', 'salaire_max')) {
                    $table->decimal('salaire_max', 10, 2)->nullable()->after('salaire_min');
                }
                if (!Schema::hasColumn('grades', 'etat')) {
                    $table->boolean('etat')->default(1)->after('salaire_max');
                }
                if (!Schema::hasColumn('grades', 'ent1d')) {
                    $table->unsignedBigInteger('ent1d')->default(1)->after('etat');
                }
                if (!Schema::hasColumn('grades', 'user1d')) {
                    $table->unsignedBigInteger('user1d')->nullable()->after('ent1d');
                }
                if (!Schema::hasColumn('grades', 'update_user')) {
                    $table->unsignedBigInteger('update_user')->nullable()->after('user1d');
                }
            });
        }

        // Insérer les grades par défaut
        $this->insertDefaultGrades();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }

    /**
     * Insérer les grades par défaut
     */
    private function insertDefaultGrades()
    {
        $defaultGrades = [
            [
                'libelle' => 'Stagiaire',
                'description' => 'Niveau d\'entrée pour les stagiaires',
                'niveau' => 1,
                'salaire_min' => 50000.00,
                'salaire_max' => 100000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Assistant',
                'description' => 'Assistant administratif ou technique',
                'niveau' => 2,
                'salaire_min' => 80000.00,
                'salaire_max' => 150000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Agent',
                'description' => 'Agent de base',
                'niveau' => 3,
                'salaire_min' => 120000.00,
                'salaire_max' => 200000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Agent Senior',
                'description' => 'Agent avec expérience',
                'niveau' => 4,
                'salaire_min' => 180000.00,
                'salaire_max' => 280000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Superviseur',
                'description' => 'Superviseur d\'équipe',
                'niveau' => 5,
                'salaire_min' => 250000.00,
                'salaire_max' => 400000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Chef de Service',
                'description' => 'Responsable de service',
                'niveau' => 6,
                'salaire_min' => 350000.00,
                'salaire_max' => 550000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Manager',
                'description' => 'Manager de département',
                'niveau' => 7,
                'salaire_min' => 500000.00,
                'salaire_max' => 800000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'libelle' => 'Directeur',
                'description' => 'Directeur général ou de division',
                'niveau' => 8,
                'salaire_min' => 750000.00,
                'salaire_max' => 1200000.00,
                'etat' => 1,
                'ent1d' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insérer seulement si la table est vide
        if (\DB::table('grades')->count() === 0) {
            \DB::table('grades')->insert($defaultGrades);
        }
    }
};