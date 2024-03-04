<?php

namespace App\Console;

use App\Console\Commands\BlockUser;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\MettreAJourStatutTrajets;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('trajets:update-status')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
