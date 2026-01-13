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
            $table->string('signature_token', 64)->unique()->nullable()->after('statut');
            $table->timestamp('token_expires_at')->nullable()->after('signature_token');
            $table->timestamp('token_used_at')->nullable()->after('token_expires_at');
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
            $table->dropColumn(['signature_token', 'token_expires_at', 'token_used_at']);
        });
    }
};
