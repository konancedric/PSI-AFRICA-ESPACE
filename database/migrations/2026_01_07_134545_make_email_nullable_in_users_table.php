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
        // Utiliser une requête SQL brute pour éviter de dépendre de doctrine/dbal
        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');

        // Ajouter l'index unique sur contact s'il n'existe pas déjà
        if (!Schema::hasColumn('users', 'contact')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('contact')->nullable()->unique()->after('email');
            });
        } else {
            // Vérifier si l'index unique n'existe pas déjà
            try {
                DB::statement('ALTER TABLE `users` ADD UNIQUE INDEX `users_contact_unique` (`contact`)');
            } catch (\Exception $e) {
                // L'index existe déjà, c'est OK
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remettre email comme obligatoire
        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL');

        // Retirer l'index unique de contact
        try {
            DB::statement('ALTER TABLE `users` DROP INDEX `users_contact_unique`');
        } catch (\Exception $e) {
            // L'index n'existe pas, c'est OK
        }
    }
};
