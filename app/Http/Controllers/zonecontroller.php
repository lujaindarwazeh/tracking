<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\zones;
use App\Http\Requests\zonerequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\zoneresource;


class zonecontroller extends Controller
{
    //

    public function store(zonerequest $request)
    {

        $zone = new zones();
        $zone->name = $request->name;
        $zone->radius = $request->radius;
        $zone->company_id = $request->company_id;
        $zone->description = $request->description;
        $zone->save();

        DB::statement("
        UPDATE zone 
        SET center_coordinates = ST_GeomFromText('POINT({$request->longitude} {$request->latitude})')
        WHERE id = {$zone->id}
        ");
        
        return new zoneresource($zone);


        //
    }
}
