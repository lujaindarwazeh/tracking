<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    //
    use HasFactory;
    protected $table = 'device';
     protected $fillable = [
        'vehicle_id', 'devicename'
    ];

       public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'device_id');
    }


}
