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
        ->orderBy('date', 'desc')
        ->orderBy('value')
        ->take(200)
        ->get();


        $categories = \App\Category::get();

        return view('pages.transactions.index', ['transactions' => $transactions,'categories'=>$categories]);
    }

    public function indexVue(Request $request)
    {
        $queryTerm = $request->get('requests')[0]['params']['query'];




        $facetFilters=[];
        $numericFilters=[];
        if (isset($request->get('requests')[0]['params']['facetFilters'])) {
            $facetFilters = ($request->get('requests')[0]['params']['facetFilters'][0]);
        }

        if (isset($request->get('requests')[0]['params']['numericFilters'])) {
            $numericFilters = ($request->get('requests')[0]['params']['numericFilters']);
        }

        $tq = \App\Transaction::where('date', '>', '2017-05-01')
        ->with('categories')->with('account')->with('payee')
        ->orderBy('date', 'desc')
        ->orderBy('value');

        $tq = $tq->where(
            function ($query) use ($queryTerm) {
                $query->where('location', 'like', '%'.$queryTerm . '%')->orWhere('value', $queryTerm);
            }
        );

        foreach ($facetFilters as $filter) {
            list($facet,$value) = explode(':', $filter);
            if ($facet == 'category') {
                $tq->whereHas('categories', function ($query) use ($value) {
                    $query->where('name', $value);
                });
            }
            if ($facet == 'payee') {
                if ($value =='UNKNOWN') {
                    $tq->doesnthave('payee');
                } else {
                    $tq->whereHas('payee', function ($query) use ($value) {
                        $query->where('name', $value);
                    });
                }
            }
            if ($facet == 'account') {
                $tq->whereHas('account', function ($query) use ($value) {
                    $query->where('name', $value);
                });
            }
            if ($facet == 'method') {
                switch ($value) {
                    case 'manual':
                        $tq->where('allocation_type', 0);
                        break;
                    case 'regex':
                        $tq->where('allocation_type', 1);
                        break;
                    case 'Learned':
                        $tq->where('allocation_type', 2);
                        break;
                }
            }
            if ($facet == 'direction') {
                if ($value=='credit') {
                    $tq->where('value', '<', 0);
                }
                if ($value=='debit') {
                    $tq->where('value', '>', 0);
                }
            }
        }

        foreach ($numericFilters as $filter) {
            list($facet,$value) = explode('=', $filter);
            if ($facet=='amount>') {
                $tq->where('value', '>=', $value);
            }
            if ($facet=='amount<') {
                $tq->where('value', '<=', $value);
            }
            if ($facet=='date<') {
                $dateValue = \Carbon\Carbon::createFromTimestamp(substr($value, 0, 10));
                $tq->whereDate('date', '<=', $dateValue);
            }
            if ($facet=='date>') {
                $dateValue = \Carbon\Carbon::createFromTimestamp(substr($value, 0, 10));
                $tq->whereDate('date', '>=', $dateValue);
            }
        }

        $numHits  =$tq->count();
        $hitsPerPage = 500;
        $curPage = $request->get('requests')[0]['params']['page'];

        


        $sql = $tq->toSql();
        $transactions = $tq
        ->take($hitsPerPage)
        ->skip($curPage * $hitsPerPage)
        ->get();
        
        
        $hits = $transactions->transform(function ($e) {
            $e->objectId=$e->id;
            $e->label = ($e->payee) ?"◖" .  $e->payee->name : $e->location;
            $e->accountName = ($e->account) ? $e->account->name : '[UNKNOWN]';
            $e->url=route('transaction.show', $e->id);
            return $e;
        });


        $sc = new \App\Repositories\SearchController();
        $result = [
        'facets'=>[
        'category'=>$sc->getCategoryStats(),
        'payee'=>$sc->payees(),
        'account'=>$sc->accounts(),
        'method' =>$sc->methods(),
        'direction'=>$sc->directions(),
        'amount'=>$transactions->groupBy('value')->map(function($t) { return $t->count(); }),
        ],
        'facets_stats'=>[
        'amount'=>[
        'avg'=>(float)number_format(\App\Transaction::pluck('value')->average(),2,'.',''),
        'max'=>(float)number_format(\App\Transaction::pluck('value')->max(),2,'.',''),
        'min'=>(float)number_format(\App\Transaction::pluck('value')->min(),2,'.',''),
        'sum'=>(float)number_format(\App\Transaction::pluck('value')->sum(),2,'.',''),
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

        for ($i=0; $i<sizeof($request->get('requests',[0])); $i++) {
            $response[]=$result;
        }
        return ['results'=>$response];
    }

    public function search($query)
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->where('location', 'like', '%'.$query . '%')->orderBy('date', 'desc')->orderBy('value')->get();

        return view('pages.transactions.index', ['transactions' => $transactions]);
    }
    public function payee($payee)
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->where('payee_id', $payee)->orderBy('date', 'desc')->orderBy('value')->get();

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
        if ($request->category) {
            foreach ($request->category as $index => $categoryID) {
                if (\App\Category::find($categoryID)) {
                }
                {
                $transaction->categories()->attach($categoryID, [
                  'value' => $request->value[$index],
                  'file_date'=>$transaction->date]);
                }
            }
        }
        $transaction->allocation_type=0;
        $transaction->type=$request->get('type');
        $transaction->save();

        return redirect()->route('transaction.show', $transaction->id);
    }

    public function usePrediction(Request $request, $id)
    {
        $transaction = \App\Transaction::find($id);
        $transaction->categories()->detach();
        $prediction =  dispatch(new \App\Jobs\PredictAllocations($transaction));
        foreach ($prediction as $category) {
            $transaction->categories()->attach([$category->id=>['value'=>$category->actual,'file_date'=>$transaction->date]]);
            $transaction->allocation_type=2;
            $transaction->save();
        }
        return redirect()->route('transaction.show', $transaction->id);
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
