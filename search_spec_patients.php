<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
	
	$search = mysql_real_escape_string($_POST['search']);
	$doc_id = (int)$_POST['doc_id'];
		
	$search_words = explode(" ",$search);
	$sql = "SELECT * FROM patient WHERE pat_name LIKE '$search_words[0]%' AND pat_surname LIKE '$search_words[1]%' AND doc_id='$doc_id' OR pat_name LIKE '$search_words[1]%' AND pat_surname LIKE '$search_words[0]%' AND doc_id='$doc_id'";
	$res = mysql_query($sql);
	
	while($rows = mysql_fetch_array($res))
	{
		echo "<div id='link' onclick='addText(\"".$rows['pat_name']. ' ' .$rows['pat_surname']."\");'>" . $rows['pat_name'] . ' ' . $rows['pat_surname'] . "</div>";
	}
?>