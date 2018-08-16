<?php

namespace App\Charts;

class NetWorth extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_tsf($values, 7);
            //Fill in array with nulls to keep length the same
            $i = 0;
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

    public function __construct()
    {
        parent::__construct('line', 'google');
        $reportedBalances=\DB::table('account_balance')->groupBy('date')
        ->whereRaw('DAYOFWEEK(date)=1')
        ->select(['date',\DB::raw('sum(value) value')])->get();
        $netData = $reportedBalances->pluck('value')->all();

$tsf = trader_tsf ($netData);
$tsf[0]=0;
        $this
                      ->title('Net Worth')

                      ->dimensions(1250, 500)
                      ->responsive(false)
                      ->dataset('Net Income', $netData)
                      ->dataset('Networth', $this->movingAverage($netData))
                     // ->dataset('tsf',$tsf)
                    //  ->colors(['#CC444B'])
                      ->labels($reportedBalances->pluck('date')->all())
                    ;
    }
}
