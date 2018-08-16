<?php

namespace App\Charts;

class NetWorth extends BaseChart
{
    public function __construct()
    {
        parent::__construct('line', 'google');
        $reportedBalances=\DB::table('account_balance')->groupBy('date')
        ->whereRaw('DAYOFWEEK(date)=1')
        ->select(['date',\DB::raw('sum(value) value')])->get();
        $netData = $reportedBalances->pluck('value')->all();

        $this
                      ->title('Net Worth')
                      ->dimensions(1250, 500)
                      ->responsive(false)
                      ->dataset('Net', $netData)
                      ->dataset('Average', $this->movingAverage($netData))
                      ->labels($reportedBalances->pluck('date')->all())
                    ;
    }
}
