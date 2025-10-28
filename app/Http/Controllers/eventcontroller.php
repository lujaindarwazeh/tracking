<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\eventrequest;
use App\Http\Resources\eventresource;
use App\Models\event;
use App\Models\company;
use App\Models\companyusers;
use App\Mail\EventCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\addeventresource;
use App\Http\Requests\addeventrequest;
use App\Models\zones;

class eventcontroller extends Controller
{
    //

    public function store(eventrequest $request){

        $event = new Event();
        $event->name = $request->name;
        $event->save();


        // $company = company::find($event->company_id);
        // $creator = companyusers::find($event->created_by);

        // if ($company && $company->email) {
        //     Log::info('Sending event created email to: ' . $company->email);
        //     Mail::to($company->email)->send(new EventCreatedMail($event, $creator, $company));
        // }
        // else{
          
        //     Log::warning('Company or company email not found for event ID: ' . $event->id);
        // }
        
        return new eventresource($event);

    }


    public function addEventToCompany(addeventrequest $request)
    {

        $company = company::find($request->company_id);
        $event = event::find($request->event_id);

        if (!$company || !$event) {
            return response()->json(['error' => 'Company or Event not found'], 404);
        }

        if ($company->events()->where('event_id', $event->id)->exists()) {
            return response()->json(['message' => 'Event already associated with the company'], 200);
        }
        

        preg_match('/zone\s+([A-Z])/i', $event->name, $matches);
        $zoneName = $matches[1] ?? null;

        if ($zoneName) {
            
            $zoneExists = zones::where('company_id', $company->id)
                            ->where('name', $zoneName)
                            ->exists();

            if (!$zoneExists) {
                return response()->json([
                    'error' => "Company does not have a zone named '{$zoneName}'"
                ], 422);
            }
        }


        // Attach the event to the company
        $company->events()->attach($event->id);

        return response()->json([
        'message' => 'Event successfully linked to company.',
        'company_id' => $company->id,
        'event_id' => $event->id,
        ], 200);


        // Create a record in the companyevent pivot table
      




   
    }   

    

}
