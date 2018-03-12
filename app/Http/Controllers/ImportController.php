<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $accountID = $request->input('account_id');
        $ofxParser = new \OfxParser\Parser();
        $ofx = $ofxParser->loadFromFile($statementFile);
        $bankAccount = reset($ofx->bankAccounts);
        /**
         * @var string Used to group transactions by import batch for easier duplication resolution
         */
        $timestamp = date('Y-m-d H:i:s');

        // Get the statement transactions for the account
        $transactions = $bankAccount->statement->transactions;

        $importCount = 0;
        foreach ($transactions as $ofxTransaction) {
            if (0 == Transaction::where('fitid', $ofxTransaction->uniqueId)->count()) {
                $transaction = new Transaction();
                $transaction->account_id = $accountID;
                $transaction->created_at = $timestamp;
                $transaction->value = -1 * $ofxTransaction->amount;
                $transaction->location = $ofxTransaction->name;
                $transaction->fitid = $ofxTransaction->uniqueId;
                $transaction->date = $ofxTransaction->date->format('Y-m-d');
                if ($transaction->save()) {
                    ++$importCount;
                }
            }
        }

        if (0 == \DB::table('account_balance')->where(['account_id' => $accountID, 'date' => $bankAccount->balanceDate->format('Y-m-d')])->count()) {
            \DB::table('account_balance')->insert(['account_id' => $accountID, 'date' => $bankAccount->balanceDate->format('Y-m-d'), 'value' => $bankAccount->balance * -1]);
        }

        $existingCount = sizeof($transactions) - $importCount;
        session()->flash('flash_success', "$importCount transactions imported. $existingCount transactions already exist.");

        return back();
    }
}
