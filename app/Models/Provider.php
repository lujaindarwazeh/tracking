<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    //
     use HasFactory;

    protected $table = 'provider';

    protected $fillable = [
        'device_id', 'providername'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

}

