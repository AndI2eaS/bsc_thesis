<?
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	ses_name();
	
	//query the database for new messages
	$check_pm = "SELECT mes_id FROM private_messages WHERE to_id='" . $_SESSION['user_id'] . "' AND opened='0' LIMIT 1";
	$res = mysql_query($check_pm) or die(mysql_error());
	
	if (mysql_num_rows($res)>=1)
	{
		//if we have results returned show blinked envelope
		echo '<a href="pm_inbox">Προσωπικά Μηνύματα <img src="images/pm2.gif" width="18" height="11" /></a>';
	}
	else
	{
		//else show just a simple envelope
		echo '<a href="pm_inbox">Προσωπικά Μηνύματα <img src="images/pm1.gif" width="18" height="11"/></a>';
	}
?>