<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	require_once('lib/authorization/admin_auth.php');
	
	require_once('timeout.php');
		
	$get_doctors = mysql_query("SELECT COUNT(*) FROM doctor");
	$count_doctors = mysql_fetch_array($get_doctors);
	$get_patients = mysql_query("SELECT COUNT(*) FROM patient");
	$count_patients = mysql_fetch_array($get_patients);
	
	$total = $count_doctors[0] + $count_patients[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php 
			//get the title and the link to CSS
			require_once('title_head.php'); 
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header and set the page title value
				$page_title = 'Προβολή Χρηστών';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<br />
					<h4>Στοιχεία Εφαρμογής</h4>
					<br />
					<table>
						<tr>
							<td>Σύνολο Εγγεγραμμένων Χρηστών: &nbsp;&nbsp;</td> <td><?php echo $total; ?></td>
						</tr>
						<tr>
							<td>Σύνολο Ιατρών: &nbsp;&nbsp;</td> <td><a href="view_doctors"><?php echo $count_doctors[0]; ?></a></td>
						</tr>
						<tr>
							<td>Σύνολο Ασθενών: &nbsp;&nbsp;</td> <td><a href="view_patients"><?php echo $count_patients[0]; ?></a></td>
						</tr>
					</table>
						<br /><br /><br />
					<div align="center">
						<hr width="50%"/>
						<table>
							<tr>
								<td><a href="view_doctors">Προβολή Ιατρών</a></td>
							</tr>
							<tr>
								<td><a href="view_patients">Προβολή Ασθενών</a></td>
							</tr>
						</table>
						<hr width="50%"/>
					</div>
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>