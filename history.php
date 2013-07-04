<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//check if the user is a doctor and replace the cookie variables with sessions
	require_once('lib/authorization/doctor_auth.php');
	
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
				$page_title = 'Προβολή Μετρήσεων';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<div align="center">
						<?php
							//if the variables are sent via POST, get them
							if (isset($_POST['submit']))
							{
								$pat_id = (int)$_POST['pat_id'];
								$eidos = mysql_real_escape_string(trim($_POST['eidos']));
								$id = (int)$_POST['id'];
							}
							//if the variables are sent via GET, get them
							else
							{
								$pat_id = (int)$_GET['pat_id'];
								$eidos = mysql_real_escape_string(trim($_GET['eidos']));
								$id = (int)$_GET['id'];
							}
							
							//set the variables in order to create a pagination feature
							$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
							$results_per_page = 10;  // number of results per page
							$skip = (($cur_page - 1) * $results_per_page);
										
							//get the patient's measurements
							$sql = "SELECT m_date, glikozi, varos, perifereia_mesis, sistoliki_piesi, diastoliki_piesi FROM measurements WHERE pat_id='$pat_id'";
							$result = mysql_query($sql);
							
							$total = mysql_num_rows($result);
							$num_pages = ceil($total / $results_per_page);
										
							$query =  $sql . " LIMIT $skip, $results_per_page";
							$result = mysql_query($query);
							
							//depending on the type of measurement, show different tables filled with values and different diagrams
							if ($eidos=='glikozi')
							{
								echo '<table cellspacing="10">';
									echo '<th>Ημερομηνία</th>';
									echo '<th>Γλυκόζη</th>';
									while ($rows = mysql_fetch_array($result))
									{
										echo '<tr align="center">';
											echo '<td>'.date('d-m-Y',strtotime($rows['m_date'])).'</td>';
											echo '<td>'.$rows['glikozi'].'</td>';
										echo '</tr>';
									}
								echo '</table>';
								if ($num_pages > 1) 
								{
									echo generate_page_links_history($cur_page, $num_pages, $eidos, $pat_id, $id);
								}
								echo '<img src="history_graph.php?eidos=glikozi&id='.$pat_id.'" name="line graph"/>';
							}
							else if ($eidos=='varos')
							{
								echo '<table cellspacing="10">';
									echo '<th>Ημερομηνία</th>';
									echo '<th>Βάρος</th>';
									while ($rows = mysql_fetch_array($result))
									{
										echo '<tr align="center">';
											echo '<td>'.date('d-m-Y',strtotime($rows['m_date'])).'</td>';
											echo '<td>'.$rows['varos'].'</td>';
										echo '</tr>';
									}
								echo '</table>';
								if ($num_pages > 1) 
								{
									echo generate_page_links_history($cur_page, $num_pages, $eidos, $pat_id, $id);
								}
								echo '<img src="history_graph.php?eidos=varos&id='.$pat_id.'" name="line graph"/>';
							}
							else if ($eidos=='perifereia_mesis')
							{
								echo '<table cellspacing="10">';
									echo '<th>Ημερομηνία</th>';
									echo '<th>Περιφέρεια Μέσης</th>';
									while ($rows = mysql_fetch_array($result))
									{
										echo '<tr align="center">';
											echo '<td>'.date('d-m-Y',strtotime($rows['m_date'])).'</td>';
											echo '<td>'.$rows['perifereia_mesis'].'</td>';
										echo '</tr>';
									}
								echo '</table>';
								if ($num_pages > 1) 
								{
									echo generate_page_links_history($cur_page, $num_pages, $eidos, $pat_id, $id);
								}
								echo '<img src="history_graph.php?eidos=perifereia_mesis&id='.$pat_id.'" name="line graph"/>';
							}
							else if ($eidos=='piesi')
							{
								echo '<table cellspacing="10">';
									echo '<th>Ημερομηνία</th>';
									echo '<th>Συστολική Πίεση</th>';
									echo '<th>Διαστολική Πίεση</th>';
									while ($rows = mysql_fetch_array($result))
									{
										echo '<tr align="center">';
											echo '<td>'.date('d-m-Y',strtotime($rows['m_date'])).'</td>';
											echo '<td>'.$rows['sistoliki_piesi'].'</td>';
											echo '<td>'.$rows['diastoliki_piesi'].'</td>';
										echo '</tr>';
									}
								echo '</table>';
								if ($num_pages > 1) 
								{
									echo generate_page_links_history($cur_page, $num_pages, $eidos, $pat_id, $id);
								}
								echo '<img src="history_graph.php?eidos=sistoliki_piesi&id='.$pat_id.'" name="line graph"/>';
								echo '<img src="history_graph.php?eidos=diastoliki_piesi&id='.$pat_id.'" name="line graph"/>';
							}
							
						?>
						<br /><br />
						<a href="measurements?id=<?php echo $id; ?>">Επιστροφή</a>
					</div>
				</div>

			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>