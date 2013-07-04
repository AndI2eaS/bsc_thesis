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
	
	// Mailbox Parsing for deleting inbox messages
	if (isset($_POST['submit']))
	{
		foreach ($_POST as $key => $value) 
		{
			$id = $value;
			if ($key != "submit") 
			{
			   $sql = mysql_query("UPDATE private_messages SET recipient_delete='1', opened='1' WHERE mes_id='$id' AND to_id='" . $_SESSION['user_id'] . "' LIMIT 1");
			}
		}
		header("location: pm_inbox");
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<?php 
			//get the title and the link to CSS
			require_once('title_head.php'); 
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script language="javascript" type="text/javascript">
			//function to check all fields in order to delete
			function toggleChecks(field) 
			{
				if (document.form.toggleAll.checked == true)
				{
					for (i = 0; i < field.length; i++) 
					{
						field[i].checked = true;
					}
				} 
				else 
				{
					for (i = 0; i < field.length; i++) 
					{
						field[i].checked = false;
					}		
				}
			}

			//function to show and hide messages
			$(document).ready(function() 
			{ 
				$(".show_message").click(function () 
				{ 
					if ($(this).next().is(":hidden")) 
					{
						$(".hiddenDiv").hide();
						$(this).next().slideDown("fast"); 
					} 
					else 
					{ 
						$(this).next().hide(); 
					} 
				}); 
			});
			
			//function to call markAsRead.php in order to set the messages into the DB as read
			function markAsRead(msgID) 
			{
				$.post("markAsRead.php",{ mes_id:msgID, owner_id:<?php echo $_SESSION['user_id']; ?> } ,function(data) 
				{
					$('#subject_line_'+msgID).addClass('msgRead');
				});
			}
		</script>
		<style type="text/css"> 
			.hiddenDiv{display:none}
			.msgUnread {font-weight:bold;}
			.msgRead {font-weight:100;color:#666;}
		</style>
    </head>
	<body>
		<div id="wrap">
			<?php
				//get the page header and set the page title value
				$page_title = 'Εισερχόμενα';
				require_once("header.php");
			?>
			<div id="content">
				<div class="right">
					<a href="pm_inbox">Εισερχόμενα</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="pm_sentbox">Εξερχόμενα</a>
					<br /><br />
					<table width="100%" style="background-color:#F2F2F2;" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="732" valign="top">
								<form name="form" action="" method="POST" enctype="multipart/form-data">
									<table width="94%" border="0" align="center" cellpadding="4">
										<tr>
											<td width="3%" align="right" valign="bottom">
												<img src="images/crookedArrow.png" width="16" height="17" /><br /><br />
											</td>
											<td width="97%" valign="top">
												<input type="submit" name="submit" id="submit" value="Διαγραφή" /><br /><br />
											</td>
										</tr>
									</table>
									<table width="96%" border="0" align="center" cellpadding="5" style="border: #999 1px solid;">
										<tr>
											<td width="4%" valign="top">
												<input name="toggleAll" id="toggleAll" type="checkbox" onclick="toggleChecks(document.form.cb)" />
											</td>
											<td width="20%">Από</td>
											<td width="58%">Θέμα</td>
											<td width="18%">Ημερομηνία</td>
										</tr>
									</table> 
									<?php
									
									$cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
									$results_per_page = 3;  // number of results per page
									$skip = (($cur_page - 1) * $results_per_page);
									
									//get personal messages from the database
									$sql = "SELECT * FROM private_messages WHERE to_id='" . $_SESSION['user_id'] . "' AND recipient_delete='0' ORDER BY mes_id DESC";
									$result = mysql_query($sql);
									
									
									$total = mysql_num_rows($result);
									$num_pages = ceil($total / $results_per_page);
									
									$query =  $sql . " LIMIT $skip, $results_per_page";
									$result = mysql_query($query);
									
									while($row = mysql_fetch_array($result)){
										$date = date('d-m-Y H:i:s', strtotime($row['time_sent']));
										
										if($row['opened'] == "0")
										{
											$textWeight = 'msgDefault';
										} 
										else 
										{
											$textWeight = 'msgRead';
										}
										$from_id = $row['from_id'];    
										
										//get the sender's username
										$sql1 = "SELECT user_id, username,user_type FROM user WHERE user_id='$from_id' LIMIT 1";
										$result1 = mysql_query($sql1);
										while($row1 = mysql_fetch_array($result1))
										{ 
											$f_id = $row1['user_id']; 
											$f_name = $row1['username'];
											$f_type = $row1['user_type'];
										}
									?>
										<table width="96%" border="0" align="center" cellpadding="5">
											<tr>
												<td width="4%" valign="top">
													<input type="checkbox" name="cb<?php echo $row['mes_id']; ?>" id="cb" value="<?php echo $row['mes_id']; ?>" />
												</td>
												<td width="20%" valign="top"><?php if ($f_type != 1) { ?><a href="profile?id=<?php echo $f_id; ?>"><?php echo $f_name; ?></a><?php } else echo '<i>Σύστημα</i>'; ?></td>
												<td width="58%" valign="top">
													<span class="show_message" style="padding:3px;">
														<a class="<?php echo $textWeight; ?>" id="subject_line_<?php echo $row['mes_id']; ?>" style="cursor:pointer;" onclick="markAsRead(<?php echo $row['mes_id']; ?>)"><?php echo stripslashes($row['subject']); ?></a>
													</span>
													<div class="hiddenDiv"> <br />
													<?php echo stripslashes(wordwrap(nl2br(makeClickableLinks($row['message'])), 54, "\n", true)); ?>
													<br /><br /><?php if ($f_type != 1) {?><a href="send_pm?id=<?php echo  $f_id; ?>">Απάντηση</a><?php }?><br />
													</div>
												</td>
												<td width="18%" valign="top"><span style="font-size:10px;"><?php echo $date; ?></span></td>
											</tr>
										</table>
										<hr style="margin-left:20px; margin-right:20px;" />
									<?php
									}//close Main while loop
									
									echo '<center>';
									if ($num_pages > 1) {
										echo generate_page_links($cur_page, $num_pages);
									}
									echo '</center>';
									?>
								</form>
							</td>
						</tr>
					</table>
				</div>
			<?php
				//inlclude the footer and navigation menu
				require_once('footer.php');
			?>