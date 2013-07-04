<?php
	require_once("lib/functions/functions.php");
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();	
	
	require_once('timeout.php');
	
	if (isset($_POST['submit']))
	//show the data that have been submitted from the form
	{
		$password = mysql_real_escape_string(trim($_POST['password']));
		$new_password = mysql_real_escape_string(trim($_POST['new_password']));
		$repeat_password = mysql_real_escape_string(trim($_POST['repeat_password']));
		
		if (!empty($password) && !empty($new_password) &&!empty($repeat_password))
		{
			//query to get the database password for the user
			$sql = "SELECT password FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			
			if (isset($row))
			{
				$db_pass = $row['password'];
			}
			
			if (sha1($password)==$db_pass)
			{
				//check if the passwords are the same
				if ($new_password == $repeat_password)
				{
					$encrypted_pass = sha1($new_password);
					$sql1 = "UPDATE user set password='$encrypted_pass' WHERE user_id = '" . $_SESSION['user_id'] . "'";
					$result1 = mysql_query($sql1);
					header('Location: profile');
					exit();
				}
				else
				{
					$msg = 'Τα πεδία ~Νέος Κωδικός~ και ~Επαλήθευση κωδικού~ δεν ταιριάζουν';
				}
			}
			else
			{
				//display an error message
				$msg = 'Ο Ισχύων κωδικός που έχετε εισάγει είναι λάθος.';
			}
		}
		else
		{
			$msg = 'Πρέπει να συμπληρώσετε όλα τα πεδία';
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
				jQuery("#password").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#new_password").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#repeat_password").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				
				jQuery("#repeat_password").validate({
                    expression: "if ((VAL == jQuery('#new_password').val()) && VAL) return true; else return false;",
                    message: "Επαληθεύστε τον κωδικό"
                });
			});
			
			
		</script>
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Αλλαγή Κωδικού';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<h4></h4>
					<br />
					<br />
					<form id="form" name="form" action="" method="POST">
								<table border="0">
									<tr>
										<td><label for="password">Ισχύων Κωδικός</label>&nbsp;</td>
										<td><input type="password" id="password" name="password"/></td>
									</tr>
									<tr>
										<td><label for="new_password">Νέος Κωδικός</label>&nbsp;</td>
										<td><input type="password" id="new_password" name="new_password"/></td>
									</tr>
									<tr>
										<td><label for="repeat_password">Επαλήθευση κωδικού</label>&nbsp;</td>
										<td><input type="password" id="repeat_password" name="repeat_password"/><span id="passInfo"></span></td>
									</tr>
									<tr>
										<td><br /><br /><br /><br /></td>
										<td>
										<input type="reset" id="submit" name="reset" value="Καθαρισμός"/>
										<input type="submit" id="submit" name="submit" value="Αλλαγή"/>
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
						?>
						
				</div>
				

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>