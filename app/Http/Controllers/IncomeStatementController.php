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

        $incomeTransactions = $transactions->filter(function ($item, $key) {
            return $item->value < 0;
        });

        $designations = \App\Designation::all();
        $categoryCharts = [];
        foreach ($designations as $designation) {
            $designation->categories = $designation->categories()->with(['transactions' => function ($query) use ($startDate,$endDate) {
                $query->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString());
            }, 'budgets' => function ($query) {
                $query->where('id', 5);
            }])->get();

            $designation->categories->map(function ($y) use ($previousStart,$previousEnd){
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
                if ($y->previous == $y->actual){
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
        ]);
    }
}
