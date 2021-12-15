<?php

namespace App\Http\Controllers;

use App\Payee;
use Illuminate\Http\Request;

class PayeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $payees = \App\Payee::orderBy('name')
        ->withCount('transactions')
        ->withCount(['transactions as totalSpend' => function ($query) {
            $query->select(\DB::raw('sum(value)'));
        }])
        ->get();
        return view('pages.payees.index', ['payees'=>$payees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('pages.payees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $payee = new \App\Payee;
        $payee->fill($request->all());
        $payee->save();
        \Artisan::call('assign', []);

        return redirect(route('payee.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function show(Payee $payee)
    {
        //
        return view('pages.payees.edit', ['payee'=>$payee]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function edit(Payee $payee)
    {
        //
        return view('pages.payees.edit', ['payee'=>$payee]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payee $payee)
    {
        //
        $payee->fill($request->all());
        $payee->save();
        \Artisan::call('assign', []);

        return redirect(route('payee.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payee  $payee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payee $payee)
    {
        //
    }
}
