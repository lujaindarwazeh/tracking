<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventCreatedMail;
use App\Models\event;
use App\Models\companyusers;
use App\Models\company;



class checkspeedjob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyEvents = DB::table('companyevent')
            ->join('event', 'companyevent.event_id', '=', 'event.id')
            ->where('event.name', 'like', 'speed more than %')
            ->select('companyevent.company_id', 'event.id as event_id', 'event.name')
            ->get();

        foreach ($companyEvents as $entry) {
            $companyId = $entry->company_id;
            $eventId = $entry->event_id;
            $eventName = $entry->name;

            // Extract speed threshold from event name
            preg_match('/speed more than (\d+)/', $eventName, $matches);
            $speedLimit = isset($matches[1]) ? (int)$matches[1] : null;
            if (!$speedLimit) continue;

            $users = DB::table('companyusers')
                ->where('company_id', $companyId)
                ->where('role', '!=', 'Admin')
                ->select('id', 'speed', 'created_by', 'recipient_id')
                ->get();

            foreach ($users as $user) {
                if ($user->speed <= $speedLimit) continue;

                $recentEvent = DB::table('userevent')
                    ->where('user_id', $user->id)
                    ->where('event_id', $eventId)
                    ->where('created_at', '>=', now()->subHour())
                    ->exists();

                if ($recentEvent) {
                    Log::info("⏳ Skipped: User {$user->id} already triggered event '{$eventName}' within the last hour.");
                    continue;
                }

                DB::table('userevent')->insert([
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("✅ Speed event triggered: User {$user->id} exceeded {$speedLimit} km/h for company {$companyId}.");

                // Notify created_by
                if ($user->created_by) {
                    $creator = DB::table('companyusers')->where('id', $user->created_by)->first();
                    if ($creator && $creator->email) {
                        Mail::to($creator->email)->send(new EventCreatedMail(
                            Event::find($eventId),
                            companyusers::find($user->id),
                            Company::find($companyId)
                        ));
                        Log::info("📧 Email sent to created_by {$creator->email} for user {$user->id}.");
                    }
                }

               
                if ($user->recipient_id) {
                    $recipient = DB::table('companyusers')->where('id', $user->recipient_id)->first();
                    if ($recipient && $recipient->email) {
                        Mail::to($recipient->email)->send(new EventCreatedMail(
                            Event::find($eventId),
                            companyusers::find($user->id),
                            Company::find($companyId)
                        ));
                        Log::info("📧 Email sent to recipient_id {$recipient->email} for user {$user->id}.");
                    }
                }
            }
        }

        //
    }
}
