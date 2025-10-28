<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    //
    use HasFactory;
    protected $table = 'vehicle';

     protected $fillable = [
        'namecar', 'serialnumber', 'lastupdatetime',
        'longitude', 'latitude', 'odometer', 'drivername'
    ];

    public function devices()
    {
        return $this->hasMany(Device::class, 'vehicle_id');
    }
}
