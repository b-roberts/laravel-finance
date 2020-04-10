<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
  public $table = 'payees';
  public $fillable = ['name','regex'];
  public $timestamps = false;


  public function setRegexAttribute($value){
    $value = ltrim($value, "/");
    $value = rtrim($value, "/");
    $this->attributes['regex']=$value;
    return $value;
  }

  public function getPatternAttribute()
  {
    return $this->regex;
  }
  public function transactions()
  {
      return $this->hasMany('App\Transaction');
  }
}
