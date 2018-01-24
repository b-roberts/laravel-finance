<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataFormats\Ofx;
use App\DataFormats\Qbo;
use App\Transaction;
use App\Account;

class ImportController extends Controller
{
    public function index()
    {
        $accounts = Account::all();

        return view('pages.import.index', ['accounts' => $accounts]);
    }

    /**
     * Update the avatar for the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
          'account_id' => 'required|exists:accounts,id',
          'statement' => 'required|mimetypes:text/plain',
        ]);

        $statementFile = $request->file('statement');

        //Determine the DataFormat Processor
        $fileType = studly_case($statementFile->getClientOriginalExtension());
        switch (strtolower()) {
           case 'ofx':
            $parser = new Ofx();
            break;
           case 'qbo':
            $parser = new Qbo();
            break;
           default:
            session()->flash('flash_error', 'Cannot parse this file type.');

            return back();
         }

        $path = storage_path('app/'.$statementFile->store('statements'));

        /**
         * @var string Used to group transactions by import batch for easier duplication resolution
         */
        $timestamp = date('Y-m-d H:i:s');

        $transactions = $parser->parse($path);

        $importCount = 0;
        foreach ($transactions as $transaction) {
            if (0 == Transaction::where('fitid', $transaction->fitid)->count()) {
                $transaction->account_id = $request->input('account_id');
                $transaction->created_at = $timestamp;
                if ($transaction->save()) {
                    ++$importCount;
                }
            }
        }
        $existingCount = $transactions->count() - $importCount;
        session()->flash('flash_success', "$importCount transactions imported. $existingCount transactions already exist.");

        return back();
    }
}
