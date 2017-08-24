<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'categories';

    public function getColorAttribute($value)
    {
      return str_pad(dechex($value),6,'0',STR_PAD_LEFT);
    }
}
