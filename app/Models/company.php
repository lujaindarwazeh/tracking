<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class company extends Model
{
    //

    use SoftDeletes;
  
    protected $table = 'company';
    protected $fillable = ['name', 'email', 'phone_number'];


    public function users()
    {
        return $this->hasMany(companyusers::class, 'company_id');
    }

    public function zones()
    {
        return $this->hasMany(zones::class, 'company_id');
    }

    public function events()
    {
        return $this->belongsToMany(event::class, 'companyevent', 'company_id', 'event_id');
    }

    


}
