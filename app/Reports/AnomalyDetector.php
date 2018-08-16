<?php namespace App\Reports;

use \Carbon\Carbon;

class AnomalyDetector
{
    public function detect($date)
    {
        $startDate = new  Carbon($date);
        $endDate  = $startDate->copy()->addMonth();

        $referenceStartDate = $startDate->copy()->addMonth(-2);

        //Load all transactions in the period
        $transactions = \App\Transaction::where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString())->orderBy('date')->orderBy('value')->get();

        $referenceTransactions = \App\Transaction::where('date', '>', $referenceStartDate->toDateString())->where('date', '<', $startDate->toDateString())->orderBy('date')->orderBy('value')->get();

        $payees=$transactions->filter(function ($item, $key) {
            return $item->value > 0;
        })->pluck('location');

        $referencePayees=$referenceTransactions->filter(function ($item, $key) {
            return $item->value > 0;
        })->pluck('location');

        $anomaly = $transactions->whereNotIn('location', $referencePayees);
        //print_r($anomaly);
    }
}
