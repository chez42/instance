<?php // content="text/plain; charset=utf-8"
require_once ('/var/www/sites/vcrm/include/utils/jpgraph/jpgraph.php');
require_once ('/var/www/sites/vcrm/include/utils/jpgraph/jpgraph_pie.php');
require_once ('/var/www/sites/vcrm/include/utils/jpgraph/jpgraph_pie3d.php');

/*global $pdf_pie;

foreach($pdf_pie AS $a => $b)
    foreach($b AS $k => $v)
    {
        $legends[] = $k;
        $data[] = $v['value'];//The values for the pie graph
    }
*/
// Some data
$data = array(33518.07,82242.84,14110.04);

// Create the Pie Graph.
$graph = new PieGraph(350,200);
$graph->SetShadow();

// Set A title for the plot
$graph->title->Set("Asset Allocation");
$graph->title->SetFont(FF_VERDANA,FS_BOLD,18); 
$graph->title->SetColor("darkblue");
$graph->legend->Pos(0.1,0.2);

// Create pie plot
$p1 = new PiePlot3d($data);
$p1->SetTheme("sand");
$p1->SetCenter(0.4);
$p1->SetAngle(30);
$p1->value->SetFont(FF_ARIAL,FS_NORMAL,12);
$p1->SetLegends(array("Cash","Stocks","Fixed Income"));

$graph->Add($p1);
$graph->Stroke();

?>
