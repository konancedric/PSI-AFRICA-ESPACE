// ==================== MIGRATION 2 : add_crm_permissions_to_users_table.php ====================
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si la colonne n'existe pas déjà
            if (!Schema::hasColumn('users', 'crm_permissions')) {
                $table->json('crm_permissions')->nullable()->after('remember_token');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'crm_permissions')) {
                $table->dropColumn('crm_permissions');
            }
        });
    }
};