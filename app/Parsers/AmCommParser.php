<?php

namespace App\Parsers;

use App\Transaction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Eloquent\Collection;

class AmCommParser
{
    public $transactions;
    public $dailyBalances=[];

    public const COLUMN_ACCOUNT=0;
    public const COLUMN_DATE=1;
    public const COLUMN_PENDING=2;
    public const COLUMN_DESCRIPTION=3;
    public const COLUMN_CATEGORY=4;
    public const COLUMN_CHECK=5;
    public const COLUMN_CREDIT=6;
    public const COLUMN_DEBIT=7;


    public function __construct()
    {
        $this->transactions = new Collection();
    }
    public function import($file, $accountID)
    {
        $fp = fopen($file, 'r');
        $timestamp = date('Y-m-d H:i:s');
        fgets($fp);

        while ($row = fgetcsv($fp)) {
            $timeFormat = 'Y-m-d';
            $date = \Carbon\Carbon::createFromFormat($timeFormat, $row[static::COLUMN_DATE]);
            $value = -1 * ($row[static::COLUMN_DEBIT] ?: $row[static::COLUMN_CREDIT]);

            $location = $row[static::COLUMN_DESCRIPTION];

            $transaction = new Transaction();
            $transaction->forceFill([
				'date' => $date,
				'location' => $location,
				'value' => $value,
				'fitid' => null,
				'created_at' => $timestamp,
			]);
            $this->transactions->push($transaction);
        }
        return $this->transactions;
    }
   
}