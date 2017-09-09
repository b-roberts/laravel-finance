<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'categories';

    public function getColorAttribute($value)
    {
        return '#'.str_pad(dechex($value), 6, '0', STR_PAD_LEFT);
    }
    public function getAltColorAttribute()
    {
        $hexcolor=substr($this->color, 1);
        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));
        $yiq = (($r*299)+($g*587)+($b*114))/1000;
        return ($yiq >= 128) ? '#000000' : '#ffffff';
    }

    public function transactions()
    {
        return $this->belongsToMany('App\Transaction', 'transaction_detail')->withPivot('value', 'file_date');
    }
    public function budgets()
    {
        return $this->belongsToMany('App\Budget', 'budget_category', 'category_id', 'budget_id')->withPivot('value');
    }
}
