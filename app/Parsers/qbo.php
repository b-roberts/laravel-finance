<?php
function import($file,$accountID)
{
global $mysql;
$doc = new DOMDocument();
$doc->load($file);

$xpath = new DOMXPath($doc);

// We starts from the root element
$query = '/OFX/CREDITCARDMSGSRSV1/CCSTMTTRNRS/CCSTMTRS/BANKTRANLIST/STMTTRN';
$entries = $xpath->query($query);

$timestamp = date('Y-m-d H:i:s');
$ledgerBalance = $xpath->query('//LEDGERBAL/BALAMT')[0]->nodeValue;
$ledgerDate = DateTime::createFromFormat ( 'YmdHis',substr($xpath->query('//LEDGERBAL/DTASOF')[0]->nodeValue,0,14))->format ( 'Y-m-d' );

$mysql->query("insert IGNORE into account_balance (account_id,date,value) VALUES ($accountID,'$ledgerDate','$ledgerBalance')");

foreach ($entries as $entry) {
$date = $xpath->query('DTUSER',$entry)[0]->nodeValue;
$date = $mysql->real_escape_string(DateTime::createFromFormat('YmdHis.u',$date)->format('Y-m-d'));
$value = $mysql->real_escape_string(-1 * $xpath->query('TRNAMT',$entry)[0]->nodeValue);
$location = $mysql->real_escape_string($xpath->query('MEMO',$entry)[0]->nodeValue);
$fitid = $mysql->real_escape_string($xpath->query('FITID',$entry)[0]->nodeValue);
$budgetID = Budget::getBudgetIDByDateRange($mysql,$date,$date);
		$sql = "SELECT id from transactions where fitid='$fitid'";

		$result = $mysql->query($sql);
		if ($result->num_rows ==0)
		{
		$sql = "INSERT INTO transactions (date,location,value,fitid,budget_id,created_at,account_id) VALUES ('$date','$location','$value','$fitid',$budgetID,'$timestamp','$accountID');";
    echo $sql . PHP_EOL;
		$mysql->query($sql);
		}

}
}
