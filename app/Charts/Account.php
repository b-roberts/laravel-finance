<?php

namespace App\Charts;

class Account extends BaseChart
{
    public function __construct($accountID)
    {
        parent::__construct('line', 'google');
        $reportedBalances=\DB::table('account_balance')->groupBy('date')
        ->whereRaw('DAYOFWEEK(date)=3')
        ->where('account_id', $accountID)
        ->select(['date',\DB::raw('sum(value) value')])->get();
        $netData = $reportedBalances->pluck('value')->all();
        if (!sizeof($netData)) {
            $this->dataset('NO DATA', [0])
            ->labels([0]);
            return;
        }
        $this
                      ->title('Account Balance')
                      ->dimensions(1000, 250)
                      ->responsive(false)
                      ->dataset('Balance', $netData)
                      ->dataset('Average', $this->movingAverage($netData))
                    //  ->colors(['#CC444B'])
                      ->labels($reportedBalances->pluck('date')->all())
                    ;
    }
}
