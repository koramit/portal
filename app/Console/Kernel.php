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
        $schedule->command('token:expired-reminder')->timezone('Asia/Bangkok')->at('08:00');

        // tracking in-patient
        $schedule->command('admission:update-list')->hourlyAt(0);
        $schedule->command('admission:update-list')->hourlyAt(10);
        $schedule->command('admission:build-list')->hourlyAt(15);
        $schedule->command('admission:update-list')->hourlyAt(30);
        $schedule->command('admission:build-list')->hourlyAt(45);
        $schedule->command('admission:update-list')->hourlyAt(50);
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
