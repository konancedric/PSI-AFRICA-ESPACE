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
            // Token pour la consultation du contrat (lien de visualisation)
            $table->string('view_token', 100)->nullable()->unique()->after('signature_token');
            $table->timestamp('view_token_generated_at')->nullable()->after('view_token');
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
            $table->dropColumn(['view_token', 'view_token_generated_at']);
        });
    }
};
