<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\UserRepository;
use \Carbon\Carbon;
use Charts;

class CategoryBreakdownChartComposer
{


    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $parameters = collect(request()->route()->parameters())->union(request()->input());

        $startDate=$parameters->get('startDate', null);

        $endDate = (new  Carbon($startDate))->addMonth();
            $startDate = new  Carbon($startDate);


        $categories = \App\Category::with(['budgets' => function ($query) {
            $query->where('id', 4);
        }])->get();

        $categories->map(function ($y) {
            $y->expected = 0;
            if ($y->budgets->first()) {
                $y->expected = $y->budgets->first()->pivot->value;
            }
        });
        $categories->map(function ($y) use ($startDate, $endDate) {
            $y->actual = $y->transactions()->where('date', '>', $startDate->toDateString())->where('date', '<', $endDate->toDateString())->sum('transaction_detail.value');
        });

        $categoryBreakdown = Charts::create('pie', 'google')
        ->title('Category Breakdown')
        ->elementLabel('Category')
        ->responsive(false)
        ->colors($categories->pluck('color')->values())
        ->values($categories->map(function ($c) {
            return $c->actual;// > 0 ? $c->actual : 0;
        }))
        ->labels($categories->pluck('name')->values())
        ;
        return  $view->with('categoryBreakdown', $categoryBreakdown);
    }
}
