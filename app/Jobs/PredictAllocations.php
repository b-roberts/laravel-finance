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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $svc = new SVC(Kernel::RBF);
        $transaction = $this->transaction;

        $result =  \DB::table('transactions')
          ->select('location','transactions.value',
            \DB::raw("GROUP_CONCAT(color,'=',name,'=',category_id, '@', round(transaction_detail.value / transactions.value *100)) v"))
          ->join('transaction_detail','transactions.id','=','transaction_id')
          ->join('categories','transaction_detail.category_id', '=', 'categories.id')
          ->where('location',$transaction->location)
          ->orWhere(function ($query) use ($transaction) {
            $query->where('transactions.value','=',$transaction->value);
            })
          ->where('allocation_type','!=',2)
          ->groupBy('transactions.id','transactions.value','location')
          ->get();
        $samples=[];
        $labels=[];
        $locations = [];
        foreach ($result as $row)
        {
          if (!in_array($row->location,$locations))
          {
            $locations[]=$row->location;
          }
          $samples[]=[array_search($row->location,$locations),$row->value];
          $labels[]=$row->v;
        }
        $svc->train($samples, $labels);
        $prediction=($svc->predict([$transaction->value]));

        $predictionArray = [];
        foreach(explode(',',$prediction) as $categoryPrediction)
        {
          $values = [];
          preg_match('/(\d+)=([\w ]+)=(\d+)\@([\d\.]+)/',$categoryPrediction,$values);
          if (isset($values[4]))
        {
          $dollarValue = $values[4] /100*$transaction->value;
          $category = \App\Category::find($values[3]);
          $category->actual = $dollarValue;
          $predictionArray[] = $category;}
        }

        return collect($predictionArray);
    }
}
