<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	//initialize the error variables for the server side validation
	$error1 = false;
	$error2 = false;
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is a doctor and replace the cookie variables with sessions
	require_once('lib/authorization/admin_auth.php');
	
	require_once('timeout.php');
	
	if (isset($_POST['submit']))
	//show the data that have been submitted from the form
	{
		$admin_email = mysql_real_escape_string(trim($_POST['admin_email']));
		
		if (!empty($admin_email))
		{
			if (!validateEmail($admin_email))
			{
				$error2 = TRUE;
			}
			else
			{
				//query
				$query1 = "UPDATE user SET user_email = '$admin_email' WHERE user_id = '" . $_SESSION['user_id'] . "'";
				mysql_query($query1);
				
				header('Location: profile');
				exit();
			}
		}
		else
		{
			$error1 = TRUE;
		}
	}
	else
	//grab and show the data from the database
	{
		$query = "SELECT user_email FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		if ($row != NULL)
		{
			$admin_email = $row['user_email'];
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
				jQuery("#admin_email").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//validate the email format
				jQuery("#admin_email").validate({
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
					<form id="form" method="POST" action="">
						<table border="0">
							<tr>
								<td><label for="admin_email">Email</label>&nbsp;</td>
								<td><input type="text" id="admin_email" name="admin_email" value="<?php if (!empty($admin_email)) echo $admin_email; ?>"/></td>
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
								<?php if(empty($admin_email)):?>
									<li><strong>Πεδίο Email:</strong> Πρέπει να συμπληρώσετε το πεδίο</li>
								<?php endif?>
							</ul>
						<?php
						}
									
						if ($error2)
						{
						//server side validation
						?>
							<ul>
								<?if(!validateEmail($admin_email)):?>
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
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>