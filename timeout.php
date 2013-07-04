<?php
	//set the lifetime of a session and expire after being inactive for 20 minutes
	$inactive = 1200;
	if(isset($_SESSION['start']) ) {
		$session_life = time() - $_SESSION['start'];
		if($session_life > $inactive){
			header("Location: logout");
		}
	}
	$_SESSION['start'] = time();

?>