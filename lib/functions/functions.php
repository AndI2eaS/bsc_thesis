<?php
	//session, in order to have that command to every page.
	//the cookie that is sent by the session id is only accessible by http and not by javascript, for security reasons
	ini_set('session.cookie_httponly', true);
	session_start();
	
	//if a cookie is set, it sets the cookie into a session
	function ses_name()
	{
		if (isset($_COOKIE['username']) && $_COOKIE['user_id'] && $_COOKIE['user_type'])
		{
			$_SESSION['username'] = $_COOKIE['username'];
			$_SESSION['user_id'] = $_COOKIE['user_id'];
			$_SESSION['user_type'] = $_COOKIE['user_type'];
		}
	}
	
	//login check function
	function loggedin()
	{
		if (isset($_SESSION['username']) || isset($_COOKIE['username'])){
			$loggedin = TRUE;
			return $loggedin;
		}
	}
	
	//check into the database if a username already exists
	function uniqueUser($user)
	{
		$username = $user;
		$sql = "SELECT COUNT(*) as NUMBER FROM user WHERE username='$username'";
		$res = mysql_query($sql);
		$num = mysql_result($res,0,"NUMBER");

		if ($num > 0)
			return true;
		return false;	
	}
	
	//check into the database if an email already exists
	function uniqueEmail($email)
	{
		$user_email = $email;
		$sql = "SELECT COUNT(*) as NUMBER FROM user WHERE user_email='$user_email'";
		$res = mysql_query($sql);
		$num = mysql_result($res,0,"NUMBER");
		
		if ($num > 0)
			return true;
		return false;	
	}
	
	//validate the length of the value entered
	function validateLengthName($name)
	{
		//if it's NOT valid
		if(strlen($name) < 3)
			return false;
		//if it's valid
		else
			return true;
	}
	
	//validate the value characters
	function validateName($name)
	{
		return preg_match('/^\pL+$/u', $name);
	}
	
	//validate the value numbers
	function validateNumber($number)
	{
		return preg_match('/^[0-9]+$/', $number);
	}
	
	//validate the email format
	function validateEmail($email)
	{
		return preg_match("^[a-zA-Z0-9.]+[a-zA-Z0-9_.-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$^", $email);
	}
	
	function makeClickableLinks($s) {
		return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $s);
	}
	
	//make pagination of our results
	function generate_page_links($cur_page, $num_pages) {
		$page_links = '';

		// If this page is not the first page, generate the "previous" link
		if ($cur_page > 1) {
		  $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page - 1) . '"><</a> ';
		}
		else {
		  $page_links .= '< ';
		}

		// Loop through the pages generating the page number links
		for ($i = 1; $i <= $num_pages; $i++) {
		  if ($cur_page == $i) {
			$page_links .= ' ' . $i;
		  }
		  else {
			$page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '"> ' . $i . '</a>';
		  }
		}

		// If this page is not the last page, generate the "next" link
		if ($cur_page < $num_pages) {
		  $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page + 1) . '">></a>';
		}
		else {
		  $page_links .= ' >';
		}

		return $page_links;
	}
	
	//make a new similar function for the medical history of our patients
	function generate_page_links_history($cur_page, $num_pages, $eidos, $pat_id, $id) {
		$page_links = '';

		// If this page is not the first page, generate the "previous" link
		if ($cur_page > 1) {
		  $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page - 1) . '&eidos=' . $eidos . '&pat_id=' . $pat_id . '&id=' . $id . '"><</a> ';
		}
		else {
		  $page_links .= '< ';
		}

		// Loop through the pages generating the page number links
		for ($i = 1; $i <= $num_pages; $i++) {
		  if ($cur_page == $i) {
			$page_links .= ' ' . $i;
		  }
		  else {
			$page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '&eidos=' . $eidos . '&pat_id=' . $pat_id . '&id=' . $id . '"> ' . $i . '</a>';
		  }
		}

		// If this page is not the last page, generate the "next" link
		if ($cur_page < $num_pages) {
		  $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page + 1) . '&eidos=' . $eidos . '&pat_id=' . $pat_id . '&id=' . $id . '">></a>';
		}
		else {
		  $page_links .= ' >';
		}

		return $page_links;
	}
	
	
	//make a new similar function for the view of reports from the doctor
	function generate_page_links_doc($cur_page, $num_pages, $pat_id) {
		$page_links = '';

		// If this page is not the first page, generate the "previous" link
		if ($cur_page > 1) {
		  $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page - 1) . '&id=' . $pat_id . '"><</a> ';
		}
		else {
		  $page_links .= '< ';
		}

		// Loop through the pages generating the page number links
		for ($i = 1; $i <= $num_pages; $i++) {
		  if ($cur_page == $i) {
			$page_links .= ' ' . $i;
		  }
		  else {
			$page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . $i . '&id=' . $pat_id . '"> ' . $i . '</a>';
		  }
		}

		// If this page is not the last page, generate the "next" link
		if ($cur_page < $num_pages) {
		  $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($cur_page + 1) . '&id=' . $pat_id . '">></a>';
		}
		else {
		  $page_links .= ' >';
		}

		return $page_links;
	}
?>