<?php

namespace App\Charts;

class BaseChart extends \ConsoleTVs\Charts\Builder\Multi
{
    protected function movingAverage($values, $period = 7)
    {
        if (function_exists('trader_kama')) {
            $averages = trader_kama($values, $period);
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
            $range = $period;
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
}
