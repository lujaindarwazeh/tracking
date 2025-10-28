<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\companyusers;
use App\Models\zones;
use App\Models\event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventCreatedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use PgSql\Lob;
use App\Models\company;


class CheckUserLocationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // distance in meters
    }


    public function __construct()
    {
        //
    }



   public function handle(): void
   {
       $companyEvents = DB::table('companyevent')
        ->join('event', 'companyevent.event_id', '=', 'event.id')
        ->where('event.name', 'like', 'enter zone %')
        ->select('companyevent.company_id', 'event.id as event_id', 'event.name')
        ->get();

        foreach ($companyEvents as $entry) {
            $companyId = $entry->company_id;
            $eventId = $entry->event_id;
            $eventName = $entry->name;

            
            preg_match('/zone\s+(.+)/i', $eventName, $matches);
            $zoneName = $matches[1] ?? null;
            if (!$zoneName) continue;

            
            $zone = DB::table('zone')
                ->where('company_id', $companyId)
                ->where('name', $zoneName)
                ->selectRaw('id, ST_Y(center_coordinates) as lat, ST_X(center_coordinates) as lon, radius')
                ->first();

            if (!$zone || !$zone->lat || !$zone->lon || !$zone->radius) continue;

            $users = DB::table('companyusers')
                ->where('company_id', $companyId)
                ->where('role', '!=', 'Admin')
                ->select('id', 'location', 'created_by', 'recipient_id')
                ->get();

            foreach ($users as $user) {
                if (!$user->location) continue;

                $coords = DB::table('companyusers')
                    ->where('id', $user->id)
                    ->selectRaw('ST_Y(location) as lat, ST_X(location) as lon')
                    ->first();

                if (!$coords || !$coords->lat || !$coords->lon) continue;

                $dist = $this->distance($coords->lat, $coords->lon, $zone->lat, $zone->lon);
                if ($dist > $zone->radius) continue;

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

                Log::info("✅ User {$user->id} is inside zone '{$zoneName}' for company {$companyId}. Event stored.");

                
                if ($user->created_by) {
                    $creator = DB::table('companyusers')->where('id', $user->created_by)->first();
                    if ($creator && $creator->email) {
                        Mail::to($creator->email)->send(new EventCreatedMail(Event::find($eventId), companyusers::find($user->id), Company::find($companyId)));
                        Log::info("📧 Email sent to created_by {$creator->email} for user {$user->id}.");
                    }
                }

               
                if ($user->recipient_id) {
                    $recipient = DB::table('companyusers')->where('id', $user->recipient_id)->first();
                    if ($recipient && $recipient->email) {
                        Mail::to($recipient->email)->send(new EventCreatedMail(Event::find($eventId), companyusers::find($user->id), Company::find($companyId)));
                        Log::info("📧 Email sent to recipient_id {$recipient->email} for user {$user->id}.");
                    }
                }
            }
        }
}



}
