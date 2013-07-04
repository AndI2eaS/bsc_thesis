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
	
	if (isset($_GET['id']))
	{
		$pat_id = (int)$_GET['id'];
	}
	else
	{
		$select_id = mysql_query("SELECT pat_id FROM patient WHERE user_id='" . $_SESSION['user_id'] . "'");
		$pid = mysql_fetch_array($select_id);
		$pat_id = $pid['pat_id'];
	}
	
	$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
	$results_per_page = 3;  // number of results per page
	$skip = (($cur_page - 1) * $results_per_page);
	
	$sql = "SELECT rep_id, rep_text, rep_date, rep_file FROM doctor_report WHERE pat_id='$pat_id' ORDER BY rep_date DESC";
	$result = mysql_query($sql);
	
	$total = mysql_num_rows($result);
	$num_pages = ceil($total / $results_per_page);
	
	$query =  $sql . " LIMIT $skip, $results_per_page";
	$result = mysql_query($query);
									
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
	</head>
	<body>
		
		<div id="wrap">
			<?php
				//get the page header
				$page_title = 'Προβολή Αναφορών';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<h4>Αναφορές</h4>
					<br /><br />
					<?php 
					if (mysql_num_rows($result)>0){
						while ($selected = mysql_fetch_array($result)) {
						?>
							<table width="80%">
								<tr>
									<td>Ημερομηνία :</td>
									<td><?php echo $selected['rep_date']; ?></td>
								</tr>
								<tr>
									<td>Αναφορά :</td>
									<td><?php echo makeClickableLinks($selected['rep_text']); ?></td>
								</tr>
							</table>
							<br />
							<a href='pdf?id=<?php echo $selected['rep_id']; ?>&pat_id=<?php echo $pat_id; ?>' target='_blank'>Κατέβασμα ως PDF <img src="images/Download.png"></a>
							<br /><br />
						<?php 
							if (!empty($selected['rep_file']))
							{
								echo '<a href="download?id='.$selected['rep_id'].'">Πρόσθετο Διαθέσιμο Αρχείο <img src="images/Download.png"></a>';
							}
						?>
							<br />
							<br />
							<br />
							<hr />
							<br /><br />
						<?php
						}
						
						echo '<center>';
							if ($num_pages > 1)
							{
								if($_SESSION['user_type'] == 3)
								{
									echo generate_page_links($cur_page, $num_pages);
								}
								else if ($_SESSION['user_type'] == 2)
								{
									echo generate_page_links_doc($cur_page, $num_pages, $pat_id);
								}
							}
						echo '</center>';
					}
					else
					{
						echo '<div id="error">Δεν υπάρχουν αναφορές για τον ασθενή</div>';
					}
						?>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>