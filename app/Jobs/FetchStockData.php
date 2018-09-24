<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class FetchStockData
{
    use Dispatchable;
    private $ticker;

    /**
     * Create a new job instance.
     */
    public function __construct(String $ticker)
    {
        $this->ticker = $ticker;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $apikey = env('ALPHA_VANTAGE_API');
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY_ADJUSTED&symbol={$this->ticker}&apikey={$apikey}&outputsize=full";

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $url, []);


        $data = json_decode($res->getBody(), true)['Time Series (Daily)'];
        foreach ($data as $date => $pricing) {
            $stockPrice = new \App\StockPrice;
            $stockPrice->ticker = $this->ticker;
            $stockPrice->date = $date;
            $stockPrice->open = $pricing["1. open"];
            $stockPrice->high = $pricing["2. high"];
            $stockPrice->low = $pricing["3. low"];
            $stockPrice->close = $pricing["4. close"];
            $stockPrice->adjusted_close = $pricing["5. adjusted close"];
            $stockPrice->volume = $pricing["6. volume"];
            $stockPrice->dividend_amount = $pricing["7. dividend amount"];
            $stockPrice->split_coefficient = $pricing["8. split coefficient"];
            try {
                $stockPrice->save();
            } catch (\Illuminate\Database\QueryException $ex) {
            }
        }
    }
}
