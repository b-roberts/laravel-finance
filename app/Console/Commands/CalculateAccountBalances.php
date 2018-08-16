<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        foreach([8] as $accountID)
        {
        \DB::table('account_balance')->where('account_id',$accountID)->where('calculated',1)->delete();

        $reportedBalances=\DB::table('account_balance')->where('account_id',$accountID)->where('calculated',0)->orderBy('date')->get();
        $balance = $reportedBalances->first()->value;
        $date = $reportedBalances->first()->date;
        $startBalance = $balance;
        $startDate = new \Carbon\Carbon($date);

        $transactionsByDate = \App\Transaction::where('account_id',$accountID)
        ->where('date','>',$date)
        ->select(['value','date'])
        ->get()->groupBy('date');
        foreach($transactionsByDate as $date =>$transactions)
        {
          $balance-=$transactions->sum('value');

          if ($reportedBalances->where('date',$date)->count())
          {
            $reported = $reportedBalances->where('date',$date)->first()->value;
            $this->info("$date: Reported" . ($balance - $reported));
            $balance = $reported;

          }
          else
          {
          \DB::table('account_balance')->insert(['account_id'=>$accountID,'date'=>$date,'value'=>$balance,'calculated'=>1]);
          }
          $this->info("$date: $balance");
        }

        $today = new \Carbon\Carbon();
        $balance = $reportedBalances->first()->value;
        $date = new \Carbon\Carbon($reportedBalances->first()->date);
        while($date < $today){
          $record=\DB::table('account_balance')
          ->where('account_id',$accountID)
          ->where('date',$date)->first();
          if($record)
          {
            $balance=$record->value;
          }
          else
          {
            \DB::table('account_balance')->insert(['account_id'=>$accountID,'date'=>$date,'value'=>$balance,'calculated'=>1]);
          }
          $this->info("$date: $balance");
          $date->addDay();
        }


      }

    }
}
