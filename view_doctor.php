<?
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/authorization/patient_auth.php');
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
	
	$get_doctor = "SELECT d.user_id FROM patient as p INNER JOIN doctor as d USING (doc_id) WHERE p.user_id = '" . $_SESSION['user_id'] . "'";
	$res_doctor = mysql_query($get_doctor);
	$found = mysql_fetch_array($res_doctor);
	
	header('Location: profile?id='.$found['user_id']);
	
?>