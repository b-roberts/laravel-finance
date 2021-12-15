<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        \View::composer(
            'charts.categoryBreakdown',
            'App\Http\ViewComposers\CategoryBreakdownChartComposer'
        );
        \View::composer(
            'charts.account_chart',
            'App\Http\ViewComposers\AccountChart'
        );
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
