<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDossiersClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dossiers_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Client qui possède/reçoit le document
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade'); // Qui a uploadé (client ou admin)
            $table->string('file_name'); // Nom du fichier stocké
            $table->string('file_path'); // Chemin complet du fichier
            $table->string('original_name'); // Nom original du fichier
            $table->integer('file_size'); // Taille en bytes
            $table->string('file_type'); // Extension du fichier
            $table->enum('type', ['client_to_admin', 'admin_to_client']); // Direction de l'envoi
            $table->enum('status', ['pending', 'viewed', 'processed'])->default('pending'); // Statut du document
            $table->text('description')->nullable(); // Description optionnelle
            $table->timestamps();

            // Index pour améliorer les performances de recherche
            $table->index(['user_id', 'type']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dossiers_clients');
    }
}
