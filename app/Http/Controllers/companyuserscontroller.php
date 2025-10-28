<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\companyusers;
use App\Http\Requests\companyusersrequest;
use App\Http\Resources\companyusersresource;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log ;
use App\Observers\CompanyUserObserver;

class companyuserscontroller extends Controller
{
    //

    public function store(companyusersrequest $request)
    {
        $companyuser = new companyusers();
        $companyuser->password = bcrypt($request->password);
        $companyuser->role = $request->role;
        $companyuser->company_id = $request->company_id;
        $companyuser->first_name = $request->first_name;
        $companyuser->last_name = $request->last_name;
        $companyuser->email = $request->email;
        $companyuser->created_by = $request->created_by;
        $companyuser->speed = $request->speed;
        $companyuser->recipient_id = $request->recipient_id;
        
        $companyuser->save();
        
        DB::statement(
            "UPDATE companyusers 
                SET location = ST_GeomFromText('POINT({$request->longitude} {$request->latitude})') 
                WHERE id = {$companyuser->id}"
        );

 
        return new companyusersresource($companyuser);
    }

    public function updateSpeed(Request $request, $id)
    {
        $companyuser = companyusers::findOrFail($id);
        $companyuser->speed = $request->speed;
        $companyuser->save();

        return new companyusersresource($companyuser);
    }


 public function updateLocation(Request $request, $id)
{


    $companyuser = companyusers::findOrFail($id);
    Log::info("Updating location for User ID: {$id} to Latitude: {$request->latitude}, Longitude: {$request->longitude}");

    $companyuser->location = DB::raw("ST_GeomFromText('POINT({$request->longitude} {$request->latitude})')");

 
    $companyuser->save();

    return new companyusersresource($companyuser);
}


    public function show($id){

        $companyuser = companyusers::findOrFail($id);
        return new companyusersresource($companyuser);
    }
}
