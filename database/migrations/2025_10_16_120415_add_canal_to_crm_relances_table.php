<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crm_relances', function (Blueprint $table) {
            //  Ajouter le champ canal
            $table->enum('canal', ['sms', 'whatsapp', 'email'])
                  ->default('whatsapp')
                  ->after('statut')
                  ->comment('Canal de communication utilisé pour la relance');
            
            //  Index pour optimiser les requêtes
            $table->index('canal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_relances', function (Blueprint $table) {
            $table->dropColumn('canal');
        });
    }
};