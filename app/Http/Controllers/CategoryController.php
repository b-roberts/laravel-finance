<?php

namespace App\Http\Controllers;

use Charts;

class CategoryController extends Controller
{
    public function show($id)
    {
        $category = \App\Category::find($id);
        $transactions = \App\Transaction::whereHas('categories', function ($query) use ($id) {
            $query->where('category_id', $id);
        })->orderBy('date')->orderBy('value')->get();

        $chartCashFlow = new \App\Charts\Category($transactions, $id);

        return view('pages.categories.index', [
        'category' => $category,
                              'charts' => [
                                'cashflow' => $chartCashFlow,
                              ],
                            ]);
    }
}
