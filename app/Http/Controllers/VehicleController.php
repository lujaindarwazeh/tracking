<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Storevehicle;
use App\Models\vehicle;
use App\Http\Resources\StorevehicleResource;
use App\Http\Resources\allvehicleResource;
use App\Http\Requests\updatevehicle;



class VehicleController extends Controller
{
    //
    public function store(Storevehicle $request)
    {
        $vehicle = new vehicle();
        $vehicle->fill($request->all());
        $vehicle->save();
        return new StorevehicleResource($vehicle);
        
        
    }


     public function show($id)
    {
        // $vehicle = Vehicle::find($id);
        $vehicle = Vehicle::with('devices.providers')->find($id);

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        return new allvehicleResource($vehicle);
    }


    public function index()
    {
        $vehicles = Vehicle::all();
        return allvehicleResource::collection($vehicles);
    }

    public function update(updatevehicle $request, $id)
    {
        $vehicle = vehicle::find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }
        $vehicle->fill($request->all());
        $vehicle->save();
        return new StorevehicleResource($vehicle);
    }


    public function destroy($id)
    {
        $vehicle = vehicle::find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted successfully'], 200);
    }


}
