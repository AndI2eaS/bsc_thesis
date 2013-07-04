<?php
	require_once("lib/functions/functions.php");
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/ini_set.php');
	
	
	if (isset($_POST['submit']))
	{
		$email = mysql_real_escape_string(trim($_POST['email']));
		$username = mysql_real_escape_string(trim($_POST['username']));
		
		$sql = mysql_query("SELECT user_id, username, password, user_email FROM user WHERE user_email = '".$email."' AND  username = '" . $username . "'");
		$num = mysql_num_rows($sql);
		$row = mysql_fetch_array($sql);
		
		if ($num==1)
		{
			$temp_password = rand(000000,999999);
			$temp_pass = sha1($temp_password);
			
			$update = mysql_query("UPDATE user SET password='".$temp_pass."' WHERE user_email='".$email."' AND username = '" . $username . "'");
			
			if ($update)
			{
				$adm = mysql_query("SELECT user_email FROM user WHERE user_id = '1'");
				$row1 = mysql_fetch_array($adm);
				$admin_email = $row1['user_email'];
				$headers = array(
				"From: $admin_email",
				"Content-Type: text/html; charset: UTF-8"
				);
		
				$to = $row['user_email'];
				$subject = "Στοιχεία εισαγωγής";
				$message = 'Ζητήσατε δημιουργία νέου κωδικού για την σύνδεσή σας στην <a href="http://localhost/final">Εφαρμογή Παρακολούθησης - Συμμόρφωσης Ασθενών με Μεταβολικό Σύνδρομο</a>.<br /><br />Μπορείτε να κάνετε είσοδο με τα στοιχεία:<br />Όνομα Χρήστη: '.$row['username'].'<br />Κωδικός: '.$temp_password.'<br /><br />Προτείνεται να αλλάξετε κωδικό από το προφίλ σας αφού συνδεθείτε, για λόγους ασφαλείας.<br /><br /><br /><br /><br /><i>Παρακαλούμε μην απαντήσετε σε αυτό το μήνυμα</i>';
				
				if (mail($to, $subject, $message, implode("\r\n", $headers)))
				{
					$msg = 'Σας ενημερώνουμε η διαδικασία ολοκληρώθηκε<br /> και μπορείτε να συνδεθείτε με το νέο κωδικό!';
				}
				//else show him a failure message
				else
				{
					$error = 'Δυστυχώς διαδικασία δεν ολοκληρώθηκε. <br />';
				}
			}
		}
		else
		{
			$error = 'Τα στοιχεία που έχετε εισάγει δεν βρέθηκαν';
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
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function()
			{
			 	//check for empty fields
				jQuery("#email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#username").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the email format
				jQuery("#email").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/)) return true; else return false;",
                    message: "Εισάγετε μια έγκυρη μορφή Email"
                });
			});
		</script>
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Υπενθύμιση Κωδικού';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<?php
						if (!empty($msg))
						{
							echo '<br /><br /><br />';
							echo '<center>';
							echo $msg;
							echo '</center>';
							}
						else
						{
					?>
							<h4>Εισάγετε τα στοιχεία σας για αποστολή νέου κωδικού</h4>
								<!-- The login form -->
								<br /><br />
								<form id="form" action="" method="POST">
									<table>
										<tr>
											<td><label for="username">Όνομα Χρήστη</label></td>
											<td><input type="text" id="username" name="username"/></td>
										</tr>
										<tr>
											<td><label for="email">Email</label></td>
											<td><input type="text" id="email" name="email" /></td>
										</tr>
										<tr>
											<td></td>
											<td><br /><input type="submit" id="submit" name="submit" value="Αποστολή"></td>
										</tr>
									</table>
								</form>
								
								<br /><br />
								
								<div id="error">
									<?php
										if (!empty($error))
										{
											echo $error;
										}
									?>
								</div>
						<?php
						}
						?>
				</div> <!-- end right div -->
			<?php
				//insert the page footer and navigation menu stuff
				require_once("footer.php");
			?>
