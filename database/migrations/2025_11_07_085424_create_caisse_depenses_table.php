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
        Schema::create('caisse_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID unique généré côté client
            $table->date('date'); // Date de la dépense
            $table->string('categorie'); // Catégorie de la dépense
            $table->text('description'); // Description de la dépense
            $table->decimal('montant', 15, 2); // Montant de la dépense
            $table->string('beneficiaire')->nullable(); // Bénéficiaire
            $table->string('mode_paiement'); // Mode de paiement
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Utilisateur créateur
            $table->string('created_by_username'); // Username du créateur
            $table->timestamps();
            $table->softDeletes(); // Pour les suppressions logiques

            // Index pour améliorer les performances
            $table->index('date');
            $table->index('categorie');
            $table->index('created_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caisse_depenses');
    }
};
