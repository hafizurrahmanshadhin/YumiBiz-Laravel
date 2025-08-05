<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule) {
        //! Scheduler for updating boost status
        $schedule->call(function () {
            User::whereHas('boosts', function ($query) {
                $query->where('expires_at', '<=', now());
            })->update(['is_boost' => 0]);
        })->everyMinute();

        //! Scheduler for updating subscription status
        $schedule->call(function () {
            User::whereHas('memberships', function ($query) {
                $query->where('end_date', '<=', now());
            })->update(['is_subscribed' => 0]);
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
