<?php
	require('tfpdf.php');
	require_once('lib/connections/connect_db.php');
	require_once('lib/functions/functions.php');

	$id = (int)$_GET['id'];
	$pat_id = (int)$_GET['pat_id'];
	//get the report data
	$sql = "SELECT rep_text, rep_date, rep_file FROM doctor_report WHERE rep_id = '$id'";
	$result = mysql_query($sql);
	$res = mysql_fetch_array($result);

	//format the date to dd-mm-YYYY
	$date = date('d-m-Y',strtotime($res['rep_date']));
	$text = $res['rep_text'];

	$sql1 = "SELECT d.doc_name, d.doc_surname, d.doc_city, d.doc_address, d.doc_personal_phone, d.doc_office_phone FROM doctor as d INNER JOIN patient as p USING (doc_id) WHERE pat_id='$pat_id'";
	$result1 = mysql_query($sql1);
	$res1 = mysql_fetch_array($result1);

	$pdf = new tFPDF();
	$pdf->AddPage();

	$message = "
Στοιχεία Ιατρού
Όνομα: $res1[doc_name]
Επώνυμο: $res1[doc_surname]
Διεύθυνση: $res1[doc_city], $res1[doc_address]
Τηλέφωνο Γραφείου: $res1[doc_office_phone]
Τηλέφωνο Προσωπικό: $res1[doc_personal_phone]\n

ΑΝΑΦΟΡΑ ~ $date
$text";

	// Add a Unicode font (uses UTF-8)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->SetFont('DejaVu','',12);


	$pdf->MultiCell(0,10,$message,'c');

	
	$pdf->Output();
?>