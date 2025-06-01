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
        // Clean expired password reset codes every hour
        $schedule->command('password-reset:clean-expired')->hourly();
 // Clean expired password reset codes every hour
        $schedule->command('password-reset:clean-expired')->hourly();
        
        // Update expired prescriptions daily at 6 AM
        $schedule->command('prescriptions:update-expired')->dailyAt('06:00');
        
        // You can add more scheduled tasks here
        // $schedule->command('inspire')->hourly();
        // You can add more scheduled tasks here
        // $schedule->command('inspire')->hourly();
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