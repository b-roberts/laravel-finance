<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules';

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
        $transactions = \App\Transaction::doesntHave('categories')->where('value','>',0)->get();
        foreach($transactions as $transaction)
        {
          dispatch(new \App\Jobs\RunRules($transaction));
        }
    }
}
