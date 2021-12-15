<?php

namespace App\Charts;

use Phpml\Regression\LeastSquares;

class Forecast extends BaseChart
{
    public function __construct($transactions)
    {
        parent::__construct('line', 'google');


        $x = [[1], [2], [3], [4], [5], [6]];
        $y = [1, 3, 5, 6, 8, 10];



        $nonExceptionalTransactionsByMonth = $transactions->filter(function ($item) {
            return !$item->exceptional;
        })->groupBy(function ($item, $key) {
            return date('m-y', strtotime($item['date']));
        })->map(function ($chunk) {
            return $chunk->where('value', '>', 0)->sum('value');
        })->values()->all();
        array_pop($nonExceptionalTransactionsByMonth);



        $x = array_map(function ($element) {
            return [$element];
        }, array_keys($nonExceptionalTransactionsByMonth));
        $y = $nonExceptionalTransactionsByMonth;


        $regression = new LeastSquares();
        $regression->train($x, $y);

        $count = sizeof($nonExceptionalTransactionsByMonth);

        $forecast = array_fill(0, $count-1, null);
        $forecast[]=$y[$count-1];

        for ($i=$count; $i<$count+6; $i++) {
            $y[] = null;
            $forecast[] = $regression->predict([$i]);
        }


        echo $regression->predict([7]);
        $expenses=$y;


        $this
        ->title('Forecast')
        ->dimensions(1250, 750)
        ->responsive(false)
        ->dataset('Expense', $expenses)
        ->dataset('$forecast', $forecast)
        ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963'])
        ->labels(array_keys($expenses))
        ;
    }
}
