<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/ini_set.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is a doctor and replace the cookie variables with sessions
	require_once('lib/authorization/doctor_auth.php');
	
	require_once('timeout.php');
	
	//initialize the error variables we are going to use for server side data validation
	$error1 = FALSE;
	$error2 = FALSE;
	
	//the submit button has been pressed
	if (isset($_POST['submit']))
	{
		//get the post data and provide some security
		$name = mysql_real_escape_string(trim($_POST['name']));
		$surname = mysql_real_escape_string(trim($_POST['surname']));
		$email = mysql_real_escape_string(trim($_POST['email']));
		$username = mysql_real_escape_string(trim($_POST['username']));
		
		//write and execute a sql query in order to get the doctor's data
		$sql1 = "SELECT d.doc_name, d.doc_surname, d.doc_id, u.user_email FROM doctor as d INNER JOIN user as u USING (user_id) WHERE user_id='" . $_SESSION['user_id'] . "'";
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		
		//specify the headers that will be sent with the email
		$headers = array(
		"From: $row1[user_email]",
		"Content-Type: text/html; charset: UTF-8"
		);
		
		//select the admin's email
		$sql3 = "SELECT user_email FROM user WHERE user_type = '1' LIMIT 1";
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		
		//email elements
		$to = $row3['user_email'];
		$subject = 'Αίτηση Εισαγωγής Ασθενή';
		$text = '
		Ο ιατρός με στοιχεία: <br /><br />
		<strong>Όνομα:</strong> '.$row1['doc_name'].'<br />
		<strong>Επώνυμο:</strong> '.$row1['doc_surname'].'<br />
		<strong>Αναγνωριστικός Αριθμός Ιατρού:</strong> '.$row1['doc_id'].' ,<br /><br />
		
		επιθυμεί να εγγράψει στο σύστημα ένα νέο ασθενή με στοιχεία, <br /><br />
		<strong>Όνομα:</strong> '.$name.' <br />
		<strong>Επώνυμο:</strong> '.$surname.'<br />
		<strong>Email:</strong> '.$email.'<br />
		<strong>Όνομα Χρήστη:</strong> '.$username.'<br />
		<p>Για να συνεχίσετε με την εγγραφή, εφόσον συνδεθείτε στην εφαρμογή, μπορείτε να επιλέξετε αυτό το  <a href="http://localhost/final/patient_insert?name='.$name.'&surname='.$surname.'&email='.$email.'&doc_id='.$row1['doc_id'].'&username='.$username.'">σύνδεσμο</a> ή να κάνετε την εγγραφή χειροκίνητα από την εφαρμογή επιλέγοντας στο μενού: <a href="http://localhost/final/patient_insert">Εγγραφή Ασθενή</a>.</p>
		';
		
		//validation checks and error determination
		if (!empty($name) && !empty($surname) && !empty($email) && !empty($username)){
			if (!validateLengthName($name) || !validateLengthName($surname) || !validateName($name) || !validateName($surname) || !validateEmail($email))
			{
				$error2 = TRUE;
			}
			else
			{
				//if the username entered, does not exist in the database
				if (!uniqueEmail($email) && !uniqueUser($username))
				{
					//If success, show a message to the doctor
					if (mail($to, $subject, $text, implode("\r\n", $headers))){
						$msg = 'Σας ενημερώνουμε ότι η αίτηση ολοκληρώθηκε επιτυχώς!';
					}
					//else show him a failure message
					else
					{
						$msg = 'Δυστυχώς διαδικασία δεν ολοκληρώθηκε. <br /> <p>Προσπαθήστε ξανά ή επικοινωνήστε με το διαχειριστή <a href="contact">εδώ</a>.</p>';
					}
				}
				else
				{
					if (uniqueUser($username))
					{
						$msg = 'Αυτό το όνομα χρήστη υπάρχει ήδη! <br /><a href="new_patient">Δοκιμάστε ξανά</a>';
					}
					else if (uniqueEmail($email))
					{
						$msg = 'Αυτό το Email υπάρχει ήδη! <br /><a href="new_patient">Δοκιμάστε ξανά</a>';
					}
				}
			}
		}
		else{
			$error1 = TRUE;
		}
		
		//close the database connection
		mysql_close();
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
				jQuery("#surname").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
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
                
				//validate the characters of the surname
                jQuery("#surname").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the surname
				jQuery("#surname").validate({
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
				$page_title = 'Εγγραφή Νέου Ασθενή';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<?php
						if(!empty($msg))
						{
							//if the variable msg is not empty, reveal its content
							echo '<div align="center">';
							echo '<br /><br /><br /><p class="error">'.$msg.'</p>';
							echo '</div>';
						}
						else
						{
					?>
							<h4>Εισάγετε τα στοιχεία του χρήστη <br /> προς εγγραφή: </h4>
							<br />
							<br />
							<form id="form" action="" method="POST">
								<table border="0">
									<tr>
										<td><label for="name">Όνομα</label>&nbsp;</td>
										<td><input type="text" id="name" name="name" value="<?php if(isset($name)) echo $name; ?>"/></td>
									</tr>
									<tr>
										<td><label for="surname">Επώνυμο</label>&nbsp;</td>
										<td><input type="text" id="surname" name="surname" value="<?php if(isset($surname)) echo $surname; ?>"/></td>
									</tr>
									<tr>
										<td><label for="email">Email</label>&nbsp;</td>
										<td><input type="text" id="email" name="email" value="<?php if(isset($email)) echo $email; ?>"/></td>
									</tr>
									<tr>
										<td><label for="username">Όνομα Χρήστη</label>&nbsp;</td>
										<td><input type="text" id="username" name="username" value="<?php if(isset($username)) echo $username; ?>"/></td>
									</tr>
									
									<tr>
										<td><br /><br /><br /></td>
										<td>
										<input type="reset" id="submit" name="reset" value="Καθαρισμός"/>
										<input type="submit" id="submit" name="submit" value="Αίτηση"/>
										</td>
									</tr>
								</table>
								<br />
							</form>
							<div id="error">
								<?php
								if ($error1)
								{
								//server side validation
								?>
									<ul>
										<?php if(empty($name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif ?>
										<?php if(empty($email)):?>
											<li><strong>Πεδίο Email:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($username)):?>
											<li><strong>Πεδίο Όνομα Χρήστη:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
									</ul>
								<?php
								}
									
								if ($error2)
								{
								//server side validation
								?>
									<ul>
										<?if(!validateLengthName($name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
										<?endif?>
										<?if(!validateLengthName($surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
										<?endif?>
										<?if(!validateName($name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Επιτρέπονται μόνο γράμματα</li>
										<?endif?>
										<?if(!validateName($surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Επιτρέπονται μόνο γράμματα</li>
										<?endif?>
										<?if(!validateEmail($email)):?>
											<li><strong>Πεδίο Email:</strong> Εισάγετε μια έγκυρη μορφή Email</li>
										<?endif?>
								</ul>
								<?php	
								}
								?>
							</div> <!-- end error div -->
					<?php
						}
					?>
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>
	</body>
</html>