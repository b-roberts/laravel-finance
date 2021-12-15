<?php

namespace App\Parsers;

use App\Transaction;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Eloquent\Collection;

class QfxParser
{
    public $transactions;
    public $dailyBalances=[];

    public function __construct()
    {
        $this->transactions = new Collection();
    }

    public function import($file, $accountID)
    {
        $parser = new SgmlParser();
        $timestamp = date('Y-m-d H:i:s');

$raw = preg_replace('/<INTU\.[A-Z]+>[0-9]*/', '', file_get_contents($file));
    
        $doc = $parser->loadFromString($raw);
        $xpath = new \DOMXPath($doc);
    
        // We starts from the root element
        $query = '//STMTTRN';
    
        $entries = $xpath->query($query);
    
        foreach ($entries as $entry) {
            $date = $xpath->query('DTPOSTED', $entry)[0]->nodeValue;
            if (!$date) {
                echo 'NO DATE!';
                break;
            }
            $date = (substr($date, 0, 14));
    
            $date = (DateTime::createFromFormat('YmdHis', $date)->format('Y-m-d'));
            $value = (- 1 * $xpath->query('TRNAMT', $entry)[0]->nodeValue);
    
            if ($xpath->query('MEMO', $entry)->length > 0) {
                $location = ($xpath->query('MEMO', $entry)[0]->nodeValue);
            } else {
                $location = ($xpath->query('NAME', $entry)[0]->nodeValue);
            }
    
            //$fitid = ($xpath->query('FITID', $entry)[0]->nodeValue);
            $transaction = new Transaction();
            $transaction->forceFill([
              'date' => $date,
              'location' => $location,
              'value' => $value,
            //  'fitid' => $fitid,
              'created_at' => $timestamp,
            ]);
            $this->transactions->push($transaction);
        }
        return $this->transactions;
    }
}
