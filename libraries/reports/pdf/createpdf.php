<?php

require_once("modules/Portfolios/pdf/cPDFGenerator.php");
require_once("modules/Portfolios/classes/cTransactions.php");
require_once("modules/report_settings/classes/cReportSettings.php");
//require_once("modules/PDFMaker/mpdf/mpdf.php");

$report = $_REQUEST['report'];
$record = $_REQUEST['record'];
$module = $_REQUEST['m'];

if(!$pids)
    $pids = $ids;
$t = new cTransactions();
$transactions = $t->GetAllPortfolioTransactions($pids);
$t->FillTransactionTable($transactions);
$inception = $t->GetInceptionDate($pids);
$inception = str_replace("00:00:00", "", $inception);
$inception = ConvertDateToMDY($inception);
ob_end_clean();
ob_end_flush();

$report_settings = new cReportSettings();
$settings = $report_settings->GetSettings($current_user->id);

switch($report)
{
    case "overview":
    {
        $pdf = new cPDFGenerator('c','LETTER-L','20','Arial',10,10,20,10,1,1);       
        $pdf->SetupPDF($record, $module, $accountname, $current_user->id); 
        $name = $pdf->AutoReportName();
        $pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);
        $pdf->SetupFooter();
        $pdf->CreatePieChart($pdf_pie);
        $pdf->CreateBarChart($pdf_bar);
        //$stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/overview_style.css';
        $template_file = "overview_pdf.tpl";
        $pdf_name = "overview.pdf";
    }
    break;
    case "holdings":
    {
        $pdf = new cPDFGenerator('c','LETTER-L','20','Arial',10,20,30,40,1,51);
        $pdf->SetupPDF($record, $module, $accountname, $current_user->id); 
        $name = $pdf->AutoReportName();
        $pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);
        $pdf->SetupFooter();
        $pdf->CreatePieChart($pdf_pie);
        $stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/holdings_style.css';
        $template_file = "holdings_pdf.tpl";
        $pdf_name = "holdings.pdf";
    }
    break;
    case "monthly_income":
    {
        $pdf=new cPDFGenerator('c','LETTER-L','20','Arial',10,10,20,10,1,1);
        $pdf->SetupPDF($record, $module, $accountname, $current_user->id);
        $name = $pdf->AutoReportName();
        $pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);
        $pdf->SetupFooter();
        $bar = $pdf->CreateBarChart($pdf_bar, "graph.png", 900, 150, 90,30,30,30, "Trailing 12 Months Income");
        $projected = $pdf->CreateBarChart($pdf_estimate, "projected.png", 900, 150, 90,30,30,30, "Projected 12 Months Income");
        $stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/monthly_style.css';
        $template_file = "monthly_income_pdf.tpl";
        $pdf_name = "monthly_income.pdf";
    }
    break;
    case "positions":
    {
        $pdf=new cPDFGenerator('c','LETTER-L','20','Arial',10,20,30,40,1,1);
        $pdf->SetupPDF($record, $module, $accountname, $current_user->id);
        $name = $pdf->AutoReportName();
        $pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);
        $pdf->SetupFooter();
        $pdf->CreatePieChart($pdf_pie, "positions_pie.png", 600, 200);
        $stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/positions_style.css';
        $template_file = "positions_pdf.tpl";
        $pdf_name = "positions.pdf";
    }
    break;
    case "performance":
    {
        $pdf=new cPDFGenerator('c','LETTER-L','20','Arial',10,20,30,40,1,1);
        $pdf->SetupPDF($record, $module, $accountname, $current_user->id);
        $name = $pdf->AutoReportName();
        $pdf->SetupHeader("storage/logos/".$settings['logo'], $name, $inception);
        $pdf->SetupFooter();
        $stylesheet = 'layouts/vlayout/modules/PortfolioInformation/css/pdf/performance_style.css';
        $template_file = "performance_pdf.tpl";
        $pdf_name = "performance.pdf";
    }
}

$smarty->assign("USER_NAME",$pdf->user_name);
$template = $smarty->fetch($template_file);
$pdf->WritePDF($stylesheet, $template);
$pdf->DownloadPDF($pdf_name);


?>
