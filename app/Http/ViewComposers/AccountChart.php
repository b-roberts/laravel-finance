<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class AccountChart
{


    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $chart = new \App\Charts\Account($view->account->id);
        $chart->view="charts.simplearea";
        $chart->colors(['#424546']);
        $chart->dimensions(200, 100);
        array_shift($chart->datasets);
        $view->with('chart', $chart)->with('account', $view->account);
    }
}
