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

    public function __construct()
    {
        parent::__construct('line', 'google');

        $totals = collect();
        foreach([1,2,3,6] as $accountID)
        {
          foreach($this->getDailyBalances($accountID) as $day=>$value)
          {
            if($totals->has($day))
            {
              $totals[$day]+= $value;
            }
            else
            {
              $totals[$day]=$value;
            }
          }
        }
        $dailyArray=$totals->sort()->all();
        ksort($dailyArray);
        $this
                              ->title('Account Balance')

                              ->dimensions(1250, 500)
                              ->responsive(false)
                              ->dataset('Net Income', array_values($dailyArray))
                              ->dataset('Average Net Income', $this->movingAverage($dailyArray))

                              ->colors(['#CC444B', '#44cc4B'])
                              ->labels(array_keys($dailyArray))
                            ;
        //  dd($dailyBalances);
    }

    private function getDailyBalances($accountID)
    {
        $dailyBalances=collect();
        $explicitBalances = \DB::table('account_balance')->where('account_id', $accountID)->orderBy('date', 'asc')->get();
        foreach ($explicitBalances as $explicitBalance) {
            $dailyBalances[$explicitBalance->date]=$explicitBalance->value;
        }
        $transactions = \App\Transaction::where('account_id', $accountID)->orderBy('date', 'asc')->get()->groupBy(function ($item, $key) {
            return date('Y-m-d', strtotime($item['date']));
        })->each(function ($transactions, $date) use ($dailyBalances) {
            static $value=0;
            $carbon = new \Carbon\Carbon($date);
            $previous = $carbon->subDay()->format('Y-m-d');

            if ($dailyBalances->has($previous)) {
                $value = $dailyBalances[$previous];
            }

            if (!$dailyBalances->has($date)) {
                foreach ($transactions as $transaction) {
                    $value -= $transaction->value;
                }
                $dailyBalances[$date]=$value;
            }
        });
        $dailyBalances=$dailyBalances->sort();

        $dailyBalances= $this->fillDates($dailyBalances);

        return $dailyBalances;
    }

    private function fillDates($dailyBalances)
    {
      $start=new \Carbon\Carbon($dailyBalances->keys()->sort()->first());
      $end=new \Carbon\Carbon($dailyBalances->keys()->sort()->last());

      $current=$start;
      $value = $dailyBalances[$current->format('Y-m-d')];
      while($current < $end)
      {
        $date = $current->format('Y-m-d');
        if(!$dailyBalances->has($date))
        {
          $dailyBalances[$date]=$value;
        }
        $value = $dailyBalances[$date];
        $current = $current->addDay();
      }
      return $dailyBalances;
    }
}
