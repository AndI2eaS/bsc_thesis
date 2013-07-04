<?php
	//we do not need to include the functions.php file because it is already included in the file we call this file
	header('Content-type: text/html; charset=UTF-8');
	
	//set the session variables if the cookies are set
	ses_name();
	
	if ($_SESSION['user_type'] != 2)
	{
		//if the user type does not equal to 2 (=doctor), kill the script and echo message, else allow him to see the content
		die('Δεν έχετε δικαίωμα να δείτε αυτή τη σελίδα.<br /> Επιστρέψτε <a href="login.php">εδώ</a>.');
	}
?>