<?php

namespace App\Http\Controllers;

use Charts;
use Carbon\Carbon;

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

        //Load all transactions in the period
        $transactions = \App\Transaction::with('categories')->
        with('account')->
        where('date', '>=', $startDate->toDateString())->
        where('date', '<=', $endDate->toDateString())->
        where('type', 'payment')->
        orderBy('date')->
        orderBy('value')->
        get();
        $incomeTransactions = $transactions->filter(function ($item, $key) {
            return $item->value < 0;
        });

        $designations = \App\Designation::all();
        $categoryCharts = [];
        foreach ($designations as $designation) {
            $designation->categories = $designation->categories()->with(['transactions' => function ($query) use ($startDate,$endDate) {
                $query->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString());
            }, 'budgets' => function ($query) {
                $query->where('id', 3);
            }])->get();

            $designation->categories->map(function ($y) {
                    $expenseTransactions = $y->transactions->filter(function ($item, $key) {
            return $item->value > 0;
        });
                $y->actual = $expenseTransactions->sum('pivot.value');
            });

            $chartSince = (new  Carbon($startDate))->subMonths(6);
            foreach ($designation->categories->where('actual', '>', 0) as $category) {
                $id = $category->id;
                $categoryCharts[$id] = new \App\Charts\CategorySpending($id, 'chartjs', $chartSince, $endDate);
                $categoryCharts[$id]->view = 'charts.simpleline';
                unset($categoryCharts[$id]->datasets[1]);
                $categoryCharts[$id]->colors([$category->color]);
            }
        }

        $unallocatedTransactions = \App\Transaction::with('categories')->
        with('account')->
        where('date', '>', $startDate->toDateString())->
        where('date', '<', $endDate->toDateString())->
        where('type', 'payment')->
        where('value','>',0)->
        orderBy('date')->
        orderBy('value')->
        doesntHave('categories')->
        get();

        return view('pages.income_statement', [
          'transactions' => $transactions,
          'incomeTransactions' => $incomeTransactions,
          'unallocatedTransactions'=>$unallocatedTransactions,
          'designations' => $designations,
          'startDate' => $startDate,
          'charts' => $categoryCharts,
        ]);
    }
}
