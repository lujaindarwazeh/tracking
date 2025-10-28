<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class poly extends Model
{
    //

    protected $fillable=["way_id","area"];

    public function way()
    {
        return $this->belongsTo(way::class);
    }

    
}
