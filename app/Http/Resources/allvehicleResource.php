<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class allvehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return [
            'id' => $this->id,
            'namecar' => $this->namecar,
            'serialnumber' => $this->serialnumber,
            'lastupdatetime' => $this->lastupdatetime,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'odometer' => $this->odometer,
            'drivername' => $this->drivername,

            // Include all devices for this vehicle
            'devices' => $this->devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'devicename' => $device->devicename,
                    'providers' => $device->providers->map(function ($provider) {
                        return [
                            'id' => $provider->id,
                            'providername' => $provider->providername,
                        ];
                    }),
                ];
            }),
        ];
    }
}
