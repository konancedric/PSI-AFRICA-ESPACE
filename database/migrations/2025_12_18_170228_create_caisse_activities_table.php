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
        if (!Schema::hasTable('caisse_activities')) {
            Schema::create('caisse_activities', function (Blueprint $table) {
                $table->id();
                $table->string('action', 100); // Type d'action (Entr�e Cr��e, Sortie Modifi�e, etc.)
                $table->text('details'); // D�tails de l'action
                $table->string('user_name', 191); // Nom de l'utilisateur (pour historique)
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID utilisateur
                $table->string('entity_type', 50)->nullable(); // Type d'entit� (entree, sortie, cloture, etc.)
                $table->string('entity_id', 50)->nullable(); // ID de l'entit� concern�e
                $table->timestamps();

                // Index pour am�liorer les performances
                $table->index('created_at');
                $table->index('user_id');
                $table->index('action');
                $table->index(['entity_type', 'entity_id'], 'caisse_activities_entity_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caisse_activities');
    }
};
