<?php
	require_once("lib/functions/functions.php");
	require_once('lib/connections/connect_db.php');
	
	if (loggedin())
	{ //using the function loggedin from functions.php we check if it TRUE, from the value returned. 
		ses_name();
		
		if ($_SESSION['user_type'] == 1)
		{
			header('Location: admin');
			exit();
		}
		else if ($_SESSION['user_type'] == 2)
		{
			header('Location: doctor');
			exit();
		}
		else if ($_SESSION['user_type'] == 3)
		{
			header('Location: patient');
			exit();
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php
			//get the page title, and the link to CSS
			require_once('title_head.php');
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header
				$page_title = '';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<center>
						<br /><br />
						Η Εφαρμογή Παρακολούθησης - Συμμόρφωσης Ασθενών με Μεταβολικό Σύνδρομο δίνει τη<br /><br /> δυνατότητα στους ιατρούς να παρακολουθούν τους ασθενείς τους μέσω του διαδικτύου.<br /><br />  Αυτόγίνεται εφικτό εφόσον οι ασθενείς ανεβάσουν μόνοι τους τις απαραίτητες μετρήσεις στο<br /><br /> σύστημα. Οι ιατροί έχουν τότε τη δυνατότητα να παρατηρήσουν αυτές τις μετρήσεις με <br /><br />διαγράματα ή πίνακες τιμών και να συντάξουν αναφορά ως ανατροφοδότηση προς τους<br /><br />ασθενείς τους.
					
				
					</center>
				</div> <!-- end right div -->
			<?php
				//insert the page footer and navigation menu stuff
				require_once("footer.php");
			?>