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
        Schema::table('crm_invoices', function (Blueprint $table) {
            $table->timestamp('client_validated_at')->nullable()->after('view_token');
            $table->text('client_signature_data')->nullable()->after('client_validated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_invoices', function (Blueprint $table) {
            $table->dropColumn(['client_validated_at', 'client_signature_data']);
        });
    }
};
