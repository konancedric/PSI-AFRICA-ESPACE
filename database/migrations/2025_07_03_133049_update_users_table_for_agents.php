<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableForAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier et ajouter les colonnes manquantes
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'contact')) {
                $table->string('contact')->nullable()->after('matricule');
            }
            if (!Schema::hasColumn('users', 'id_grade')) {
                $table->unsignedBigInteger('id_grade')->nullable()->after('contact');
            }
            if (!Schema::hasColumn('users', 'id_categorie')) {
                $table->unsignedBigInteger('id_categorie')->nullable()->after('id_grade');
            }
            if (!Schema::hasColumn('users', 'type_user')) {
                $table->enum('type_user', ['public', 'agent_comptoir', 'commercial', 'admin'])->default('public')->after('id_categorie');
            }
            if (!Schema::hasColumn('users', 'photo_user')) {
                $table->string('photo_user')->nullable()->after('type_user');
            }
            if (!Schema::hasColumn('users', 'etat')) {
                $table->tinyInteger('etat')->default(1)->after('photo_user');
            }
            if (!Schema::hasColumn('users', 'ent1d')) {
                $table->unsignedBigInteger('ent1d')->default(1)->after('etat');
            }
            if (!Schema::hasColumn('users', 'update_user')) {
                $table->unsignedBigInteger('update_user')->nullable()->after('ent1d');
            }
            if (!Schema::hasColumn('users', 'date_embauche')) {
                $table->date('date_embauche')->nullable()->after('update_user');
            }
            if (!Schema::hasColumn('users', 'salaire')) {
                $table->decimal('salaire', 10, 2)->nullable()->after('date_embauche');
            }
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->text('adresse')->nullable()->after('salaire');
            }
            if (!Schema::hasColumn('users', 'statut_emploi')) {
                $table->enum('statut_emploi', ['actif', 'suspendu', 'conge', 'demission'])->default('actif')->after('adresse');
            }
        });

        // Ajouter des index pour optimiser les performances
        Schema::table('users', function (Blueprint $table) {
            $table->index(['type_user', 'etat']);
            $table->index(['ent1d', 'etat']);
            $table->index(['statut_emploi']);
            $table->unique(['matricule', 'ent1d']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['type_user', 'etat']);
            $table->dropIndex(['ent1d', 'etat']);
            $table->dropIndex(['statut_emploi']);
            $table->dropUnique(['matricule', 'ent1d']);
            
            $table->dropColumn([
                'matricule', 'contact', 'id_grade', 'id_categorie', 
                'type_user', 'photo_user', 'etat', 'ent1d', 
                'update_user', 'date_embauche', 'salaire', 
                'adresse', 'statut_emploi'
            ]);
        });
    }
}