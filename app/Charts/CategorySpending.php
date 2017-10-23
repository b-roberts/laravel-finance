<?php

namespace App\Charts;

use Carbon\Carbon;

class CategorySpending extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values, 2);
            //Fill in array with nulls to keep length the same
            $i = 0;
            while (!isset($averages[$i]) && $i < sizeof($averages)) {
                $averages[$i++] = null;
            }

            return $averages;
        } else {
            $sma = [];
            $position = 0;
            while (empty($values[$position])) {
                ++$position;
            }
            $i = $position;
            while (true) {
                if (empty($values[$i + $range - 1])) {
                    break;
                }
                $temp_sum = 0;
                for ($j = $i; $j < $i + $range; ++$j) {
                    $temp_sum += $values[$j];
                }
                $sma[$i + $range - 1] = $temp_sum / $range;
                ++$i;
            }

            return $sma;
        }
    }

    public function __construct($categoryID, $library = 'google', $since = '2014-02-01', $until = null)
    {
        parent::__construct('line', $library);
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

        $this
      ->title('Monthly Cashflow')
      ->dimensions(1000, 250)
      ->responsive(false)
->dataset('actual', $expenses->values()->all())
      ->dataset('averageExpense', $this->movingAverage($expenses->values()->all()))
      ->colors(['#FBE1C8', '#C7D5E3'])
      ->labels($transactionsByMonth->keys())
    ;
    }
}
