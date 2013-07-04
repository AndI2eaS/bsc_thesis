<?php
	header('Content-type: text/html; charset=UTF-8');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	//Of all the patients, allow only the one who is connected to him to be able to see his profile
	if (isset($_GET['id']))
	//if we were brought here by url, check if the user is a patient, if yes then see if he is connected to this doctor
	{
		if ($_SESSION['user_type'] == 3)
		{
			$query1 = "SELECT d.user_id FROM patient as p INNER JOIN doctor as d USING (doc_id) WHERE p.user_id = '" . $_SESSION['user_id'] . "'";
			$data = mysql_query($query1);
			$result_query = mysql_fetch_array($data);
			
			if ($_GET['id'] != $result_query['user_id'])
			{
				header('Location: profile');
				exit();
			}
		}
	}
	
	
	//get the user email from the database
	$query = "SELECT user_email FROM user as u INNER JOIN doctor as d USING(user_id) WHERE doc_id = '" . $row['doc_id'] . "'";
	$res = mysql_query($query);
	$row1 = mysql_fetch_array($res);
	
	
	echo '<div class="profile_info">';
		echo 'Κατηγορία Χρήστη: &nbsp;Ιατρός';
		echo '<hr /><br />';
		echo '<table>';
			if (!empty($row['doc_name']))
			{
				echo '<tr><td>Όνομα: &nbsp;</td><td>' . $row['doc_name'] . '</td></tr>';
			}
			if (!empty($row['doc_surname']))
			{
				echo '<tr><td>Επώνυμο: &nbsp;</td><td>' . $row['doc_surname'] . '</td></tr>';
			}
			if (!empty($row['doc_city']) || !empty($row['doc_address']))
			{
				echo '<tr><td>Περιοχή: &nbsp;</td><td>' . $row['doc_city'] . ', ' . $row['doc_address'] . '</td></tr>';
			}
			if (!empty($row['doc_office_phone']))
			{
				echo '<tr><td>Τηλ. Γραφείου: &nbsp;</td><td>' . $row['doc_office_phone'] . '</td></tr>';
			}
			if (!empty($row['doc_personal_phone']))
			{
				echo '<tr><td>Τηλ. Προσωπικό: &nbsp;</td><td>' . $row['doc_personal_phone'] . '</td></tr>';
			}
			if (!empty($row1['user_email']))
			{
				$wrapped_email = wordwrap($row1['user_email'], 30, "<br/>", 1);
				echo '<tr><td>Email: &nbsp;</td><td><a href="mailto:$wrapped_email">' . $wrapped_email . '</a></td></tr>';
			}
		echo '</table>';
		    
	echo '</div>';
	
	echo '<div class="profile_pic">';
		if (!empty($row['doc_picture']))
		{
			echo '<img src="' . MM_UPLOADPATH . $row['doc_picture'] . '" alt="Εικόνα Προφίλ" width="120px" height="120px" />';
		}
		else
		{
			echo '<img src="' . MM_UPLOADPATH . 'nopic.jpg' . '" alt="Εικόνα Προφίλ" width="120px" height="120" />';
		}
		if (!isset($_GET['id']) || ($_SESSION['user_id'] == $_GET['id'])) {
			echo '<br /><br />';
			echo '<center>';
				echo '<a href="edit_doc_profile"><img src="images/edit.png" title="Επεξεργασία Προφίλ"></a>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
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