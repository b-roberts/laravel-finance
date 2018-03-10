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
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->orderBy('date','desc')->orderBy('value')->get();

        return view('pages.transactions.index', ['transactions' => $transactions]);
    }
    public function payee($payee)
    {
        $transactions = \App\Transaction::where('date', '>', '2017-05-01')->where('location',$payee)->orderBy('date','desc')->orderBy('value')->get();

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
