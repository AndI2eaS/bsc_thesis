<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/connections/pathvars.php');
		
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php
			//get the title, and the link to CSS
			require_once('title_head.php');
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta content="Download free icons for web design and software development. 24x24 Free Pixel Icons set features dozens of images commonly used in applications, including New, Open, Save, Cut, Copy, Paste, and so on." name=description>
		<meta content="free icons, download icons, pixel icons, toolbar icons, ico" name=keywords>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Προβολή Προφίλ';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<br />
				<?php
					//if we are directed in this page from click profile in the menu, get the user's data from the database
					if (!isset($_GET['id']))
					{
						if ($_SESSION['user_type'] == 1)
						{
							$sql = "SELECT user_email FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'";
						}
						else if ($_SESSION['user_type'] == 2)
						{
							$sql = "SELECT * FROM doctor WHERE user_id = '" . $_SESSION['user_id'] . "'";
						}
						else if ($_SESSION['user_type'] == 3)
						{
							$sql = "SELECT * FROM patient WHERE user_id = '" . $_SESSION['user_id'] . "'";
						}
					}
					//if we have url variable, get the user's data from the database
					else
					{
						$user_id = (int)$_GET['id'];
						$sql1 = "SELECT * FROM user WHERE user_id='$user_id'";
						$result1 = mysql_query($sql1);
						$row1 = mysql_fetch_array($result1);
						
						if ($row1['user_type'] == 1)
						{
							$sql = "SELECT user_email FROM user WHERE user_id = '$user_id'";
						}
						else if ($row1['user_type'] == 2)
						{
							$sql = "SELECT * FROM doctor WHERE user_id = '$user_id'";
						}
						else if ($row1['user_type'] == 3)
						{
							$sql = "SELECT * FROM patient WHERE user_id = '$user_id'";
						}
					}
					
					//execute the query
					$result = mysql_query($sql);
					
					//the user row was found so display the user data
					if (mysql_num_rows($result) == 1)
					{
						$row = mysql_fetch_array($result);
						
						if (isset($row['doc_id']) && !isset($row['pat_id']))
						{
							require_once('doc_profile.php');
						}
						else if (isset($row['pat_id']))
						{
							require_once('pat_profile.php');
						}
						else
						{
							require_once('admin_profile.php');
						}
					}
					else
					{
						//There was a problem accessing your profile
						//error div
						echo '<div id="error">';
							echo '<br /><br />';
							echo 'Ο χρήστης που προσπαθείτε να δείτε είτε <br />δεν υπάρχει, είτε κάτι πήγε στραβά';
						echo '</div>';
					}
					
					//close the database connection
					mysql_close();
				?>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>