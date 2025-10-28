<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class way extends Model
{
    protected $fillable = ['name'];

    public function points()
    {
        return $this->hasMany(point::class);
    }

    public function polygon()
    {
        return $this->hasOne(poly::class);
    }

}
