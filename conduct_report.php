<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/pathvars.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is a doctor and replace the cookie variables with sessions
	require_once('lib/authorization/doctor_auth.php');
	
	require_once('timeout.php');
	
	if (isset($_GET['pat_id']) && isset($_GET['doc_id']))
	{
		$p_id = (int)$_GET['pat_id'];
		$d_id = (int)$_GET['doc_id'];
	}
	
	if (isset($_POST['insert']))
	{
		$rep_text = htmlentities($_POST['rep_text']);
		$rep_text = mysql_real_escape_string($_POST['rep_text']);
		$pat_id = (int)$_POST['pat_id'];
		$doc_id = (int)$_POST['doc_id'];
		$rep_file = $_FILES['rep_file']['name'];
		$rep_file_size = $_FILES['rep_file']['size'];
		$rep_file_type = $_FILES['rep_file']['type'];
		$date = date('Y-m-d');
		
		$get_reports = mysql_query("SELECT * FROM doctor_report WHERE rep_date='$date' AND pat_id='$pat_id'");
		if (mysql_num_rows($get_reports) >0)
		{
			$msg1 = 'Έχετε ήδη συντάξει την αναφορά για τον συγκεκριμένο ασθενή σήμερα.';
		}
		else
		{
			$allowed = array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword", "application/pdf");
			$error = false;
			$max_size = 4194304;
			if (!empty($rep_file))
			{
				//check the type of the file
				if (($rep_file_size<=$max_size) && (in_array($rep_file_type,$allowed)))
				{
					//if nothing is wrong with the file and the upload process
					
						//move the file to the target upload folder.
						$random_digit=rand(000000,999999);
						$new_rep_file = $random_digit.$rep_file;
						
						$target = D_UPLOADPATH . basename($new_rep_file);
						move_uploaded_file($_FILES['rep_file']['tmp_name'], $target);
						
						if (!move_uploaded_file($_FILES['rep_file']['tmp_name'], $target))
						{
							//the new picture file move failed, so delete the temporary file and set the error flag
							@unlink($_FILES['rep_file']['tmp_name']);
							$error = true;
							$fail_file = 'Υπήρξε ένα πρόβλημα κατά το ανέβασμα του αρχείου σας.';
						}
					
					$query1 = mysql_query("INSERT INTO doctor_report VALUES ('', '$doc_id', '$pat_id', '$rep_text', '$new_rep_file', '$date')");
				}
				else 
				{
					//the new picture file is not valid, so delete the temporary file and set the error flag
					@unlink($_FILES['rep_file']['tmp_name']);
					$error = true;
					$failure = 'Μπορείτε να ανεβάσετε αρχείο του τύπου .doc, .docx ή .pdf μικρότερο από 4MB';
					
				}
				//try to delete the temporary screen shot image file
				@unlink($_FILES['rep_file']['tmp_name']);
			}
			else	
			{
				$query1 = mysql_query("INSERT INTO doctor_report VALUES ('', '$doc_id', '$pat_id', '$rep_text', '', '$date')");
			}
			
			$sql = mysql_query("SELECT user_id FROM patient WHERE pat_id = '$pat_id'");
			$pat = mysql_fetch_array($sql);
			
			$message = 'Μια νέα αναφορά είναι διαθέσιμη προς προβολή';
			$pm = htmlentities($message);
			$pm = mysql_real_escape_string($message);
			$to_id = $pat['user_id'];
			$from_id = $_SESSION['user_id'];

			$send_pm = mysql_query("INSERT INTO private_messages VALUES ('', '$to_id', '$from_id', now(), 'Μετρήσεις', '$pm', '0', '0', '0')");
			
			if ($send_pm && $query1)
			{
				$msg = 'Η διαδικασία ολοκληρώθηκε με επιτυχία.';
			}
			else
			{
				$fail = 'Η διαδικασία δεν ολοκληρώθηκε. <br />';
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
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<script type="text/javascript">
			jQuery(function()
			{
			 	//check for empty fields
				jQuery("#rep_text").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
			});
		</script>
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Σύνταξη Αναφοράς';
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
					else if(!empty($msg))
					{
						echo '<br /><br />';
						echo '<div align="center">';
						echo '<p>'.$msg.'</p>';
						echo '</div>';
					}
					else
					{
				?>
				<h4>Αναφορά προς Ασθενή</h4>
					<form enctype="multipart/form-data" id="form" name="form" action="" method="POST">
						<table>
							<tr>
								<td><label for="rep_text">Εισάγετε σε ελεύθερο κείμενο συμβουλές προς τον ασθενή:<br /><font size="1">Ασκήσεις - Διατροφή κλπ.</font><br /></label></td>
								<td>
								<textarea id="rep_text" name="rep_text" rows="10" cols="45"><?php if(isset($rep_text)) echo $rep_text; ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Εφόσον το επιθυμείτε ανεβάστε ένα αρχείο .doc, .docx ή .pdf με συμβουλές:</td>
								<td><input type="file" id="rep_file" name="rep_file" /></td>
								<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $p_id; ?>" />
								<input type="hidden" name="doc_id" id="doc_id" value="<?php echo $d_id; ?>" />
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" name="insert" id="submit" value="Αποστολή"></td>
							</tr>
						</table>
					</form>
				<?php
						if (!empty($failure))
						{
							echo '<br /><br />';
							echo '<div align="center">';
							echo '<p class="error">'.$failure.'</p>';
							echo '</div>';
						}
						if (!empty($fail_file))
						{
							echo '<br /><br />';
							echo '<div align="center">';
							echo '<p class="error">'.$fail_file.'</p>';
							echo '</div>';
						}
					}
				?>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>