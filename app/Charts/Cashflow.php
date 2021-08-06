<?php

namespace App\Charts;

class Cashflow extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $this->view='charts.cashflow';

$transactions = $transactions->filter(function($item){
  return date('m-y', strtotime($item['date'])) != date('m-y');
});


        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });



        $nonExceptionalTransactionsByMonth = $transactions->filter(function($item){
          return !$item->exceptional;
        })->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });

$expense = $nonExceptionalTransactionsByMonth->map(function ($chunk) {
    return $chunk->where('value', '>', 0)->sum('value');
})->values()->all();
$income = $nonExceptionalTransactionsByMonth->map(function ($chunk) {
    return $chunk->where('value', '<', 0)->sum('value') * -1;
})->values()->all();

        $averageExpenseData = $this->movingAverage($expense, 3);
        $averageIncomeData = $this->movingAverage($income, 3);

        $this
  ->title('Monthly Cashflow')
  ->dimensions(1250, 500)
  ->responsive(false)
  ->dataset('Expense', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '>', 0)->sum('value');
  })->values()->all())
  ->dataset('Income', $transactionsByMonth->map(function ($chunk) {
      return $chunk->where('value', '<', 0)->sum('value') * -1;
  })->values()->all())
  ->dataset('averageExpense (3mo.)', $averageExpenseData)
  ->dataset('averageIncome (3mo.)', $averageIncomeData)
  ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963','#B94CA3'])
  ->labels($transactionsByMonth->keys())
;
    }
}
