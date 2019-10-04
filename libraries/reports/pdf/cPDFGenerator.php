<?php
require_once ('libraries/mpdf/mpdf.php');
require_once ('libraries/jpgraph/jpgraph.php');
require_once ('libraries/jpgraph/jpgraph_pie.php');
require_once ('libraries/jpgraph/jpgraph_bar.php');
require_once ('libraries/jpgraph/jpgraph_pie3d.php');
require_once ('libraries/jpgraph/jpgraph_line.php');
include_once("modules/PortfolioInformation/PortfolioInformation.php");

class cPDFGenerator extends MPDF{
    var $pdf_pie = array();
    var $pdf_bar = array();
    var $pdf_line = array();
    var $colors = array("#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec");
    var $user_name;
    var $account_name;
    var $nickname;
    var $portfolio_name;
    var $contact_name;
    var $logo;
    var $stylesheet;
    var $html;
    
    public function __construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {
        parent::mPDF($mode,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$orientation);
    }
    
    public function SetupPDF($record='', $module='', $accountname='', $userid=0)
    {
        if($record && $module == "Accounts")
            $values = getRecordValues(array($record), 'Accounts');
        else
        if($record && $module == "Contacts")
            $values = getRecordValues(array($record), "Contacts");
        
        $all_values=$values[0];
        if($all_values)
        foreach($all_values AS $k => $v)
            if($v['Portfolio Name'])
                $this->portfolio_name = $v['Portfolio Name'][0]['disp_value'];

        if(strlen($this->portfolio_name) <= 0)
            $this->portfolio_name = $accountname;

        $this->account_name = $accountname;//$accountname is set in overview.php or whatever the 'main' file that brings us to this file is
        $this->contact_name = getContactName($record);
        $this->user_name = GetUserFirstLastNameByID($userid, true);//Reverse so it is first name then Last name
    }
    
    public function CreateLineChart($values, $filename="line.png", $width=800, $height=800){
        $h = $this->GetLineHandle($values, $filename, $width, $height);
        $this->pdf_line[]=array("handle"=>$h,
                                "filename"=>$filename);
    }
    
    public function CreatePieChart($values, $filename='pie.png', $width=800, $height=800)
    {
        $h = $this->GetPieHandle($values, $filename, $width, $height);
        $this->pdf_pie[]=array("handle"=>$h,
                               "filename"=>$filename);
    }
    
    public function CreateBarChart($values, $filename='chart.png', $graphWidth=800, 
                                   $graphHeight=400, $marginLeft=170, $marginRight=50, $marginTop=40, $marginBottom=80,
                                   $title = '', $xaxisTitle='')
    {
        $h = $this->GetBarHandle($values, $filename, $graphWidth,
                          $graphHeight, $marginLeft, $marginRight, $marginTop, $marginBottom,
                          $title, $xaxisTitle);
        $this->pdf_bar[]=array("handle"=>$h,
                               "filename"=>$filename);
    }

    public function CreateAUMBarChart($values, $filename='chart.png', $graphWidth=800, 
                                   $graphHeight=400, $marginLeft=170, $marginRight=50, $marginTop=40, $marginBottom=80,
                                   $title = '', $xaxisTitle='')
    {
        $h = $this->GetAUMBarHandle($values, $filename, $graphWidth,
                          $graphHeight, $marginLeft, $marginRight, $marginTop, $marginBottom,
                          $title, $xaxisTitle);
        $this->pdf_bar[]=array("handle"=>$h,
                               "filename"=>$filename);
    }

    public function SetupHeader($logo='', $text, $inception='')
    {
        $date = date("m/d/Y");
        $viewer = new Vtiger_Viewer();
        $viewer->assign("DATE", $date);
        $viewer->assign("TEXT", $text);
        $viewer->assign("LOGO", $logo);
        $viewer->assign("INCEPTION", $inception);
        $header = $viewer->view("pdf/header.tpl", "PortfolioInformation", true);

        $this->logo = $logo;
        $this->SetHTMLHeader($header);
    }
    
    public function SetupFooter()
    {
        $viewer = new Vtiger_Viewer();
        $viewer->assign("PAGENO", "{PAGENO}");
        $viewer->assign("NB", "{nb}");
		
		$viewer->assign("LOGO", $this->logo);

        $footer = $viewer->view("pdf/footer.tpl", "PortfolioInformation", true);
//        $footer = $viewer->view("layouts/vlayout/modules/PortfolioInformation/pdf/footer.tpl", "PortfolioInformation", true);
        $this->SetHTMLFooter ($footer);

        $this->SetTextColor(44,44,44);
        $this->AliasNbPages();
        $trigger = $this->PageBreakTrigger;

        $this->AddPage();
        $this->SetFont('Arial','',10);

        $keep_table_proportions = TRUE;           
    }
    
    public function WritePDF($stylesheetfile, $content)
    {
        $this->stylesheet = file_get_contents($stylesheetfile);
        $this->html = $content;
        $this->WriteHTML($this->stylesheet,1);
        $this->WriteHTML($this->html);
    }
    
    public function DownloadPDF($filename)
    {
        $this->Output($filename, 'D');
    }
    
    /**
     * Automatically return the report name.  Order is: Portfolio Name, Account Name, Contact Name
     * @return type
     */
    public function AutoReportName()
    {
        if($this->portfolio_name)
            $name = $this->portfolio_name;
        else
        if($this->account_name)
            $name = $this->account_name;
        else
        if($this->contact_name)
            $name = $this->contact_name;
        
        return $name;
    }
    
    /**
     * Gets the handle to the bar graph image
     * @param type $pdf_bar
     * @return type
     */
    function GetAUMBarHandle($pdf_bar, $filename="graph.png", $graphWidth=800, 
                          $graphHeight=400, $marginLeft=170, $marginRight=50, $marginTop=40, $marginBottom=80,
                          $title = '', $xaxisTitle='')
    {    
        $months = array();
        $currentMonth = (int)date('m');

/*        for ($x = $currentMonth; $x < $currentMonth + 12; $x++) {
            $tmp_month = date('M', mktime(0, 0, 0, $x, 1));
            $legends[] = $tmp_month;
            $data[] = $pdf_bar[$tmp_month];
        }
/*        foreach($pdf_bar AS $k => $v)
        {
            $legends[] = $k;
            $data[] = $v;//The values for the pie graph
        }*/
        foreach($pdf_bar AS $k => $v){
            $legends[] = $v['date'];
            $data[] = $v['value'];
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
        $bplot->SetFillColor('#00a517');
        $bplot->SetColor("#007610");
        $graph->ygrid->Show();
        $graph->xgrid->Show();
//        $bplot->SetShadow();
        //$bplot->value->Show();
        $bplot->value->SetFont(FF_ARIAL,FS_NORMAL,10);
//        $bplot->value->SetAngle(45);
        $bplot->value->SetFormat('$%01.2f');


        // Setup the titles
        if($title)
        $graph->title->Set($title);
            $graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
        $graph->title->SetColor("black");
        $graph->xaxis->SetTickLabels($legends);
        if($xaxisTitle)
            $graph->xaxis->title->Set($xaxisTitle);
        $graph->yaxis->SetLabelFormatString("$%01.2f");
        //$graph->yaxis->title->Set("Values");

        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

        //$bar = $graph->Stroke(_IMG_HANDLER);
        $bar = $graph->Stroke("storage/pdf/{$filename}");
        return $bar;
    }
    
    /**
     * Gets the handle to the bar graph image
     * @param type $pdf_bar
     * @return type
     */
    function GetBarHandle($pdf_bar, $filename="graph.png", $graphWidth=800, 
                          $graphHeight=400, $marginLeft=170, $marginRight=50, $marginTop=40, $marginBottom=80,
                          $title = '', $xaxisTitle='')
    {    
$months = array();
$currentMonth = (int)date('m');

for ($x = $currentMonth; $x < $currentMonth + 12; $x++) {
    $tmp_month = date('M', mktime(0, 0, 0, $x, 1));
    $legends[] = $tmp_month;
    $data[] = $pdf_bar[$tmp_month];
}
/*        foreach($pdf_bar AS $k => $v)
        {
            $legends[] = $k;
            $data[] = $v;//The values for the pie graph
        }*/

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
        $bplot->SetFillColor('#00a517');
        $bplot->SetColor("#007610");
        $graph->ygrid->Show();
        $graph->xgrid->Show();
//        $bplot->SetShadow();
        //$bplot->value->Show();
        $bplot->value->SetFont(FF_ARIAL,FS_NORMAL,10);
//        $bplot->value->SetAngle(45);
        $bplot->value->SetFormat('$%01.2f');


        // Setup the titles
        if($title)
        $graph->title->Set($title);
            $graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
        $graph->title->SetColor("black");
        $graph->xaxis->SetTickLabels($legends);
        if($xaxisTitle)
            $graph->xaxis->title->Set($xaxisTitle);
        $graph->yaxis->SetLabelFormatString("$%01.2f");
        //$graph->yaxis->title->Set("Values");

        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

        //$bar = $graph->Stroke(_IMG_HANDLER);
        $bar = $graph->Stroke("storage/pdf/{$filename}");
        return $bar;
    }

    function GetLineHandle($pdf_line, $filename="line.png", $width=800, $height=400){
//        print_r($pdf_line);exit;
        foreach($pdf_line AS $k => $v)
        {
            $market[]      = $v['market_value'];
            $cash[]        = $v['cash_value'];
            $fixed[]       = $v['fixed_income'];
            $equities[]    = $v['equities'];
            $total_value[] = $v['value'];
            $dates[]       = $v['date'];
/*            $value = number_format($v, 2, '.', ',');
            $legends[] = $k . ': $' . $value;
            $data[] = $v;//The values for the pie graph*/
        }
//        print_r($market);exit;
/*        $market = array(20,15,23,15);
        $cash = array(12,9,42,8);
        $fixed = array(5,17,32,24);*/

        // Setup the graph
        $graph = new Graph($width,$height);
        $graph->SetScale("textlin");
        $graph->SetMargin(100, 100, 0, 0);//, $rm, $tm, $bm)

        $theme_class=new UniversalTheme;

//        $graph->SetTheme($theme_class) title can be Trailing 12 AUM;
        $graph->img->SetAntiAliasing(false);
		//$graph->title->Set('Trailing 12 AUM');
        $graph->title->Set('');
        $graph->title->SetFont(FF_VERDANA,FS_BOLD,14);
        $graph->SetBox(false);

        $graph->img->SetAntiAliasing();

        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xaxis->SetTickLabels($dates);//array('A','B','C','D'));
        $graph->xgrid->SetColor('#E3E3E3');

        // Create the first line
        $p1 = new LinePlot($market);
        $graph->Add($p1);

        $p1->SetColor("#6bd7d6");
        $p1->SetLegend('Market');

        // Create the second line
        $p2 = new LinePlot($cash);
        $graph->Add($p2);
        $p2->SetColor("#02B90E");
        $p2->SetLegend('Cash');

        // Create the third line
        $p3 = new LinePlot($fixed);
        $graph->Add($p3);
        $p3->SetColor("#0033FF");
        $p3->SetLegend('Fixed Income');

        $p4 = new LinePlot($equities);
        $graph->Add($p4);
        $p4->SetColor("#8383ff");
        $p4->SetLegend('Equities');

        $p5 = new LinePlot($total_value);
        $graph->Add($p5);
        $p5->SetColor("#aade98");
        $p5->SetLegend('Total Value');
        
        $graph->legend->SetFrameWeight(1);

        // Output line
//        $graph->Stroke();
//        exit;
        $line = $graph->Stroke("storage/pdf/{$filename}");
        return $line;

    }
    
    /**
     * Pass in the array of title/values and receive the handle to the image
     * @param type $pdf_pie
     * @return type
     */
    function GetPieHandle($pdf_pie, $filename="pie.png", $width=800, $height=400)
    {
        $counter = 0;
//        foreach($pdf_pie AS $a => $b)
            foreach($pdf_pie AS $k => $v)
            {
                $value = number_format($v, 2, '.', ',');
                $legends[] = $k . ': $' . $value;
                if($value < 0){
                    $data[] = 0;
                }else
                    $data[] = $v;//The values for the pie graph
                $c = PortfolioInformation::GetChartColorForTitle($k);
                if($c){
                    $colors[] = $c;
                }else{
                    echo $k . " ";
                    echo $this->colors[$counter] . "<br />";
                    $colors[] = $this->colors[$counter];
                    $counter++;
                }
            }

        // Create the Pie Graph.
        $graph = new PieGraph($width, $height,"auto");
        $graph->SetShadow();

        // Set A title for the plot title set can be Asset Allocation
        //$graph->title->Set("Asset Allocation");
		$graph->title->Set("");
        $graph->title->SetFont(FF_VERDANA,FS_BOLD,14); 
        $graph->title->SetColor("black");

            // Legend settings
            $graph->legend->SetLayout(LEGEND_VERT);
        $graph->legend->Pos(-0.002,0.9,'right','center');   
            $graph->legend->SetFont(FF_ARIAL,FS_NORMAL,10);
            $graph->legend->SetMarkAbsSize(10);


        // Create pie plot
        $p1 = new PiePlot($data);
//        $p1->SetTheme("sand");
        $p1->SetSize(0.35);
        $p1->SetCenter(0.2, 0.52, 0.2);
        $p1->SetLabelPos(0.2);
//        $p1->SetAngle(90);
        $p1->value->SetFont(FF_ARIAL,FS_NORMAL,10);
        $p1->SetLegends($legends);/////array("Cash","Stocks","Fixed Income"));


        $graph->Add($p1);
        $p1->SetSliceColors($colors);
        //$p1->SetSliceColors(array("#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"));
        $pie = $graph->Stroke("storage/pdf/{$filename}");
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
        $bar = $graph->Stroke("storage/pdf/{$filename}");

        return $bar;
    }    
}

?>
