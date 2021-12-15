<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

class SearchController
{
    public function getCategoryStats()
    {
        $categories =[];
        foreach (\App\Category::withCount('transactions')->get() as $category) {
            $categories[$category->name]=$category->transactions_count;
        }
        return $categories;
    }
    public function payees()
    {
        $payees =[];
        $payees['UNKNOWN']=\App\Transaction::doesnthave('payee')->count();
        foreach (\App\Payee::withCount('transactions')->get() as $payee) {
            $payees[$payee->name]=$payee->transactions_count;
        }
        return $payees;
    }
    public function accounts()
    {
        $accounts =[];
        foreach (\App\Account::withCount('transactions')->get() as $account) {
            $accounts[$account->name]=$account->transactions_count;
        }
        return $accounts;
    }
    public function methods()
    {
        $methods =[];
        foreach (['manual','regex','Learned'] as $id => $method) {
        
            $methods[$method]=\App\Transaction::where('allocation_type', $id)->count();
        }
        return $methods;
    }
    public function directions()
    {
        $directions =[];
        
        $directions['credit']=\App\Transaction::where('value', '<', '0')->count();
        $directions['debit']=\App\Transaction::where('value', '>', '0')->count();
        
        return $directions;
    }
}