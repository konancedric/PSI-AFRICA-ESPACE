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
            $table->string('download_token', 64)->nullable()->unique()->after('token_used_at');
            $table->timestamp('download_token_expires_at')->nullable()->after('download_token');
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
            $table->dropColumn(['download_token', 'download_token_expires_at']);
        });
    }
};
