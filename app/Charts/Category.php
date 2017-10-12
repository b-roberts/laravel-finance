<?php

namespace App\Charts;

class Category extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values);
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

    public function __construct($transactions, $categoryID)
    {
        parent::__construct('line', 'google');
        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });

        $categoryTransactions = $transactionsByMonth->map(function ($chunk) use ($categoryID) {
            $total = 0;
            foreach ($chunk as $transaction) {
                $total += $transaction->categories->where('pivot.category_id', $categoryID)->sum('pivot.value');
            }

            return $total;
        })->values()->all();

        $averageExpenseData = $this->movingAverage($categoryTransactions);

        $this
  ->title('Monthly Cashflow')
  ->dimensions(1000, 250)
  ->responsive(false)
  ->dataset('Expense', $categoryTransactions)
  ->dataset('Average Expense', $averageExpenseData)

  ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963'])
  ->labels($transactionsByMonth->keys())
;
    }
}
