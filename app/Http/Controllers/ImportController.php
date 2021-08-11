<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Account;
use App\Parsers\QboParser;
use App\Parsers\AmCommParser;
use App\Parsers\OfxParser;

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
        //  'statement' => 'required|mimetypes:text/plain',
        ]);
        $statementFile = $request->file('statement');
        $accountID = $request->input('account_id');
        //if ammcomm
        switch ($statementFile->getClientOriginalExtension()) {
            case 'csv':
            case 'CSV':
                $parser = new AmCommParser;
            break;
            case 'qbo':
                $parser = new QboParser;
            break;
            case 'ofx':
            case 'OFX':
                $parser = new OfxParser;
                break;
            case 'qfx':
            case 'QFX':
                include 'import/qfx.php';
            break;
            case 'TXT':
            case 'txt':
                include 'import/amcomm_statement.php';
            break;
        }

        $transactions = $parser->import($statementFile->getPathname(), 1);
        //--------------------------------------------------------------------------------------------------------------------------------
        /**
         * @var string Used to group transactions by import batch for easier duplication resolution
         */
        $timestamp = date('Y-m-d H:i:s');


        $importCount = 0;
        foreach ($transactions as $transaction) {
            if ( $transaction->fitid == null || 0 == Transaction::where('fitid', $transaction->fitid)->count()) {
                $transaction->account_id = $accountID;
                $transaction->created_at = $timestamp;

                if ($transaction->save()) {
                    dispatch(new \App\Jobs\RunRules($transaction));
                    ++$importCount;
                }
            }
        }
        foreach($parser->dailyBalances as $date=>$balance)
        if (0 == \DB::table('account_balance')->where(['account_id' => $accountID, 'date' => $date])->count()) {
            \DB::table('account_balance')->insert(['account_id' => $accountID, 'date' => $date, 'value' => $balance * -1]);
        }

        $existingCount = sizeof($transactions) - $importCount;
        session()->flash('flash_success', "$importCount transactions imported. $existingCount transactions already exist.");

        return back();
    }
}
