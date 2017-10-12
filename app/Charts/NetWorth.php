<?php

namespace App\Charts;

class NetWorth extends \ConsoleTVs\Charts\Builder\Multi
{
    private function movingAverage($values)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values, 60);
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

    public function __construct($transactions)
    {
        parent::__construct('line', 'google');
        $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
            return date('m/d/y', strtotime($item['date']));
        });

        $netData = $transactionsByMonth->map(function ($chunk) {
            static $runningTotal = 0;
            $runningTotal += $chunk->sum('value') * -1;

            return $runningTotal;
        })->values()->all();

        $this
                      ->title('Net Worth')

                      ->dimensions(1000, 250)
                      ->responsive(false)
                      ->dataset('Net Income', $netData)
                      ->dataset('Average', $this->movingAverage($netData))

                      ->colors(['#FBE1C8', '#CC444B'])
                      ->labels($transactionsByMonth->keys())
                    ;
    }
}
