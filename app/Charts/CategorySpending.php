<?php

namespace App\Charts;

use Carbon\Carbon;

class CategorySpending extends BaseChart
{
    public function __construct($categoryID, $library = 'google', $since = '2014-02-01', $until = null)
    {
        parent::__construct('line', $library);
        $transactions = \App\Transaction::whereHas('categories', function ($query) use ($categoryID) {
            $query->where('category_id', $categoryID);
        })->where('date', '>', $since)->where('date', '<', new Carbon($until))->orderBy('date')->orderBy('value')->with('categories')->get();
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

        $this
      ->title('Monthly Cashflow')
      ->dimensions(1250, 500)
      ->responsive(false)
      ->dataset('actual', $expenses->values()->all())
      ->dataset('Average (3mo.)', $this->movingAverage($expenses->values()->all()),3)
      ->colors(['#FBE1C8', '#C7D5E3'])
      ->labels($transactionsByMonth->keys())
    ;
    }
}
