<?php

namespace App\Charts;

class Forecast extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $recentTransactions = $transactions->filter(function($item){
          return !$item->exceptional;
        })->filter(function ($item) {
            return $item->date > (new \Carbon\Carbon)->subYears(3);
        });
        $recentTransactions = $recentTransactions->filter(function ($item) {
            $dt = \Carbon\Carbon::now();
            $dt->day=0;

            return $item->date < $dt;
        });

        $transactionsByMonth = $recentTransactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });


        $expenses = $transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '>', 0)->sum('value');
        })->values()->all();
        $income = $transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '<', 0)->sum('value')*-1;
        })->values()->all();
        $out = trader_kama(trader_tsf($expenses));

        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });


                $expenses = $transactionsByMonth->map(function ($chunk) {
                    return $chunk->where('value', '>', 0)->sum('value');
                })->values()->all();
                $income = $transactionsByMonth->map(function ($chunk) {
                    return $chunk->where('value', '<', 0)->sum('value')*-1;
                })->values()->all();



        $expenses = array_merge($expenses, $out);
        //$income = array_merge($income, $incomeForecast);


        $averageExpenseData = $this->movingAverage($expenses);
$averageIncomeData = $this->movingAverage($income);

for ($i=sizeof($averageIncomeData);$i< sizeof($averageExpenseData); $i++){
  $averageIncomeData[$i]=0;
}



        $this
  ->title('Monthly Cashflow')
  ->dimensions(1250, 750)
  ->responsive(false)
  ->dataset('Expense', $expenses)
  ->dataset('averageExpense', $averageExpenseData)
  //->dataset('Expense2', $expenseForecast)
  ->dataset('Income2', $averageIncomeData)


  ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963'])
  ->labels(array_keys($expenses))
;
    }
}
