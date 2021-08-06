<?php



function import($file,$accountID)
{
global $mysql;
	  $timestamp = date('Y-m-d H:i:s');

	
	$fp = fopen($file,'r');
	
	while ($line = fgets($fp)){
		if (trim($line)=='Checking Account Transactions'){
			fgets($fp);//The following blank line
			break;
		}
	}
	
	

	
	
	$start =  ftell($fp);
	while ($line = fgets($fp)){
	//	echo $line . PHP_EOL;
		if (trim($line)==''){
			
			break;
		}
		
		fseek($fp,$start);
	
	$date = date('Y-m-d',strtotime(trim(fread($fp,9))));
	$type = fread($fp,16);
	$location = trim(fread($fp,50));
	$amount = trim(fgets($fp));
	$direction = trim(substr($amount,-1));
	$value = preg_replace('/[^0-9.]/','',$amount);
	
	if ($direction == '+'){
		$value = floatval($value) *  -1;
	}
	
	$start =  ftell($fp);
	
	
	
	$sql = "INSERT INTO transactions (date,location,value,fitid,created_at,account_id) VALUES ('$date','$location','$value',null,'$timestamp','$accountID')";
    $mysql->query ( $sql );
	//echo $sql . PHP_EOL;
	
	}
	
	fclose($fp);
	
	
	
	return;
	
	$timestamp = date('Y-m-d H:i:s');
	foreach ( $sheetData as $row )
	{
		$date = date('Y-m-d',strtotime($row['B']));
		$location = $mysql->real_escape_string($row['D']);
		$value = $mysql->real_escape_string($row['E']);
		if ($row['F'] == 'CR')
		{
			$value = $value * -1;
		}

    $budgetID = Budget::getBudgetIDByDateRange($mysql,$date,$date);

		/* @var $result mysqli_result  */
		echo "CHECKING '$date' '$location'  $value <br />";
		$result = $mysql->query("SELECT id from transactions where date = '$date' and location = '$location' and value = $value");
		if ($result->num_rows ==0)
		{
			echo "NOT FOUND INSERTING. <br />";
			$mysql->query("INSERT INTO transactions (date,location,value,budget_id, created_at,account_id) VALUES ('$date','$location','$value',$budgetID,'$timestamp','$accountID')");
		}

	}
}
