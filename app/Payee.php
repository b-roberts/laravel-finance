<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
  public $table = 'payees';

  public function getPatternAttribute()
  {
    return $this->match;
  }
  public function transactions()
  {
      return $this->hasMany('App\Transaction');
  }
}
