<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    public $timestamps = false;
    protected $appends = ['icon'];


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
        return $this->morphOne(Note::class, 'related');
    }
    public function payee()
    {
        return $this->belongsTo('App\Payee');
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
        switch ($this->attributes['allocation_type']) {
            case '0':
                return 'manual';
            case '1':
                return 'regex';
            case '2':
                return 'Learned';
        }
    }

    public function getIconAttribute()
    {
        if ($this->type=='transfer') {
            return 'fa-random text-success';
        }
        if ($this->value < 0) {
            return 'fa-hand-holding-usd text-success';
        }
        if ($this->categories->count()==0) {
            return 'fa-question text-danger';
        }
        switch ($this->attributes['allocation_type']) {
            case '0':
                return 'fa-user';
            case '1':
                return 'fa-shapes text-warning';
            case '2':
                return 'fa-magic';
        }
    }
}
