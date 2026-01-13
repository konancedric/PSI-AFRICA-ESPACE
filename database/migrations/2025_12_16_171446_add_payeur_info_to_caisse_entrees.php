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
        Schema::table('caisse_entrees', function (Blueprint $table) {
            if (!Schema::hasColumn('caisse_entrees', 'type_payeur')) {
                $table->enum('type_payeur', ['lui_meme', 'autre_personne'])->default('lui_meme')->after('montant_verse_tiers');
            }
            if (!Schema::hasColumn('caisse_entrees', 'payeur_nom_prenom')) {
                $table->string('payeur_nom_prenom')->nullable()->after('type_payeur');
            }
            if (!Schema::hasColumn('caisse_entrees', 'payeur_telephone')) {
                $table->string('payeur_telephone')->nullable()->after('payeur_nom_prenom');
            }
            if (!Schema::hasColumn('caisse_entrees', 'payeur_relation')) {
                $table->string('payeur_relation')->nullable()->after('payeur_telephone');
            }
            if (!Schema::hasColumn('caisse_entrees', 'payeur_reference_dossier')) {
                $table->string('payeur_reference_dossier')->nullable()->after('payeur_relation');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caisse_entrees', function (Blueprint $table) {
            $table->dropColumn([
                'type_payeur',
                'payeur_nom_prenom',
                'payeur_telephone',
                'payeur_relation',
                'payeur_reference_dossier'
            ]);
        });
    }
};
