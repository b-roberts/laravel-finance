<?php

namespace App\Http\Controllers;

use DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cashFlow()
    {
        $transactions = \App\Transaction::select(
            DB::raw('YEAR(date) y'),
            DB::raw('MONTH(date) m'),
            DB::raw('sum(value)')
        )
          ->groupBy('y')
          ->groupBy('m')
          ->get();

        dd($transactions);

        $chart1 = \Charts::create('percentage', 'justgage')
        ->values([$this->expectedExpense(), 0, $this->monthly_income])
        ->responsive(false)
        ->title('Estimated Spend Percentage')
        ->elementLabel($this->expectedExpense() / $this->monthly_income * 100 .'%')
        ->height(400)
        ->width(0);

        return view('pages.cashflow', ['chart1' => $chart1]);
    }
}
