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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'billet' ou 'hotel'
            $table->string('reference')->unique();
            $table->date('date_document');
            $table->text('clients'); // Noms des clients/voyageurs (JSON)

            // Champs communs
            $table->string('destination')->nullable();
            $table->string('ville')->nullable();

            // Champs pour billet d'avion
            $table->string('compagnie')->nullable();
            $table->date('date_depart')->nullable();
            $table->date('date_retour')->nullable();
            $table->string('ref_reservation')->nullable();
            $table->text('voyageurs')->nullable(); // JSON avec détails passeports

            // Champs pour hôtel
            $table->string('nom_hotel')->nullable();
            $table->text('adresse_hotel')->nullable();
            $table->string('telephone_hotel')->nullable();
            $table->string('email_hotel')->nullable();
            $table->date('date_arrivee')->nullable();
            $table->date('date_depart_hotel')->nullable();
            $table->integer('nuits')->nullable();
            $table->string('type_appartement')->nullable();
            $table->integer('adultes')->nullable();
            $table->integer('enfants')->nullable();
            $table->decimal('tarif_euro', 10, 2)->nullable();
            $table->decimal('tarif_fcfa', 15, 2)->nullable();

            // Métadonnées
            $table->string('agent_name')->nullable();
            $table->string('agent_fonction')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
