<?php

namespace App\Http\Controllers;

use Charts;
use Carbon\Carbon;
use \App\Repositories\TransactionRepository;

class IncomeStatementController extends Controller
{
    public function index($startDate, $endDate = null)
    {
        $startDate = new  Carbon($startDate);
        $startDate->day=1;
        if (null == $endDate) {
            $endDate = (new  Carbon($startDate))->addMonth();
        } else {
            $endDate = new Carbon($endDate);
        }

        $previousStart = (new Carbon($startDate))->subMonth();
        $previousEnd = new Carbon($startDate);

        //Load all transactions in the period
        $transactions=TransactionRepository::paymentsByDate($startDate, $endDate);

        $notes = \App\Note::where('related_type', 'period')->where('related_id', $startDate->format('Y-m-d'))->get();

        $incomeTransactions = $transactions->filter(function ($item, $key) {
            return $item->value < 0;
        });

        $designations = \App\Designation::all();
        $categoryCharts = [];
        foreach ($designations as $designation) {
            $designation->categories = $designation->categories()->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString());
            }, 'budgets' => function ($query) {
                $query->where('id', 5);
            }])->get();

            $designation->categories->map(function ($y) use ($previousStart, $previousEnd) {
                    $expenseTransactions = $y->transactions->filter(function ($item, $key) {
                        return $item->value > 0;
                    });
                $y->actual = $expenseTransactions->sum('pivot.value');



                $previousTransactions = $y->transactions()->where('date', '>', $previousStart->toDateString())->where('date', '<', $previousEnd->toDateString())->sum('transaction_detail.value');
                $y->previous=$previousTransactions;


                $y->changeIcon = ($y->previous > $y->actual)  ? 'fas fa-chevron-down text-success' : 'fas fa-chevron-up text-danger';
                if ($y->actual > 2* $y->previous) {
                    $y->changeIcon = 'fas fa-angle-double-up text-danger';
                }
                if ($y->previous == $y->actual) {
                    $y->changeIcon = '';
                }
            });

            $chartSince = (new  Carbon($startDate))->subMonths(6);
            // foreach ($designation->categories->where('actual', '>', 0) as $category) {
            //     $id = $category->id;
            //     $categoryCharts[$id] = new \App\Charts\CategorySpending($id, 'chartjs', $chartSince, $endDate);
            //     $categoryCharts[$id]->view = 'charts.simpleline';
            //     unset($categoryCharts[$id]->datasets[1]);
            //     $categoryCharts[$id]->colors([$category->color]);
            // }
        }

        $unallocatedTransactions = TransactionRepository::unallocatedByDate($startDate, $endDate);

        // $designationChart = new \App\Charts\DesignationPercentage('google',$startDate,$endDate);
        $designationChart=null;

        return view('pages.income_statement', [
          'designationChart'=>$designationChart,
          'transactions' => $transactions,
          'incomeTransactions' => $incomeTransactions,
          'unallocatedTransactions'=>$unallocatedTransactions,
          'designations' => $designations,
          'startDate' => $startDate,
          'charts' => $categoryCharts,
          'notes' => $notes,
        ]);
    }

    public function balance($startDate, $endDate = null)
    {
        \DB::connection()->getPdo()->query('SET SQL_MODE=""');
        $startDate = new  Carbon($startDate);
        $startDate->day=1;
        if (null == $endDate) {
            $endDate = (new  Carbon($startDate))->addYear();
        } else {
            $endDate = new Carbon($endDate);
        }
        $designations = \App\Designation::all();
        $accounts = \App\Account::all();
        $expenses = collect();
        $incomes = collect();
        for ($i=0; $i < 12; $i++) {
            $periodStart = $startDate->copy()->addMonth($i);
            $periodEnd = $periodStart->copy()->addMonth();
            $notes[$i] = \App\Note::where('related_type', 'period')->where('related_id', $periodStart->format('Y-m-d'))->get();
            $expenses[$i] = collect();
            //Load all transactions in the period
            $incomes[$i] = \App\Transaction::with('categories')
        
                ->where('date', '>=', $periodStart->toDateString())
                ->where('date', '<', $periodEnd->toDateString())
                ->where('value', '<', 0)
                ->where('type', 'payment')
                ->groupBy('account_id')
                ->selectRaw('account_id, sum(value) total')
                ->pluck('total', 'account_id');
        
            foreach ($designations as $designation) {
                $expenses[$i][$designation->id] = $designation->categories()
                ->with(['transactions' => function ($query) use ($periodStart, $periodEnd) {
                    $query->where('date', '>', $periodStart->toDateString())
                        ->where('date', '<', $periodEnd->toDateString());
                }])->get()->map(function ($y) {
                    $expenseTransactions = $y->transactions
                    ->filter(function ($item, $key) {
                        return $item->value > 0;
                    });
                    return $expenseTransactions->sum('pivot.value');
                })->sum();
            }

            $expenses[$i][99] = \App\Transaction::with('categories')
            ->with('account')
            ->where('date', '>=', $periodStart->toDateString())
            ->where('date', '<=', $periodEnd->toDateString())
            ->where('type', 'payment')
            ->where('value', '>', 0)
            ->orderBy('date')
            ->orderBy('value')
            ->doesntHave('categories')
            ->sum('value');
        }
        return view('pages.balance', [
        'startDate' => $startDate,
        'designations' => $designations,
        'accounts' => $accounts,
        'expenses' => $expenses,
        'incomes' => $incomes,
        'notes' => $notes,
        ]);


        

        dd($months);
    }
}
