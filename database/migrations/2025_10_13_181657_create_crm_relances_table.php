<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmRelancesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_relances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->string('agent_name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('statut', ['En cours', 'Clôturé'])->default('En cours');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_relance');
            $table->timestamp('prochaine_relance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_relances');
    }
}