<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // ✅ Register your commands here
    protected $commands = [
        \App\Console\Commands\SendTaskReminders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Schedule your command here
        $schedule->command('reminders:send')->dailyAt('18:50');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
