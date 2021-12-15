<?php

namespace App\Charts;

class NetIncome extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $this->view = 'charts.net-income';
        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });
        $nonExceptionalTransactionsByMonth = $transactions->filter(function ($item) {
            return !$item->exceptional;
        })->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });


        $netData = $transactionsByMonth->map(function ($chunk) {
            return $chunk->sum('value') * -1;
        })->values()->all();

        $netData2 = $nonExceptionalTransactionsByMonth->map(function ($chunk) {
            return $chunk->sum('value') * -1;
        })->values()->all();
        $averageNetData = $this->movingAverage($netData2);
        $this
                      ->title('Monthly Net Income')

                      ->dimensions(1250, 500)
                      ->responsive(false)
                      ->dataset('Net Income', $netData)
                      ->dataset('Average (3mo)', $this->movingAverage($netData2, 3))
                      ->dataset('Average (6mo)', $this->movingAverage($netData2, 6))
                      ->dataset('Average (12mo)', $this->movingAverage($netData2, 12))
                      ->colors(['#FBE1C8', '#CC444B','#444BCC','#44CC4B'])
                      ->labels($transactionsByMonth->keys())
                    ;
    }
}
