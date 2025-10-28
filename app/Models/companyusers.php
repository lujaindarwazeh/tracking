<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class companyusers extends Model
{
    //
    protected $table = 'companyusers';
    protected $fillable = ['first_name', 'last_name', 'email', 'password',
        'role', 'company_id','location'];

    public function company()
    {
        return $this->belongsTo(company::class, 'company_id');
    }

    public function events(){
        return $this->belongsToMany(event::class, 'userevent', 'user_id', 'event_id');
    }

    


}
