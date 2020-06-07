<?php

namespace App\Http\Controllers;

use Charts;
use \App\Repositories\TransactionRepository;


class CashflowController extends Controller
{
    public function cashflow()
    {
        //Load all transactions and group them by month
        $transactions =TransactionRepository::payments();

        $chartCashFlow = new \App\Charts\Cashflow($transactions);
        $chartForecast = new \App\Charts\Forecast($transactions);
        $chartNetIncome = new \App\Charts\NetIncome($transactions);
        $chartNetWorth = new \App\Charts\NetWorth();

        return view('pages.accounting_periods.cashflow', [
                              'charts' => [
                                'cashflow' => $chartCashFlow,
                                'netIncome' => $chartNetIncome,
                                'forecast' => $chartForecast,
                                'netWorth' => $chartNetWorth,
                              ],
                            ]);
    }
}
