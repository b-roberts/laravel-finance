<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    public function categories()
    {
        return $this->hasMany('App\Category');
    }
}
