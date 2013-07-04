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
	
	//check if the user is a patient and replace the cookie variables with sessions
	require_once('lib/authorization/patient_auth.php');
	
	require_once('timeout.php');
	
	if (isset($_POST['submit']))
	//show the data that have been submitted from the form
	{
		$pat_name = mysql_real_escape_string(trim($_POST['pat_name']));
		$pat_surname = mysql_real_escape_string(trim($_POST['pat_surname']));
		$pat_email = mysql_real_escape_string(trim($_POST['pat_email']));
		$pat_city = mysql_real_escape_string(trim($_POST['pat_city']));
		$pat_address = mysql_real_escape_string(trim($_POST['pat_address']));
		$pat_phone = mysql_real_escape_string(trim($_POST['pat_phone']));
		$pat_birth_date = mysql_real_escape_string(trim($_POST['pat_birth_date']));
		$pat_sex = mysql_real_escape_string(trim($_POST['pat_sex']));
		$pat_height = mysql_real_escape_string(trim($_POST['pat_height']));
		$pat_accept_email = mysql_real_escape_string(trim($_POST['pat_accept_email']));
		$old_pat_picture = mysql_real_escape_string(trim($_POST['old_pat_picture']));
		$pat_picture = mysql_real_escape_string(trim($_FILES['pat_picture']['name']));
		$pat_picture_type = $_FILES['pat_picture']['type'];
		$pat_picture_size = $_FILES['pat_picture']['size'];
		list($pat_picture_width, $pat_picture_height) = getimagesize($_FILES['pat_picture']['tmp_name']);
		$error = false;
		
		// Validate and move the uploaded picture file, if necessary
		if (!empty($pat_picture))
		{
			//check the type and the size of the picture
			if ((($pat_picture_type == 'image/gif') || ($pat_picture_type == 'image/jpeg') || ($pat_picture_type == 'image/pjpeg') ||
			($pat_picture_type == 'image/png') || ($pat_picture_type == 'image/jpg')) && ($pat_picture_size > 0) && ($pat_picture_size <= MM_MAXFILESIZE) &&
			($pat_picture_width <= MM_MAXIMGWIDTH) && ($pat_picture_height <= MM_MAXIMGHEIGHT)) 
			{
				//if nothing is wrong with the file and the upload process
				if ($_FILES['file']['error'] == 0) 
				{
					//move the file to the target upload folder
					$target = MM_UPLOADPATH . basename($pat_picture);
					if (move_uploaded_file($_FILES['pat_picture']['tmp_name'], $target)) 
					{
						//the new picture file move was successful, now make sure any old picture is deleted
						if (!empty($old_pat_picture) && ($old_pat_picture != $pat_picture)) 
						{
							unlink(MM_UPLOADPATH . $old_pat_picture);
						}
					}
					else 
					{
						//the new picture file move failed, so delete the temporary file and set the error flag
						unlink($_FILES['pat_picture']['tmp_name']);
						$error = true;
						$msg = 'Υπήρξε ένα πρόβλημα κατά το ανέβασμα της φωτογραφίας σας.';
					}
				}
			}
			else 
			{
				//the new picture file is not valid, so delete the temporary file and set the error flag
				unlink($_FILES['pat_picture']['tmp_name']);
				$error = true;
				$msg = 'Η εικόνα πρέπει να είναι τύπου: GIF, JPEG, JPG ή PNG<br /> μέχρι ' . (MM_MAXFILESIZE / 1024) .
				  ' KB και ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' pixels σε μέγεθος. <br /><br /><a href="edit_pat_profile">Επιστρέψτε</a>';
			}
		}
		
		if (!$error)
		{
			if (!empty($pat_name) && !empty($pat_surname) && !empty($pat_email) && !empty($pat_city) && !empty($pat_address) && !empty($pat_phone) && !empty($pat_birth_date) && !empty($pat_height))
			{
				if (!validateLengthName($pat_name) || !validateLengthName($pat_surname) || !validateName($pat_name) || !validateName($pat_surname) || !validateEmail($pat_email) || !validateName($pat_city) || !validateNumber($pat_phone) || !validateNumber($pat_height))
				{
					$error2 = TRUE;
				}
				else{
					if (!empty($pat_picture)) 
					{
						$query1 = "UPDATE patient SET pat_name = '$pat_name', pat_surname = '$pat_surname', pat_city = '$pat_city', " .
						" pat_address = '$pat_address', pat_phone = '$pat_phone', pat_birth_date = '$pat_birth_date', pat_sex = '$pat_sex', pat_height = '$pat_height', pat_accept_email = '$pat_accept_email', pat_picture = '$pat_picture' WHERE user_id = '" . $_SESSION['user_id'] . "'";
						$query2 = "UPDATE user SET user_email = '$pat_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
					}
					else
					{
						$query1 = "UPDATE patient SET pat_name = '$pat_name', pat_surname = '$pat_surname', pat_city = '$pat_city', " .
						" pat_address = '$pat_address', pat_phone = '$pat_phone', pat_birth_date = '$pat_birth_date', pat_sex = '$pat_sex', pat_height = '$pat_height', pat_accept_email = '$pat_accept_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
						$query2 = "UPDATE user SET user_email = '$pat_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
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
		$query = "SELECT p.pat_name, p.pat_surname, p.pat_city, p.pat_address, p.pat_phone, p.pat_birth_date, p.pat_sex, p.pat_height, p.pat_picture, p.pat_accept_email, u.user_email FROM patient as p INNER JOIN user as u USING (user_id) WHERE user_id = '" . $_SESSION['user_id'] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		if ($row != NULL)
		{
			$pat_name = $row['pat_name'];
			$pat_surname = $row['pat_surname'];
			$pat_city = $row['pat_city'];
			$pat_address = $row['pat_address'];
			$pat_phone = $row['pat_phone'];
			$pat_birth_date = $row['pat_birth_date'];
			$pat_email = $row['user_email'];
			$pat_sex = $row['pat_sex'];
			$pat_height = $row['pat_height'];
			$pat_accept_email = $row['pat_accept_email'];
			$old_pat_picture = $row['pat_picture'];
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
		<link rel="stylesheet" type="text/css" href="css/datepicker.css" />
		<script src="js/jquery.validate.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="css/datepicker.css" />
		<script type="text/javascript" src="js/datepicker.js"></script>
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
				jQuery("#pat_email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_city").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_address").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_phone").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_birth_date").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pat_height").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the characters of name
				jQuery("#pat_name").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the characters of surname
				jQuery("#pat_surname").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the email format
				jQuery("#pat_email").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/)) return true; else return false;",
                    message: "Εισάγετε μια έγκυρη μορφή Email"
                });
				
				//validate the characters of the city
				jQuery("#pat_city").validate({
                    expression: "if (VAL.match(/^[a-zA-Z-α-ωΑ-Ω-ά-ώΆ-Ώ]+$/)) return true; else return false;",
                    message: "Επιτρέπονται μόνο γράμματα"
                });
				
				//validate the length of the name
				jQuery("#pat_name").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
				
				//validate the length of the surname
				jQuery("#pat_surname").validate({
                    expression: "if (VAL.length >= 3) return true; else return false;",
                    message: "Πρέπει να είναι 3 γράμματα και άνω"
                });
				
				//validate the phone field
				jQuery("#pat_phone").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				//validate the height field
				jQuery("#pat_height").validate({
					expression: "if (VAL.match(/^[0-9]+$/)) return true; else return false;",
					message: "Επιτρέπονται μόνο αριθμοί"
				});
				
				$('#pat_birth_date').DatePicker({
					format:'Y-m-d',
					date: $('#pat_birth_date').val(),
					current: $('#pat_birth_date').val(),
					starts: 1,
					position: 'right',
					view: 'years',
					onBeforeShow: function(){
						$('#pat_birth_date').DatePickerSetDate($('#pat_birth_date').val(), true);
					},
					onChange: function(formated, dates)
					{
						$('#pat_birth_date').val(formated);
					}
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
								<td><label for="pat_name">Όνομα</label>&nbsp;</td>
								<td><input type="text" id="pat_name" name="pat_name" value="<?php if (!empty($pat_name)) echo $pat_name; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_surname">Επώνυμο</label>&nbsp;</td>
								<td><input type="text" id="pat_surname" name="pat_surname" value="<?php if (!empty($pat_surname)) echo $pat_surname; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_email">Email</label>&nbsp;</td>
								<td><input type="text" id="pat_email" name="pat_email" value="<?php if (!empty($pat_email)) echo $pat_email; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_city">Πόλη</label>&nbsp;</td>
								<td><input type="text" id="pat_city" name="pat_city" value="<?php if (!empty($pat_city)) echo $pat_city; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_address">Διεύθυνση</label>&nbsp;</td>
								<td><input type="text" id="pat_address" name="pat_address" value="<?php if (!empty($pat_address)) echo $pat_address; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_phone">Τηλέφωνο</label>&nbsp;</td>
								<td><input type="text" id="pat_phone" name="pat_phone" value="<?php if (!empty($pat_phone)) echo $pat_phone; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_birth_date">Ημ. Γέννησης</label>&nbsp;</td>
								<td><input type="text" id="pat_birth_date" name="pat_birth_date" value="<?php if (!empty($pat_birth_date)) { echo $pat_birth_date; } else { echo '0000-00-00'; }?>"/></td>
								</tr>
							<tr>
								<td><label for="pat_sex">Φύλο</label>&nbsp;</td>
								<td>
									<select id="pat_sex" name="pat_sex">
										<option value="Α" <?php if (!empty($pat_sex) && $pat_sex == 'Α') echo 'selected = "selected"'; ?>>Αρσενικό</option>
										<option value="Θ" <?php if (!empty($pat_sex) && $pat_sex == 'Θ') echo 'selected = "selected"'; ?>>Θηλυκό</option>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="pat_height">Ύψος (cm)</label>&nbsp;</td>
								<td><input type="text" id="pat_height" name="pat_height" value="<?php if (!empty($pat_height)) echo $pat_height; ?>"/></td>
							</tr>
							<tr>
								<td><label for="pat_accept_email">Αποδοχή Email Υπενθύμισης</label>&nbsp;</td>
								<td><select id="pat_accept_email" name="pat_accept_email">
										<option value="1" <?php if (isset($pat_accept_email) && $pat_accept_email == 1) echo 'selected = "selected"'; ?>>Ναι</option>
										<option value="0" <?php if (isset($pat_accept_email) && $pat_accept_email == 0) echo 'selected = "selected"'; ?>>Όχι</option>
									</select>
									<font size="1">Θα σας έρχεται κάθε πρωί ένα email ως <br />υπενθύμιση εισαγωγής των μετρήσεών σας.</font>
								</td>
							</tr>
							
							<tr>
								
								<td><label for="pat_picture">Φωτογραφία</label>&nbsp;</td>
								<td><input type="file" id="pat_picture" name="pat_picture" />
								<input type="hidden" name="old_pat_picture" id="old_pat_picture" value="<?php if (!empty($old_pat_picture)) echo $old_pat_picture; ?>" />
								<?php
								if (!empty($old_pat_picture)) 
								{
									echo '<img src="' . MM_UPLOADPATH . $old_pat_picture . '" alt="Εικόνα Προφίλ" width="120px" height="120px"/>';
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
								<?php if(empty($pat_name)):?>
									<li><strong>Πεδίο Όνομα:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_surname)):?>
									<li><strong>Πεδίο Επώνυμο:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif ?>
								<?php if(empty($pat_email)):?>
									<li><strong>Πεδίο Email:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_city)):?>
									<li><strong>Πεδίο Πόλη:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_address)):?>
									<li><strong>Πεδίο Διεύθυνση:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_phone)):?>
									<li><strong>Πεδίο Τηλέφωνο:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_birth_date)):?>
									<li><strong>Πεδίο Ημ. Γέννησης:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
								<?php if(empty($pat_height)):?>
									<li><strong>Πεδίο Ύψος:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
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
								<?if(!validateEmail($pat_email)):?>
									<li><strong>Πεδίο Email:</strong> Εισάγετε μια έγκυρη μορφή Email</li>
								<?endif?>
								<?if(!validateName($pat_city)):?>
									<li><strong>Πεδίο Πόλη:</strong> Επιτρέπονται μόνο γράμματα</li>
								<?endif?>
								<?if(!validateNumber($pat_phone)):?>
									<li><strong>Πεδίο Τηλέφωνο:</strong> Επιτρέπονται μόνο αριθμοί</li>
								<?endif?>
								<?if(!validateNumber($pat_height)):?>
									<li><strong>Πεδίο Ύψος:</strong> Επιτρέπονται μόνο αριθμοί</li>
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