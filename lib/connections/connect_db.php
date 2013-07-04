<?php
	// Set database connection
	mysql_connect('localhost','root','') or die(mysql_error());
	mysql_select_db('thesis');
  
	//query the database using utf-8 collation
	mysql_query("SET NAMES 'utf8'");
?>