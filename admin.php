<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is an administrator and replace the cookie variables with sessions
	require_once('lib/authorization/admin_auth.php');
	
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
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = '';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<div align="center">
						<h3>Καλως ήλθατε !</h3>
						
						<br /><br />
						<h4>Χρησιμοποιείστε το μενού περιήγησης στα αριστερά για να χρησιμοποιήσετε την εφαρμογή.</h4>
					</div>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>