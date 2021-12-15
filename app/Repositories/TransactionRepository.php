<?php

namespace App\Repositories;

use \App\Transaction;
use Carbon\Carbon;

class TransactionRepository
{
    public static function byDate(Carbon $startDate, Carbon $endDate)
    {
        $transactions = Transaction::where('date', '>', $startDate->toDateString())
          ->where('date', '<', $endDate->toDateString())
          ->orderBy('date')
          ->orderBy('value')
          ->with('note')
          ->get();
        return $transactions;
    }

    public static function payments()
    {
        $transactions = Transaction::orderBy('date')->orderBy('value')->where('type', 'payment')->with('note')->get();
        return $transactions;
    }

    public static function paymentsByDate(Carbon $startDate, Carbon $endDate)
    {
        $transactions = Transaction::with('categories')
        ->with('account')
        ->with('note')
        ->where('date', '>=', $startDate->toDateString())
        ->where('date', '<=', $endDate->toDateString())
        ->where('type', 'payment')
        ->orderBy('date')
        ->orderBy('value')
        ->get();
        return $transactions;
    }

    public static function unallocatedByDate(Carbon $startDate, Carbon $endDate)
    {
        return \App\Transaction::with('categories')
        ->with('account')
        ->with('note')
        ->where('date', '>', $startDate->toDateString())
        ->where('date', '<', $endDate->toDateString())
        ->where('type', 'payment')
        ->where('value', '>', 0)
        ->orderBy('date')
        ->orderBy('value')
        ->doesntHave('categories')
        ->get();
    }
}
