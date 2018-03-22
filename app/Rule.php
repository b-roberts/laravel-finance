<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
  public $table = 'auto_allocate';

  public function getPatternAttribute()
  {
    return $this->match;
  }
}
