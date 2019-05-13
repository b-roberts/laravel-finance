<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignPayees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign';

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
      $transactions = \App\Transaction::whereNull('payee_id')->get();
  //    dd($transactions);
      $payees=\App\Payee::get();
      foreach($transactions as $transaction)
      {
        foreach($payees as $payee){
          if (preg_match('/'.$payee->regex.'/', $transaction->location)) {
            $transaction->payee_id = $payee->id;
            $transaction->save();
          }
        }

      }
    }
}
