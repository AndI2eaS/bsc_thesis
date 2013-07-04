<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	//check if the user is an administrator and replace the cookie variables with sessions
	require_once('lib/authorization/admin_auth.php');
	
	echo '<div class="profile_info">';
		echo 'Κατηγορία Χρήστη: &nbsp;Διαχειριστής';
		echo '<hr /><br />';
		echo '<table>';
			if (!empty($row['user_email']))
			{
				$wrapped_email = wordwrap($row['user_email'], 30, "<br/>", 1);
				echo '<tr><td>Email: &nbsp;</td><td><a href="mailto:$wrapped_email">' . $wrapped_email . '</a></td></tr>';
			}
		echo '</table>';
		echo '<center>';
			echo '<br />';
			echo '<a href="edit_adm_profile"><img src="images/edit.png" title="Επεξεργασία Προφίλ"></a>';
			
		echo '</center>';
	echo '</div>';
	echo '<div class="profile_pic">';
		echo '<img src="images/profiles/admin.jpg" alt="Εικόνα Προφίλ" width="120px" height="120px" />';
	echo '</div>';
?>