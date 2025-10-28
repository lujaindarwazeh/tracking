<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class zoneresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

         $coords = DB::table('zone')
                ->selectRaw('ST_X(center_coordinates) as longitude, ST_Y(center_coordinates) as latitude')
                ->where('id', $this->id)
                ->first();





        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "radius" => $this->radius,
            "center_coordinates" => [
                "latitude" => $coords->latitude,
                "longitude" => $coords->longitude,
                ],
            
            "company_id" => $this->company_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
