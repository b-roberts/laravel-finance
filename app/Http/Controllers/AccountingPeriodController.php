<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Charts;
use \Carbon\Carbon;

class AccountingPeriodController extends Controller
{
    //

    public function cashflow()
    {

        //Load all transactions in the period
        $transactions = \App\Transaction::orderBy('date')->orderBy('value')->get();
        $transactionsByMonth=$transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });












        $averageExpenseData = trader_kama($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '>', 0)->sum('value');
        })->values()->all());
        $i=0;
        while (!isset($averageExpenseData[$i])) {
            $averageExpenseData[$i++] =null;
        }

        $averageIncomeData = trader_kama($transactionsByMonth->map(function ($chunk) {
            return $chunk->where('value', '<', 0)->sum('value')*-1;
        })->values()->all());
        $i=0;
        while (!isset($averageIncomeData[$i])) {
            $averageIncomeData[$i++] =null;
        }

        $chartSpendingByDay = Charts::multi('line', 'google')
          ->title('Spending By Day')

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



        $netData=$transactionsByMonth->map(function ($chunk) {
            return $chunk->sum('value')*-1;
        })->values()->all();
        $averageNetData = trader_kama($netData);
        $i=0;
        while (!isset($averageNetData[$i])) {
            $averageNetData[$i++] =null;
        }

        $chartSpendingByDay2 = Charts::multi('line', 'google')
                  ->title('Spending By Day')

                  ->dimensions(1000, 250)
                  ->responsive(false)
                  ->dataset('averageExpense', $netData)
                  ->dataset('averageIncome', $averageNetData)

                  ->colors(['#FBE1C8','#C7D5E3','#CC444B','#4CB963'])
                  ->labels($transactionsByMonth->keys())
                ;

        return view('pages.accounting_periods.cashflow', [
                          'charts'=>[
                            'cashflow'=>$chartSpendingByDay,
                            'cashflow2'=>$chartSpendingByDay2,


                          ]
                        ]);
    }

    public function index($startDate, $endDate=null)
    {
        $startDate = new  Carbon($startDate);
        if ($endDate == null) {
            $endDate = (new  Carbon($startDate))->addMonth();
        } else {
            $endDate = new Carbon($endDate);
        }

        //Load all transactions in the period
        $transactions = \App\Transaction::where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString())->orderBy('date')->orderBy('value')->get();

        $categories = \App\Category::with(['transactions'=>function ($query) use ($startDate,$endDate) {
            $query->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString());
        },'budgets'=>function ($query) {
            $query->where('id', 3);
        }])->get();



        $transactionsByDay=$transactions->groupBy(function ($item, $key) {
            return date('m-d', strtotime($item['date']));
        });

        $incomeTransactions=$transactions->filter(function ($item, $key) {
            return $item->value < 0;
        });
        $expenseTransactions=$transactions->filter(function ($item, $key) {
            return $item->value > 0;
        });



        $chartSpendingByDay = Charts::create('line', 'google')
          ->title('Spending By Day')
          ->elementLabel("Total")
          ->dimensions(1000, 250)
          ->responsive(false)
          ->values($transactionsByDay->map(function ($chunk) {
              return $chunk->where('value', '>', 0)->sum('value');
          })->values()->all())
          ->labels($transactionsByDay->keys())
        ;


        $categories->map(function ($y) {
            $y->expected = 0;
            if ($y->budgets->first()) {
                $y->expected = $y->budgets->first()->pivot->value ;
            }
        });
        $categories->map(function ($y) {
            $y->actual = $y->transactions->sum('pivot.value');
        });

        $balance = $categories->map(function ($y) {
            return $y->expected - $y->actual;
        });



        $categoryBalance = Charts::create('bar', 'google')
          ->title('Category Balance')
          ->elementLabel("Balance")
          ->dimensions(1000, 250)
          ->responsive(false)
          ->colors($categories->pluck('color')->values())
          ->values($balance->values())
          ->labels($categories->pluck('name')->values())
        ;


        $categoryBreakdown = Charts::create('pie', 'google')
          ->title('Category Breakdown')
          ->elementLabel("Category")

          ->responsive(false)
          ->colors($categories->pluck('color')->values())
          ->values($categories->map(function ($c) {
              return $c->actual >  0 ? $c->actual : 0;
          }))
          ->labels($categories->pluck('name')->values())
        ;


        $spendPercentage = 100;
        $spendColor = '#00ff00';
        if ($incomeTransactions->sum('value') < 0) {
            $spendPercentage=$expenseTransactions->sum('value')/$incomeTransactions->sum('value')*-100;
        }

        $spendPercentage = Charts::create('progressbar', 'progressbarjs')
           ->title(round($spendPercentage, 2) .' Percent of Income Spent')
           ->elementLabel('')
           ->values([$spendPercentage,0,100])
           ->responsive(false)
           ->height(30)
           ->width(0);

        $expectedExpensePercentage=$expenseTransactions->sum('value')/$categories->sum('expected')*100;
        $expectedExpensePercentage = Charts::create('progressbar', 'progressbarjs')
              ->title(round($expectedExpensePercentage, 2) .' Percent of Budget Spent')
              ->elementLabel('')
              ->values([$expectedExpensePercentage,0,100])
              ->responsive(false)
              ->height(30)
              ->width(0);

        $expectedIncomePercentage=$incomeTransactions->sum('value')/$categories->first()->budgets->first()->monthly_income*-100;
        $expectedIncomePercentage = Charts::create('progressbar', 'progressbarjs')
                    ->title(round($expectedIncomePercentage, 2) .' Percent of Income Received')
                    ->elementLabel('')
                    ->values([$expectedIncomePercentage,0,100])
                    ->responsive(false)
                    ->height(30)
                    ->width(0);



        return view('pages.accounting_periods.index', [
          'transactions' => $transactions,
          'startDate'=>$startDate,
          'charts'=>[
            'spendingByDay'=>$chartSpendingByDay,
            'categoryBalance'=>$categoryBalance,
            'categoryBreakdown'=>$categoryBreakdown,
            'spendPercentage'=>$spendPercentage,
            'expectedExpensePercentage'=>$expectedExpensePercentage,
            'expectedIncomePercentage'=>$expectedIncomePercentage,

          ]
        ]);
    }
}
