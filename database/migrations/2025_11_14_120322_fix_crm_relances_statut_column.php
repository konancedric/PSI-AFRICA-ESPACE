<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCrmRelancesStatutColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Solution sans doctrine/dbal : utiliser du SQL brut
        DB::statement('ALTER TABLE crm_relances MODIFY statut VARCHAR(50) DEFAULT "En cours"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE crm_relances MODIFY statut ENUM("En cours", "Clôturé") DEFAULT "En cours"');
    }
}
