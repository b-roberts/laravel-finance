<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Jobs\PredictAllocations;
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')
        ->with('categories')
        ->orderBy('date','desc')
        ->orderBy('value')
        ->take(200)
        ->get();


        $categories = \App\Category::get();

        return view('pages.transactions.index', ['transactions' => $transactions,'categories'=>$categories]);
    }

    public function indexVue(Request $request)
    {


      $categories =[];
      foreach(\App\Category::get() as $category)
      {
        $categories[$category->name]=100;
      }

      $payees =[];
      foreach(\App\Payee::get() as $payee)
      {
        $payees[$payee->name]=100;
      }
      $accounts =[];
      foreach(\App\Account::get() as $account)
      {
        $accounts[$account->name]=100;
      }
      $facetFilters=[];
      $numericFilters=[];
      if(isset($request->get('requests')[0]['params']['facetFilters']))
      $facetFilters = ($request->get('requests')[0]['params']['facetFilters'][0]);

      if(isset($request->get('requests')[0]['params']['numericFilters']))
      $numericFilters = ($request->get('requests')[0]['params']['numericFilters']);

$tq = \App\Transaction::where('date', '>', '2017-05-01')
->with('categories')->with('account')
->orderBy('date','desc')
->orderBy('value');

$tq = $tq->where('location','like','%'.$request->get('requests')[0]['params']['query'] . '%');

      foreach($facetFilters as $filter)
      {
        list($facet,$value) = explode(':',$filter);
        if($facet == 'category')
        {
          $tq->whereHas('categories',function($query) use ($value){
            $query->where('name',$value);
          });
        }
        if($facet == 'payee')
        {
          $tq->whereHas('payee',function($query) use ($value){
            $query->where('name',$value);
          });
        }
        if($facet == 'account')
        {
          $tq->whereHas('account',function($query) use ($value){
            $query->where('name',$value);
          });
        }
        if($facet == 'direction')
        {
          $tq->where('value','<',0);
        }
        if($facet == 'direction')
        {
          if($value=='credit')
            $tq->where('value','<',0);
          if($value=='debit')
            $tq->where('value','>',0);
        }
      }

      foreach($numericFilters as $filter){
        list($facet,$value) = explode('=',$filter);
        if($facet=='amount>'){
            $tq->where('value','>=',$value);
        }
        if($facet=='amount<'){
            $tq->where('value','<=',$value);
        }

      }

$numHits  =$tq->count();
$hitsPerPage = 50;
$curPage = $request->get('requests')[0]['params']['page'];


$sql = $tq->toSql();
      $hits = $tq
    ->take($hitsPerPage)
    ->skip($curPage * $hitsPerPage)
    ->get()->map(function($e){
      $e->objectId=$e->id;
      $e->accountName = $e->account->name;
      $e->url=route('transaction.show',$e->id);
      return $e;
    });

$result = [
  'facets'=>[
    'category'=>$categories,
    'payee'=>$payees,
    'account'=>$accounts,
    'direction'=>['credit'=>1000,'debit'=>1000],
    'amount'=>[50=>4,40=>3]
  ],
  'facets_stats'=>[
    'amount'=>[
    'avg'=>38,'max'=>1000,'min'=>0,'sum'=>39
    ]
  ]
  ,
  'hitsPerPage'=>$hitsPerPage,
  'index'=>'x',
  'nbHits'=>$numHits,
  'nbPages'=>ceil($numHits/$hitsPerPage),
  'page'=>0,
  'params'=>'',
  'processingTime'=>0,
  'query'=>$sql,
  'exhaustiveFacetsCount'=>true,
  'exhaustiveNbHits'=>false,

  'hits'=>$hits];

  $response = [];
  for($i=0;$i<sizeof($request->get('requests'));$i++)
  {
    $response[]=$result;
  }
  return ['results'=>$response];

      return  (['results'=>[

        [
          'facets'=>[
            'category'=>$categories,
            'payee'=>$payees,
            'amount'=>[50=>4,40=>3]
          ],
          'hitsPerPage'=>10,
          'index'=>'x',
          'nbHits'=>20,
          'nbPages'=>40,
          'page'=>0,
          'params'=>'',
          'processingTime'=>0,
          'query'=>$sql,
          'exhaustiveFacetsCount'=>true,
          'exhaustiveNbHits'=>false,

          'hits'=>$hits]


      ]]);
    }

    public function search($query)
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->where('location','like','%'.$query . '%')->orderBy('date','desc')->orderBy('value')->get();

        return view('pages.transactions.index', ['transactions' => $transactions]);
    }
    public function payee($payee)
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->where('payee_id',$payee)->orderBy('date','desc')->orderBy('value')->get();

        return view('pages.transactions.index', ['transactions' => $transactions]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = \App\Transaction::find($id);
        $categories = \App\Category::all()->sortBy('name');
        $prediction =  dispatch(new \App\Jobs\PredictAllocations($transaction));
        return view('pages.transactions.show', [
          'transaction' => $transaction,
          'categories'=>$categories,
          'prediction'=>$prediction
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $transaction = \App\Transaction::find($id);
      $transaction->categories()->detach();
      if($request->category)
      {
      foreach($request->category as $index=>$categoryID)
      {
        if (\App\Category::find($categoryID));
      {
        $transaction->categories()->attach($categoryID, [
          'value' => $request->value[$index],
          'file_date'=>$transaction->date] );
        }
      }
    }
      $transaction->allocation_type=0;
      $transaction->save();

      return redirect()->route('transaction.show',$transaction->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Transaction::destroy($id);
    }
}
