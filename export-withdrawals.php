<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$currentdate = date('Y-m-d');

$sql = "SELECT u.name, u.mobile,u.email,CONCAT(',', u.account_num, ',') AS account_num, u.ifsc, u.bank, u.branch
FROM users u JOIN  withdrawals w ON u.id = w.user_id";
	$db->sql($sql);
	$developer_records = $db->getResult();
	
	$filename = "withdrawals-data".date('Ymd') . ".xls";			
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"$filename\"");	
	$show_coloumn = false;
	if(!empty($developer_records)) {
	  foreach($developer_records as $record) {
		if(!$show_coloumn) {
		  // display field/column names in first row
		  echo implode("\t", array_keys($record)) . "\n";
		  $show_coloumn = true;
		}
		echo implode("\t", array_values($record)) . "\n";
	  }
	}
	exit;  
?>
