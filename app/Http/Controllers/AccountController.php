<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Account;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::orderBy('name')->get();
        return view('pages.accounts.index', ['accounts'=>$accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.accounts.create');
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
        $this->validate($request, [
        'name' => 'required|max:24',
        'description' => 'required',
        ]);
        $account = new Account;
        $account->fill($request->input());
        $account->account_number = rand(1, 30000);
        $account->save();
        return back();
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
        $account = Account::find($id);
        $accountGraph = new \App\Charts\Account($id);
        return view('pages.accounts.show', ['account'=>$account,'accountGraph'=>$accountGraph]);
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
        $account = Account::find($id);
        return view('pages.accounts.edit', ['account'=>$account]);
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

        $account = Account::find($id);

        $this->validate($request, [
        'name' => 'required|max:24',
        'description' => 'required',
        ]);

        $account->fill($request->input());
        $account->save();
        return back();
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
    }
}
