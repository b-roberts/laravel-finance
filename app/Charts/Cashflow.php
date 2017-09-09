<?php
namespace App\Charts;

use \ConsoleTVs\Charts\Builder;

class Cashflow extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values);
            //Fill in array with nulls to keep length the same
            $i=0;
            while (!isset($averages[$i]) && $i < sizeof($averages)) {
                $averages[$i++] =null;
            }
            return $averages;
        } else {
            $sma=[];
            $position = 0;
            while (empty($values[ $position ])) {
                $position++;
            }
            $i = $position;
            while (true) {
                if (empty($values[ $i + $range - 1 ])) {
                    break;
                }
                $temp_sum = 0;
                for ($j = $i; $j < $i + $range; $j++) {
                    $temp_sum += $values[ $j ];
                }
                $sma[ $i + $range - 1 ] = $temp_sum / $range;
                $i++;
            }
            return $sma;
        }
    }
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $transactionsByMonth=$transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });

        $averageExpenseData = $this->movingAverage($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '>', 0)->sum('value');
        })->values()->all());

        $averageIncomeData = $this->movingAverage($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '<', 0)->sum('value')*-1;
        })->values()->all());

        $this
  ->title('Monthly Cashflow')
  ->dimensions(1000, 250)
  ->responsive(false)
  ->dataset('Expense', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '>', 0)->sum('value');
  })->values()->all())
  ->dataset('Income', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '<', 0)->sum('value') *-1;
  })->values()->all())
  ->dataset('averageExpense', $averageExpenseData)
  ->dataset('averageIncome', $averageIncomeData)

  ->colors(['#FBE1C8','#C7D5E3','#CC444B','#4CB963'])
  ->labels($transactionsByMonth->keys())
;
    }
}
