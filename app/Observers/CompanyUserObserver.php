<?php

namespace App\Observers;

use App\Models\companyusers;
use App\Models\zones;
use Illuminate\Support\Facades\DB;
use App\Models\event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventCreatedMail;
use App\Models\company;



class CompanyUserObserver
{


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

   

   



   
    private function hasActiveEvent($userId, $eventId)
    {
        return DB::table('userevent')
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->where('active', true)
            ->exists();
    }

    private function recentlyTriggered($userId, $eventId, $minutes = 10)
    {
    return DB::table('userevent')
        ->where('user_id', $userId)
        ->where('event_id', $eventId)
        ->where('created_at', '>=', now()->subMinutes($minutes))
        ->exists();
    }




    private function triggerEvent(companyusers $user, event $event, $companyId)
    {
        DB::table('userevent')->insert([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("✅ Observer: User {$user->id} triggered event '{$event->name}'.");

        foreach (['created_by', 'recipient_id'] as $field) {
            if ($user->$field) {
                $target = companyusers::find($user->$field);
                if ($target && $target->email) {
                    Mail::to($target->email)->send(new EventCreatedMail($event, $user, company::find($companyId)));
                    Log::info("📧 Email sent to {$field} {$target->email} for user {$user->id}.");
                }
            }
        }
   }



    /**
     * Handle the CompanyUser "created" event.
     */
    public function created(companyusers $companyUser): void
    {

        //
    }

    /**
     * Handle the CompanyUser "updated" event.
     */
    public function updated(companyusers $user): void
    {
        Log::info("🔄 CompanyUser {$user->id} updated, checking changes...");
        


        $companyId = $user->company_id;

      
       if ($user->isDirty('location')){

            Log::info("📍 Location changed for User {$user->id}, checking zones...");
            $zones = zones::where('company_id', $companyId)->get();

            foreach ($zones as $zone) {
                $zoneCoords = DB::table('zone')
                    ->where('id', $zone->id)
                    ->selectRaw('ST_Y(center_coordinates) as lat, ST_X(center_coordinates) as lon, radius')
                    ->first();

                Log::info("Checking zone '{$zoneCoords->lat}','{$zoneCoords->lon}' with radius {$zoneCoords->radius} meters.");

                $userCoords = DB::table('companyusers')
                    ->where('id', $user->id)
                    ->selectRaw('ST_Y(location) as lat, ST_X(location) as lon')
                    ->first();
                Log::info("User coordinates: lat='{$userCoords->lat}', lon='{$userCoords->lon}'.");

                if (!$zoneCoords || !$userCoords) continue;

                $distance = $this->distance($userCoords->lat, $userCoords->lon, $zoneCoords->lat, $zoneCoords->lon);
                $event = event::where('name', 'enter zone ' . $zone->name)->first();
                if (!$event) continue;

                $eventId = $event->id;
                $isActive = $this->hasActiveEvent($user->id, $eventId);


                Log::info("➡️ User {$user->id} is {$distance} meters from zone '{$zone->name}' (radius: {$zoneCoords->radius} meters).");


                if ($distance <= $zoneCoords->radius) {

                    Log::info("✅ Zone entered: User {$user->id} is inside zone '{$zone->name}'.");
                   if (!$isActive && !$this->recentlyTriggered($user->id, $eventId, 10)) {
                        $this->triggerEvent($user, $event, $companyId);
                    } 
                    else {
                        Log::info("⏳ Skipped: User {$user->id} already triggered zone '{$zone->name}' in last 10 minutes.");
                    }


                } else {
                    Log::info("❌ User {$user->id} is outside zone '{$zone->name}'.");
                    if ($isActive) {
                        DB::table('userevent')
                            ->where('user_id', $user->id)
                            ->where('event_id', $eventId)
                            ->update(['active' => false]);
                        Log::info("✅ Zone exited: User {$user->id} left zone '{$zone->name}'.");
                    }
                }
            }
        }


        if ($user->isDirty('speed')) {
            Log::info("🚀 Speed changed for User {$user->id}, checking speed events...");

                $speedEvents = DB::table('companyevent')
                    ->join('event', 'companyevent.event_id', '=', 'event.id')
                    ->where('companyevent.company_id', $companyId)
                    ->where('event.name', 'like', 'speed more than %')
                    ->select('event.id as event_id', 'event.name')
                    ->get();

                foreach ($speedEvents as $entry) {
                    preg_match('/speed more than (\d+)/', $entry->name, $matches);
                    $limit = isset($matches[1]) ? (int)$matches[1] : null;
                    if (!$limit) continue;

                    $eventId = $entry->event_id;
                    $isActive = $this->hasActiveEvent($user->id, $eventId);

                    if ($user->speed > $limit) {
                        if (!$isActive && !$this->recentlyTriggered($user->id, $eventId, 10)) {
                            $this->triggerEvent($user, event::find($eventId), $companyId);
                        } 
                        else {
                            Log::info("⏳ Skipped: User {$user->id} already triggered '{$entry->name}' in last 10 minutes.");
                        }

                    } else {
                        if ($isActive) {
                            DB::table('userevent')
                                ->where('user_id', $user->id)
                                ->where('event_id', $eventId)
                                ->update(['active' => false]);
                            Log::info("✅ Speed normalized: User {$user->id} no longer violating '{$entry->name}'.");
                        }
                    }
                }
        }









        
    }

    /**
     * Handle the CompanyUser "deleted" event.
     */
    public function deleted(companyusers $companyUser): void
    {
        
    }

    /**
     * Handle the CompanyUser "restored" event.
     */
    public function restored(companyusers $companyUser): void
    {
        
    }

    /**
     * Handle the CompanyUser "force deleted" event.
     */
    public function forceDeleted(companyusers $companyUser): void
    {
        //
    }
}
