<?php

namespace App\Http\Controllers;

use Charts;
use \App\Repositories\TransactionRepository;


class CashflowController extends Controller
{
    public function cashflow()
    {
      abort('501', 'Charts Not Implemented');
        //Load all transactions and group them by month
        $transactions =TransactionRepository::payments();

        $chartCashFlow = new \App\Charts\Cashflow($transactions);
        $chartForecast = new \App\Charts\Forecast($transactions);
        $chartNetIncome = new \App\Charts\NetIncome($transactions);
        $chartNetWorth = new \App\Charts\NetWorth();
        $chartAnnotation = new \App\Charts\Annotation();
        return view('pages.accounting_periods.cashflow', [
                              'charts' => [
                                'annotation'=>$chartAnnotation,
                                'cashflow' => $chartCashFlow,
                                'netIncome' => $chartNetIncome,
                                'forecast' => $chartForecast,
                                'netWorth' => $chartNetWorth,
                              ],
                            ]);
    }
}
