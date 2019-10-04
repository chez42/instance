<?php
require_once ('include/utils/jpgraph/jpgraph.php');
require_once ('include/utils/jpgraph/jpgraph_pie.php');
require_once ('include/utils/jpgraph/jpgraph_pie3d.php');
require_once ('include/utils/jpgraph/jpgraph_bar.php');

/*
include/utils/jpgraph (All files)
include/mpdf (All files)
modules/Portfolios/pdf
modules/Portfolios/print_monthly.php
modules/Portfolios/print_overview.php

 */
/**
 * Gets the handle to the bar graph image
 * @param type $pdf_bar
 * @return type
 */
function GetBarHandle($pdf_bar, $filename="graph.png", $graphWidth=800, 
                      $graphHeight=400, $marginLeft=170, $marginRight=50, $marginTop=40, $marginBottom=80,
                      $title = '', $xaxisTitle='')
{    
    foreach($pdf_bar AS $k => $v)
    {
        $legends[] = $k;
        $data[] = $v;//The values for the pie graph
    }

    //$datay=array(12,8,19,3,10,5);

    $graph = new Graph($graphWidth,$graphHeight,"auto");    
    $graph->SetScale("textlin");

    // Add a drop shadow
    $graph->SetShadow();

    // Adjust the margin a bit to make more room for titles
    $graph->img->SetMargin($marginLeft, $marginRight, $marginTop, $marginBottom);

    // Create a bar pot
    $bplot = new BarPlot($data);
    $graph->Add($bplot);
    // Adjust fill color
    $bplot->SetFillColor('orange');
    $bplot->SetShadow();
    //$bplot->value->Show();
    $bplot->value->SetFont(FF_ARIAL,FS_NORMAL,10);
    $bplot->value->SetAngle(45);
    $bplot->value->SetFormat('$%01.2f');


    // Setup the titles
    if($title)
    $graph->title->Set($title);
        $graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
    $graph->title->SetColor("darkblue");
    $graph->xaxis->SetTickLabels($legends);
    if($xaxisTitle)
        $graph->xaxis->title->Set($xaxisTitle);
    $graph->yaxis->SetLabelFormatString("$%01.2f");
    //$graph->yaxis->title->Set("Values");

    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

    //$bar = $graph->Stroke(_IMG_HANDLER);
    $bar = $graph->Stroke("modules/Portfolios/pdf/{$filename}");
    return $bar;
}

/**
 * Pass in the array of title/values and receive the handle to the image
 * @param type $pdf_pie
 * @return type
 */
function GetPieHandle($pdf_pie, $filename="pie.png", $width=800, $height=400)
{
    foreach($pdf_pie AS $a => $b)
        foreach($b AS $k => $v)
        {
            $value = number_format($v, 2, '.', ',');
            $legends[] = $k . ': $' . $value;
            $data[] = $v;//The values for the pie graph
        }

    // Create the Pie Graph.
    $graph = new PieGraph($width, $height,"auto");
    $graph->SetShadow();
	

    // Set A title for the plot
    $graph->title->Set("Asset Allocation");
    $graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
    $graph->title->SetColor("darkblue");
	
	// Legend settings
	$graph->legend->SetLayout(LEGEND_VERT);
    $graph->legend->Pos(-0.002,0.3,'right','center');   
	$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,10);
	$graph->legend->SetMarkAbsSize(8);
	
    
    // Create pie plot
    $p1 = new PiePlot3d($data);
    $p1->SetTheme("sand");
    $p1->SetCenter(0.5,0.52);
    $p1->SetLabelPos(0.5);
    $p1->SetAngle(80);
    $p1->value->SetFont(FF_ARIAL,FS_NORMAL,12);
    $p1->SetLegends($legends);/////array("Cash","Stocks","Fixed Income"));
	

    $graph->Add($p1);
    $p1->SetSliceColors(array("#6bd7d6", "#02B90E", "#0033FF", "#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"));
    $pie = $graph->Stroke("modules/Portfolios/pdf/{$filename}");
//    $graph->Stroke("/var")
    
    return $pie;
}

/**
 * Gets the handle to the bar graph image
 * @param type $pdf_bar
 * @return type
 */
function GetBarHandleIncome($pdf_bar, $filename="graph.png")
{

foreach($pdf_bar AS $k => $v)
{
    $legends[] = $k;
    $data[] = $v;//The values for the bar graph
}
    
//$datay=array(12,8,19,3,10,5);

$graph = new Graph(900,150,"auto");    
$graph->SetScale("textlin");

// Add a drop shadow
$graph->SetShadow();

// Adjust the margin a bit to make more room for titles
$graph->img->SetMargin(90,30,30,30);

$graph->SetFrame(false);

// Create a bar pot
$bplot = new BarPlot($data);
$graph->Add($bplot);
// Adjust fill color
$bplot->SetFillColor('orange');
$bplot->SetShadow();
//$bplot->value->Show();
$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,10);
$bplot->value->SetAngle(45);
$bplot->value->SetFormat('$%01.2f');

// Setup the titles
//$graph->title->Set("Account Value Over Last 12 Months");
    $graph->title->SetFont(FF_ARIAL,FS_NORMAL,10); 
    $graph->title->SetColor("darkblue");
$graph->xaxis->SetTickLabels($legends);
//$graph->xaxis->title->Set("Trailing 12");
$graph->yaxis->SetLabelFormatString("$%01.2f");
//$graph->yaxis->title->Set("Values");

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

//$bar = $graph->Stroke(_IMG_HANDLER);
$bar = $graph->Stroke("modules/Portfolios/pdf/{$filename}");

return $bar;
}

/**
 * Pass in the array of title/values and receive the handle to the image
 * @param type $pdf_pie
 * @return type
 */



?>
