<?php
	session_start();
	
	//delete the session vars by clearing the $_SESSION array
	$_SESSION = array();
	
	//delete the session cookie by setting its expiration to an hour ago (3600)
    if (isset($_COOKIE[session_name()])) 
	{
      setcookie(session_name(), '', time() - 3600);
    }
	
	//destroy session
	session_destroy();
	
	//unset cookies
	setcookie("username", "", time()-7200);
	setcookie("user_id", "", time()-7200);
	setcookie("user_type", "", time()-7200);
	
	//redirect the user to the login page
	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/login';
	header('Location: ' . $home_url);
?>