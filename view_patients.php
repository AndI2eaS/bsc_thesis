<?php
	require_once('lib/functions/functions.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin())
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	ses_name();
	
	require_once('timeout.php');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php 
			//get the title and the link to CSS
			require_once('title_head.php');
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta content="Download free icons for web design and software development. 24x24 Free Pixel Icons set features dozens of images commonly used in applications, including New, Open, Save, Cut, Copy, Paste, and so on." name=description>
		<meta content="free icons, download icons, pixel icons, toolbar icons, ico" name=keywords>
		<script type="text/javascript">
			function getSuggestions(value){
				if(value!=""){
					$.post("search_patients.php", {search:value}, function(data){
						$("#suggestions").html(data);
						doCSS();
						if (data==""){
							undoCSS();
						}
					});
				}
				else{
					removeSuggestions();
				}
			}
			function removeSuggestions(){
				$("#suggestions").html("");
				undoCSS();
			}
			
			function addText(value){
				$("#search").val(value);
			}
			
			function doCSS(){
				$("#suggestions").css({
					'border' : 'solid',
					'border-width' : '1px',
					'border-color' : '#C8C8C8'
				});
			}
			
			function undoCSS(){
				$("#suggestions").css({
					'border' : '',
					'border-width' : ''
				});
			}
		</script>
		<style type="text/css">
			#content_holder{
				margin-top: 20px;
				width: 300px;
			}
			
			#suggestions{
				text-align: left;
				padding-left: 3px;
				font-family: Georgia;
				font-size: 15px;
			}
			
			#link:hover{
				background-color: #E0E0E0;
				cursor: default;
			}
		</style>
	</head>
	<body>
		<div id="wrap">
			<?php
				//get the page header and set the page title value
				$page_title = 'Προβολή Ασθενών';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
				<div id="content_holder">
					<form id="form" name="form" method="POST" action="">
						<table>
							<tr>
								<td><input type="text" name="search" id="search" onkeyup="getSuggestions(this.value);" onblur="setTimeout('removeSuggestions()',200);" /></td>
								<td><input type="submit" name="submit" id="submit" value="Search" /></td>
							</tr>	
						</table>
						<div id="suggestions"></div>
					</form>
				</div>
					<br /><br />
					<table width="80%" border="0" align="center" cellpadding="5" style="border: #999 1px solid;">
						<tr>
							<td width="50%">Ονοματεπώνυμο</td>
							<td width="30%">Ενέργεια</td>
						</tr>
					</table>
					<?php
					//if the submit button has been pressed: meaning that a search was carried out
					if (isset($_POST['submit']))
					{
						//get the search variable
						$search = mysql_real_escape_string(trim($_POST['search']));
						//split the search variable in two words
						$search_words = explode(" ",$search);
						
						//if the search word was splitted query the database
						if (isset($search_words[0]) && isset($search_words[1]))
						{
							$patient = mysql_query("SELECT user_id, pat_name, pat_surname FROM patient WHERE pat_name LIKE '$search_words[0]%' AND pat_surname LIKE '$search_words[1]%'");
						}
						//else if the search word was only 1 word query the database
						else 
						{
							$patient = mysql_query("SELECT user_id, pat_name, pat_surname FROM patient WHERE pat_name LIKE '$search%' OR pat_surname LIKE '$search%'");
						}
						
						//echo the results and place links to their profiles or private messages
						while($rows = mysql_fetch_array($patient))
						{
					?>
							<table width="80%" border="0" align="center" cellpadding="5">
								<tr>
									<td width="50%" align="top"><?php echo $rows['pat_name'].' '.$rows['pat_surname']; ?></td>
									<td width="30%" align="top">
										<a href="profile?id=<?php echo $rows['user_id']; ?>"><img src="images/info.png" title="Προβολή Προφίλ"></a>
										&nbsp;&nbsp;&nbsp;
										<a href="send_pm?id=<?php echo $rows['user_id']; ?>"><img src="images/pm.png" title="Προσωπικό Μήνυμα"></a>
										&nbsp;&nbsp;&nbsp;
										<?php 
											if ($_SESSION['user_type'] == 1)
											{
										?>
												<a href="delete?id=<?php echo $rows['user_id']; ?>"><img src="images/delete.png" title="Διαγραφή Λογαριασμού"></a>
										<?php
											}
											else
											{
										?>
												<a href="measurements?id=<?php echo $rows['user_id']; ?>"><img src="images/diagram.png" title="Προβολή Μετρήσεων"></a>
										<?php
											}
										
										?>
									</td>
								</tr>
							</table>
							<hr width="80%" />
					<?php
						}
					}
					//if the submit button was not pressed
					else
					{
						//do the pagination stuff
						$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
						$results_per_page = 15;  // number of results per page
						$skip = (($cur_page - 1) * $results_per_page);
						
						//query database and echo again the results
						$get_patients = "SELECT user_id, pat_name, pat_surname FROM patient";
						$result = mysql_query($get_patients);
						
						$total = mysql_num_rows($result);
						$num_pages = ceil($total / $results_per_page);
						
						$query =  $get_patients . " LIMIT $skip, $results_per_page";
						$result = mysql_query($query);
						while($row = mysql_fetch_array($result))
						{
					?>
							<table width="80%" border="0" align="center" cellpadding="5">
								<tr>
									<td width="50%" align="top"><?php echo $row['pat_name'].' '.$row['pat_surname']; ?></td>
									<td width="30%" align="top">
										<a href="profile?id=<?php echo $row['user_id']; ?>"><img src="images/info.png" title="Προβολή Προφίλ"></a>
										&nbsp;&nbsp;&nbsp;
										<a href="send_pm?id=<?php echo $row['user_id']; ?>"><img src="images/pm.png" title="Προσωπικό Μήνυμα"></a>
										&nbsp;&nbsp;&nbsp;
										<?php 
											if ($_SESSION['user_type'] == 1)
											{
										?>
												<a href="delete?id=<?php echo $row['user_id']; ?>"><img src="images/delete.png" title="Διαγραφή Λογαριασμού"></a>
										<?php
											}
											else
											{
										?>
												<a href="measurements?id=<?php echo $row['user_id']; ?>"><img src="images/diagram.png" title="Προβολή Μετρήσεων"></a>
										<?php
											}
										
										?>
									</td>
								</tr>
							</table>
							<hr width="80%" />
					<?php
						}
					?>
						<table width="80%" border="0" align="center" cellpadding="5">
							<tr>
								<td width="50%" align="center"><?php if ($num_pages > 1) 
							{
								echo generate_page_links($cur_page, $num_pages);
							} ?></td>
								</tr>
						</table>
					<?php
					}
					?>
					
					
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>