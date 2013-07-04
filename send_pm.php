<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
	
	if (isset($_GET['id']))
	{
		$id = (int)$_GET['id'];
	}
	
	if (isset($_POST['submit']))
	{
		//insert the message into the DB and head back to the home page
		$pm_subject = mysql_real_escape_string($_POST['pm_subject']);
		$pm_text = mysql_real_escape_string($_POST['pm_text']);
		$pm_receiver_id = (int)$_POST['pm_receiver_id'];
		$pm_sender_id = (int)$_POST['pm_sender_id'];
		
		$msg = array();
		
		//prevent duplicate messages
		$prevent_duplicate = "SELECT mes_id FROM private_messages WHERE from_id='$pm_sender_id' AND time_sent between subtime(now(),'0:0:20') and now()";
		$query = mysql_query($prevent_duplicate);
		$rows = mysql_num_rows($query);
		if ($rows > 0)
		{
			$msg[] = 'Πρέπει να περιμένετε 20 δευτερόλεπτα προτού ξαναστείλετε μήνυμα.';
		}
		
		if (empty($pm_subject) || empty($pm_text) || empty($pm_receiver_id) || empty($pm_sender_id)) 
		{ 
			$msg[] = 'Λείπουν στοιχεία';
		}
		else
		{
			// Delete the message residing at the tail end of their list so they cannot archive more than 100 messages
			$sqldeleteTail = "SELECT * FROM private_messages WHERE to_id='$pm_receiver_id' ORDER BY time_sent DESC LIMIT 0,100"; 
			$query1 = mysql_query($sqldeleteTail);
			$i = 1;
			while($row = mysql_fetch_array($query1)){ 
					$pm_id = $row["mes_id"];
					if ($i > 99) {
						$deleteTail = mysql_query("DELETE FROM private_messages WHERE mes_id='$pm_id'"); 
					}
					$i++;
			}
			
			
			// INSERT the data into the database
			
			$sql = "INSERT INTO private_messages (to_id, from_id, time_sent, subject, message) VALUES ('$pm_receiver_id', '$pm_sender_id', now(), '$pm_subject', '$pm_text')";
			if (mysql_query($sql))
			{
				$success_message = 'Το μήνυμα στάλθηκε με επιτυχία.';
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
				jQuery("#pm_subject").validate({
					expression: "if (VAL) return true; else return false;",
					message: "Συμπληρώστε το πεδίο"
				});
				
				//check for empty fields
				jQuery("#pm_text").validate({
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
				$page_title = 'Προσωπικό Μήνυμα';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<?php
						if (isset($success_message))
						{
							echo '<br /><br />';
							echo '<div align="center">';
							echo '<p>'.$success_message.'</p>';
							echo '</div>';
						}
						else
						{
					?>
					<h4></h4>
					<form id="form" action="" method="POST">
									<table border="0">
										<tr>
											<td><label for="pm_subject">Θέμα</label>&nbsp;</td>
											<td><input type="text" id="pm_subject" name="pm_subject" value="<?php if(isset($pm_subject)) echo $pm_subject; ?>"/><br /><br /></td>
										</tr>
										<tr>
											<td><label for="pm_text">Μήνυμα</label>&nbsp;</td>
											<td>
												<textarea id="pm_text" name="pm_text" rows="10" cols="39"><?php if(isset($pm_text)) echo $pm_text; ?></textarea>
											</td>
										</tr>
										<input name="pm_sender_id" id="pm_sender_id" type="hidden" value="<?php echo $_SESSION['user_id']; ?>" />
										<input name="pm_receiver_id" id="pm_receiver_id" type="hidden" value="<?php echo $id; ?>" />
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
								if (!empty($msg)){
									//if the variable msg is not empty, reveal its content
									echo '<div align="center">';
									foreach ($msg as $error){
										echo '<p class="error">'.$error.'</p>';
									}
									echo '</div>';
								}
						}
						?>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>