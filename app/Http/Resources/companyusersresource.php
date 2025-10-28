<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class companyusersresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         $coords = DB::table('companyusers')
                ->selectRaw('ST_X(location) as longitude, ST_Y(location) as latitude')
                ->where('id', $this->id)
                ->first();


        return[
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "role" => $this->role,
            "company_id" => $this->company_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "location" => [
            "latitude" => $coords->latitude,
            "longitude" => $coords->longitude,
             ],
            "created_by" => $this->created_by,
            "recipient_id" => $this->recipient_id,
            "speed" => $this->speed,
            
        ];
    }
}
