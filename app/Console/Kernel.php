<?php

namespace App\Console;

use App\Console\Commands\SavePlayersBattlelog;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\SavePlayersBattlelog::class,
        Commands\DemoCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('word:day')
        //     ->daily();
        // $schedule->command('demo:cron')
        // ->everyTwoMinutes();
        $schedule->command('battlelog:save')
        ->hourly();
        Log::debug("Schedule successfully run");
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
 
        require base_path('routes/console.php');
    }
}
