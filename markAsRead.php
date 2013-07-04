<?php
	require_once('lib/functions/functions.php');
	require_once ('lib/connections/connect_db.php');
	
	ses_name();
	
	require_once('timeout.php');
	
	$mes_id = (int)$_POST['mes_id']; 
	$owner_id = (int)$_POST['owner_id'];
	
	if ($owner_id != $_SESSION['user_id']) 
	{
		exit(); //exit because there might be malicious activity
	} 
	else 
	{
		$query = "UPDATE private_messages SET opened='1' WHERE mes_id='$mes_id' LIMIT 1"; 
		$res = mysql_query($query);
	}
?>