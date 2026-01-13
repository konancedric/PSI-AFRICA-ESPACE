<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convertir la colonne statut de ENUM a VARCHAR avec SQL brut
        // Cela evite tous les problemes d encodage ENUM
        DB::statement('ALTER TABLE crm_contracts MODIFY statut VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT "En attente"');

        // Nettoyer les valeurs existantes au cas ou il y aurait des problemes d encodage
        DB::statement('UPDATE crm_contracts SET statut = "En attente" WHERE statut LIKE "%attente%"');
        DB::statement('UPDATE crm_contracts SET statut = "Signé" WHERE statut LIKE "%ign%"');
        DB::statement('UPDATE crm_contracts SET statut = "Annulé" WHERE statut LIKE "%nnul%"');
    }

    public function down()
    {
        // Revenir a ENUM si necessaire
        DB::statement("ALTER TABLE crm_contracts MODIFY statut ENUM('En attente', 'Signé', 'Annulé') DEFAULT 'En attente'");
    }
};
