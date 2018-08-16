<?php

namespace App\Charts;

class Account extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values, 7);
            //Fill in array with nulls to keep length the same
            $i = 0;
            if (!$averages) {
                return [0];
            }
            while (!isset($averages[$i]) && $i < sizeof($averages)) {
                $averages[$i++] = null;
            }

            return $averages;
        } else {
            $sma = [];
            $position = 0;
            while (empty($values[$position])) {
                ++$position;
            }
            $i = $position;
            while (true) {
                if (empty($values[$i + $range - 1])) {
                    break;
                }
                $temp_sum = 0;
                for ($j = $i; $j < $i + $range; ++$j) {
                    $temp_sum += $values[$j];
                }
                $sma[$i + $range - 1] = $temp_sum / $range;
                ++$i;
            }

            return $sma;
        }
    }

    public function __construct($accountID)
    {
        parent::__construct('line', 'google');
        $reportedBalances=\DB::table('account_balance')->groupBy('date')
        ->whereRaw('DAYOFWEEK(date)=1')
        ->where('account_id', $accountID)
        ->select(['date',\DB::raw('sum(value) value')])->get();
        $netData = $reportedBalances->pluck('value')->all();
        if(!sizeof($netData))
        {
            $this->dataset('NO DATA',[0])
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
