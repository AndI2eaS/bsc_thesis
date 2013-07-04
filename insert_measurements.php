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
	require_once('lib/authorization/patient_auth.php');
	
	require_once('timeout.php');
	
	if (isset($_POST['submit']))
	{
		$glikozi = mysql_real_escape_string(trim($_POST['glikozi']));
		$varos = mysql_real_escape_string(trim($_POST['varos']));
		$perifereia_mesis = mysql_real_escape_string(trim($_POST['perifereia_mesis']));
		$sistoliki_piesi = mysql_real_escape_string(trim($_POST['sistoliki_piesi']));
		$diastoliki_piesi = mysql_real_escape_string(trim($_POST['diastoliki_piesi']));
		
		//get the doctor's and patient's id
		$get_doctor = mysql_query("SELECT d.user_id, p.pat_id, p.pat_sex, p.pat_name, p.pat_surname FROM doctor as d INNER JOIN patient as p USING(doc_id) WHERE p.user_id = '" . $_SESSION['user_id'] . "'");
		$doctor = mysql_fetch_array($get_doctor);
		
		//get the doctor's email
		$get_doc_email = mysql_query("SELECT user_email FROM user WHERE user_id = '" . $doctor['user_id'] . "'");
		$doc_email = mysql_fetch_array($get_doc_email);
		
		//get the patient's email
		$get_pat_email = mysql_query("SELECT user_email FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'");
		$pat_email = mysql_fetch_array($get_pat_email);
		
		//insert the values into the database 
		$date = date('Y-m-d');
		$pat_id = $doctor['pat_id'];
		
		//if the patient has already inserted his measurements for today
		$get_measurements = mysql_query("SELECT * FROM measurements WHERE m_date='$date' AND pat_id='$pat_id'");
		if (mysql_num_rows($get_measurements) >0)
		{
			$msg1 = 'Έχετε ήδη εισάγει τις μετρήσεις σας για σήμερα.';
		}
		else
		{
			//if some of the measurements uploaded exceed some low-high limits send an email to the doctor
			if ($glikozi<70 || $glikozi>200 || $sistoliki_piesi<80 || $sistoliki_piesi>140 || $diastoliki_piesi<50 || $diastoliki_piesi>95)
			{
				if ($doctor['pat_sex'] == 'Α')
				{
					$suffix = 'Ο ';
				}
				else if ($doctor['pat_sex'] == 'Θ')
				{
					$suffix = 'Η ';
				}
				
				$patient_email = $pat_email['user_email'];
				$doctor_email = $doc_email['user_email'];
				
				$headers = array(
				"From: $patient_email",
				"Content-Type: text/html; charset: UTF-8"
				);
				$to = $doctor_email;
				$message = $suffix.'ασθενής '.$doctor['pat_surname'].' '.$doctor['pat_name'].',<br /><br /> εισήγαγε ένα νέο σύνολο μετρήσεων στο οποίο κάποιες τιμές που εισήχθησαν είναι κοντά σε άνω ή κάτω όρια και πρέπει να ελεγχθούν άμεσα. <br/> Μπορείτε να δείτε τις μετρήσεις εφόσον συνδεθείτε μέσω του συνδέσμου: <a href="http://localhost/final/measurements?id='.$_SESSION['user_id'].'">Μετρήσεις</a>';
				$subject = 'Measurements close to low-high limits';
				
				mail($to, $subject, $message, implode("\r\n", $headers));
			}
			
			//insert the measurements
			$insert_met = mysql_query("INSERT INTO measurements VALUES ('', '$pat_id', '$date', '$glikozi', '$varos', '$perifereia_mesis', '$sistoliki_piesi', '$diastoliki_piesi')");
			
			$message = 'Εισήγαγα ένα νέο σύνολο μετρήσεων το οποίο μπορείτε να δείτε μέσω του συνδέσμου: http://localhost/final/measurements?id='.$_SESSION['user_id'].'';
			$pm = htmlentities($message);
			$pm = mysql_real_escape_string($message);
			$to_id = $doctor['user_id'];
			$from_id = $_SESSION['user_id'];
			
			$send_pm = mysql_query("INSERT INTO private_messages VALUES ('', '$to_id', '$from_id', now(), 'Μετρήσεις', '$pm', '0', '0', '0')");
			
			if ($send_pm)
			{
				$success_message = 'Η διαδικασία ολοκληρώθηκε με επιτυχία.';
			}
			else
			{
				$msg = 'Η διαδικασία δεν ολοκληρώθηκε. <br /> <a href="insert_measurements">Προσπαθείστε ξανά</a>';
			}
		}
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
		<style type="text/css">
			input[type="text"]{
				width: 30px;
				padding: 10px;
				color: #484848;
				font-family: Arial,  Verdana, Helvetica, sans-serif;
				font-size: 14px;
				border: 1px solid #cecece;
			}
			
			.right {
				width: 400px;
				float: right;
				color: #797979;
				font-weight: 700;
				line-height: 1.4em;
				margin-left: 80px;
				margin-right: 0px;
			}

		</style>
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function()
			{
			 	//check for empty fields
				jQuery("#glikozi").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#varos").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#perifereia_mesis").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#sistoliki_piesi").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#diastoliki_piesi").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the field
				jQuery("#glikozi").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the field
				jQuery("#varos").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the field
				jQuery("#perifereia_mesis").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the field
				jQuery("#sistoliki_piesi").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the field
				jQuery("#diastoliki_piesi").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//specify the range of values
				jQuery("#glikozi").validate({
					expression: "if (VAL>50 && VAL<500) return true; else return false;",
					message: "Μη αποδεκτή τιμή"
				});
				
				//specify the range of values
				jQuery("#varos").validate({
					expression: "if (VAL>0 && VAL<350) return true; else return false;",
					message: "Μη αποδεκτή τιμή"
				});
				
				//specify the range of values
				jQuery("#perifereia_mesis").validate({
					expression: "if (VAL>0 && VAL<200) return true; else return false;",
					message: "Μη αποδεκτή τιμή"
				});
				
				//specify the range of values
				jQuery("#sistoliki_piesi").validate({
					expression: "if (VAL>60 && VAL<200) return true; else return false;",
					message: "Μη αποδεκτή τιμή"
				});
				
				//specify the range of values
				jQuery("#diastoliki_piesi").validate({
					expression: "if (VAL>40 && VAL<130) return true; else return false;",
					message: "Μη αποδεκτή τιμή"
				});
			});
		</script>
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Εισαγωγή Μετρήσεων';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<?php
						if (!empty($msg1))
						{
							echo '<br /><br />';
							echo '<p class="error">'.$msg1.'</p>';
						}
						else if (!empty($success_message))
						{
							echo '<br /><br />';
							echo '<p>'.$success_message.'</p>';
						}
						else if (!empty($msg))
						{
							echo '<br /><br />';
							echo '<p class="error">'.$msg.'</p>';
						}
						else
						{
					?>
					<center>
						<h4>Εισάγετε τις μετρήσεις για <br />αποστολή στον ιατρό:</h4>
					</center>
					<form id="form" action="" method="POST">
						<table border="0">
							<tr>
								<td><label for="glikozi">Γλυκόζη (mg/dl)</label>&nbsp;</td>
								<td><input type="text" id="glikozi" name="glikozi" value="<?php if(isset($glikozi)) echo $glikozi; ?>"/> </td>
							</tr>
							<tr>
								<td><label for="varos">Βάρος (kg)</label>&nbsp;</td>
								<td><input type="text" id="varos" name="varos" value="<?php if(isset($varos)) { echo $varos; } ?>"/> </td>
							</tr>
							<tr>
								<td><label for="perifereia_mesis">Περιφέρεια Μέσης (cm)</label>&nbsp;</td>
								<td><input type="text" id="perifereia_mesis" name="perifereia_mesis" value="<?php if(isset($perifereia_mesis)) echo $perifereia_mesis; ?>"/> </td>
							</tr>
							<tr>
								<td><label for="sistoliki_piesi">Συστολική Πίεση (mmHg)</label>&nbsp;</td>
								<td><input type="text" id="sistoliki_piesi" name="sistoliki_piesi" value="<?php if(isset($sistoliki_piesi)) echo $sistoliki_piesi; ?>"/> </td>
							</tr>
							<tr>
								<td><label for="diastoliki_piesi">Διαστολική Πίεση (mmHg)</label>&nbsp;</td>
								<td><input type="text" id="diastoliki_piesi" name="diastoliki_piesi" value="<?php if(isset($diastoliki_piesi)) echo $diastoliki_piesi; ?>"/> </td>
							</tr>
							<tr>
								<td><br /><br /><br /></td>
								<td>
									<input type="submit" id="submit" name="submit" value="Εισαγωγή"/>
								</td>
							</tr>
						</table>
					</form>
					<?php
					}
					?>
				</div>
			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>