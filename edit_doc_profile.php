<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/pathvars.php');
	
	//initialize the error variables for the server side validation
	$error1 = false;
	$error2 = false;
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is a doctor and replace the cookie variables with sessions
	require_once('lib/authorization/doctor_auth.php');
	
	require_once('timeout.php');
	
	if (isset($_POST['submit']))
	//show the data that have been submitted from the form
	{
		$doc_name = mysql_real_escape_string(trim($_POST['doc_name']));
		$doc_surname = mysql_real_escape_string(trim($_POST['doc_surname']));
		$doc_email = mysql_real_escape_string(trim($_POST['doc_email']));
		$doc_city = mysql_real_escape_string(trim($_POST['doc_city']));
		$doc_address = mysql_real_escape_string(trim($_POST['doc_address']));
		$doc_office_phone = mysql_real_escape_string(trim($_POST['doc_office_phone']));
		$doc_personal_phone = mysql_real_escape_string(trim($_POST['doc_personal_phone']));
		$old_doc_picture = mysql_real_escape_string(trim($_POST['old_doc_picture']));
		$doc_picture = mysql_real_escape_string(trim($_FILES['doc_picture']['name']));
		$doc_picture_type = $_FILES['doc_picture']['type'];
		$doc_picture_size = $_FILES['doc_picture']['size'];
		list($doc_picture_width, $doc_picture_height) = getimagesize($_FILES['doc_picture']['tmp_name']);
		$error = false;
		
		// Validate and move the uploaded picture file, if necessary
		if (!empty($doc_picture))
		{
			//check the type and the size of the picture
			if ((($doc_picture_type == 'image/gif') || ($doc_picture_type == 'image/jpeg') || ($doc_picture_type == 'image/pjpeg') ||
			($doc_picture_type == 'image/png') || ($doc_picture_type == 'image/jpg')) && ($doc_picture_size > 0) && ($doc_picture_size <= MM_MAXFILESIZE) &&
			($doc_picture_width <= MM_MAXIMGWIDTH) && ($doc_picture_height <= MM_MAXIMGHEIGHT)) 
			{
				//if nothing is wrong with the file and the upload process
				if ($_FILES['file']['error'] == 0) 
				{
					//move the file to the target upload folder
					$target = MM_UPLOADPATH . basename($doc_picture);
					if (move_uploaded_file($_FILES['doc_picture']['tmp_name'], $target)) 
					{
						//the new picture file move was successful, now make sure any old picture is deleted
						if (!empty($old_doc_picture) && ($old_doc_picture != $doc_picture)) 
						{
							unlink(MM_UPLOADPATH . $old_doc_picture);
						}
					}
					else 
					{
						//the new picture file move failed, so delete the temporary file and set the error flag
						unlink($_FILES['doc_picture']['tmp_name']);
						$error = true;
						$msg = 'Υπήρξε ένα πρόβλημα κατά το ανέβασμα της φωτογραφίας σας.';
					}
				}
			}
			else 
			{
				//the new picture file is not valid, so delete the temporary file and set the error flag
				unlink($_FILES['doc_picture']['tmp_name']);
				$error = true;
				$msg = 'Η εικόνα πρέπει να είναι τύπου: GIF, JPEG, JPG ή PNG<br /> μέχρι ' . (MM_MAXFILESIZE / 1024) .
				  ' KB και ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' pixels σε μέγεθος. <br /><br /><a href="edit_doc_profile">Επιστρέψτε</a>';
			}
		}
		
		if (!$error)
		{
			if (!empty($doc_name) && !empty($doc_surname) && !empty($doc_email) && !empty($doc_city) && !empty($doc_address) && !empty($doc_office_phone) && !empty($doc_personal_phone))
			{
				if (!validateLengthName($doc_name) || !validateLengthName($doc_surname) || !validateName($doc_name) || !validateName($doc_surname) || !validateEmail($doc_email) || !validateName($doc_city) || !validateNumber($doc_office_phone) || !validateNumber($doc_personal_phone))
				{
					$error2 = TRUE;
				}
				else{
					if (!empty($doc_picture)) 
					{
						$query1 = "UPDATE doctor SET doc_name = '$doc_name', doc_surname = '$doc_surname', doc_city = '$doc_city', " .
						" doc_address = '$doc_address', doc_office_phone = '$doc_office_phone', doc_personal_phone = '$doc_personal_phone', doc_picture = '$doc_picture' WHERE user_id = '" . $_SESSION['user_id'] . "'";
						$query2 = "UPDATE user SET user_email = '$doc_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
					}
					else
					{
						$query1 = "UPDATE doctor SET doc_name = '$doc_name', doc_surname = '$doc_surname', doc_city = '$doc_city', " .
						" doc_address = '$doc_address', doc_office_phone = '$doc_office_phone', doc_personal_phone = '$doc_personal_phone' WHERE user_id = '" . $_SESSION['user_id'] . "'";
						$query2 = "UPDATE user SET user_email = '$doc_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
					}
					mysql_query($query1);
					mysql_query($query2);

					// Confirm success with the user
					header('Location: profile');
					exit();
				}
			}
			else 
			{
				$error1 = true;
			}
		}
		
	}
	else
	//grab and show the data from the database
	{
		$query = "SELECT d.doc_name, d.doc_surname, d.doc_city, d.doc_address, d.doc_office_phone, d.doc_personal_phone, d.doc_picture, u.user_email FROM doctor as d INNER JOIN user as u USING (user_id) WHERE user_id = '" . $_SESSION['user_id'] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		if ($row != NULL)
		{
			$doc_name = $row['doc_name'];
			$doc_surname = $row['doc_surname'];
			$doc_city = $row['doc_city'];
			$doc_address = $row['doc_address'];
			$doc_office_phone = $row['doc_office_phone'];
			$doc_personal_phone = $row['doc_personal_phone'];
			$doc_email = $row['user_email'];
			$old_doc_picture = $row['doc_picture'];
		}
		else
		{
			$msg = 'Υπήρξε κάποιο πρόβλημα κατά την<br /> προσπάθεια προβολής του προφίλ';
		}
	}
	
	//close the database connection
	mysql_close();
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
				jQuery("#doc_name").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_surname").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_city").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_address").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_office_phone").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#doc_personal_phone").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the characters of name
				jQuery("#doc_name").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the characters of surname
				jQuery("#doc_surname").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the email format
				jQuery("#email").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/)) return true; else return false;",
                    message: "Εισάγετε μια έγκυρη μορφή Email"
                });
				
				//validate the characters of the city
				jQuery("#doc_city").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the name
				jQuery("#doc_name").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
				
				//validate the length of the surname
				jQuery("#doc_surname").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
				
				//validate the office phone field
				jQuery("#doc_office_phone").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the personal phone field
				jQuery("#doc_personal_phone").validate({
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
				$page_title = 'Επεξεργασία Προφίλ';
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
				<h4>Επεξεργαστείτε το προφίλ σας</h4>
				<br />
					<form enctype="multipart/form-data" id="form" method="POST" action="">
						<table border="0">
							<tr>
								<td><label for="doc_name">Όνομα</label>&nbsp;</td>
								<td><input type="text" id="doc_name" name="doc_name" value="<?php if (!empty($doc_name)) echo $doc_name; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_surname">Επώνυμο</label>&nbsp;</td>
								<td><input type="text" id="doc_surname" name="doc_surname" value="<?php if (!empty($doc_surname)) echo $doc_surname; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_email">Email</label>&nbsp;</td>
								<td><input type="text" id="doc_email" name="doc_email" value="<?php if (!empty($doc_email)) echo $doc_email; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_city">Πόλη</label>&nbsp;</td>
								<td><input type="text" id="doc_city" name="doc_city" value="<?php if (!empty($doc_city)) echo $doc_city; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_address">Διεύθυνση</label>&nbsp;</td>
								<td><input type="text" id="doc_address" name="doc_address" value="<?php if (!empty($doc_address)) echo $doc_address; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_office_phone">Τηλ. Γραφείου</label>&nbsp;</td>
								<td><input type="text" id="doc_office_phone" name="doc_office_phone" value="<?php if (!empty($doc_office_phone)) echo $doc_office_phone; ?>"/></td>
							</tr>
							<tr>
								<td><label for="doc_personal_phone">Τηλ. Προσωπικό</label>&nbsp;</td>
								<td><input type="text" id="doc_personal_phone" name="doc_personal_phone" value="<?php if (!empty($doc_personal_phone)) echo $doc_personal_phone; ?>"/></td>
							</tr>
							<tr>
								
								<td><label for="doc_picture">Φωτογραφία</label>&nbsp;</td>
								<td><input type="file" id="doc_picture" name="doc_picture" />
								<input type="hidden" name="old_doc_picture" id="old_doc_picture" value="<?php if (!empty($old_doc_picture)) echo $old_doc_picture; ?>" />
								<?php
								if (!empty($old_doc_picture)) 
								{
									echo '<img src="' . MM_UPLOADPATH . $old_doc_picture . '" alt="Εικόνα Προφίλ" width="120px" height="120px"/>';
								}
								?>
								</td>
							</tr>
							
							<tr>
								<td><br /><br /><br /></td>
								<td>
								<input type="submit" id="submit" name="submit" value="Αποθήκευση"/>
								</td>
							</tr>
						</table>
						<br />
					</form>
					<a href="change_password">Αλλαγή Κωδικού</a>
					
					<div id="error">
						<?php
						if ($error1)
						{
						//server side validation
						?>
							<ul>
								<?php if(empty($doc_name)):?>
									<li><strong>Πεδίο Όνομα:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($doc_surname)):?>
									<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif ?>
								<?php if(empty($doc_email)):?>
									<li><strong>Πεδίο Email:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($doc_city)):?>
									<li><strong>Πεδίο Πόλη:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($doc_address)):?>
									<li><strong>Πεδίο Διεύθυνση:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($doc_office_phone)):?>
									<li><strong>Πεδίο Τηλ. Γραφείου:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($doc_personal_phone)):?>
									<li><strong>Πεδίο Τηλ. Προσωπικό:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
							</ul>
						<?php
						}
									
						if ($error2)
						{
						//server side validation
						?>
							<ul>
								<?if(!validateLengthName($doc_name)):?>
									<li><strong>Πεδίο Όνομα:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
								<?endif?>
								<?if(!validateLengthName($doc_surname)):?>
									<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να είναι 3 γράμματα και άνω</li>
								<?endif?>
								<?if(!validateName($doc_name)):?>
									<li><strong>Πεδίο Όνομα:</strong> Επιτρέπονται μόνο γράμματα</li>
								<?endif?>
								<?if(!validateName($doc_surname)):?>
									<li><strong>Πεδίο Επώνυμο:</strong> Επιτρέπονται μόνο γράμματα</li>
								<?endif?>
								<?if(!validateLengthName($doc_city)):?>
									<li><strong>Πεδίο Πόλη:</strong> Επιτρέπονται μόνο γράμματα</li>
								<?endif?>
								<?if(!validateEmail($doc_email)):?>
									<li><strong>Πεδίο Email:</strong> Εισάγετε μια έγκυρη μορφή Email</li>
								<?endif?>
								<?if(!validateNumber($doc_office_phone)):?>
									<li><strong>Πεδίο Τηλ. Γραφείου:</strong> Επιτρέπονται μόνο αριθμοί</li>
								<?endif?>
								<?if(!validateNumber($doc_personal_phone)):?>
									<li><strong>Πεδίο Τηλ. Προσωπικό:</strong> Επιτρέπονται μόνο αριθμοί</li>
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
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>