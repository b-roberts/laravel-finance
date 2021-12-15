<?php

namespace App\Jobs;

use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Illuminate\Foundation\Bus\Dispatchable;

class PredictAllocations
{
    use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\Transaction $transaction)
    {
        //
        $this->transaction = $transaction;
    }



    public function predictByHabit()
    {
        $averageTransactions = (float) \DB::select(\DB::raw('select avg(c) average from (select payee_id, count(*) c from transactions group by payee_id) x'))[0]->average;

      //Check the number of times we made a transaction at this location
        $locationVisits = \App\Transaction::where('location', $this->transaction->payee_id)
        ->where('id', '<>', $this->transaction->id)
        ->count();


      //if we're greater than average, lets try to see if the dollar amount is common
        if ($locationVisits < $averageTransactions) {
            \Debugbar::error(__LINE__);
      //    return;
        }

        $commonDollarTransactions = \App\Transaction::where('payee_id', $this->transaction->payee_id)
        ->where('id', '<>', $this->transaction->id)
        ->whereRaw('(abs(value-?)/(value+?)/2) <= 0.05', [$this->transaction->value, $this->transaction->value])->whereHas('categories')->with('categories')->get();
        if (!$commonDollarTransactions) {
            \Debugbar::error(__LINE__);
            return;
        }
        \Debugbar::info('common transaction count:' . $commonDollarTransactions->count());


        $commonTransactionTotal=0;
        $commonTransactionCategories=[];

      //calculate the average spend per category
        foreach ($commonDollarTransactions as $t) {
            $commonTransactionTotal+=$t->value;
            foreach ($t->categories as $c) {
                if (!isset($commonTransactionCategories[$c->id])) {
                    $commonTransactionCategories[$c->id]=0;
                }
                $commonTransactionCategories[$c->id]+=$c->pivot->value;
            }
        }

      //Convert our results into the existing format
        $predictionArray= [];
        foreach ($commonTransactionCategories as $i => $c) {
            $category = \App\Category::find($i);
            $category->actual = $c/$commonTransactionTotal*$this->transaction->value;
            $predictionArray[] = $category;
        }
        \Debugbar::error($predictionArray);

        return collect($predictionArray);

        dd([$averageTransactions,$locationVisits,$commonTransactionCategories,$commonTransactionTotal]);
    }












    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = $this->predictByHabit();
        if ($result && $result->count()) {
            return $result;
        }
        //
        $svc = new SVC(Kernel::RBF);
        $transaction = $this->transaction;
        if ($transaction->payee_id==null) {
            return collect();
        }
        $result =  \DB::table('transactions')
          ->select(
              'payee_id',
              'transactions.value',
              \DB::raw("GROUP_CONCAT(color,'=',name,'=',category_id, '@', round(transaction_detail.value / transactions.value *100)) v")
          )
          ->join('transaction_detail', 'transactions.id', '=', 'transaction_id')
          ->join('categories', 'transaction_detail.category_id', '=', 'categories.id')
          ->where(function ($query) use ($transaction) {
            $query->where('payee_id', $transaction->payee_id)
            ->orWhere('transactions.value', '=', $transaction->value)
            ->orWhere('transactions.location', '=', $transaction->location);
          })
          ->where('allocation_type', '!=', 2)
          ->groupBy('transactions.id', 'transactions.value', 'payee_id')
          ->orderBy('transactions.value')
          ->get();


        $samples=[];
        $labels=[];
        $locations = [];
        foreach ($result as $row) {
            if (!in_array($row->payee_id, $locations)) {
                $locations[]=$row->payee_id;
            }
            $samples[]=[(float)$row->value];
            $labels[]=$row->v;
        }

        $svc->train($samples, $labels);

        $prediction=($svc->predict([$transaction->value]));

        $predictionArray = [];
        foreach (explode(',', $prediction) as $categoryPrediction) {
            $values = [];
            preg_match('/(\d+)=([\w ]+)=(\d+)\@([\d\.]+)/', $categoryPrediction, $values);
            if (isset($values[4])) {
                $dollarValue = $values[4] /100*$transaction->value;
                $category = \App\Category::find($values[3]);
                $category->actual = $dollarValue;
                $predictionArray[] = $category;
            }
        }

        return collect($predictionArray);
    }
}
