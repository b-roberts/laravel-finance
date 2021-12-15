<?php

namespace App\Parsers;

use App\Transaction;
use Illuminate\Database\Eloquent\Collection;

class AmcommStatement
{
    public $transactions;

    public function __construct()
    {
        $this->transactions = new Collection();
    }

    public function import($file, $accountID)
    {
        $fp = fopen($file, 'r');
        while ($line = fgets($fp)) {
            if (trim($line)=='Checking Account Transactions') {
                fgets($fp);//The following blank line
                break;
            }
        }
        $start =  ftell($fp);
        while ($line = fgets($fp)) {
            if (trim($line)=='') {
                break;
            }
            fseek($fp, $start);

            $date = date('Y-m-d', strtotime(trim(fread($fp, 9))));
            $location = trim(fread($fp, 50));
            $amount = trim(fgets($fp));
            $direction = trim(substr($amount, -1));
            $value = preg_replace('/[^0-9.]/', '', $amount);
    
            if ($direction == '+') {
                $value = floatval($value) *  -1;
            }
    
            $start =  ftell($fp);

            $transaction = new Transaction([
                'date' => $date,
                'location' => $location,
                'value' => $value,
                'fitid' => null,
                'created_at' => $timestamp,
            ]);

            $this->transactions->push($transaction);
        }
        fclose($fp);
        return $this->transactions;
    }
}
