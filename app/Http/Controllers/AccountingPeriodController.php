<?php

namespace App\Http\Controllers;

// use Charts;
use Carbon\Carbon;
use \App\Repositories\TransactionRepository;

class AccountingPeriodController extends Controller
{
    public function index($startDate, $endDate = null)
    {

        $startDate = new  Carbon($startDate);
        if (null == $endDate) {
            $endDate = (new  Carbon($startDate))->addMonth();
        } else {
            $endDate = new Carbon($endDate);
        }

        //Load all transactions in the period

        $transactions = TransactionRepository::byDate($startDate, $endDate);
        $categories = \App\Category::with(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString());
        }, 'budgets' => function ($query) {
            $query->where('id', 4);
        }])->get();

        $transactionsByDay = $transactions->groupBy(function ($item, $key) {
            return date('m-d', strtotime($item['date']));
        });

        $incomeTransactions = $transactions->filter(function ($item, $key) {
            return $item->value < 0;
        });
        $expenseTransactions = $transactions->filter(function ($item, $key) {
            return $item->value > 0;
        });

        // $chartSpendingByDay = Charts::create('line', 'google')
        //   ->title('Spending By Day')
        //   ->elementLabel('Total')
        //   ->dimensions(1000, 250)
        //   ->responsive(false)
        //   ->values($transactionsByDay->map(function ($chunk) {
        //       return $chunk->where('value', '>', 0)->sum('value');
        //   })->values()->all())
        //   ->labels($transactionsByDay->keys())
        // ;

        $categories->map(function ($y) {
            $y->expected = 0;
            if ($y->budgets->first()) {
                $y->expected = $y->budgets->first()->pivot->value;
            }
        });
        $categories->map(function ($y) {
            $y->actual = $y->transactions->sum('pivot.value');
        });

        $balance = $categories->map(function ($y) {
            return $y->expected - $y->actual;
        });

        // $categoryBalance = Charts::create('bar', 'google')
        //   ->title('Category Balance')
        //   ->elementLabel('Balance')
        //   ->dimensions(1000, 250)
        //   ->responsive(false)
        //   ->colors($categories->pluck('color')->values())
        //   ->values($balance->values())
        //   ->labels($categories->pluck('name')->values())
        // ;



        $spendPercentage = 100;
        $spendColor = '#00ff00';
        if ($incomeTransactions->sum('value') < 0) {
            $spendPercentage = $expenseTransactions->sum('value') / $incomeTransactions->sum('value') * -100;
        }

        // $spendPercentage = Charts::create('progressbar', 'progressbarjs')
        //    ->title(round($spendPercentage, 2).' Percent of Income Spent')
        //    ->elementLabel('')
        //    ->values([$spendPercentage, 0, 100])
        //    ->responsive(false)
        //    ->height(30)
        //    ->width(0);

           $expectedExpensePercentage=0;
        if ($categories->sum('expected') > 0) {
            $expectedExpensePercentage = $expenseTransactions->sum('value') / $categories->sum('expected') * 100;
        }
        // $expectedExpensePercentage = Charts::create('progressbar', 'progressbarjs')
        //       ->title(round($expectedExpensePercentage, 2).' Percent of Budget Spent')
        //       ->elementLabel('')
        //       ->values([$expectedExpensePercentage, 0, 100])
        //       ->responsive(false)
        //       ->height(30)
        //       ->width(0);

        $expectedIncomePercentage = 0;//$incomeTransactions->sum('value') / $categories->first()->budgets->first()->monthly_income * -100;
        // $expectedIncomePercentage = Charts::create('progressbar', 'progressbarjs')
        //             ->title(round($expectedIncomePercentage, 2).' Percent of Income Received')
        //             ->elementLabel('')
        //             ->values([$expectedIncomePercentage, 0, 100])
        //             ->responsive(false)
        //             ->height(30)
        //             ->width(0);

        return view('pages.accounting_periods.index', [
          'transactions' => $transactions,
          'startDate' => $startDate,
          'charts' => [
           // 'spendingByDay' => $chartSpendingByDay,
           // 'categoryBalance' => $categoryBalance,
        //    'categoryBreakdown' => $categoryBreakdown,
            'spendPercentage' => $spendPercentage,
            'expectedExpensePercentage' => $expectedExpensePercentage,
            'expectedIncomePercentage' => $expectedIncomePercentage,
          ],
        ]);
    }
}
