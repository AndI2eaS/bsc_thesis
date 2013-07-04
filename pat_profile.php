<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	if (isset($_GET['id']))
	//if we were brought here by url, check if the user is a patient, if yes then check if he is the owner of this profile
	{
		if (($_SESSION['user_type'] == 3) && ($_SESSION['user_id'] != $_GET['id']))
		{
			header('Location: profile');
			exit();
		}
		
		//check if the doctor is connected to this patient
		if ($_SESSION['user_type'] == 2)
		{
			$q = mysql_query("SELECT doc_id FROM doctor WHERE user_id='".$_SESSION['user_id']."'");
			$d = mysql_fetch_array($q);
			
			$s = mysql_query("SELECT doc_id FROM patient WHERE user_id = '".$_GET['id']."'");
			$p = mysql_fetch_array($s);
			
			if ($d['doc_id'] != $p['doc_id']) 
			{
				header('Location: profile');
				exit();
			}
			
		}
	}
	else
	{
		//get the user email from the database
		$query = "SELECT user_email FROM user as u INNER JOIN patient as p USING(user_id) WHERE pat_id = '" . $row['pat_id'] . "'";
		$res = mysql_query($query);
		$row1 = mysql_fetch_array($res);
	}
	
	
	
	
	echo '<div class="profile_info">';
		echo 'Κατηγορία Χρήστη: &nbsp;Ασθενής';
		echo '<hr /><br />';
		echo '<table>';
			if (!empty($row['pat_name']))
			{
				echo '<tr><td>Όνομα: &nbsp;</td><td>' . $row['pat_name'] . '</td></tr>';
			}
			if (!empty($row['pat_surname']))
			{
				echo '<tr><td>Επώνυμο: &nbsp;</td><td>' . $row['pat_surname'] . '</td></tr>';
			}
			if (!empty($row['pat_city']) || !empty($row['pat_address']))
			{
				echo '<tr><td>Περιοχή: &nbsp;</td><td>' . $row['pat_city'] . ', ' . $row['pat_address'] . '</td></tr>';
			}
			if (!empty($row['pat_phone']))
			{
				echo '<tr><td>Τηλέφωνο: &nbsp;</td><td>' . $row['pat_phone'] . '</td></tr>';
			}
			if (!empty($row1['user_email']))
			{
				$wrapped_email = wordwrap($row1['user_email'], 30, "<br/>", 1);
				echo '<tr><td>Email: &nbsp;</td><td><a href="mailto:$wrapped_email">' . $wrapped_email . '</a></td></tr>';
			}
			if (!empty($row['pat_birth_date'])) 
			{
				//if it is this user's profile
				if (!isset($_GET['id']) || ($_SESSION['user_id'] == $_GET['id'])) 
				{
					//show full birth date
					echo '<tr><td>Ημ. Γέννησης: &nbsp;</td><td>' . $row['pat_birth_date'] . '</td></tr>';
				}
				else 
				{
					//else just show the year
					list($year, $month, $day) = explode('-', $row['pat_birth_date']);
					$now = date("Y");
					$age = $now - $year;
					echo '<tr><td>Ημ. Γέννησης: &nbsp;</td><td>' . $year . ' (' . $age . ')</td></tr>';
				}
			}
			if (!empty($row['pat_sex'])) 
			{
				echo '<tr><td>Φύλο: &nbsp;</td><td>';
				if ($row['pat_sex'] == 'Α') 
				{
					echo 'Αρσενικό';
				}
				else if ($row['pat_sex'] == 'Θ') 
				{
					echo 'Θηλυκό';
				}
				else 
				{
					echo '?';
				}
				echo '</td></tr>';
			}
			if (!empty($row['pat_height']))
			{
				echo '<tr><td>Ύψος: &nbsp;</td><td>' . $row['pat_height'] . ' cm</td></tr>';
			}
		echo '</table>';
		    
	echo '</div>';
	
	echo '<div class="profile_pic">';
		if (!empty($row['pat_picture']))
		{
			echo '<img src="' . MM_UPLOADPATH . $row['pat_picture'] . '" alt="Εικόνα Προφίλ" width="120px" height="120px" />';
		}
		else
		{
			echo '<img src="' . MM_UPLOADPATH . 'nopic.jpg' . '" alt="Εικόνα Προφίλ" width="120px" height="120" />';
		}
		if (!isset($_GET['id']) || ($_SESSION['user_id'] == $_GET['id'])) {
			echo '<br /><br />';
			echo '<center>';
				echo '<a href="edit_pat_profile"><img src="images/edit.png" title="Επεξεργασία Προφίλ"></a>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				echo '<a href="delete?id='.$_SESSION['user_id'].'"><img src="images/delete.png" title="Διαγραφή Λογαριασμού"></a>';
			echo '</center>';
		}
		else
		{
			echo '<center>';
			echo '<br /><br /><a href="send_pm?id='.$_GET['id'].'"><img src="images/pm.png" title="Προσωπικό Μήνυμα"></a>';
			echo '</center>';
		}
	echo '</div>';
?>