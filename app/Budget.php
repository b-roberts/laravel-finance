<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
protected $table='budget';
    //
    
        public function categories()
    {
        return $this->belongsToMany('App\Category','budget_category','budget_id','category_id')->withPivot('value');
    }
    
  public function toDataTableArray()
	{
		$return = array();
		$return[]=array('Category','Value');
		foreach($this->categories as $category)
		{
			$return[] = array($category->name,$category->pivot->value);
		}
		return json_encode($return, JSON_NUMERIC_CHECK);
	}
}
