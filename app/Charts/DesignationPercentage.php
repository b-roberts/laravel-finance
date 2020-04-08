<?php

namespace App\Charts;

use Carbon\Carbon;
use App\Repositories\TransactionRepository;

class DesignationPercentage extends \ConsoleTVs\Charts\Builder\Chart
{
    public function __construct( $library = 'google', $startDate = null, $endDate = null)
    {
        parent::__construct('pie', 'google');

        $categories = \App\Category::with(['designation'])->with(['transactions'=>function($query) use ($startDate, $endDate){
          $query->      where('date', '>=', $startDate->toDateString())->
                where('date', '<=', $endDate->toDateString())->
                where('type', 'payment');
        }])->get();

$categories->map(function($cat){
  $cat->total = $cat->transactions->sum('pivot.value');
});
$cbyd = $categories->groupBy('designation_id')->map(function($collection){
  $designation = $collection->first()->designation;
  $designation->value = $collection->sum('total');
  return $designation;
});
$values = $cbyd->pluck('value');
$labels = $cbyd->pluck('name');

$unallocatedTransactions=TransactionRepository::unallocatedByDate($startDate,$endDate);
$labels->push('Unallocated');
$values->push($unallocatedTransactions->sum('value'));

        $this
      ->title('Monthly Cashflow')
      ->dimensions(1250, 500)
      ->responsive(false)
      ->values($values->values())
      ->labels($labels->values())
    ;




    }

}
