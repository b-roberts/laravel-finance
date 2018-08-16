<?php

namespace App\Charts;

class Cashflow extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });

        $averageExpenseData = $this->movingAverage($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '>', 0)->sum('value');
        })->values()->all());

        $averageIncomeData = $this->movingAverage($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '<', 0)->sum('value') * -1;
        })->values()->all());

        $this
  ->title('Monthly Cashflow')
  ->dimensions(1250, 750)
  ->responsive(false)
  ->dataset('Expense', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '>', 0)->sum('value');
  })->values()->all())
  ->dataset('Income', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '<', 0)->sum('value') * -1;
  })->values()->all())
  ->dataset('averageExpense', $averageExpenseData)
  ->dataset('averageIncome', $averageIncomeData)

  ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963'])
  ->labels($transactionsByMonth->keys())
;
    }
}
