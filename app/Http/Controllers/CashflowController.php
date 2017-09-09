<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Charts;
use \Carbon\Carbon;

class CashflowController extends Controller
{
    //
    public function cashflow()
    {

    //Load all transactions and group them by month
        $transactions = \App\Transaction::orderBy('date')->orderBy('value')->get();


        $chartCashFlow= new \App\Charts\Cashflow($transactions);
        $chartNetIncome= new \App\Charts\NetIncome($transactions);
        $chartNetWorth= new \App\Charts\NetWorth($transactions);

        return view('pages.accounting_periods.cashflow', [
                              'charts'=>[
                                'cashflow'=>$chartCashFlow,
                                'netIncome'=>$chartNetIncome,
                                'netWorth'=>$chartNetWorth,


                              ]
                            ]);
    }
}
