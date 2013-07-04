<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/pathvars.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
	
	if(isset($_GET['id']))
	{
		$file_id = (int)$_GET['id'];
		
		$file = mysql_query("SELECT rep_file FROM doctor_report WHERE rep_id='$file_id'");
		
		if (mysql_num_rows($file) != 1)
		{
			die('Λάθος αναγνωριστικό αρχείου');
		}
		else
		{
			$row = mysql_fetch_array($file);
			
			$path = D_UPLOADPATH . $row['rep_file'];
			
			header('Content-Type: application/octetstream');
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			header("Content-Disposition: attachment; filename=\"$row[rep_file]\"");
			header('Content-Length: ' . filesize($path));
			
			readfile($path);
		}
	}
?>