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
				
				
				<?php
					if (isset($_GET['id']))
					{
						$id = (int)$_GET['id'];
					}
					//get the patient's id from table patient
					$get_pat = mysql_query("SELECT pat_id, pat_name, pat_surname, pat_height FROM patient WHERE user_id = '$id'");
					$pat_id = mysql_fetch_array($get_pat);
					
					$get = mysql_query("SELECT glikozi,varos, perifereia_mesis,	sistoliki_piesi, diastoliki_piesi, m_date FROM measurements WHERE pat_id='" . $pat_id['pat_id'] . "' ORDER BY m_id DESC LIMIT 1");
					$rows = mysql_fetch_array($get);
				?>
					<br /><h4>Στοιχεία Ασθενή:</h4>
					<div align="center">
						<table>
							<tr>
								<td>Όνομα:</td> <td><?php echo $pat_id['pat_name']; ?></td>
							</tr>
							<tr>
								<td>Επώνυμο:</td> <td><?php echo $pat_id['pat_surname']; ?></td>
							</tr>
							<tr>
								<td>Ύψος (cm):</td> <td><?php echo $pat_id['pat_height']; ?></td>
							</tr>
						</table>
						<br>
						</br>
					</div>	
						<?php
					if (mysql_num_rows($get)==0){
						echo '<div id="error">Δεν υπάρχουν μετρήσεις για τον ασθενή</div>';
					}
					else
					{
						?>
						<div align="center">
							<h4>Μετρήσεις:</h4>
							<table cellspacing="10">
								<th>Ημερομηνία</th>
								<th>Γλυκόζη</th>
								<th>Βάρος</th>
								<th>Περιφέρεια Μέσης</th>
								<th>Συστολική Πίεση</th>
								<th>Διαστολική Πίεση</th>
								<tr align="center">
									<td><?php echo date('d-m-Y',strtotime($rows['m_date'])); ?></td>
									<td><?php echo $rows['glikozi']; ?></td>
									<td><?php echo $rows['varos']; ?></td>
									<td><?php echo $rows['perifereia_mesis']; ?></td>
									<td><?php echo $rows['sistoliki_piesi']; ?></td>
									<td><?php echo $rows['diastoliki_piesi']; ?></td>
								</tr>
							</table>
						</div>
						<br />
						<br />
						<br />
						<center><p class="error">Παρακάτω βλέπετε τις μετρήσεις ως διαγράμματα</p></center><br /><br />
						
						<h3>Γλυκόζη (Σάκχαρο Νηστείας)</h3>
						<br />Τιμή: <?php echo $rows['glikozi']; ?>
						<br />Ημερομηνία: <?php echo date('d-m-Y',strtotime($rows['m_date'])); ?>
						
						<img src="show_measurements.php?eidos=glikozi&id=<?php echo $pat_id['pat_id']; ?>" name="line graph"/>
						<form name="form" id="form" action="history" method="POST">
							<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $pat_id['pat_id']; ?>">
							<input type="hidden" name="eidos" id="eidos" value="glikozi">
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
							<input type="submit" name="submit" id="submit" value="Πλήρες Ιστορικό Γλυκόζης">
						</form>
						<br /><br />			
						<hr />
						<h3>Βάρος</h3>
						<br />Τιμή: <?php echo $rows['varos']; ?>
						<br />Ημερομηνία: <?php echo date('d-m-Y',strtotime($rows['m_date'])); ?>
						<img src="show_measurements.php?eidos=varos&id=<?php echo $pat_id['pat_id']; ?>" name="line graph"/>
						<form name="form" id="form" action="history" method="POST">
							<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $pat_id['pat_id']; ?>">
							<input type="hidden" name="eidos" id="eidos" value="varos">
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
							<input type="submit" name="submit" id="submit" value="Πλήρες Ιστορικό Βάρους">
						</form>
						<br /><br />
						<hr />
						<h3>Περιφέρεια Μέσης</h3>
						<br />Τιμή: <?php echo $rows['perifereia_mesis']; ?>
						<br />Ημερομηνία: <?php echo date('d-m-Y',strtotime($rows['m_date'])); ?>
						<img src="show_measurements.php?eidos=perifereia_mesis&id=<?php echo $pat_id['pat_id']; ?>" name="line graph"/>
						<form name="form" id="form" action="history" method="POST">
							<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $pat_id['pat_id']; ?>">
							<input type="hidden" name="eidos" id="eidos" value="perifereia_mesis">
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
							<input type="submit" name="submit" id="submit" value="Πλήρες Ιστορικό Περιφέρειας Μέσης">
						</form>
						<br /><br />
						<hr />
						<h3>Πίεση</h3>
						<br />Συστολική πίεση: <?php echo $rows['sistoliki_piesi']; ?>
						<br />Διαστολική πίεση: <?php echo $rows['diastoliki_piesi']; ?>
						<br />Ημερομηνία: <?php echo date('d-m-Y',strtotime($rows['m_date'])); ?>
						<img src="show_measurements.php?eidos=piesi&id=<?php echo $pat_id['pat_id']; ?>" name="line graph"/>
						<form name="form" id="form" action="history" method="POST">
							<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $pat_id['pat_id']; ?>">
							<input type="hidden" name="eidos" id="eidos" value="piesi">
							<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
							<input type="submit" name="submit" id="submit" value="Πλήρες Ιστορικό Πίεσης">
						</form>
						<br /><br />
						<hr />
						<br /><br /><br /><br />
						<a href="#wrap">Πίσω στην κορυφή</a>
						<div align="right">
						<?php
							//get doctor's id
							$sql = mysql_query("SELECT doc_id FROM doctor WHERE user_id = '" . $_SESSION['user_id'] . "'");
							$doctor = mysql_fetch_array($sql);
							$d_id = $doctor['doc_id'];
							
							//get patient's id
							$p_id = $pat_id['pat_id'];
						?>
							<form id="form" name="form" method="GET" action="conduct_report" >
								<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $p_id; ?>" />
								<input type="hidden" name="doc_id" id="doc_id" value="<?php echo $d_id; ?>" />
								<input type="submit" id="submit" name="submit" value="Σύνταξη Αναφοράς">
							</form>
						</div>
					<?php
					}
					?>
					
				</div>
			<?php
				//insert the page footer and navigation menu
				require_once('footer.php');
			?>