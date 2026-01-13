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
        Schema::table('crm_payments', function (Blueprint $table) {
            // Champs pour la signature individuelle de chaque paiement/reçu
            $table->timestamp('receipt_signed_at')->nullable()->after('payment_method');
            $table->text('receipt_signature')->nullable()->after('receipt_signed_at');
            $table->string('client_ip', 45)->nullable()->after('receipt_signature');
            $table->string('receipt_number', 50)->nullable()->after('client_ip');

            // Index pour améliorer les performances
            $table->index('receipt_signed_at');
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_payments', function (Blueprint $table) {
            $table->dropIndex(['receipt_signed_at']);
            $table->dropIndex(['receipt_number']);
            $table->dropColumn([
                'receipt_signed_at',
                'receipt_signature',
                'client_ip',
                'receipt_number'
            ]);
        });
    }
};
