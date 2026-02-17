<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Clôture mensuelle automatique le 1er de chaque mois à 00:01
        $schedule->command('caisse:cloture-mensuelle')
                 ->monthlyOn(1, '00:01')
                 ->timezone('Africa/Abidjan')
                 ->withoutOverlapping()
                 ->onOneServer();

        // Sync automatique Profil Visa → CRM toutes les minutes
        $schedule->command('crm:sync-profil-visa')
                 ->everyMinute()
                 ->timezone('Africa/Abidjan')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Relances hebdomadaires SMS (chaque lundi à 09h00)
        $schedule->command('sms:relances-hebdomadaires')
                 ->weeklyOn(1, '09:00')
                 ->timezone('Africa/Abidjan')
                 ->withoutOverlapping()
                 ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
