<?php
namespace App\Http\Controllers;
use App\Models\Device;
use App\Http\Requests\StoreDevice;
use App\Http\Resources\StoreDeviceResource;
use App\Http\Requests\updateDevice;

class DeviceController extends Controller
{
    public function store(StoreDevice $request)
    {
        $device = new Device();
        $device->fill($request->all());
        $device->save();

        return new StoreDeviceResource($device);
    }

    public function index(){
        $devices = Device::all();
        return StoreDeviceResource::collection($devices);
    }


    public function show($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        return new StoreDeviceResource($device);
    }

    public function destroy($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        $device->delete();
        return response()->json(['message' => 'Device deleted successfully'], 200);
    }


    public function update(updateDevice $request, $id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        $device->fill($request->all());
        $device->save();
        return new StoreDeviceResource($device);
    }






}




?>