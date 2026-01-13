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
        Schema::create('messagerie_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('recipient_id')->nullable(); // NULL = message public
            $table->text('text')->nullable(); // Nullable pour messages vocaux uniquement
            $table->longText('audio')->nullable(); // Base64 audio data
            $table->integer('audio_duration')->nullable(); // Durée en secondes
            $table->enum('type', ['text', 'voice', 'system'])->default('text');
            $table->boolean('is_private')->default(false);
            $table->timestamps();
            
            // Index pour performance
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['sender_id', 'recipient_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messagerie_messages');
    }
};