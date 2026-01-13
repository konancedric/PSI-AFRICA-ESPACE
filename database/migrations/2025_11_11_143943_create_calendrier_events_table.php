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
        Schema::create('calendrier_events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titre de l'événement (poste)
            $table->string('candidateName'); // Nom du candidat
            $table->string('candidateContact')->nullable(); // Contact du candidat
            $table->date('date'); // Date de l'événement
            $table->time('time'); // Heure de l'événement
            $table->string('eventType')->default('phone'); // Type d'entretien
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // Priorité
            $table->string('agent'); // Agent responsable
            $table->text('description')->nullable(); // Description/Notes
            $table->boolean('alarm')->default(false); // Alarme activée
            $table->date('alarmDate')->nullable(); // Date de l'alarme
            $table->time('alarmTime')->nullable(); // Heure de l'alarme
            $table->string('alarmFrequency')->default('once'); // Fréquence de l'alarme
            $table->string('status')->default('pending'); // Statut de l'événement
            $table->boolean('decision')->default(false); // Décision prise
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Utilisateur créateur
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('date');
            $table->index('agent');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendrier_events');
    }
};
