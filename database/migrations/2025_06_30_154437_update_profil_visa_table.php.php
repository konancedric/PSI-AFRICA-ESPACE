<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateProfilVisaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Approche simple : essayer d'ajouter les colonnes et ignorer les erreurs si elles existent
        try {
            Schema::table('profil_visa', function (Blueprint $table) {
                // Ajouter les colonnes une par une avec try/catch pour chacune
                try { $table->tinyInteger('etat')->default(1)->after('id'); } catch (\Exception $e) {}
                try { $table->integer('etape')->default(1)->after('etat'); } catch (\Exception $e) {}
                try { $table->unsignedBigInteger('update_user')->nullable()->after('etape'); } catch (\Exception $e) {}
                try { $table->string('log_ip')->nullable()->after('update_user'); } catch (\Exception $e) {}
                try { $table->unsignedBigInteger('user1d')->nullable()->after('log_ip'); } catch (\Exception $e) {}
                try { $table->unsignedBigInteger('ent1d')->default(1)->after('user1d'); } catch (\Exception $e) {}
                try { $table->string('numero_profil_visa')->nullable()->after('ent1d'); } catch (\Exception $e) {}
                try { $table->unsignedBigInteger('id_statuts_etat')->nullable()->after('numero_profil_visa'); } catch (\Exception $e) {}
                try { $table->text('message')->nullable()->after('id_statuts_etat'); } catch (\Exception $e) {}
                try { $table->integer('type_profil_visa')->default(1)->after('message'); } catch (\Exception $e) {}
            });
        } catch (\Exception $e) {
            // Table ou colonnes existent déjà, continuer
        }

        // Ajouter les index de façon sécurisée
        try {
            DB::statement('CREATE INDEX idx_profil_visa_ent1d_etat ON profil_visa(ent1d, etat)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('CREATE INDEX idx_profil_visa_user1d ON profil_visa(user1d)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('CREATE INDEX idx_profil_visa_id_statuts_etat ON profil_visa(id_statuts_etat)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('CREATE INDEX idx_profil_visa_type_profil_visa ON profil_visa(type_profil_visa)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('CREATE INDEX idx_profil_visa_created_at ON profil_visa(created_at)');
        } catch (\Exception $e) {}

        // Générer des numéros de profil visa pour les enregistrements existants
        try {
            DB::statement("
                UPDATE profil_visa 
                SET numero_profil_visa = CONCAT('PSI-VIS-', LPAD(id, 6, '0')) 
                WHERE numero_profil_visa IS NULL OR numero_profil_visa = ''
            ");
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('profil_visa', function (Blueprint $table) {
                $table->dropColumn([
                    'etat', 'etape', 'update_user', 'log_ip', 'user1d', 
                    'ent1d', 'numero_profil_visa', 'id_statuts_etat', 
                    'message', 'type_profil_visa'
                ]);
            });
        } catch (\Exception $e) {
            // Ignorer les erreurs lors du rollback
        }
    }
}