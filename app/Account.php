<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $hidden = ['account_number'];
    public $fillable = ['name', 'description', 'account_number'];
    public $timestamps = false;
    public $attributes = ['account_number' => 0];


    public function balances()
    {
        return $this->hasMany('App\AccountBalance');
    }

    public function getBalance()
    {
        $balance = $this->balances()->orderBy('date', 'desc')->first();
        if (!$balance) {
            return 0;
        }
        return $balance->value;
    }

}
