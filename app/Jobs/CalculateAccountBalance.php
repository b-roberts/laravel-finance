<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class CalculateAccountBalance
{
    use Dispatchable;
    protected $accountID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accountID)
    {
        //
        $this->accountID = $accountID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $accountID=$this->accountID;
        \DB::table('account_balance')->where('account_id', $accountID)->where('calculated', 1)->delete();

        $reportedBalances=\DB::table('account_balance')->where('account_id', $accountID)->where('calculated', 0)->orderBy('date')->get();
        $balance = $reportedBalances->first()->value;
        $date = $reportedBalances->first()->date;
        $startBalance = $balance;
        $startDate = new \Carbon\Carbon($date);

        $transactionsByDate = \App\Transaction::where('account_id', $accountID)
          ->where('date', '>', $date)
          ->select(['value','date'])
          ->get()->groupBy('date');
        foreach ($transactionsByDate as $date =>$transactions) {
            $balance-=$transactions->sum('value');

            if ($reportedBalances->where('date', $date)->count()) {
                $reported = $reportedBalances->where('date', $date)->first()->value;

                $balance = $reported;
            } else {
                \DB::table('account_balance')->insert(['account_id'=>$accountID,'date'=>$date,'value'=>$balance,'calculated'=>1]);
            }
        }

        $today = new \Carbon\Carbon();
        $balance = $reportedBalances->first()->value;
        $date = new \Carbon\Carbon($reportedBalances->first()->date);
        while ($date < $today) {
            $record=\DB::table('account_balance')
              ->where('account_id', $accountID)
              ->where('date', $date)->first();
            if ($record) {
                $balance=$record->value;
            } else {
                \DB::table('account_balance')->insert(['account_id'=>$accountID,'date'=>$date,'value'=>$balance,'calculated'=>1]);
            }

            $date->addDay();
        }
    }
}
