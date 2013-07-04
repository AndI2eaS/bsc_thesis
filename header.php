<div id="header">
	<h1>Παρακολούθηση - Συμμόρφωση Ασθενών με Μεταβολικό Σύνδρομο</h1>
	<h2><?php echo $page_title; ?></h2>
</div>
<?php
if (loggedin())
{
?>
	<div id="info_header">
		<h1>Καλώς ήλθατε, <a href='profile'><?php echo $_SESSION['username']; ?></a>, <a href='logout'>Έξοδος</a></h1>
	</div>
<?php
}
?>