<?php 
	require_once('lib/functions/functions.php');
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_line.php');
	require_once('lib/connections/connect_db.php');
	
	if (!loggedin()) 
	{ 	//if the user is not logged in. loggedin variable returned from functions.php is FALSE
		header("Location: login");
		exit();
	}
	
	//get the variables with the GET method via url
	$eidos = mysql_real_escape_string(trim($_GET['eidos']));
	$id = (int)$_GET['id'];
	
	$data = array();
	$data1 = array();
	$data2 = array();
	$data3 = array();
	$data4 = array();
	$data5 = array();
	
	$query = "SELECT m_id, glikozi, varos, perifereia_mesis, sistoliki_piesi, diastoliki_piesi, m_date FROM measurements WHERE pat_id = '$id' ORDER BY m_id DESC LIMIT 7";
	$result = mysql_query($query);
	
	if ($result && mysql_num_rows($result)>=1)
	{
		while($row = mysql_fetch_array($result))
		{	
			if ($eidos=='glikozi')
			{
				array_push($data,$row['glikozi']);
				$data1 = array_reverse($data);
				$timestamp = strtotime($row['m_date']);
				array_push($data3,date('D,d/m',$timestamp));
				$data2 = array_reverse($data3);
			}
			else if ($eidos=='varos')
			{
				array_push($data,$row['varos']);
				$data1 = array_reverse($data);
				$timestamp = strtotime($row['m_date']);
				array_push($data3,date('D,d/m',$timestamp));
				$data2 = array_reverse($data3);
			}
			else if ($eidos=='perifereia_mesis')
			{
				array_push($data,$row['perifereia_mesis']);
				$data1 = array_reverse($data);
				$timestamp = strtotime($row['m_date']);
				array_push($data3,date('D,d/m',$timestamp));
				$data2 = array_reverse($data3);
			}
			else if ($eidos=='piesi')
			{
				array_push($data,$row['sistoliki_piesi']);
				$data1 = array_reverse($data);
				array_push($data4,$row['diastoliki_piesi']);
				$data5 = array_reverse($data4);
				$timestamp = strtotime($row['m_date']);
				array_push($data3,date('D,d/m',$timestamp));
				$data2 = array_reverse($data3);
			}
		}
	}
		// Setup the graph
		$graph = new Graph(550,400);
		$graph->SetScale("textlin");
		
		$theme_class= new UniversalTheme;
		$graph->SetTheme($theme_class);
		
		//$graph->img->SetAntiAliasing(false);
		//$graph->img->SetAntiAliasing();
		
		//if condition to choose what to title to give
		$graph->title->Set("Τελευταίες 7 Ημέρες");
		$graph->SetBox(false);

		
		$graph->yaxis->HideZeroLabel();
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		//$graph->ygrid->SetColor('#C0C0C0');
		$graph->ygrid->SetFill(false);
		
		//$graph->xgrid->Show();
		$graph->xaxis->HideTicks(false,false);
		$graph->xgrid->SetLineStyle("solid");
		$graph->xaxis->SetTickLabels($data2);
		$graph->xgrid->SetColor('#C0C0C0'); //#E3E3E3
		
		// Create the first line
		$p1 = new LinePlot($data1);
		$graph->Add($p1);
		
		//for the pressure measurement we have to create 2 lines, because high and low pressure will be shown in the same diagram
		if ($eidos=='piesi')
		{
			$p2 = new LinePlot($data5);
			$graph->Add($p2);
		}
		
		//assign values to the lines and show them
		if ($eidos=='glikozi')
		{
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
		else if ($eidos=='piesi')
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
			
			$p2->SetColor("#FF0000");
			$p2->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
			$p2->mark->SetColor('#FF0000');
			$p2->mark->SetFillColor('#FF0000');
			$p2->SetLegend('Διαστολική Πίεση');
			$p2->value->SetFormat('%d');
			$p2->value->Show();
			$p2->value->SetColor('#000000');
			$p2->value->SetMargin(8);
			$p2->SetCenter();
			
		}
			
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetMarkAbsSize(10);
		
		// Output line
		$graph->Stroke();
?>