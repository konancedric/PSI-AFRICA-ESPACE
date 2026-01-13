<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStatutsEtatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ✅ CORRECTION CRITIQUE : Vérifier et supprimer la table si elle existe avec des problèmes
        if (Schema::hasTable('statuts_etat')) {
            // Vérifier si la table a des problèmes de structure
            try {
                $hasCorrectId = Schema::hasColumn('statuts_etat', 'id');
                if (!$hasCorrectId) {
                    Schema::dropIfExists('statuts_etat');
                }
            } catch (\Exception $e) {
                Schema::dropIfExists('statuts_etat');
            }
        }

        // Créer la table avec une structure correcte
        if (!Schema::hasTable('statuts_etat')) {
            Schema::create('statuts_etat', function (Blueprint $table) {
                // ✅ CORRECTION : ID auto-increment PRIMARY KEY avec configuration explicite
                $table->id()->autoIncrement()->unsigned();
                $table->string('libelle')->index();
                $table->text('description')->nullable();
                $table->string('couleur', 50)->default('secondary');
                $table->string('icone', 100)->nullable();
                $table->integer('ordre')->default(0)->index();
                $table->tinyInteger('etat')->default(1)->index();
                
                // ✅ CORRECTION MAJEURE : Utiliser ent1d (avec 1) et non entId
                $table->unsignedBigInteger('ent1d')->default(1)->index();
                $table->unsignedBigInteger('user1d')->nullable();
                
                $table->timestamps();

                // Index pour améliorer les performances
                $table->index(['etat', 'ordre']);
                $table->index(['ent1d', 'etat']);
                
                // Clés étrangères conditionnelles
                if (Schema::hasTable('users')) {
                    $table->foreign('user1d')->references('id')->on('users')->onDelete('set null');
                }
                
                if (Schema::hasTable('entreprises')) {
                    $table->foreign('ent1d')->references('id')->on('entreprises')->onDelete('cascade');
                }
            });
        }

        // ✅ Insérer les statuts par défaut seulement si la table est vide
        $this->insertDefaultStatuses();
    }

    /**
     * Insérer les statuts par défaut - VERSION CORRIGÉE
     */
    private function insertDefaultStatuses()
    {
        try {
            // Vérifier si des statuts existent déjà
            if (DB::table('statuts_etat')->count() > 0) {
                return; // Des statuts existent déjà
            }

            $defaultStatuses = [
                [
                    'libelle' => 'En attente',
                    'description' => 'Demande reçue et en attente de traitement',
                    'couleur' => 'warning',
                    'icone' => 'fas fa-clock',
                    'ordre' => 1,
                    'etat' => 1,
                    'ent1d' => 1, // ✅ CORRECTION : ent1d avec 1
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'En cours de traitement',
                    'description' => 'Demande en cours d\'analyse',
                    'couleur' => 'info',
                    'icone' => 'fas fa-cogs',
                    'ordre' => 2,
                    'etat' => 1,
                    'ent1d' => 1,
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Documents complémentaires requis',
                    'description' => 'Documents supplémentaires nécessaires',
                    'couleur' => 'primary',
                    'icone' => 'fas fa-file-alt',
                    'ordre' => 3,
                    'etat' => 1,
                    'ent1d' => 1,
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Approuvé',
                    'description' => 'Demande approuvée avec succès',
                    'couleur' => 'success',
                    'icone' => 'fas fa-check-circle',
                    'ordre' => 4,
                    'etat' => 1,
                    'ent1d' => 1,
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Rejeté',
                    'description' => 'Demande rejetée',
                    'couleur' => 'danger',
                    'icone' => 'fas fa-times-circle',
                    'ordre' => 5,
                    'etat' => 1,
                    'ent1d' => 1,
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'libelle' => 'Visa délivré',
                    'description' => 'Visa délivré et prêt pour retrait',
                    'couleur' => 'success',
                    'icone' => 'fas fa-passport',
                    'ordre' => 6,
                    'etat' => 1,
                    'ent1d' => 1,
                    'user1d' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];

            // ✅ Insertion en lot pour de meilleures performances
            DB::table('statuts_etat')->insert($defaultStatuses);
            
        } catch (\Exception $e) {
            \Log::error('Erreur insertion statuts par défaut: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuts_etat');
    }
}