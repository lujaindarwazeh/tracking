<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckUserLocationJob;
use App\Jobs\checkspeedjob;

class Kernel extends ConsoleKernel
{
    //cron job in linux 
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //$schedule->command('check:late-entry')->dailyAt('11:36');
      //  $schedule->job(new CheckUserLocationJob)->everyMinute();
      //  $schedule->job(new checkspeedjob)->everyMinute();

        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


    protected $commands = [
    //\App\Console\Commands\checkstudententertime::class,



    
];

}
