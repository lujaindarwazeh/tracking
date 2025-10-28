<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class zones extends Model
{
    //
    protected $table = 'zone';
    protected $fillable = ['name', 'company_id','radius','center_coordinates'];
    
    public function company()
    {
        return $this->belongsTo(company::class, 'company_id');
    }


}
