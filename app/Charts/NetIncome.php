<?php

namespace App\Charts;

class NetIncome extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        });

        $netData = $transactionsByMonth->map(function ($chunk) {
            return $chunk->sum('value') * -1;
        })->values()->all();
        $averageNetData = $this->movingAverage($netData);

        $this
                      ->title('Monthly Net Income')

                      ->dimensions(1250, 500)
                      ->responsive(false)
                      ->dataset('Net Income', $netData)
                      ->dataset('Average Net Income', $averageNetData)

                      ->colors(['#FBE1C8', '#CC444B'])
                      ->labels($transactionsByMonth->keys())
                    ;
    }
}
