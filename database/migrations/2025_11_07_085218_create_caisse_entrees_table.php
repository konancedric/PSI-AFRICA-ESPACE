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
        Schema::create('caisse_entrees', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID unique généré côté client
            $table->date('date'); // Date de l'entrée
            $table->string('ref')->unique(); // Référence unique
            $table->string('nom'); // Nom du client
            $table->string('prenoms'); // Prénoms du client
            $table->string('categorie'); // Catégorie (Frais de Cabinet, Documents de Voyage, Autre)
            $table->string('nature'); // Nature/Prestation
            $table->decimal('montant', 15, 2); // Montant total
            $table->string('mode_paiement'); // Mode de paiement
            $table->json('detail_prestations')->nullable(); // Détail des prestations avec montants
            $table->string('tiers_nom')->nullable(); // Nom du tiers (pour documents de voyage)
            $table->decimal('montant_verse_tiers', 15, 2)->default(0); // Montant versé au tiers
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Utilisateur créateur
            $table->string('created_by_username'); // Username du créateur
            $table->timestamps();
            $table->softDeletes(); // Pour les suppressions logiques

            // Index pour améliorer les performances
            $table->index('date');
            $table->index('ref');
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
        Schema::dropIfExists('caisse_entrees');
    }
};
