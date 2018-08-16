<?php

namespace App\Charts;

use Carbon\Carbon;

class DesignationPercentage extends BaseChart
{
    public function __construct( $library = 'google', $since = '2014-02-01', $until = null)
    {
        parent::__construct('line', $library);

        $designations = \App\Designation::all();

        foreach ($designations as $designation) {
          foreach($designation->categories as $category)
          {
            $expenses = $this->getCategoryDataset($category->id);

                  $this->dataset($category->name, array($this->movingAverage($expenses->values()->all())));
                  break;
          }
        }

        $this
      ->title('Monthly Cashflow')
      ->dimensions(1250, 500)
      ->responsive(false)

    ;
    dump($this);
    }


    private function getCategoryDataset($categoryID){
      $since = '2014-02-01'; $until = null;
      $transactions = \App\Transaction::whereHas('categories', function ($query) use ($categoryID) {
          $query->where('category_id', $categoryID);
      })->where('date', '>', $since)->where('date', '<', new Carbon($until))->orderBy('date')->orderBy('value')->get();
      $transactions = $transactions->map(function ($transaction) use ($categoryID) {
          return $transaction->categories->where('id', $categoryID)->first()->pivot;
      });

      $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
          return date('Y-m-01', strtotime($item->file_date));
      });

      $fillerDate = new Carbon($since);
      $now = new Carbon($until);
      while ($fillerDate < $now) {
          $transactionsByMonth = $transactionsByMonth->union(([$fillerDate->format('Y-m-01') => null]));
          $fillerDate->addMonth();
      }

      $transactionsByMonth = $transactionsByMonth->sortBy(function ($item, $key) {
          return strtotime($key);
      });

      $expenses = $transactionsByMonth->map(function ($chunk) {
          if ($chunk) {
              return $chunk->where('value', '>', 0)->sum('value');
          }
      });
      return $expenses;
    }
}
