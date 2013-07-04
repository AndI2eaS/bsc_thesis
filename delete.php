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
	
	if (isset($_GET['id']))
	{ 
		$secureid = (int)$_GET['id'];
	}
	
	if (isset($_POST['submit']))
	{
		$user_id = (int)$_POST['user_id'];
		
		$sql = mysql_query("SELECT pat_name, pat_surname, pat_picture FROM patient WHERE user_id = '$user_id'");
		$row = mysql_fetch_array($sql);
		
		if (!empty($row['pat_picture'])) 
		{
			unlink(MM_UPLOADPATH . $row['pat_picture']);
		}
		//delete the user from the patient table
		$delete1 = mysql_query("DELETE FROM patient WHERE user_id = '$user_id'");
		//delete user's private messages
		$delete2 = mysql_query("DELETE FROM private_messages WHERE from_id = '$user_id'");
		//delete the user from the user table
		$delete3 = mysql_query("DELETE FROM user WHERE user_id = '$user_id'");
		
		$to_id = 1;
		$from_id = 1;
		$pm_subject = 'Διαγραφή Χρήστη';
		$pm_text = 'Ο χρήστης '.$row['pat_surname'].' '.$row['pat_name'].' διαγράφηκε από την εφαρμογή';
		
		$inform = mysql_query("INSERT INTO private_messages (to_id, from_id, time_sent, subject, message) VALUES ('$to_id', '$from_id', now(), '$pm_subject', '$pm_text')");
		
		if ($_SESSION['user_id'] != $user_id)
		{
			header('Location: login');
			exit();
		}
		else
		{
			header('Location: logout');
			exit();
		}
	}
	
	if (isset($_POST['cancel']))
	{
		header('Location: login');
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php 
			//get the title and the link to CSS
			require_once('title_head.php'); 
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header and set the page title value
				$page_title = 'Διαγραφή';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<br />
					<br />
					Θέλετε σίγουρα να διαγράψετε το προφίλ; 
					<br /><p class="error">Προειδοποίηση: Όλα τα δεδομένα στην εφαρμογή θα χαθούν και δεν θα είναι δυνατό να ανακτηθούν.</p>
					<form id="form" action="" method="POST">
						<input type="hidden" name="user_id" value="<?php echo $secureid; ?>">
						<input type="submit" id="submit" name="cancel" value="Ακύρωση">
						<input type="submit" id="submit" name="submit" value="Επιβεβαίωση">
					</form>
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>
