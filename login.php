<?php
	require_once("lib/functions/functions.php");
	require_once('lib/connections/connect_db.php');
	
	if (loggedin())
	{ //using the function loggedin from functions.php we check if it TRUE, from the value returned. 
		ses_name();
		
		if ($_SESSION['user_type'] == 1)
		{
			header('Location: admin');
			exit();
		}
		else if ($_SESSION['user_type'] == 2)
		{
			header('Location: doctor');
			exit();
		}
		else if ($_SESSION['user_type'] == 3)
		{
			header('Location: patient');
			exit();
		}
	}
	
	if (isset($_POST['submit'])) //if submit button has been pressed
	{
		//get secure posted data
		$username = mysql_real_escape_string(trim($_POST['username']));
		$password = mysql_real_escape_string(trim($_POST['password']));
		$rememberme = $_POST['rememberme'];
		
		//check if the variables exist
		if ($username&&$password){
			
			//get data from DB using the username entered
			$login = "SELECT * FROM user WHERE username='$username'";
			$result = mysql_query($login);
			
			//Grab data from the database included in the query 
			$row = mysql_fetch_array($result);
			
			//get the password from the database. It's SHA-1 encrypted
			if (isset($row))
			{
				$db_password = $row['password'];
			}
			
			if (sha1($password)==$db_password)
			{
				//if entered password equals to the password from the DB
				$loginok = TRUE;
			}
			else
			{
				$loginok = FALSE;
			}
			
			//check if the login data are correct and set the cookie or session, depending on the user's choice
			if ($loginok==TRUE&&mysql_num_rows($result)==1) //if the username exists and the password is correct
			{
				if ($rememberme=="on") //check if Remember me checkbox has been clicked
				{
					//if it has been clicked set the cookies (expires in 2 days)
					setcookie("username",$username, time()+(60 * 60 * 24 * 2)); 
					setcookie("user_id",$row['user_id'], time()+(60 * 60 * 24 * 2));
					setcookie("user_type",$row['user_type'], time()+(60 * 60 * 24 * 2));
				}
				else if ($rememberme=="")
				{
					//if the Remember me checkbox has not been clicked, set the session variables
					$_SESSION['username']=$username;
					$_SESSION['user_id']=$row['user_id'];
					$_SESSION['user_type']=$row['user_type'];
				}
				
				//Depending on the user type that tries to login, redirect him to the appropriate page and set the session 
				if ($row['user_type'] == 1)
				{
					//redirect to admin page
					header('Location: admin');
					exit();
				}
				else if ($row['user_type'] == 2)
				{
					//redirect to doctor's page
					header('Location: doctor');
					exit();
				}
				else if ($row['user_type'] == 3)
				{
					//redirect to patient's page
					header('Location: patient');
					exit();
				}
			}
			else
			{
				$error = "Έχετε εισάγει λάθος Όνομα Χρήστη/Κωδικό ή συνδυασμό αυτών."; 
				//echo an error message, if the entered values do not match the DB data
			}
			
		}
		else
		{
			$error = "Παρακαλούμε εισάγετε Όνομα Χρήστη και Κωδικό.";  
			//If the variables do not exist show this message
		}
	}
	//close the database connection
	mysql_close();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php
			//get the page title, and the link to CSS
			require_once('title_head.php');
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header
				$page_title = '';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<center>
						<h4>Είσοδος Χρήστη</h4>
						<!-- The login form -->
						<form id="form" action="" method="POST">
							<div>
								<label class="label" for="username">Όνομα Χρήστη</label>
								<input type="text" id="username" name="username"/>
							</div>
							<div>
								<label class="label" for="password">Κωδικός</label>
								<input type="password" id="password" name="password" />
							</div>
							<div>
								<label class="label" for="rememberme">Να με θυμάσαι</label>
								<input type="checkbox" name="rememberme" id="rememberme"/>
							</div>
							<div>
								<input type="submit" id="submit" name="submit" value="Είσοδος">
							</div>
						</form>
						
						<br /><br />
						<a href="forgotpassword">Ξεχάσατε τον κωδικό σας;</a>
						<div id="error">
							<?php
								if (!empty($error))
								{
									echo $error;
								}
							?>
						</div>
					</center>
				</div> <!-- end right div -->
			<?php
				//insert the page footer and navigation menu stuff
				require_once("footer.php");
			?>
