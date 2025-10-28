<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorevehicleResource extends JsonResource
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
         

        ];
    }
}
