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
        Schema::create('caisse_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID unique généré côté client
            $table->string('mois')->unique(); // Mois au format YYYY-MM
            $table->decimal('montant', 15, 2); // Montant du budget
            $table->text('remarques')->nullable(); // Remarques éventuelles
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Utilisateur créateur
            $table->string('created_by_username'); // Username du créateur
            $table->timestamps();
            $table->softDeletes(); // Pour les suppressions logiques

            // Index pour améliorer les performances
            $table->index('mois');
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
        Schema::dropIfExists('caisse_budgets');
    }
};
