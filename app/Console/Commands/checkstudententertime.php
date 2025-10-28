<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Log;


use Illuminate\Console\Command;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;
use App\Mail\LateEnterNotification;
use Carbon\Carbon;
use App\Mail\LateEnterMail;

class checkstudententertime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:late-entry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if students entered more than 1 hour late and send email';


    /**
     * Execute the console command.
     *
     * @return int
     */

public function handle()
{
    Log::info('Checking for late student entries...');

   
    return Command::SUCCESS;
}





}
