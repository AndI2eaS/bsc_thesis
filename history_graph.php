<?php
	require_once('lib/functions/functions.php');
	require_once('jpgraph/jpgraph.php');
	require_once('jpgraph/jpgraph_line.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	require_once('timeout.php');
	
	//get the variables with the GET method via url
	$eidos = mysql_real_escape_string(trim($_GET['eidos']));
	$id = (int)$_GET['id'];
	
	// Get the results from the database
	$query = "SELECT m_id, glikozi, varos, perifereia_mesis, sistoliki_piesi, diastoliki_piesi, m_date FROM measurements WHERE pat_id = '$id'";
	$result = mysql_query($query);
	
	
	// Get all results into array and count them
	$results = array();
	for ($i = 0; $row = mysql_fetch_assoc($result); $i++) {
		$results[] = $row;
	}
	// Re-format the data depending on number of results
	$data = array();
	$data1 = array();
	$data2 = array();
	
	if ($i < 14) { // Less than 14 days, show per day
		foreach ($results as $row) 
		{
			if ($eidos=='glikozi')
			{
				array_push($data,$row['glikozi']);
			}
			else if ($eidos=='varos')
			{
				array_push($data,$row['varos']);
			}
			else if ($eidos=='perifereia_mesis')
			{
				array_push($data,$row['perifereia_mesis']);
			}
			else if ($eidos=='sistoliki_piesi')
			{
				array_push($data,$row['sistoliki_piesi']);
			}
			else if ($eidos=='diastoliki_piesi')
			{
				array_push($data,$row['diastoliki_piesi']);
			}
			$timestamp = strtotime($row['m_date']);
			array_push($data1,date('d/m',$timestamp));
		}
	}
	else if ($i < 58){ //show per weeks
		$thisweek = array();
		
		$j=0;
		while(isset($results[$j])){
			if ($eidos=='glikozi')
			{
				array_push($thisweek, $results[$j]['glikozi']);
			}
			else if ($eidos=='varos')
			{
				array_push($thisweek, $results[$j]['varos']);
			}
			else if ($eidos=='perifereia_mesis')
			{
				array_push($thisweek, $results[$j]['perifereia_mesis']);
			}
			else if ($eidos=='sistoliki_piesi')
			{
				array_push($thisweek, $results[$j]['sistoliki_piesi']);
			}
			else if ($eidos=='diastoliki_piesi')
			{
				array_push($thisweek, $results[$j]['diastoliki_piesi']);
			}
			$j++;
			if ($j % 7 == 0 && $j>0) 
			{ // Every 7 days...
				$data[] = floor(array_sum($thisweek) / 7); // ...calculate the week average...
				$thisweek = array(); // ...and reset the total
			}
		}
		// if there is an incomplete week, add it to the data
		if (count($thisweek)!=0){
		$data[] = floor(array_sum($thisweek) / count($thisweek));
		}
		
		$var = count($data);
		if(isset($var))
		{
			for ($k=1; $k<=$var; $k++)
			{
				array_push($data1,"Week $k");
			}
		}
	}
	else //show per month use.. 1st month of use, 2nd month of use etc...
	{
		$thismonth = array();
		
		$j=0;
		while(isset($results[$j])){
			if ($eidos=='glikozi')
			{
				array_push($thismonth, $results[$j]['glikozi']);
			}
			else if ($eidos=='varos')
			{
				array_push($thismonth, $results[$j]['varos']);
			}
			else if ($eidos=='perifereia_mesis')
			{
				array_push($thismonth, $results[$j]['perifereia_mesis']);
			}
			else if ($eidos=='sistoliki_piesi')
			{
				array_push($thismonth, $results[$j]['sistoliki_piesi']);
			}
			else if ($eidos=='diastoliki_piesi')
			{
				array_push($thismonth, $results[$j]['diastoliki_piesi']);
			}
			$j++;
			if ($j % 30 == 0 && $j>0) 
			{ // Every 30 days...assuming 30-day months
				$data[] = floor(array_sum($thisweek) / 30); // ...calculate the month average...
				$thismonth = array(); // ...and reset the total
			}
		}
		// if there is an incomplete month, add it to the data
		if (count($thismonth)!=0){
			$data[] = floor(array_sum($thismonth) / count($thismonth));
		}
		
		$var = count($data);
		if(isset($var))
		{
			for ($k=1; $k<=$var; $k++)
			{
				array_push($data1,"$kος Μήνας");
			}
		}
	}
	
	
	// Setup the graph
	$graph = new Graph(550,400);
	$graph->SetScale("textlin");
		
	$theme_class= new UniversalTheme;
	$graph->SetTheme($theme_class);
		
	$graph->title->Set("Πλήρες Ιστορικό");
	$graph->SetBox(false);
	
	$graph->yaxis->HideZeroLabel();
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->ygrid->SetFill(false);
	
	//$graph->xgrid->Show();
	$graph->xaxis->HideTicks(false,false);
	$graph->xgrid->SetLineStyle("solid");
	$graph->xaxis->SetTickLabels($data1);
	$graph->xgrid->SetColor('#C0C0C0'); //#E3E3E3
	
	$p1 = new LinePlot($data);
	$graph->Add($p1);
	
	
	//assign values to the lines and show them
	if ($eidos=='glikozi'){
		$p1->SetColor("#6495ED");
		$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
		$p1->mark->SetColor('#6495ED');
		$p1->mark->SetFillColor('#6495ED');
		$p1->SetLegend('Σάκχαρο');
		$p1->value->SetFormat('%d');
		$p1->value->Show();
		$p1->value->SetColor('#000000');
		$p1->value->SetMargin(8);
		$p1->SetCenter();
	}
	else if ($eidos=='varos')
	{
		$p1->SetColor("#6495ED");
		$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
		$p1->mark->SetColor('#6495ED');
		$p1->mark->SetFillColor('#6495ED');
		$p1->SetLegend('Βάρος');
		$p1->value->SetFormat('%d');
		$p1->value->Show();
		$p1->value->SetColor('#000000');
		$p1->value->SetMargin(8);
		$p1->SetCenter();
	}
	else if ($eidos=='perifereia_mesis')
	{
		$p1->SetColor("#6495ED");
		$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
		$p1->mark->SetColor('#6495ED');
		$p1->mark->SetFillColor('#6495ED');
		$p1->SetLegend('Περιφέρεια Μέσης');
		$p1->value->SetFormat('%d');
		$p1->value->Show();
		$p1->value->SetColor('#000000');
		$p1->value->SetMargin(8);
		$p1->SetCenter();
	}
	else if ($eidos=='sistoliki_piesi')
	{
		$p1->SetColor("#6495ED");
		$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
		$p1->mark->SetColor('#6495ED');
		$p1->mark->SetFillColor('#6495ED');
		$p1->SetLegend('Συστολική Πίεση');
		$p1->value->SetFormat('%d');
		$p1->value->Show();
		$p1->value->SetColor('#000000');
		$p1->value->SetMargin(8);
		$p1->SetCenter();
	}
	else if ($eidos=='diastoliki_piesi')
	{
		$p1->SetColor("#6495ED");
		$p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
		$p1->mark->SetColor('#6495ED');
		$p1->mark->SetFillColor('#6495ED');
		$p1->SetLegend('Διαστολική Πίεση');
		$p1->value->SetFormat('%d');
		$p1->value->Show();
		$p1->value->SetColor('#000000');
		$p1->value->SetMargin(8);
		$p1->SetCenter();
			
	}
			
	$graph->legend->SetFrameWeight(1);
	$graph->legend->SetMarkAbsSize(10);
		
	// Output line
	$graph->Stroke();
?>