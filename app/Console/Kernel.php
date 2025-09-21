<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    
    protected function schedule(Schedule $schedule): void
    {
        // Example: run your expiry reminders at 10 AM Libya time
        $schedule->command('documents:remind-expiry')
            ->dailyAt('10:00')
            ->timezone('Africa/Tripoli');
        
        $schedule->call(function () {
            for ($i = 0; $i < 3; $i++) {
                \Artisan::call('documents:remind-expiry');
                Log::info('documents:remind-expiry executed at '.now());

                sleep(20);
            }
        })->everyMinute();
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
