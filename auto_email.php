<?
    require_once('lib/connections/connect_db.php');
	require_once('lib/connections/ini_set.php');
	
	//get the administrator email
	$sql3 = "SELECT user_email FROM user WHERE user_type=1 AND user_id=1";
	$result3 = mysql_query($sql3) or die(mysql_error());
	$row3 = mysql_fetch_array($result3);
	
    $from = $row3['user_email'];

	$headers = array(
		"From: $from",
		"Content-Type: text/html; charset: UTF-8"
	);	
	
    $text = 'σας υπενθυμίζουμε,<br /> να εισάγετε τις μετρήσεις σας στην εφαρμογή παρακολούθησης μεταβολικού συνδρόμου<br /><br /><a href="http://localhost/final/login">Σύνδεσμος</a> <br /><br /><br /><br /><br /><i>Παρακαλούμε μην απαντήσετε σε αυτό το μήνυμα.</i>';
	
    $subject = 'Email Reminder';
	
    $query = "SELECT u.user_email, p.pat_name, p.pat_surname FROM user as u INNER JOIN patient as p USING (user_id) WHERE p.pat_accept_email='1'";
	$result = mysql_query($query);
	
	if ($result && mysql_num_rows($result)>=1)
	{
		while ($row = mysql_fetch_array($result))
		{
			$to = $row['user_email'];
			$msg = 'Προς: <strong>'.$row['pat_surname'].' '.$row['pat_name'].'</strong>, <br /><br />'.$text.'';
			mail($to, $subject, $msg, implode("\r\n", $headers));
		}
	}
?>