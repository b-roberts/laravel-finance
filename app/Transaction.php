<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    public $timestamps = false;

    protected $dates = [
        'date'
    ];

    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category', 'transaction_detail')->withPivot('value', 'file_date');
    }

    public function note()
    {
        return $this->hasOne('App\Note');
    }

    public function toDataTableArray()
    {
        $return = array();
        $return[] = array('Category', 'Value');
        foreach ($this->categories as $category) {
            $return[] = array($category->name, $category->pivot->value);
        }

        return json_encode($return, JSON_NUMERIC_CHECK);
    }
    public function getAllocationTypeAttribute()
    {
      switch($this->attributes['allocation_type'])
      {
        case '0':return 'manual';
        case '1':return 'regex';
        case '2':return 'Learned';
      }
    }
}
