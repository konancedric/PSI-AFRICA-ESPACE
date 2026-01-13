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
        Schema::create('crm_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrat')->unique();
            
            // Informations client
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('lieu_naissance')->nullable();
            $table->string('nationalite');
            $table->enum('sexe', ['Masculin', 'Féminin']);
            $table->string('etat_civil');
            $table->string('profession');
            $table->string('employeur')->nullable();

            // Coordonnées
            $table->text('adresse');
            $table->string('ville');
            $table->string('telephone_mobile');
            $table->string('telephone_fixe')->nullable();
            $table->string('email');

            // Informations visa
            $table->string('type_visa');
            $table->string('pays_destination');

            // Informations financières
            $table->decimal('montant_contrat', 12, 2);
            $table->text('montant_lettres');
            $table->date('date_echeance')->nullable();
            $table->string('mode_paiement')->nullable();

            // Informations conseiller
            $table->string('conseiller')->nullable();
            $table->string('lieu_contrat')->default('Abidjan');
            $table->date('date_contrat');

            // Signature - VARCHAR pour eviter les problemes d encodage ENUM
            $table->string('statut', 50)->default('En attente');
            $table->text('signature')->nullable(); // Base64 de la signature
            $table->string('nom_signataire')->nullable();
            $table->timestamp('date_signature')->nullable();
            
            // Metadonnees
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('numero_contrat');
            $table->index('statut');
            $table->index('created_at');
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_contracts');
    }
};