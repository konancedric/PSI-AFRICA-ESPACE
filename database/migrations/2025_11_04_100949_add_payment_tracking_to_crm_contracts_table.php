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
        Schema::table('crm_contracts', function (Blueprint $table) {
            $table->decimal('avance', 12, 2)->default(0)->after('montant_contrat');
            $table->decimal('reste_payer', 12, 2)->default(0)->after('avance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_contracts', function (Blueprint $table) {
            $table->dropColumn(['avance', 'reste_payer']);
        });
    }
};
