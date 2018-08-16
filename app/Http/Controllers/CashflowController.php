<?php

namespace App\Http\Controllers;

use Charts;

class CashflowController extends Controller
{
    public function cashflow()
    {
        //Load all transactions and group them by month
        $transactions = \App\Transaction::orderBy('date')->orderBy('value')->where('type','payment')->get();

        $chartCashFlow = new \App\Charts\Cashflow($transactions);
        $chartNetIncome = new \App\Charts\NetIncome($transactions);
        $chartNetWorth = new \App\Charts\NetWorth();

        return view('pages.accounting_periods.cashflow', [
                              'charts' => [
                                'cashflow' => $chartCashFlow,
                                'netIncome' => $chartNetIncome,
                                'netWorth' => $chartNetWorth,
                              ],
                            ]);
    }
}
