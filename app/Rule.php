<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
  public $table = 'auto_allocate';
  public $fillable = ['match','category_id','type','minValue','maxValue','percentage'];
  public $timestamps = false;

  public function getPatternAttribute()
  {
    return $this->match;
  }
  public function category()
  {
    return $this->belongsTo('App\Category');
  }
}
