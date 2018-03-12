<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class PredictAndSave
{
    use Dispatchable;

  public function handle()
  {
    $transactions = \App\Transaction::doesntHave('categories')->where('value','>',0)->get();
    foreach($transactions as $transaction)
    {
      $prediction =  dispatch(new \App\Jobs\PredictAllocations($transaction));
      foreach($prediction as $category)
      {
        $transaction->categories()->attach([$category->id=>['value'=>$category->actual,'file_date'=>$transaction->date]]);
        $transaction->allocation_type=2;
        $transaction->save();
        echo $transaction->id;
      }
    }
  }
}
