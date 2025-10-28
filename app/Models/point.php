<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class point extends Model
{

    
    protected $fillable=["way_id","location"];

    public function way()
    {
        return $this->belongsTo(way::class);
    }
    
}
