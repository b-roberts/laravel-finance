<?php

namespace App\Http\Controllers;

use Charts;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function index()
    {
    }

    public function show($id)
    {
        $category = \App\Category::find($id);
        $transactions = \App\Transaction::whereHas('categories', function ($query) use ($id) {
            $query->where('category_id', $id);
        })->orderBy('date')->orderBy('value')->get();

        $allocations = $transactions->map(function ($transaction) use ($id) {
            return $transaction->categories->where('id', $id)->first()->pivot;
        });
        $twoMonthAverage = $allocations->where('file_date', '>=', new Carbon('-2 months'))->average('value');

        $cs = new \App\Charts\CategorySpending($id);

        return view('pages.categories.show', [
                            'category' => $category,
                            'charts' => [
                                'cs' => $cs,
                            ],
                            'averageTransactionTotal' => $transactions->average('value'),
                            'averageAllocation' => $allocations->average('value'),
                            'twoMonthAverage' => $twoMonthAverage,
                          ]);
    }
}
