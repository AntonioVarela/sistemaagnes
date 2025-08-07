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
        // Limpiar anuncios expirados diariamente a las 2:00 AM
        $schedule->command('anuncios:limpiar-expirados')
                ->dailyAt('02:00')
                ->appendOutputTo(storage_path('logs/anuncios-expirados.log'));
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
