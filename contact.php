<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/ini_set.php');
	
	if (loggedin()) 
	{
		//if the user is logged in check the session inactivity
		require_once('timeout.php');
	}
	
	if (isset($_POST['submit']))
	{
		$text = mysql_real_escape_string(htmlentities($_POST['text']));
		$name = mysql_real_escape_string(htmlentities($_POST['name']));
		$subject = mysql_real_escape_string($_POST['subject']);
		$email = mysql_real_escape_string($_POST['email']);
		
		//specify the headers that will be sent with the email
		$headers = array(
		"From: $email",
		"Content-Type: text/html; charset: UTF-8"
		);
		
		//select the admin's email
		$sql1 = "SELECT user_email FROM user WHERE user_type = '1' LIMIT 1";
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		
		//email elements
		$to = $row1['user_email'];
		$message = 'Από: '.$name.'<br /><br />'.$text;
		
		if (!empty($text) && !empty($name) && !empty($subject) && !empty($email))
		{  
			if (!validateEmail($email))
			{
				$msg = 'Πρέπει να εισάγετε μια έγκυρη μορφή Email';
			}
			else 
			{
				if (mail($to, $subject, $message, implode("\r\n", $headers)))
				{
					$msg1 = 'Σας ενημερώνουμε ότι το μήνυμα σας στάλθηκε επιτυχώς!';
				}
				//else show him a failure message
				else
				{
					$msg1 = 'Δυστυχώς διαδικασία δεν ολοκληρώθηκε. <br /> <p><a href="contact">Προσπαθήστε ξανά</a></p>';
				}
			}
		}
		else
		{
			$msg = 'Πρέπει να συμπληρώσετε όλα τα πεδία.';
		}
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
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function()
			{
			 	//check for empty fields
				jQuery("#name").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#text").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#subject").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the email address
                jQuery("#email").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/)) return true; else return false;",
                    message: "Εισάγετε μια έγκυρη μορφή Email"
                });
				
				//validate the characters of name
				jQuery("#name").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the name
				jQuery("#name").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
            });
		</script>
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header and set the page title value
				$page_title = 'Επικοινωνία';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<?php
						if (!empty($msg1))
						{
							echo '<div align="center">';
							echo '<br /><br /><br /><p>'.$msg1.'</p>';
							echo '</div>';
						}
						else
						{
					?>
						<h4>Φόρμα Επικοινωνίας</h4>
								<?php
									if (loggedin())
									{
										$sql = "SELECT user_email FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'";
										$result = mysql_query($sql);
										$row = mysql_fetch_array($result);
									}
								?>
								<br />
								<br />
								<form id="form" action="" method="POST">
									<table border="0">
										<tr>
											<td><label for="name">Όνομα</label>&nbsp;</td>
											<td><input type="text" id="name" name="name" value="<?php if(isset($name)) echo $name; ?>"/> <br /><br /></td>
										</tr>
										<tr>
											<td><label for="email">Email</label>&nbsp;</td>
											<td><input type="text" id="email" name="email" value="<?php if(isset($row['user_email'])) { echo $row['user_email']; } ?>"/><br /><br /></td>
										</tr>
										<tr>
											<td><label for="subject">Θέμα</label>&nbsp;</td>
											<td><input type="text" id="subject" name="subject" value="<?php if(isset($subject)) echo $subject; ?>"/><br /><br /></td>
										</tr>
										
										<tr>
											<td><label for="text">Μήνυμα</label>&nbsp;</td>
											<td>
												<textarea id="text" name="text" rows="10" cols="39"><?php if(isset($text)) echo $text; ?></textarea>
											</td>
										</tr>
										<tr>
											<td><br /><br /><br /></td>
											<td>
												<input type="submit" id="submit" name="submit" value="Αποστολή"/>
											</td>
										</tr>
									</table>
									<br />
								</form>
								<?php
							if(!empty($msg))
							{
								//if the variable msg is not empty, reveal its content
								echo '<div align="center">';
								echo '<p class="error">'.$msg.'</p>';
								echo '</div>';
							}
						}
					?>
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>