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
        Schema::create('caisse_sorties', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID unique généré côté client
            $table->date('date'); // Date de la sortie
            $table->string('ref')->unique(); // Référence unique
            $table->string('beneficiaire'); // Bénéficiaire de la sortie
            $table->text('motif'); // Motif de la sortie
            $table->decimal('montant', 15, 2); // Montant de la sortie
            $table->string('mode_paiement'); // Mode de paiement
            $table->text('remarques')->nullable(); // Remarques éventuelles
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
        Schema::dropIfExists('caisse_sorties');
    }
};
