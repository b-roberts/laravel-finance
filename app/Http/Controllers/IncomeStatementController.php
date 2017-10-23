<?php

namespace App\Http\Controllers;

use Charts;
use Carbon\Carbon;

class IncomeStatementController extends Controller
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
        $transactions = \App\Transaction::with('categories')->
        with('account')->
        where('date', '>', $startDate->toDateString())->
        where('date', '<', $endDate->toDateString())->
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
                $y->actual = $y->transactions->sum('pivot.value');
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

        return view('pages.income_statement', [
          'transactions' => $transactions,
          'incomeTransactions' => $incomeTransactions,
          'designations' => $designations,
          'startDate' => $startDate,
          'charts' => $categoryCharts,
        ]);
    }
}
