<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class event extends Model
{
    //
    protected $table = 'event';
    protected $fillable = ['name'];


    public function companies()
    {
        return $this->belongsToMany(company::class, 'companyevent', 'event_id', 'company_id');
    }

    public function users(){
        return $this->belongsToMany(companyusers::class, 'userevent', 'event_id', 'user_id');
    }











}
