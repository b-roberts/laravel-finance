<?php

namespace App\Charts;
use \App\Repositories\TransactionRepository;
class Annotation extends BaseChart
{
    public function __construct()
    {
        parent::__construct('line', 'google');
        $this->view='charts.annotation';

        $this
  ->title('Monthly Cashflow')
  ->dimensions(1250, 500)
  ->responsive(false)

  ->colors(['#FBE1C8', '#C7D5E3', '#CC444B', '#4CB963','#B94CA3'])

;
    }


    public function transactions() {
      $transactions =TransactionRepository::payments();
      $transactions = $transactions->filter(function($item){
        return date('m-y', strtotime($item['date'])) != date('m-y');
      });


              $transactionsByMonth = $transactions->groupBy(function ($item, $key) {
                  return date('Y,m,1', strtotime($item['date']));
              });
          $t =     $transactionsByMonth->map(function ($chunk) {
                  return $chunk->where('value', '>', 0)->sum('value');
              });


          //    dd($t);
              return $t;
return $transactionsByMonth;
    }
}
