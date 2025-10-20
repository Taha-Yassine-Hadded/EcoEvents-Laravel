<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Nettoyage automatique des stories expirées toutes les heures
        $schedule->command('stories:cleanup')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Nettoyage plus agressif toutes les 6 heures (supprime même les stories en vedette expirées)
        $schedule->command('stories:cleanup --force')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Nettoyage de maintenance quotidien à 2h du matin
        $schedule->command('stories:cleanup --force --older-than=48')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
