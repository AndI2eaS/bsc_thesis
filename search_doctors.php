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
		
	$search_words = explode(" ",$search);
	$sql = "SELECT * FROM doctor WHERE doc_name LIKE '$search_words[0]%' AND doc_surname LIKE '$search_words[1]%' OR doc_name LIKE '$search_words[1]%' AND doc_surname LIKE '$search_words[0]%'";
	$res = mysql_query($sql);
	
	while($rows = mysql_fetch_array($res))
	{
		echo "<div id='link' onclick='addText(\"".$rows['doc_name']. ' ' .$rows['doc_surname']."\");'>" . $rows['doc_name'] . ' ' . $rows['doc_surname'] . "</div>";
	}
?>