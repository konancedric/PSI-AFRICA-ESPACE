<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditDeletePermissionsToCrmPermissions extends Migration
{
    public function up()
    {
        // Les permissions sont déjà stockées dans users.crm_permissions (JSON)
        // On ajoute juste la documentation des nouvelles permissions disponibles
        
        // Nouvelles permissions disponibles :
        // - edit_clients : Modifier les clients
        // - delete_clients : Supprimer les clients
        // - edit_invoices : Modifier les factures
        // - delete_invoices : Supprimer les factures
        // - edit_payments : Modifier les paiements
        // - delete_payments : Supprimer les paiements
    }

    public function down()
    {
        // Pas de rollback nécessaire car on utilise un champ JSON
    }
}