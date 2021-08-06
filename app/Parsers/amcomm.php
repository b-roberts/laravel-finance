<?php
function import($file,$accountID)
{
global $mysql;
	$objReader = new PHPExcel_Reader_CSV ();
	$objPHPExcel = $objReader->load ($file );
	$sheetData = $objPHPExcel->getActiveSheet ()->toArray ( null, true, true, true );
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
