<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddMessageProfilVisaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Si la table existe déjà, ne rien faire
        if (Schema::hasTable('add_message_profil_visa')) {
            echo "Table 'add_message_profil_visa' existe déjà, migration ignorée.\n";
            return;
        }

        // Créer la table seulement si elle n'existe pas
        Schema::create('add_message_profil_visa', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('objet');
            $table->unsignedBigInteger('id_profil_visa');
            $table->unsignedBigInteger('user1d');
            $table->string('photo')->nullable();
            $table->tinyInteger('etat')->default(1);
            $table->timestamps();

            // Index
            $table->index(['id_profil_visa', 'created_at']);
            $table->index(['user1d']);
            $table->index(['etat']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_message_profil_visa');
    }
}