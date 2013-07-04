<h4>Μενού</h4>
	<ul>
		<li><a href="index">Αρχική Σελίδα</a></li>
		
		<?php
			//if the user is not logged in show this menu
			if (!loggedin())
			{
		?>
				<li><a href="login">Σύνδεση</a></li>
				<li><a href="contact">Επικοινωνία</a></li>
		<?php
			}
			//else if he is logged in show different menu
			else
			{
		?>
				<li><a href="profile">Προβολή Προφίλ</a></li>
				<!-- Mechanism to check if there is a new personal message-->
				<script type="text/javascript">
					$(function() {
						getStatus();
					});
					
					function getStatus() {
						$('div#status').load('check_pm.php');
						setTimeout("getStatus()",5000);
					}
				</script>
				<li><div id="status"></div></li>
				<?php
					//If the user is an administrator show these options
					if ($_SESSION['user_type'] == 1)
					{
				?>
						<li><a href="view_users">Προβολή Χρηστών</a></li>
						<li><a href="patient_insert">Εγγραφή Ασθενή</a></li>
				<?php
					}
					else 
					{
				?>
						<?php 
							//If the user is a doctor show these options
							if ($_SESSION['user_type'] == 2)
							{
						?>
								<li><a href="view_spec_patients">Προβολή Ασθενών & Μετρήσεων</a></li>
								<li><a href="new_patient">Αίτηση Εγγραφής Ασθενή</a></li>
						<?php 
							}
							//If the user is a patient show these options								
							if ($_SESSION['user_type'] == 3)
							{
						?>
								<li><a href="view_doctor">Προβολή Ιατρού</a></li>
								<li><a href="insert_measurements">Εισαγωγή Μετρήσεων</a></li>
								<li><a href="view_report">Προβολή Αναφορών Ιατρού</a></li>
						<?php 
							}
						?>
						<li><a href="contact">Επικοινωνία</a></li>
				<?php
					}
			}
		?>
	</ul>