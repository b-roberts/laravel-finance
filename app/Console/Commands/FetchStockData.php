<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all Stock Data';

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

        $stocks = \App\StockPrice::query()->select('ticker')->distinct()->get()->map(function ($stock) {
            dispatch(new \App\Jobs\FetchStockData($stock->ticker));
        });




    }
}
