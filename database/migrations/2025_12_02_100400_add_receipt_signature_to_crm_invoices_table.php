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
            $table->timestamp('receipt_signed_at')->nullable()->after('client_signature_data');
            $table->text('receipt_signature_data')->nullable()->after('receipt_signed_at');
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
            $table->dropColumn(['receipt_signed_at', 'receipt_signature_data']);
        });
    }
};
