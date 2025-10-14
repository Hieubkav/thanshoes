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
        // Warm up product cache every hour to ensure fast page loads
        $schedule->command('cache:products warm')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Refresh cache every 6 hours to ensure data freshness
        $schedule->command('cache:products refresh')
                 ->everySixHours()
                 ->withoutOverlapping()
                 ->runInBackground();
                 
        // NEW: Warm up homepage cache every 30 minutes for optimal performance
        $schedule->command('cache:warm-homepage')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->description('Warm up homepage cache for better user experience');
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
