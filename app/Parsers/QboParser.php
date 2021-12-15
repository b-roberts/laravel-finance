<?php

namespace App\Parsers;

use App\Transaction;
use Illuminate\Database\Eloquent\Collection;

class QboParser
{
    public $transactions;
    public $dailyBalances=[];

    public function __construct()
    {
        $this->transactions = new Collection();
    }

    public function import($file, $accountID)
    {
        $doc = new \DOMDocument();
        $doc->load($file);

        $xpath = new \DOMXPath($doc);

        // We starts from the root element
        $query = '/OFX/CREDITCARDMSGSRSV1/CCSTMTTRNRS/CCSTMTRS/BANKTRANLIST/STMTTRN';
        $entries = $xpath->query($query);

        $timestamp = date('Y-m-d H:i:s');
        $ledgerBalance = $xpath->query('//LEDGERBAL/BALAMT')[0]->nodeValue;
        $ledgerDate = \DateTime::createFromFormat('YmdHis', substr($xpath->query('//LEDGERBAL/DTASOF')[0]->nodeValue, 0, 14))->format('Y-m-d');
        $this->dailyBalances[$ledgerDate] = $ledgerBalance;

        foreach ($entries as $entry) {
            $dateNode = $xpath->query('DTUSER', $entry)[0];
            $timeFormat = 'YmdHis.u';
            if (!$dateNode) {
                $dateNode = $xpath->query('DTPOSTED', $entry)[0];
                $timeFormat = 'YmdHis.u+';
            }
            $date = $dateNode->nodeValue;
            $date = \Carbon\Carbon::createFromFormat($timeFormat, $date);
            $value = -1 * $xpath->query('TRNAMT', $entry)[0]->nodeValue;

            $locationNode = $xpath->query('NAME', $entry)[0];
            if (!$locationNode) {
                $locationNode = $xpath->query('MEMO', $entry)[0];
            }

            $location = $locationNode->nodeValue;
            $fitid = $xpath->query('FITID', $entry)[0]->nodeValue;

            $transaction = new Transaction();
            $transaction->forceFill([
                'date' => $date,
                'location' => $location,
                'value' => $value,
                'fitid' => $fitid,
                'created_at' => $timestamp,
            ]);
            $this->transactions->push($transaction);
        }
        return $this->transactions;
    }
}
