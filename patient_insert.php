<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/ini_set.php');
		
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is an administrator and replace the cookie variables with sessions
	require_once('lib/authorization/admin_auth.php');
	
	require_once('timeout.php');
	
	//initialize the error variables we are going to use for server side data validation
	$error1 = FALSE;
	$error2 = FALSE;
	
	if (isset($_GET))
	{
		//if the admin presses the link provided in the email, we get the variables from the url and provide security
		if (!empty($_GET['name']) && !empty($_GET['surname']) && !empty($_GET['email']) && !empty($_GET['doc_id']) && !empty($_GET['username']))
		{
			$pat_name = mysql_real_escape_string(trim($_GET['name']));
			$pat_surname = mysql_real_escape_string(trim($_GET['surname']));
			$user_email = mysql_real_escape_string(trim($_GET['email']));
			$doc_id = (int)$_GET['doc_id'];
			$username = mysql_real_escape_string(trim($_GET['username']));
		}
	}
	
	if (isset($_POST['submit']))
	{
		$pat_name = mysql_real_escape_string(trim($_POST['pat_name']));
		$pat_surname = mysql_real_escape_string(trim($_POST['pat_surname']));
		$user_email = mysql_real_escape_string(trim($_POST['user_email']));
		$doc_id = (int)$_POST['doc_id'];
		$username = mysql_real_escape_string(trim($_POST['username']));
		$password = mysql_real_escape_string(trim($_POST['password']));
		
		//validation checks and error determination
		if (!empty($pat_name) && !empty($pat_surname) && !empty($user_email) && !empty($doc_id) && !empty($username) && !empty($password)){
			if (!validateLengthName($pat_name) || !validateLengthName($pat_surname) || !validateName($pat_name) || !validateName($pat_surname) || !validateEmail($user_email) || !validateNumber($doc_id))
			{
				$error2 = TRUE;
			}
			else
			{
				//insert the user data into the user table inside the database
				$encrypted_pass = sha1($password);
				$sql1 = "INSERT INTO user VALUES ('', '$username', '$encrypted_pass', '$user_email', '3')";
				$result1 = mysql_query($sql1);
				
				//get the id(user_id) inserted in the user table inside the database, in order to create a foreign key to patient table
				$id = mysql_insert_id();
				
				//insert the patient data into the patient table inside the database
				$sql2 = "INSERT INTO patient VALUES ('', '$doc_id', '$id', '$pat_name', '$pat_surname', '', '','', '', '', '', '', '0')";
				$result2 = mysql_query($sql2); 
				
				//get the administrator email
				$sql3 = "SELECT user_email FROM user WHERE user_type = '" . $_SESSION['user_id'] . "'";
				$result3 = mysql_query($sql3) or die(mysql_error());
				$row3 = mysql_fetch_array($result3);
				
				$to = $user_email;
				$subject = 'Στοιχεία Εισαγωγής';
				$text = 'Προς: <strong>'.$pat_surname.' '.$pat_name.'</strong>, <br /><br />Σας ενημερώνουμε πως η εγγραφή σας στην <a href="http://localhost/final/login">Εφαρμογή Παρακολούθησης - Συμμόρφωσης Ασθενών με Μεταβολικό Σύνδρομο</a> έχει ολοκληρωθεί με επιτυχία.<br /><br />Τα στοιχεία σύνδεσής σας είναι τα εξής: <br /><br /><strong>Όνομα Χρήστη:</strong> '.$username.'<br /><strong>Κωδικός:</strong> '.$password.'<br /><strong>Email:</strong> '.$user_email.'<br /><br />Μπορείτε να αλλάξετε αυτά τα στοιχεία από την επεξεργασία προφίλ μέσω της εφαρμογής.<br /><br /><br /><br /><br /><i>Παρακαλούμε μην απαντήσετε σε αυτό το μήνυμα.</i>';
				$headers = array(
				"From: $row3[user_email]",
				"Content-Type: text/html; charset: UTF-8"
				);
				
				
				//if the queries to the database are successful and the email is sent
				if (mail($to, $subject, $text, implode("\r\n", $headers)))
				{
					$msg = 'Οι διαδικασίες Εγγραφής και Αποστολής Email ολοκληρώθηκαν με επιτυχία!';
				}
				else
				{
					//else show a failure message
					$msg = 'Δυστυχώς η διαδικασία αποστολής Email δεν ολοκληρώθηκε. <br /> <p>Ανανεώστε τη σελίδα για προσπάθεια νέας αποστολής.</p>';
				}
			}
		}
		else{
			$error1 = TRUE;
		}
		
		//close database connection
		mysql_close();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php
			//get the title, and the link to CSS
			require_once('title_head.php');
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script language="javascript" type="text/javascript">
			function randomString()
			{
				var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
				var string_length = 8;
				var randomstring = '';
				for (var i=0; i<string_length; i++)
				{
					var rnum = Math.floor(Math.random() * chars.length);
					randomstring += chars.substring(rnum,rnum+1);
				}
				document.form.password.value = randomstring;
				document.form.password.focus();
			}
		</script>
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function()
			{
			 	//check for empty fields
				jQuery("#pat_name").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_surname").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#user_email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_id").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#username").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#password").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the email address
                jQuery("#user_email").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/)) return true; else return false;",
                    message: "Εισάγετε μια έγκυρη μορφή Email"
                });
				
				//validate the characters of name
				jQuery("#pat_name").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the name
				jQuery("#pat_name").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
                
				//validate the characters of the surname
                jQuery("#pat_surname").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the surname
				jQuery("#pat_surname").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
				
				//validate the doc_id field
				jQuery("#doc_id").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
			});
		</script>
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Εγγραφή Ασθενή';
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
							<form id="form" name="form" action="" method="POST">
								<table border="0">
									<tr>
										<td><label for="pat_name">Όνομα</label>&nbsp;</td>
										<td><input type="text" id="pat_name" name="pat_name" value="<?php if(isset($pat_name)) echo $pat_name; ?>"/></td>
									</tr>
									<tr>
										<td><label for="pat_surname">Επώνυμο</label>&nbsp;</td>
										<td><input type="text" id="pat_surname" name="pat_surname" value="<?php if(isset($pat_surname)) echo $pat_surname; ?>"/></td>
									</tr>
									<tr>
										<td><label for="user_email">Email</label>&nbsp;</td>
										<td><input type="text" id="user_email" name="user_email" value="<?php if(isset($user_email)) echo $user_email; ?>"/></td>
									</tr>
									<tr>
										<td><label for="doc_id">Αναγνωριστικό Ιατρού</label>&nbsp;</td>
										<td><input type="text" id="doc_id" name="doc_id" value="<?php if(isset($doc_id)) echo $doc_id; ?>" size="2"/></td>
									</tr>
									<tr>
										<td><label for="username">Όνομα Χρήστη</label>&nbsp;</td>
										<td><input type="text" id="username" name="username" value="<?php if(isset($username)) echo $username; ?>"/></td>
									</tr>
									<tr>
										<td><label for="password">Κωδικός</label>&nbsp;</td>
										<td><input type="password" id="password" name="password"/></td>
									</tr>
									<tr>
										<td></td>
										<td><br /><input type="button" id="submit" value="Τυχαίος Κωδικός" onClick="randomString();" /></td>
									</tr>
									<tr>
										<td><br /><br /><br /><br /></td>
										<td>
										<input type="reset" id="submit" name="reset" value="Καθαρισμός"/>
										<input type="submit" id="submit" name="submit" value="Εγγραφή"/>
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
										<?php if(empty($pat_name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($pat_surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif ?>
										<?php if(empty($user_email)):?>
											<li><strong>Πεδίο Email:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($doc_id)):?>
											<li><strong>Πεδίο Αναγνωριστικό Ιατρού:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($username)):?>
											<li><strong>Πεδίο Όνομα Χρήστη:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										<?php if(empty($password)):?>
											<li><strong>Πεδίο Κωδικός:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
										<?php endif?>
										
									</ul>
								<?php
								}
									
								if ($error2)
								{
								//server side validation
								?>
									<ul>
										<?if(!validateLengthName($pat_name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
										<?endif?>
										<?if(!validateLengthName($pat_surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
										<?endif?>
										<?if(!validateName($pat_name)):?>
											<li><strong>Πεδίο Όνομα:</strong> Επιτρέπονται μόνο γράμματα</li>
										<?endif?>
										<?if(!validateName($pat_surname)):?>
											<li><strong>Πεδίο Επώνυμο:</strong> Επιτρέπονται μόνο γράμματα</li>
										<?endif?>
										<?if(!validateEmail($user_email)):?>
											<li><strong>Πεδίο Email:</strong> Εισάγετε μια έγκυρη μορφή Email</li>
										<?endif?>
										<?if(!validateNumber($doc_id)):?>
											<li><strong>Πεδίο Αναγνωριστικό Ιατρού:</strong> Επιτρέπονται μόνο αριθμοί</li>
										<?endif?>
										
								</ul>
								<?php	
								}
								?>
							</div>
					<?php
						}
					?>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>