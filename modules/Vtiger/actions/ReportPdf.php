<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

include_once("libraries/reports/new/nCommon.php");
include_once("libraries/reports/new/nCombinedAccounts.php");

class Vtiger_ReportPdf_Action extends Vtiger_Mass_Action {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function __construct() {
        
        parent::__construct();
        
        $this->exposeMethod('GHReport');
        $this->exposeMethod('OmniOverview');
        $this->exposeMethod('AssetClassReport');
        $this->exposeMethod('GainLoss');
        
        $this->exposeMethod('GHReportActual');
        $this->exposeMethod('GH2Report');
        $this->exposeMethod('GHXReport');
        $this->exposeMethod('LastYearIncome');
        $this->exposeMethod('OmniProjected');
        $this->exposeMethod('OmniIncome');
        $this->exposeMethod('MonthOverMonth');
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb, $site_URL, $current_user;
        
        $recordIds = $this->getRecordsListFromRequest($request);
        
        if(count($recordIds)<=20 || $request->get('sendEmail')){
            
            $report = $request->get('reportselect');
            
            $this->invokeExposedMethod($report, $request);
            
        }else{
            
            $adb->pquery("INSERT INTO vtiger_scheduled_portfolio_reports
                (user_id, user_email, params) VALUES (?, ?, ?)",
                array($current_user->id, $request->get('useremail'), json_encode($_REQUEST)));
            
            $response = new Vtiger_Response();
            $response->setResult(true);
            $response->emit();
            
        }
        
    }
    
    function getViewer(Vtiger_Request $request){
        
        global $vtiger_current_version, $vtiger_display_version, $onlyV7Instance;
        $viewer = new Vtiger_Viewer();
        $viewer->assign('APPTITLE', getTranslatedString('APPTITLE'));
        $viewer->assign('VTIGER_VERSION', $vtiger_current_version);
        $viewer->assign('VTIGER_DISPLAY_VERSION', $vtiger_display_version);
        $viewer->assign('ONLY_V7_INSTANCE', $onlyV7Instance);
        
        return $viewer;
    }
    
    public function GeneratePDF($fileDir){
        
        $zipname  = 'cache/'.strtotime('now').'.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($fileDir as $file) {
            if(filetype($file) == 'file') {
                if(file_exists($file)) {
                    $zip->addFile( $file, pathinfo( $file, PATHINFO_BASENAME ) );
                }
            }
        }
        $zip->close();
        while(ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.basename($zipname));
        readfile($zipname);
        
        foreach ($fileDir as $file) {
            unlink($file);
        }
        
        unlink($zipname);
        
    }
    
    function SendEmail($fileDir, $userEmail){
        
        global $adb;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $zipname  = 'cache/'.strtotime('now').'.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($fileDir as $file) {
            if(filetype($file) == 'file') {
                if(file_exists($file)) {
                    $zip->addFile( $file, pathinfo( $file, PATHINFO_BASENAME ) );
                }
            }
        }
        $zip->close();
        
        $query='SELECT vtiger_emailtemplates.subject,vtiger_emailtemplates.body
					FROM  vtiger_emailtemplates WHERE vtiger_emailtemplates.templateid = 213';
        
        $result = $adb->pquery($query, array());
        $body=decode_html($adb->query_result($result,0,'body'));
        
        $subject=decode_html($adb->query_result($result,0,'subject'));
        
        $mailer = Emails_Mailer_Model::getInstance();
        $mailer->IsHTML(true);
        $userName = $currentUserModel->getName();
        
        $emailRecordModel = Emails_Record_Model::getCleanInstance('Emails');
        $fromEmail = $emailRecordModel->getFromEmailAddress();
        $replyTo = $emailRecordModel->getReplyToEmail();
        
        $mailer->ConfigSenderInfo($fromEmail, $userName, $replyTo);
        
        $mailer->AddAddress($userEmail);
        
        $mailer->AddAttachment($zipname);
        
        $mailer->Subject = $subject;
        
        $mailer->Body = $body ;
        
        $status = $mailer->Send(true);
        
        $error = $mailer->getError();
        
        foreach ($fileDir as $file) {
            unlink($file);
        }
        
        unlink($zipname);
        
    }
    
    function GenerateTableCategories($merged_transaction_types){
        $table = array();
        foreach($merged_transaction_types AS $k => $v){
            $vals = array_unique($v);
            $table[$k] = $vals;
        }
        return $table;
    }
    
    function OmniOverview(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new/holdings_report.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
                
                $accounts = array_unique($accounts);
                
                $end = date('Y-m-d');
                PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, null, null, true);//Auto determine which intervals need calculated
                if(strlen($request->get('omni_select_end_date')) > 1) {
                    $end_date = date("Y-m-d",strtotime($request->get("omni_select_end_date")));
                }else {
                    $end_date = DetermineIntervalEndDate($accounts, date('Y-m-d'));
                }
                
                $t3_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateMinusMonths(TRAILING_3, $end_date)), $end_date);
                $t6_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateStartOfYear($end_date)), $end_date);
                $t12_performance = new Performance_Model($accounts, DetermineIntervalStartDate($accounts, GetDateMinusMonths(TRAILING_12, $end_date)), $end_date);
                $historical = new Historical_Model($accounts);
                $last_month = date('Y-m-d', strtotime('last day of previous month'));
                $last_year = date('Y-m-d', strtotime("{$last_month} - 1 year"));
                $t12_balances = $historical->GetEndValues($last_year, $end_date);
                
                $performance_summary_table['t3'] = $t3_performance->GetPerformanceSummed();
                $performance_summary_table['t6'] = $t6_performance->GetPerformanceSummed();
                $performance_summary_table['t12'] = $t12_performance->GetPerformanceSummed();
                
                $tmp = array_merge_recursive($t3_performance->GetTransactionTypes(), $t6_performance->GetTransactionTypes(), $t12_performance->GetTransactionTypes());
                $table = $this->GenerateTableCategories($tmp);
                
                $tmp_end_date = date("Y-m-d", strtotime($end_date));
                if (sizeof($accounts) > 0) {
                    
                    $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetUnsettledCashTotal($accounts);
                    $margin_balance = PortfolioInformation_HoldingsReport_Model::GetMarginBalanceTotal($accounts);
                    $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetNetCreditDebitTotal($accounts);
                    
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                    $categories = array("aclass");
                    $fields = array("symbol", "security_type", "account_number", "cusip", "description", "quantity", "price", "market_value");//, "weight", "current_value");
                    $totals = array("market_value");
                    $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "PositionValues", $fields, $categories);
                    $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable("PositionValuesPie");
                    $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("PositionValues", $totals);
                    
                    $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("PositionValues", $categories, $totals);
                    PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0) {
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                    
                };
                
                $end_date = date("m/d/Y", strtotime($end_date));
                
                $viewer = $this->getViewer($request);
                
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("MARGIN_BALANCE", $margin_balance);
                $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
                $viewer->assign("SETTLED_TOTAL", $global_total+$unsettled_cash+$margin_balance+$net_credit_debit);
                $viewer->assign("DATE_OPTIONS", $options);
                $viewer->assign("OVERVIEW_STYLE", 1);
                $viewer->assign("ESTIMATE_TABLE", $estimateTable);
                $viewer->assign("T3PERFORMANCE", $t3_performance);
                $viewer->assign("T6PERFORMANCE", $t6_performance);
                $viewer->assign("T12PERFORMANCE", $t12_performance);
                $viewer->assign("TABLECATEGORIES", $table);
                $viewer->assign("HOLDINGSPIEVALUES", json_encode($holdings_pie));
                $viewer->assign("END_DATE", $end_date);
                $viewer->assign("T12BALANCES", json_encode($t12_balances));
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("SITEURL", $site_URL);
                
                $ispdf = $request->get('pdf');
                
                //$moduleName = $request->getModule();
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Overview Performance");
                $toc[] = array("title" => "#3", "name" => "Individual Performance");
                $toc[] = array("title" => "#3", "name" => "Account Holdings");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GroupAccounts.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/OmniOverviewPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= '<div id="dynamic_chart_holder" class="dynamic_chart_holder" style = "width:1000px;height:300px;margin-top:20px;"></div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/IndividualPerformance.tpl', $moduleName);
                $pdf_content .= '<div class="pie_image" style="width:1000px;height:500px;">
					<div id="dynamic_pie_holder" class="dynamic_pie_holder" style = "width:800px;height:400px;margin-top:20mm;"></div>
				</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                $pdf_content .= '<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/core.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/charts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/themes/animated.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
				<script type="text/javascript">';
                if(!empty($t12_balances)){
                    $pdf_content .= 'createGraph();';
                }
                if(!empty($holdings_pie)){
                    $pdf_content .= 'createPie();';
                }
                $pdf_content .= 'function createPie() {
						var self = this;
						am4core.options.commercialLicense = true;
						var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
						var chartData = $.parseJSON($("#holdings_values").val());
                    
						chart.data = chartData;
                    
						var pieSeries = chart.series.push(new am4charts.PieSeries3D());
						pieSeries.slices.template.stroke = am4core.color("#555354");
						pieSeries.dataFields.value = "value";
						pieSeries.dataFields.category = "title";
						chart.fontSize = 16;
                    
						pieSeries.slices.template.strokeWidth = 2;
						pieSeries.slices.template.strokeOpacity = 1;
                    
						var colorSet = new am4core.ColorSet();
						var colors = [];
						$.each(chartData,function(){
							var element = jQuery(this);
							colors.push(element["0"].color);
						});
                    
						colorSet.list = colors.map(function(color) {
							return new am4core.color(color);
						});
						pieSeries.colors = colorSet;
                    
					}
                    
					function createGraph() {
						var self = this;
						am4core.options.commercialLicense = true;
						var chart = am4core.create("dynamic_chart_holder", am4charts.XYChart);
						var chartData = $.parseJSON($("#t12_balances").val());
                    
						chart.data = chartData;
                    
						var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
						categoryAxis.renderer.grid.template.location = 0;
						categoryAxis.dataFields.category = "intervalenddateformatted";
						categoryAxis.renderer.minGridDistance = 40;
						categoryAxis.fontSize = 11;
                    
                    
						var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
						valueAxis.min = 0;
                    
						valueAxis.renderer.minGridDistance = 30;
                    
                    
						var series = chart.series.push(new am4charts.ColumnSeries());
						series.dataFields.categoryX = "intervalenddateformatted";
						series.dataFields.valueY = "intervalendvalue";
						series.columns.template.tooltipText = "${valueY.value}";
						series.columns.template.tooltipY = 0;
						series.columns.template.strokeOpacity = 0;
                    
                    
						series.columns.template.adapter.add("fill", function(fill, target) {
							return chart.colors.getIndex(target.dataItem.index);
						});
                    
                    
					}
				</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_Overview";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right; width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec('wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html "' .$footerFileName.'" --footer-font-size 10 "'. $bodyFileName.'" "' . $whtmltopdfPath.'" 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function AssetClassReport(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        include_once("include/utils/omniscientCustom.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accountNumbers = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                
            }
            
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $calling_record = $recordId;
            
            if($calling_record) {
                $calling_instance = Vtiger_Record_Model::getInstanceById($calling_record);
                $advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
                $assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
                if(sizeof($assigned_to) == 0)
                    $assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
            }
            
            if(is_array($assigned_to))
                $assigned_to = $assigned_to[0];
            
            $pie_image = 0;
            
            $ispdf = $request->get('pdf');
            
            $viewer = $this->getViewer($request);
            //$moduleName = $request->getModule();
            $account_number = $accountNumbers;
            
            $total_weight = 0;
            if(!is_array($account_number))
                $accounts = explode(",", $accountNumbers);
            else {
                $accounts = $account_number;
            }
            $accounts = array_unique($accounts);
            
            if(strlen($request->get('asset_select_end_date')) > 1) {
                $end_date = $request->get("asset_select_end_date");
            }
            else {
                $end_date = PortfolioInformation_Module_Model::ReportValueToDate("current")['end'];
            }
            
            $tmp_end_date = date("Y-m-d", strtotime($end_date));
            if (sizeof($accounts) > 0) {
                PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                $categories = array("aclass");
                $fields = array("symbol", "security_type", "account_number", "description", "quantity", "price", "market_value");//, "weight", "current_value");"cusip",
                $totals = array("market_value");
                $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "PositionValues", $fields, $categories);
                $estimatePie = PortfolioInformation_Reports_Model::GetPieFromTable("PositionValuesPie");
                $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("PositionValues", $totals);
                
                $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("PositionValues", $categories, $totals);
                PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                
                global $adb;
                $query = "SELECT @global_total as global_total";
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) > 0) {
                    $global_total = $adb->query_result($result, 0, 'global_total');
                }
                
            };
            
            $contact_instance = null;
            $custodian = null;
            if(is_array($accounts)){
                $portfolios = array();
                $unsettled_cash = 0;
                foreach($accounts AS $k => $v) {
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                    if($crmid) {
                        $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                        $contact_id = $p->get('contact_link');
                        if ($contact_id)
                            $contact_instance[$p->get('account_number')] = Contacts_Record_Model::getInstanceById($contact_id);
                            
                            $portfolios[] = $p->getData();
                            $unsettled_cash += $p->get('unsettled_cash');
                            if (!$advisor_instance) {
                                echo "NO INSTANCE!";
                                $advisor_instance = Users_Record_Model::getInstanceById($p->get('assigned_user_id'), "Users");
                            }
                    }
                    
                    $custodian = $p->get('origination');
                }
            }
            
            if($contact_instance) {
                if(!$advisor_instance)
                    $advisor_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('assigned_user_id'), "Users");
                    
                    $household_instance = null;
                    if (reset($contact_instance)->get('account_id'))
                        $household_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('account_id'));
            }
            
            $account_info = PortfolioInformation_Module_Model::GetAccountIndividualTotals($accounts);
            $account_info_total = PortfolioInformation_module_Model::GetAccountSumTotals($accounts);
            
            $mailing_info = PortfolioInformation_Reports_Model::GetMailingInformationForAccount($moduleName, $accounts);
            
            $colors = PortfolioInformation_Module_Model::GetAllChartColors();
            $current_user = Users_Record_Model::getCurrentUserModel();
            $trailing_aum = PortfolioInformation_HistoricalInformation_Model::GetTrailing12AUM($accounts);
            $trailing_revenue = PortfolioInformation_HistoricalInformation_Model::GetTrailing12Revenue($accounts);
            
            $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("asset_allocation");
            
            $data = $advisor_instance->getData();
            $has_advisor = 0;
            if(strlen($data['user_name']) > 0)
                $has_advisor = 1;
                        
            $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "unsettled_cash", $tmp_end_date);
            $margin_balance = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "margin_balance", $tmp_end_date);
            $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetCustodianTotalAsOfDate($custodian, $accounts, "net_credit_debit", $tmp_end_date);
            
            $toc = array();
            $toc[] = array("title" => "#1", "name" => "Accounts Overview");
            $toc[] = array("title" => "#2", "name" => "Asset Allocation");
            
            $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
            $viewer->assign("MARGIN_BALANCE", $margin_balance);
            $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
            
            $viewer->assign("DATE", date("F d, Y"));
            $viewer->assign("ASSIGNED_TO", $assigned_to);
            $viewer->assign("HAS_ADVISOR", $has_advisor);
            $viewer->assign("CONTACTS", $contact_instance);
            $viewer->assign("REPORT_TYPE", "Client Statement");
            $viewer->assign("CURRENT_USER", $current_user);
            $viewer->assign("ADVISOR", $advisor_instance);
            $viewer->assign("HOUSEHOLD", $household_instance);
            $viewer->assign("MAILING_INFO", $mailing_info);
            $viewer->assign("NUM_ACCOUNTS_USED", sizeof($accounts));
            $viewer->assign("PORTFOLIO_DATA", $portfolios);
            $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
            $viewer->assign("PIE_IMAGE", $pie_image);
            $viewer->assign("DYNAMIC_PIE_FILE", $pie_image);
            $viewer->assign("COLORS", $colors);
            $viewer->assign("TOTAL_WEIGHT", $total_weight);
            $viewer->assign("CALLING_RECORD", $calling_record);
            $viewer->assign("TOC", $toc);
            $viewer->assign("ACCOUNTINFO", $account_info);
            $viewer->assign("ACCOUNTINFOTOTAL", $account_info_total);
            $viewer->assign("CATEGORIES", $categories);
            $viewer->assign("DATE_OPTIONS", $options);
            $viewer->assign("SHOW_END_DATE", 1);
            $viewer->assign("END_DATE", $end_date);
            $viewer->assign("ACCOUNT_NUMBER", json_encode($accounts));
            $viewer->assign("MODULE", "PortfolioInformation");
            
            $viewer->assign("CATEGORY_TOTALS", $category_totals);
            $viewer->assign("ESTIMATE_TABLE", $estimateTable);
            $viewer->assign("DYNAMIC_PIE", json_encode($estimatePie));
            $viewer->assign("GLOBAL_TOTAL", array("global_total" => $global_total));
            $viewer->assign("TRAILING_AUM", json_encode($trailing_aum));
            $viewer->assign("TRAILING_REVENUE", json_encode($trailing_revenue));
            $viewer->assign("RANDOM", rand(1,100000));
            $viewer->assign("SITEURL", $site_URL);
            
            $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
            $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
            
            $pdf_content  = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/MailingInfo.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TitlePage.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TableOfContents.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GroupAccounts.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/AssetClassPie.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/DynamicHoldings.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
            
            $pdf_content .= '	<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
			<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
			<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
			<script type="text/javascript">';
                        
                if(!empty($estimatePie)){
                    $pdf_content .= 'CreatePieWithDetails("dynamic_pie_holder", "estimate_pie_values");';
                }
                
                $pdf_content .= 'function CreatePieWithDetails(holder, value_source, showLegend){
					if($("#"+holder).length == 0)
						return;
                            
					var chart;
					var legend;
                            
					var chartData = $.parseJSON($("#"+value_source).val());
                            
					chart = new AmCharts.AmPieChart();
                            
					chart.dataProvider = chartData;
					chart.titleField = "title";
					chart.valueField = "value";
					chart.colorField = "color";
                            
					chart.labelRadius = -30;
					chart.radius = 125;
					chart.labelText = "[[percents]]%";
					chart.textColor= "#FFFFFF";
					chart.color = "#FFFFFF";
					chart.depth3D = 14;
					chart.angle = 25;
					chart.outlineColor = "#363942";
					chart.outlineAlpha = 0.8;
					chart.outlineThickness = 1;
					chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
					chart.startDuration = 0;
                            
					chart.write(holder);
				}
			</script>';
                        
            $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/HoldingsReport.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
            
            if (!is_dir($fileDir)) {
                mkdir($fileDir);
            }
            
            $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_Holdings";
            
            $bodyFileName = $fileDir.'/body_'.$name.'.html';
            $fb = fopen($bodyFileName, 'w');
            $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
            fwrite($fb, $b);
            fclose($fb);
            
            $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                            
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                            
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                            
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                            
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<!-- <div style='float:right;width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div> -->
						</div>
					</body>
				</html>";
                        $footerFileName = $fileDir.'/footer_'.$name.'.html';
                        $ff = fopen($footerFileName, 'w');
                        $f = $footer;
                        fwrite($ff, $f);
                        fclose($ff);
                        
                        $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                        
                        $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                        
                        unlink($bodyFileName);
                        unlink($footerFileName);
                        
                        $filePath[] = $whtmltopdfPath;
                        
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function GainLoss(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        include_once("include/utils/omniscientCustom.php");
        include_once("modules/PortfolioInformation/models/PrintingContactInfo.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        global $adb, $dbconfig, $root_directory, $site_URL;
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            $is_pdf = $request->get('pdf');
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            if(sizeof($accounts) > 0){
                
                $accounts = array_unique($accounts);
                
                foreach($accounts AS $k => $v){
                    PortfolioInformation_Module_Model::AutoGenerateTransactionsForGainLossReport($v);
                }
                PortfolioInformation_GainLoss_Model::CreateGainLossTables($accounts);//Create combined gain loss table
                
                $categories = array("security_symbol");
                $fields = array('account_number', 'description', 'trade_date', 'security_price', 'transaction_activity', 'quantity', 'position_current_value', 'net_amount', 'ugl', 'ugl_percent', 'days_held', 'system_generated', 'transactionsid');//, "weight", "current_value");
                $totals = array("quantity", "net_amount", "position_current_value", "ugl");//Totals needs to have the same names as the fields to show up properly!!!
                $hidden_row_fields = array("description");//We don't want description showing on every row, just the category row
                $comparison_table = PortfolioInformation_Reports_Model::GetTable("Positions", "TEMPORARY_TRANSACTIONS", $fields, $categories, $hidden_row_fields);
                
                $comparison_table['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("COMPARISON", $totals);
                
                $add_on_fields = array("description", "ugl", "ugl_percent");
                $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("COMPARISON", $categories, $totals, $add_on_fields);
                
                PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $comparison_table, $category_totals);
                
                $viewer = $this->getViewer($request);
                $viewer->assign("COMPARISON_TABLE", $comparison_table);
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("SITEURL", $site_URL);
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $coverpage = new FormattedContactInfo($calling_record);
                $coverpage->SetTitle("Gain/Loss");
                $coverpage->SetLogo("layouts/hardcoded_images/lhimage.jpg");
                $viewer->assign("COVERPAGE", $coverpage);
                $viewer->assign("SITEURL", $site_URL);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Reports/CoverPage.tpl',"PortfolioInformation");
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', "PortfolioInformation");
                #$pdf_content  = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/MailingInfo.tpl', $moduleName);
                #$pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TitlePage.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GainLoss.tpl', "PortfolioInformation");
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/GainLoss.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GainLoss";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right;width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function GHReport(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new//holdings_report.php");
        require_once("modules/PortfolioInformation/models/NameMapper.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        global $adb, $dbconfig, $root_directory, $site_URL;
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            $db_name = $dbconfig['db_name'];
            $custodianDB = $dbconfig['custodianDB'];
            
            $query = "CALL TD_PRICING_TO_INDEX(?, ?);";
            $adb->pquery($query, array('AGG', '2019-01-01'));
            $adb->pquery($query, array('EEM', '2019-01-01'));
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            $current_user = Users_Record_Model::getCurrentUserModel();
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                $map = new NameMapper();
                $map->RenamePortfoliosBasedOnLinkedContact($accounts);
                
                if(strlen($request->get('select_start_date')) > 1) {
                    $start_date = $request->get("select_start_date");
                }
                else {
                    $start_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['start'];
                }
                
                if(strlen($request->get('select_end_date')) > 1) {
                    $end_date = $request->get("select_end_date");
                }
                else {
                    $end_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['end'];
                }
                
                $start_date = date("Y-m-d", strtotime($start_date));
                $end_date = date("Y-m-d", strtotime($end_date));
                
                PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, $start_date, $end_date, true);
                
                $tmp = array();
                foreach($accounts AS $k => $v){
                    if (strtolower(PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v)) == 'td'){
                        $query = "CALL TD_REC_TRANSACTIONS(?)";
                        $adb->pquery($query, array($v), true);
                    };
                    if(PortfolioInformation_Module_Model::DoesAccountHaveIntervalData($v, $start_date, $end_date))
                        $tmp[] = $v;
                }
                $accounts = $tmp;
                
                //$ytd_performance = new Performance_Model($accounts, $start_date, $end_date);//GetFirstDayLastYear(), GetLastDayLastYear());
                
                if (sizeof($accounts) > 0) {
                    $ytd_performance = new Performance_Model($accounts, $start_date, $end_date);
                    PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
                    $categories = array("estimatedtype");
                    $fields = array("security_symbol", "account_type", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
                    $totals = array("current_value", "weight");
                    $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
                    $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
                    $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable();
                    
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $end_date);
                    $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                    
                    $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
                    PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0){
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                } else {
                    continue;
                }
                
                $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $end_date);
                $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $end_date);
                $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $end_date);
                
                $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
                
                $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
                $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
                
                $start_date = date("m/d/Y", strtotime($start_date));
                $end_date = date("m/d/Y", strtotime($end_date));
                
                $prepare_date = date("F d, Y");
                $viewer = $this->getViewer($request);
                
                $viewer->assign("ORIENTATION", $orientation);
                $viewer->assign("YTDPERFORMANCE", $ytd_performance);
                $viewer->assign("HOLDINGSPIEVALUES", json_encode($new_pie));
                $viewer->assign("HOLDINGSPIEARRAY", $new_pie);
                $viewer->assign("GLOBALTOTAL", $global_total);
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("MARGIN_BALANCE", $margin_balance);
                $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
                $viewer->assign("SETTLED_TOTAL", $global_total+$unsettled_cash+$margin_balance+$net_credit_debit);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("HEADING", "");
                $viewer->assign("USER_DATA", $current_user->getData());
                $viewer->assign("DATE_OPTIONS", $options);
                $viewer->assign("SHOW_START_DATE", 1);
                $viewer->assign("SHOW_END_DATE", 1);
                $viewer->assign("START_DATE", $start_date);
                $viewer->assign("END_DATE", $end_date);
                $viewer->assign("PREPARE_DATE", $prepare_date);
                $viewer->assign("ACCOUNTS", $accounts);
                $viewer->assign("SITEURL", $site_URL);
                
                
                if($calling_record) {
                    $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                    $prepared_by = PortfolioInformation_Module_Model::GetPreparedByFormattedByRecordID($calling_record);
                    $record = VTiger_Record_Model::getInstanceById($calling_record);
                    $data = $record->getData();
                    $module = $record->getModule();
                    if($module->getName() == "Accounts") {
                        $policy = $data['cf_2525'];//Investment Policy Statement
                        $viewer->assign("POLICY", $policy);
                    }
                    $viewer->assign("PREPARED_FOR", $prepared_for);
                    $viewer->assign("PREPARED_BY", $prepared_by);
                }
                
                $ispdf = $request->get('pdf');
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $personal_notes = $request->get('personal_notes');
                //$moduleName = $request->getModule();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                
                
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                $viewer->assign("PERSONAL_NOTES", $personal_notes);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Portfolio Performance");
                $viewer->assign("TOC", $toc);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GHReportNewPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $pdf_content .= '<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/core.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/charts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/themes/animated.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
				<script type="text/javascript">';
                
                if(!empty($new_pie)){
                    
                    $pdf_content .= ' am4core.options.commercialLicense = true;
					var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
					var chartData = $.parseJSON($("#holdings_values").val());
                        
					chart.data = chartData;
                        
					var pieSeries = chart.series.push(new am4charts.PieSeries3D());
					pieSeries.slices.template.stroke = am4core.color("#555354");
					pieSeries.dataFields.value = "value";
					pieSeries.dataFields.category = "title";
					pieSeries.fontSize = 14;
                        
					pieSeries.slices.template.strokeWidth = 2;
					pieSeries.slices.template.strokeOpacity = 1;
                        
					var colorSet = new am4core.ColorSet();
					var colors = [];
					$.each(chartData,function(){
						var element = jQuery(this);
						colors.push(element["0"].color);
					});
                        
					colorSet.list = colors.map(function(color) {
						return new am4core.color(color);
					});
					pieSeries.colors = colorSet;';
                    
                }
                
                $pdf_content .= '</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/GHReportPDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GH_Estimated";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<!-- <div style='float:right;width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div> -->
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function GHReportActual(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new//holdings_report.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            $db_name = $dbconfig['db_name'];
            $custodianDB = $dbconfig['custodianDB'];
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            $current_user = Users_Record_Model::getCurrentUserModel();
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $map = new NameMapper();
                $map->RenamePortfoliosBasedOnLinkedContact($accounts);
                
                
                $accounts = array_unique($accounts);
                
                if(strlen($request->get('select_start_date')) > 1) {
                    $start_date = $request->get("select_start_date");
                }
                else {
                    $start_date = PortfolioInformation_Module_Model::ReportValueToDate("2019", false)['start'];
                }
                
                if(strlen($request->get('select_end_date')) > 1) {
                    $end_date = $request->get("select_end_date");
                }
                else {
                    $end_date = PortfolioInformation_Module_Model::ReportValueToDate("2019", false)['end'];
                }
                
                $tmp_start_date = date("Y-m-d", strtotime("first day of " . $start_date));
                $tmp_end_date = date("Y-m-d", strtotime("last day of " . $end_date));
                
                $start_date = date("Y-m-d", strtotime($start_date));
                $end_date = date("Y-m-d", strtotime($end_date));
                
                PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, $start_date, $end_date, true);
                
                $tmp = array();
                
                foreach($accounts AS $k => $v){
                    if (strtolower(PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v)) == 'td'){
                        $query = "CALL TD_REC_TRANSACTIONS(?)";
                        $adb->pquery($query, array($v), true);
                    };
                    if(PortfolioInformation_Module_Model::DoesAccountHaveIntervalData($v, $start_date, $end_date))
                        $tmp[] = $v;
                }
                $accounts = $tmp;
                
                $ytd_performance = new Performance_Model($accounts, $start_date, $end_date);//GetFirstDayLastYear(), GetLastDayLastYear());
                
                if (sizeof($accounts) > 0) {
                    PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
                    $categories = array("estimatedtype");
                    $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
                    $totals = array("current_value", "weight");
                    $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
                    $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
                    $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable();
                    
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $end_date);
                    $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                    
                    $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
                    PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0){
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                };
                
                $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $end_date);
                $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $end_date);
                $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $end_date);
                
                $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
                
                $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
                $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
                
                $start_date = date("m/d/Y", strtotime($start_date));
                $end_date = date("m/d/Y", strtotime($end_date));
                
                $prepare_date = date("F d, Y");
                $viewer = $this->getViewer($request);
                
                $viewer->assign("ORIENTATION", $orientation);
                $viewer->assign("YTDPERFORMANCE", $ytd_performance);
                $viewer->assign("HOLDINGSPIEVALUES", json_encode($new_pie));
                $viewer->assign("HOLDINGSPIEARRAY", $new_pie);
                $viewer->assign("GLOBALTOTAL", $global_total);
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("MARGIN_BALANCE", $margin_balance);
                $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
                $viewer->assign("SETTLED_TOTAL", $global_total+$unsettled_cash+$margin_balance+$net_credit_debit);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("HEADING", "");
                $viewer->assign("USER_DATA", $current_user->getData());
                $viewer->assign("DATE_OPTIONS", $options);
                $viewer->assign("SHOW_START_DATE", 1);
                $viewer->assign("SHOW_END_DATE", 1);
                $viewer->assign("START_DATE", $start_date);
                $viewer->assign("END_DATE", $end_date);
                $viewer->assign("PREPARE_DATE", $prepare_date);
                $viewer->assign("ACCOUNTS", $accounts);
                $viewer->assign("SITEURL", $site_URL);
                
                if($calling_record) {
                    $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                    $prepared_by = PortfolioInformation_Module_Model::GetPreparedByFormattedByRecordID($calling_record);
                    $record = VTiger_Record_Model::getInstanceById($calling_record);
                    $data = $record->getData();
                    $module = $record->getModule();
                    if($module->getName() == "Accounts") {
                        $policy = $data['cf_2525'];//Investment Policy Statement
                        $viewer->assign("POLICY", $policy);
                    }
                    $viewer->assign("PREPARED_FOR", $prepared_for);
                    $viewer->assign("PREPARED_BY", $prepared_by);
                }
                
                $ispdf = $request->get('pdf');
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $personal_notes = $request->get('personal_notes');
                //$moduleName = $request->getModule();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                $viewer->assign("PERSONAL_NOTES", $personal_notes);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Portfolio Performance");
                $viewer->assign("TOC", $toc);
                
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GHReportNewActualPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $pdf_content .= '<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/core.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/charts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/themes/animated.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
				<script type="text/javascript">';
                
                if(!empty($new_pie)){
                    
                    $pdf_content .= ' am4core.options.commercialLicense = true;
					var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
					var chartData = $.parseJSON($("#holdings_values").val());
                        
					chart.data = chartData;
                        
					var pieSeries = chart.series.push(new am4charts.PieSeries3D());
					pieSeries.slices.template.stroke = am4core.color("#555354");
					pieSeries.dataFields.value = "value";
					pieSeries.dataFields.category = "title";
					pieSeries.fontSize = 14;
                        
					pieSeries.slices.template.strokeWidth = 2;
					pieSeries.slices.template.strokeOpacity = 1;
                        
					var colorSet = new am4core.ColorSet();
					var colors = [];
					$.each(chartData,function(){
						var element = jQuery(this);
						colors.push(element["0"].color);
					});
                        
					colorSet.list = colors.map(function(color) {
						return new am4core.color(color);
					});
					pieSeries.colors = colorSet;';
                    
                }
                
                $pdf_content .= '</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/GHReportPDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GH_Actual";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right;width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
    }
    
    function GH2Report(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new/holdings_report.php");
        require_once("libraries/Reporting/ProjectedIncomeModel.php");
        require_once("modules/PortfolioInformation/models/NameMapper.php");
        include_once("modules/PortfolioInformation/models/PrintingContactInfo.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            $db_name = $dbconfig['db_name'];
            $custodianDB = $dbconfig['custodianDB'];
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            $prepared_for = "";
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                $map = new NameMapper();
                $map->RenamePortfoliosBasedOnLinkedContact($accounts);
                
				
				if(strlen($request->get('gh2_select_start_date')) > 1) {
					$start_date =  $request->get("gh2_select_start_date");
				} else {
					$start_date = PortfolioInformation_Module_Model::ReportValueToDate("2020", false)['start'];
				}

				if(strlen($request->get('gh2_select_end_date')) > 1) {
					$end_date = $request->get("gh2_select_end_date");
				} else {
					$end_date = PortfolioInformation_Module_Model::ReportValueToDate("2020", false)['end'];
				}
				
                
                $start_date = date("Y-m-d", strtotime($start_date));
				$end_date = date("Y-m-d", strtotime($end_date));

                PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, null, null, true);
                
                $tmp = array();
                foreach($accounts AS $k => $v){
                    if (strtolower(PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v)) == 'td'){
                        $query = "CALL TD_REC_TRANSACTIONS(?)";
                        $adb->pquery($query, array($v), true);
                    };
                    if(PortfolioInformation_Module_Model::DoesAccountHaveIntervalData($v, $start_date, $end_date))
                        $tmp[] = $v;
                }
                
                $accounts = $tmp;
                
                $ytd_performance = new Performance_Model($accounts, $start_date, $end_date);//GetFirstDayLastYear(), GetLastDayLastYear());
                
                if (sizeof($accounts) > 0) {
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $end_date);
                    $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                    $sector_pie = PortfolioInformation_Reports_Model::GetPositionSectorsPie();
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0){
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                };
                
                $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $end_date);
                $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $end_date);
                $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $end_date);
                $date_options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh2_report");
                
                $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
                $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
                
                $viewer = $this->getViewer($request);
                
                $ytd_performance->CalculateIndividualTWRCumulative($start_date, $end_date);
                
                $start_date = date("m/d/Y", strtotime($start_date));
                $end_date = date("m/d/Y", strtotime($end_date));
                
                
                $viewer->assign("ORIENTATION", $orientation);
                $viewer->assign("TODAY", date("M d, Y"));
                $viewer->assign("YTDPERFORMANCE", $ytd_performance);
                $viewer->assign("HOLDINGSPIEVALUES", json_encode($new_pie));
                $viewer->assign("HOLDINGSSECTORPIESTRING", json_encode($sector_pie));
                $viewer->assign("HOLDINGSSECTORPIEARRAY", $sector_pie);
                $viewer->assign("HOLDINGSPIEARRAY", $new_pie);
                $viewer->assign("GLOBALTOTAL", $global_total);
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("MARGIN_BALANCE", $margin_balance);
                $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("SETTLED_TOTAL", $global_total+$unsettled_cash+$margin_balance+$net_credit_debit);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("HEADING", "");
                $viewer->assign("DATE_OPTIONS", $date_options);
                $viewer->assign("SHOW_START_DATE", 1);
                $viewer->assign("SHOW_END_DATE", 1);
                $viewer->assign("START_DATE", $start_date);
                $viewer->assign("END_DATE", $end_date);
                $viewer->assign("SITEURL", $site_URL);
                
                if($calling_record) {
                    $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                    $prepared_by = PortfolioInformation_Module_Model::GetPreparedByNameByRecordID($calling_record);
                    $record = VTiger_Record_Model::getInstanceById($calling_record);
                    $data = $record->getData();
                    $module = $record->getModule();
                    if($module->getName() == "Accounts") {
                        $policy = $data['cf_2525'];//Investment Policy Statement
                        $viewer->assign("POLICY", $policy);
                    }
                    $viewer->assign("PREPARED_FOR", $prepared_for);
                    $viewer->assign("PREPARED_BY", $prepared_by);
                }
                
                $ispdf = $request->get('pdf');
                
                $personal_notes = $request->get('personal_notes');
                //$moduleName = $request->getModule();
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                
                
                
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                $viewer->assign("PERSONAL_NOTES", $personal_notes);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Portfolio Performance");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $coverpage = new FormattedContactInfo($calling_record);
                $coverpage->SetTitle("Portfolio Review");
                $coverpage->SetLogo(rtrim($site_URL, '/').'/'."layouts/hardcoded_images/lhimage.jpg");
                
                $viewer->assign("COVERPAGE", $coverpage);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/lighthouse.tpl', $moduleName);
                //$pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/Reports/LighthouseCover.tpl', $moduleName);
                
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GH2ReportPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/AllocationTypesPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                
                $pdf_content .= '<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/core.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/charts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/themes/animated.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
				<script type="text/javascript">';
                
                if(!empty($new_pie)){
                    $pdf_content .= 'ValuePieChart();';
                }
                
                if(!empty($sector_pie)){
                    $pdf_content .= 'AssetPieChart();';
                }
                
                
                $pdf_content .= 'function ValuePieChart(){
						var self = this;
						am4core.options.commercialLicense = true;
						var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
						var chartData = $.parseJSON($("#holdings_values").val());
                    
						chart.data = chartData;
                    
						var pieSeries = chart.series.push(new am4charts.PieSeries3D());
						pieSeries.slices.template.stroke = am4core.color("#555354");
						pieSeries.dataFields.value = "value";
						pieSeries.dataFields.category = "title";
						pieSeries.fontSize = 14;
                    
						pieSeries.slices.template.strokeWidth = 2;
						pieSeries.slices.template.strokeOpacity = 1;
                    
						pieSeries.labels.horizontalCenter = "middle";
						pieSeries.labels.verticalCenter = "middle";
                    
						pieSeries.labels.template.disabled = true;
                    
						pieSeries.ticks.template.disabled = true;
                    
						var colorSet = new am4core.ColorSet();
						var colors = [];
						$.each(chartData,function(){
							var element = jQuery(this);
							colors.push(element["0"].color);
						});
                    
						colorSet.list = colors.map(function(color) {
							return new am4core.color(color);
						});
						pieSeries.colors = colorSet;
                    
					}
                    
					function AssetPieChart(){
						var self = this;
						am4core.options.commercialLicense = true;
						var chart = am4core.create("sector_pie_holder", am4charts.PieChart3D);
						var chartData = $.parseJSON($("#sector_values").val());
                    
						chart.data = chartData;
                    
						chart.depth = 10;
						chart.angle = 10;
                    
						var pieSeries = chart.series.push(new am4charts.PieSeries3D());
						pieSeries.slices.template.stroke = am4core.color("#555354");
						pieSeries.dataFields.value = "value";
						pieSeries.dataFields.category = "title";
						pieSeries.fontSize = 14;
                    
						pieSeries.slices.template.strokeWidth = 2;
						pieSeries.slices.template.strokeOpacity = 1;
                    
						pieSeries.labels.horizontalCenter = "middle";
						pieSeries.labels.verticalCenter = "middle";
                    
						pieSeries.labels.template.disabled = true;
                    
						pieSeries.ticks.template.disabled = true;
                    
						var colorSet = new am4core.ColorSet();
						var colors = [];
						$.each(chartData,function(){
							var element = jQuery(this);
							colors.push(element["0"].color);
						});
                    
						colorSet.list = colors.map(function(color) {
							return new am4core.color(color);
						});
						pieSeries.colors = colorSet;
                    
					}
				</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GH2";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right;width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;'  width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec('wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html "'.$footerFileName.'" --footer-font-size 10 "'. $bodyFileName.'" "'.$whtmltopdfPath.'" 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function GHXReport(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportPerformance.php");
        require_once("libraries/Reporting/ReportHistorical.php");
        require_once("libraries/reports/new//holdings_report.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $selected_indexes = PortfolioInformation_Indexes_Model::GetSelectedIndexes();
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            $current_user = Users_Record_Model::getCurrentUserModel();
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                if(strlen($request->get('select_start_date')) > 1) {
                    $start_date = $request->get("select_start_date");
                }
                else {
                    $start_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['start'];
                }
                
                if(strlen($request->get('select_end_date')) > 1) {
                    $end_date = $request->get("select_end_date");
                }
                else {
                    $end_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['end'];
                }
                
                $tmp_start_date = date("Y-m-d", strtotime("first day of " . $start_date));
                $tmp_end_date = date("Y-m-d", strtotime("last day of " . $end_date));
                
                $start_date = date("Y-m", strtotime($start_date));
                $end_date = date("Y-m", strtotime($end_date));
                
                $ytd_performance = new Performance_Model($accounts, $tmp_start_date, $tmp_end_date);
                
                if (sizeof($accounts) > 0) {
                    PortfolioInformation_HoldingsReport_Model::GenerateEstimateTables($accounts);
                    $categories = array("estimatedtype");
                    $fields = array("security_symbol", "account_number", "cusip", "description", "quantity", "last_price", "weight", "current_value");
                    $totals = array("current_value", "weight");
                    $estimateTable = PortfolioInformation_Reports_Model::GetTable("Holdings", "Estimator", $fields, $categories);
                    $estimateTable['TableTotals'] = PortfolioInformation_Reports_Model::GetTableTotals("Estimator", $totals);
                    $holdings_pie = PortfolioInformation_Reports_Model::GetPieFromTable();
                    
                    PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($accounts, $tmp_end_date);
                    $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
                    
                    $category_totals = PortfolioInformation_Reports_Model::GetTableCategoryTotals("Estimator", $categories, $totals);
                    PortfolioInformation_reports_model::MergeTotalsIntoCategoryRows($categories, $estimateTable, $category_totals);
                    
                    global $adb;
                    $query = "SELECT @global_total as global_total";
                    $result = $adb->pquery($query, array());
                    if($adb->num_rows($result) > 0){
                        $global_total = $adb->query_result($result, 0, 'global_total');
                    }
                };
                
                $unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "unsettled_cash", $tmp_end_date);
                $margin_balance = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "margin_balance", $tmp_end_date);
                $net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetFidelityFieldTotalAsOfDate($accounts, "net_credit_debit", $tmp_end_date);
                
                $options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
                
                $tmp = $ytd_performance->ConvertPieToBenchmark($new_pie);
                $ytd_performance->SetBenchmark($tmp['Stocks'], $tmp['Cash'], $tmp['Bonds']);
                $viewer = $this->getViewer($request);
                
                $viewer->assign("ORIENTATION", $orientation);
                $viewer->assign("YTDPERFORMANCE", $ytd_performance);
                $viewer->assign("HOLDINGSPIEVALUES", json_encode($new_pie));
                $viewer->assign("HOLDINGSPIEARRAY", $new_pie);
                $viewer->assign("GLOBALTOTAL", $global_total);
                $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
                $viewer->assign("MARGIN_BALANCE", $margin_balance);
                $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
                $viewer->assign("SETTLED_TOTAL", $global_total+$unsettled_cash+$margin_balance+$net_credit_debit);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("HEADING", "");
                $viewer->assign("USER_DATA", $current_user->getData());
                $viewer->assign("DATE_OPTIONS", $options);
                $viewer->assign("SHOW_START_DATE", 1);
                $viewer->assign("SHOW_END_DATE", 1);
                $viewer->assign("START_DATE", $start_date . '-01T08:05:00');
                $viewer->assign("END_DATE", $end_date . '-01T08:05:00');
                $viewer->assign("SELECTED_INDEXES", $selected_indexes);
                $viewer->assign("SITEURL", $site_URL);
                
                if($calling_record) {
                    $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                    $prepared_by = PortfolioInformation_Module_Model::GetPreparedByNameByRecordID($calling_record);
                    $record = VTiger_Record_Model::getInstanceById($calling_record);
                    $data = $record->getData();
                    $module = $record->getModule();
                    if($module->getName() == "Accounts") {
                        $policy = $data['cf_2525'];//Investment Policy Statement
                        $viewer->assign("POLICY", $policy);
                    }
                    $viewer->assign("PREPARED_FOR", $prepared_for);
                    $viewer->assign("PREPARED_BY", $prepared_by);
                }
                
                $ispdf = $request->get('pdf');
                
                $personal_notes = $request->get('personal_notes');
                //$moduleName = $request->getModule();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                
                if (strlen($request->get('pie_image')) > 0) {
                    $pie_image = cMpdf7::TextToImage($request->get('pie_image'));
                    $pie_image = '<img style="display:block; width:45%; height:30%" src=data:image/jpg;base64,' . base64_encode($pie_image) . ' />';
                    $viewer->assign("PIE_IMAGE", $pie_image);
                }
                
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                $viewer->assign("PERSONAL_NOTES", $personal_notes);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Portfolio Performance");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GHXReportNewPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $pdf_content .= '<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/core.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/charts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts4/themes/animated.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/pie.js"></script>
				<script type="text/javascript">';
                
                if(!empty($new_pie)){
                    
                    $pdf_content .= '  am4core.options.commercialLicense = true;
					var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
					var chartData = $.parseJSON($("#holdings_values").val());
                        
					chart.data = chartData;
                        
					var pieSeries = chart.series.push(new am4charts.PieSeries3D());
					pieSeries.slices.template.stroke = am4core.color("#555354");
					pieSeries.dataFields.value = "value";
					pieSeries.dataFields.category = "title";
					pieSeries.fontSize = 14;
                        
					pieSeries.slices.template.strokeWidth = 2;
					pieSeries.slices.template.strokeOpacity = 1;
                        
					var colorSet = new am4core.ColorSet();
					var colors = [];
					$.each(chartData,function(){
						var element = jQuery(this);
						colors.push(element["0"].color);
					});
                        
					colorSet.list = colors.map(function(color) {
						return new am4core.color(color);
					});
					pieSeries.colors = colorSet;';
                    
                }
                
                $pdf_content .= '</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsSummary.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/BalancesTable.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/HoldingsCharts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/GHXReportPDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GHX";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right; width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function LastYearIncome(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportIncome.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                $income = new Income_Model($accounts);
                $individual = $income->GetIndividualIncomeForDates(GetFirstDayLastYear(), GetLastDayLastYear());
                $monthly = $income->GetMonthlyTotalForDates(GetFirstDayLastYear(), GetLastDayLastYear());
                $graph = $income->GenerateGraphForDates(GetFirstDayLastYear(), GetLastDayLastYear());
                $combined = $income->GetCombinedSymbolsForDates(GetFirstDayLastYear(), GetLastDayLastYear());
                
                $year_end_totals = $income->CalculateCombineSymbolsYearEndToal(GetFirstDayLastYear(), GetLastDayLastYear());
                $grand_total = $income->CalculateGrandTotal(GetFirstDayLastYear(), GetLastDayLastYear());
                
                $start_month = date("F, Y", strtotime(GetFirstDayLastYear()));
                $end_month = date("F, Y", strtotime(GetLastDayLastYear()));
                
                $viewer = $this->getViewer($request);
                
                $viewer->assign("START_MONTH", $start_month);
                $viewer->assign("END_MONTH", $end_month);
                $viewer->assign("MONTHLY_TOTALS", $monthly);
                $viewer->assign("COMBINED_SYMBOLS", $combined);
                $viewer->assign("YEAR_END_TOTALS", $year_end_totals);
                $viewer->assign("GRAND_TOTAL", $grand_total);
                $viewer->assign("DYNAMIC_GRAPH", json_encode($graph));
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("SITEURL", $site_URL);
                
                $ispdf = $request->get('pdf');
                
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Income");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GroupAccounts.tpl', $moduleName);
                $pdf_content .= '<div class="graph_image" style="width:220mm; height:80mm; display:block; margin-left:auto; margin-right:auto; margin-top:10mm;">
					<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="display:block; width:100%; height:300px;"></div>
				</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/LastYearIncomePDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $pdf_content .= '	<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/serial.js"></script>
				<script type="text/javascript">';
                
                if(!empty($graph)){
                    $pdf_content .= 'CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");';
                }
                
                $pdf_content .= 'function CreateGraph(holder, value_source, category_field, value_field){
					if($("#"+holder).length == 0)
						return;
                    
					var chart;
					var chartData = $.parseJSON($("#"+value_source).val());
                    
					chart = new AmCharts.AmSerialChart();
                    
					chart.dataProvider = chartData;
					chart.categoryField = category_field;
					chart.marginTop = 25;
					chart.marginBottom = 80;
					chart.marginLeft = 50;
					chart.marginRight = 100;
					chart.startDuration = 1;
                    
					var valueAxis = new AmCharts.ValueAxis();
					valueAxis.minimum = 0;
					chart.addValueAxis(valueAxis);
                    
					var graph = new AmCharts.AmGraph();
					graph.valueField = value_field;
					graph.balloonText="[[category]]: $[[value]]";
					graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
					graph.type = "column";
					graph.lineAlpha = 0;
					graph.fillAlphas = 1;
					graph.fillColors = "#02B90E";
					chart.addGraph(graph);
					chart.angle = 30;
					chart.depth3D = 10;
                    
					var catAxis = chart.categoryAxis;
					catAxis.gridPosition = "start";
					catAxis.gridCount = chartData.length;
					catAxis.labelRotation = 90;
					chart.write(holder);
				}
				</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/IncomePDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_LastYearIncome";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<!-- <div style='float:right; width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div> -->
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function OmniProjected(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ProjectedIncomeModel.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                $start_date = GetDateFirstOfThisMonth();
                $end_date = GetDateLastOfPreviousMonthPlusOneYear();
                
                $positions = PositionInformation_Module_Model::GetPositionsForAccountNumber($accounts);
                
                foreach($positions AS $k => $v) {
                    
                    $crmid = ModSecurities_Module_Model::GetCrmidFromSymbol($v['security_symbol']);
                    if($crmid > 0) {
                        $instance = ModSecurities_Record_Model::getInstanceById($crmid);
                        $data = $instance->getData();
                        
                        $returned = Date("Y-m-d", strtotime($data['last_eod']));
                        $compared = Date("Y-m-d", strtotime("-3 months"));
                        //if ($returned <= $compared)
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFromEOD($v['security_symbol'], "US");
                    }
                }
                
                $projected = new ProjectedIncome_Model($accounts);
                $calendar = CreateMonthlyCalendar($start_date, $end_date);
                $projected->CalculateMonthlyTotals($calendar);
                $graph = $projected->GetMonthlyIncomeGraph();
                
                $viewer = $this->getViewer($request);
                
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("PROJECTED_INCOME", $projected);
                $viewer->assign("PROJECTED_GRAPH", json_encode($graph));
                $viewer->assign("GRAND_TOTAL", $projected->GetGrandTotal());
                $viewer->assign("CALENDAR", $calendar);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("SITEURL", $site_URL);
                
                $ispdf = $request->get('pdf');
                
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Income");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GroupAccounts.tpl', $moduleName);
                $pdf_content .= '<div class="graph_image" style="width:220mm; height:80mm; display:block; margin-left:auto; margin-right:auto; margin-top:10mm;">
					<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="display:block; width:100%; height:300px;"></div>
				</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/OmniProjectedPDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                $pdf_content .= '	<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/serial.js"></script>
				<script type="text/javascript">';
                
                if(!empty($graph)){
                    $pdf_content .= 'CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");';
                }
                
                $pdf_content .= 'function CreateGraph(holder, value_source, category_field, value_field){
					if($("#"+holder).length == 0)
						return;
                    
					var chart;
					var chartData = $.parseJSON($("#"+value_source).val());
                    
					chart = new AmCharts.AmSerialChart();
                    
					chart.dataProvider = chartData;
					chart.categoryField = category_field;
					chart.marginTop = 25;
					chart.marginBottom = 80;
					chart.marginLeft = 50;
					chart.marginRight = 100;
					chart.startDuration = 1;
                    
					var valueAxis = new AmCharts.ValueAxis();
					valueAxis.minimum = 0;
					chart.addValueAxis(valueAxis);
                    
					var graph = new AmCharts.AmGraph();
					graph.valueField = value_field;
					graph.balloonText="[[category]]: $[[value]]";
					graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
					graph.type = "column";
					graph.lineAlpha = 0;
					graph.fillAlphas = 1;
					graph.fillColors = "#02B90E";
					chart.addGraph(graph);
					chart.angle = 30;
					chart.depth3D = 10;
                    
					var catAxis = chart.categoryAxis;
					catAxis.gridPosition = "start";
					catAxis.gridCount = chartData.length;
					catAxis.labelRotation = 90;
					chart.write(holder);
				}
				</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/ProjectedPDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_ProjectedIncome";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<div style='float:right; width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    function OmniIncome(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        require_once("libraries/Reporting/ReportIncome.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accounts = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
                $accounts = explode(",", $accountNumbers);
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            if(sizeof($accounts) > 0 || strlen($calling_module) >= 0){
                
                $accounts = array_unique($accounts);
                
                $income = new Income_Model($accounts);
                $individual = $income->GetIndividualIncomeForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                $monthly = $income->GetMonthlyTotalForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                $graph = $income->GenerateGraphForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                $combined = $income->GetCombinedSymbolsForDates(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                $year_end_totals = $income->CalculateCombineSymbolsYearEndToal(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                $grand_total = $income->CalculateGrandTotal(GetFirstDayThisMonthLastYear(), GetLastDayLastMonth());
                
                $start_month = date("F, Y", strtotime(GetFirstDayThisMonthLastYear()));
                $end_month = date("F, Y", strtotime(GetLastDayLastMonth()));
                
                $viewer = $this->getViewer($request);
                
                $viewer->assign("START_MONTH", $start_month);
                $viewer->assign("END_MONTH", $end_month);
                $viewer->assign("MONTHLY_TOTALS", $monthly);
                $viewer->assign("COMBINED_SYMBOLS", $combined);
                $viewer->assign("YEAR_END_TOTALS", $year_end_totals);
                $viewer->assign("GRAND_TOTAL", $grand_total);
                $viewer->assign("DYNAMIC_GRAPH", json_encode($graph));
                $viewer->assign("ACCOUNT_NUMBER", $accountNumbers);
                $viewer->assign("CALLING_RECORD", $calling_record);
                $viewer->assign("SITEURL", $site_URL);
                
                $ispdf = $request->get('pdf');
                
                $current_user = Users_Record_Model::getCurrentUserModel();
                
                $account_totals = PortfolioInformation_Module_Model::GetAccountSumTotals($accounts);
                $account_totals['global_total'] = $account_totals['total'];
                
                if(is_array($accounts)){
                    $portfolios = array();
                    foreach($accounts AS $k => $v) {
                        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                        if($crmid) {
                            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                            $portfolios[] = $p->getData();
                        }
                    }
                }
                $viewer->assign("PORTFOLIO_DATA", $portfolios);
                $viewer->assign("GLOBAL_TOTAL", $account_totals);
                
                $toc = array();
                $toc[] = array("title" => "#1", "name" => "Accounts Overview");
                $toc[] = array("title" => "#2", "name" => "Income");
                $viewer->assign("TOC", $toc);
                
                $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
                $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
                
                $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/TableOfContents.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/GroupAccounts.tpl', $moduleName);
                $pdf_content .= '<div class="graph_image" style="width:220mm; height:80mm; display:block; margin-left:auto; margin-right:auto; margin-top:10mm;">
					<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="display:block; width:100%; height:300px;"></div>
				</div>';
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/OmniIncomePDF.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
                $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
                
                
                $pdf_content .= '	<script src="'.$site_URL.'layouts/v7/lib/jquery/jquery.min.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/amcharts.js"></script>
				<script src="'.$site_URL.'libraries/amcharts/amcharts/serial.js"></script>
				<script type="text/javascript">';
                
                if(!empty($graph)){
                    $pdf_content .= 'CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");';
                }
                
                $pdf_content .= 'function CreateGraph(holder, value_source, category_field, value_field){
					if($("#"+holder).length == 0)
						return;
                    
					var chart;
					var chartData = $.parseJSON($("#"+value_source).val());
                    
					chart = new AmCharts.AmSerialChart();
                    
					chart.dataProvider = chartData;
					chart.categoryField = category_field;
					chart.marginTop = 25;
					chart.marginBottom = 80;
					chart.marginLeft = 50;
					chart.marginRight = 100;
					chart.startDuration = 1;
                    
					var valueAxis = new AmCharts.ValueAxis();
					valueAxis.minimum = 0;
					chart.addValueAxis(valueAxis);
                    
					var graph = new AmCharts.AmGraph();
					graph.valueField = value_field;
					graph.balloonText="[[category]]: $[[value]]";
					graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
					graph.type = "column";
					graph.lineAlpha = 0;
					graph.fillAlphas = 1;
					graph.fillColors = "#02B90E";
					chart.addGraph(graph);
					chart.angle = 30;
					chart.depth3D = 10;
                    
					var catAxis = chart.categoryAxis;
					catAxis.gridPosition = "start";
					catAxis.gridCount = chartData.length;
					catAxis.labelRotation = 90;
					chart.write(holder);
				}
				</script>';
                
                $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
                $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/IncomePDF.css');
                
                if (!is_dir($fileDir)) {
                    mkdir($fileDir);
                }
                
                $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_Income";
                
                $bodyFileName = $fileDir.'/body_'.$name.'.html';
                $fb = fopen($bodyFileName, 'w');
                $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
                fwrite($fb, $b);
                fclose($fb);
                
                $footer ="<!doctype html>
				<html>
					<head>
						<meta charset='utf-8'>
						<script>
							function substitutePdfVariables() {
                    
								function getParameterByName(name) {
									var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
									return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
								}
                    
								function substitute(name) {
									var value = getParameterByName(name);
									var elements = document.getElementsByClassName(name);
                    
									for (var i = 0; elements && i < elements.length; i++) {
										elements[i].textContent = value;
									}
								}
                    
								['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
									.forEach(function(param) {
										substitute(param);
									});
							}
						</script>
					</head>
					<body onload='substitutePdfVariables()'>
						<div style='width:100%;'>
							<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
								<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
									Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
								</p>
							</div>
							<!-- <div style='float:right; width:60%;'>
								<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;' width='40%'/>
							</div> -->
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_'.$name.'.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
                
                $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
                
                $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
                
                unlink($bodyFileName);
                unlink($footerFileName);
                
                $filePath[] = $whtmltopdfPath;
                
            } else{
                continue;
            }
            
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
    /* function OmniIntervalsDaily(Vtiger_Request $request){
    
        $moduleName = $request->getModule();
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        foreach($recordIds as $recordId){
        
        $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
        
        global $adb, $dbconfig, $root_directory, $site_URL;
        
        if (!is_dir($fileDir)) {
            mkdir($fileDir);
        }
        
        $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_GHX";
        
        $bodyFileName = $fileDir.'/body_'.$name.'.html';
        $fb = fopen($bodyFileName, 'w');
        $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
        fwrite($fb, $b);
        fclose($fb);
        
        $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
        
        $output = shell_exec("wkhtmltopdf --javascript-delay 6000 -T 10.0 -B 5.0 -L 5.0 -R 5.0  " . $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
        
        //unlink($bodyFileName);
        
        }
        $this->GeneratePDF($fileDir);
        
    }*/
    
    function MonthOverMonth(Vtiger_Request $request){
        
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        include_once("include/utils/omniscientCustom.php");
        
        $module = $request->getModule();
        $moduleName = 'PortfolioInformation';
        $recordIds = $this->getRecordsListFromRequest($request);
        
        $fileDir = 'cache/'.$request->get('reportselect');//.'_'.strtotime("now");
        $printed_date = date("mdY");
        
        $filePath = array();
        
        foreach($recordIds as $recordId){
            
            if($module != 'PortfolioInformation'){
                $accountNumbers = GetAccountNumbersFromRecord($recordId);
            }else{
                $portfolio = Vtiger_Record_Model::getInstanceById($recordId);
                $accountNumbers = $portfolio->get('account_number');
            }
            
            global $adb, $dbconfig, $root_directory, $site_URL;
            $orientation = '';
            $calling_module = $moduleName;
            $calling_record = $recordId;
            
            if($calling_record) {
                $prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($calling_record);
                $prepared_by = PortfolioInformation_Module_Model::GetPreparedByNameByRecordID($calling_record);
                $calling_instance = Vtiger_Record_Model::getInstanceById($calling_record);
                $advisor_instance = Users_Record_Model::getInstanceById($calling_instance->get('assigned_user_id'), "Users");
                $assigned_to = getGroupName($calling_instance->get('assigned_user_id'));
                if(sizeof($assigned_to) == 0)
                    $assigned_to = GetUserFirstLastNameByID($calling_instance->get('assigned_user_id'), true);
            }
            
            if(is_array($assigned_to))
                $assigned_to = $assigned_to[0];
                
            $ispdf = $request->get('pdf');
            
            $viewer = $this->getViewer($request);
            //$moduleName = $request->getModule();
            $account_number = $accountNumbers;
            
            $total_weight = 0;
            if(!is_array($account_number))
                $accounts = explode(",", $accountNumbers);
            else {
                $accounts = $account_number;
            }
            $accounts = array_unique($accounts);
            if (sizeof($accounts) > 0) {
                $mom_table = PortfolioInformation_MonthOverMonth_Model::GenerateMonthOverMonthTable($accounts, "Income");
                $dow_prices = PortfolioInformation_MonthOverMonth_Model::GetMonthEndPrices("DJI");
                $years = PortfolioInformation_MonthOverMonth_Model::GetMonthOverMonthYears();
            };
            
            $contact_instance = null;
            if(is_array($accounts)){
                $portfolios = array();
                $unsettled_cash = 0;
                foreach($accounts AS $k => $v) {
                    $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);
                    if($crmid) {
                        $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
                        $contact_id = $p->get('contact_link');
                        if ($contact_id)
                            $contact_instance[$p->get('account_number')] = Contacts_Record_Model::getInstanceById($contact_id);
                            
                            $portfolios[] = $p->getData();
                            $unsettled_cash += $p->get('unsettled_cash');
                            if (!$advisor_instance) {
                                echo "NO INSTANCE!";
                                $advisor_instance = Users_Record_Model::getInstanceById($p->get('assigned_user_id'), "Users");
                            }
                    }
                }
            }
            
            if($contact_instance) {
                if(!$advisor_instance)
                    $advisor_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('assigned_user_id'), "Users");
                    
                    $household_instance = null;
                    if (reset($contact_instance)->get('account_id'))
                        $household_instance = Users_Record_Model::getInstanceById(reset($contact_instance)->get('account_id'));
            }
            
            
            $current_user = Users_Record_Model::getCurrentUserModel();
            
            $data = $advisor_instance->getData();
            $has_advisor = 0;
            if(strlen($data['user_name']) > 0)
                $has_advisor = 1;
                
            $toc = array();
            $toc[] = array("title" => "#1", "name" => "Accounts Overview");
            $toc[] = array("title" => "#2", "name" => "Month Over Month");
            
            $viewer->assign("DATE", date("F d, Y"));
            $viewer->assign("ASSIGNED_TO", $assigned_to);
            $viewer->assign("HAS_ADVISOR", $has_advisor);
            $viewer->assign("CONTACTS", $contact_instance);
            $viewer->assign("REPORT_TYPE", "Client Statement");
            $viewer->assign("CURRENT_USER", $current_user);
            $viewer->assign("ADVISOR", $advisor_instance);
            $viewer->assign("HOUSEHOLD", $household_instance);
            $viewer->assign("USER_DATA", $current_user->getData());
            $viewer->assign("MAILING_INFO", $mailing_info);
            $viewer->assign("NUM_ACCOUNTS_USED", sizeof($accounts));
            $viewer->assign("PORTFOLIO_DATA", $portfolios);
            $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
            $viewer->assign("PIE_IMAGE", $pie_image);
            $viewer->assign("DYNAMIC_PIE_FILE", $pie_file);
            $viewer->assign("COLORS", $colors);
            $viewer->assign("TOTAL_WEIGHT", $total_weight);
            $viewer->assign("CALLING_RECORD", $calling_record);
            $viewer->assign("TOC", $toc);
            $viewer->assign("ACCOUNT_NUMBER", json_encode($accounts));
            $viewer->assign("MOM_TABLE", $mom_table);
            $viewer->assign("DOW_PRICES", $dow_prices);
            $viewer->assign("YEARS", $years);
            $viewer->assign("PREPARED_FOR", $prepared_for);
            $viewer->assign("PREPARED_BY", $prepared_by);
            $viewer->assign("MODULE", "PortfolioInformation");
            $viewer->assign("SITEURL", $site_URL);
            
            $viewer->assign("RANDOM", rand(1,100000));
            
            $logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
            $viewer->assign("LOGO", rtrim($site_URL, '/').'/'.$logo);
            
            $personal_notes = $request->get('personal_notes');
            $viewer->assign("PERSONAL_NOTES", $personal_notes);
            
            $pdf_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/MonthOverMonth.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/page_break.tpl', $moduleName);
            $pdf_content .= $viewer->fetch('layouts/v7/modules/PortfolioInformation/pdf2/disclaimer.tpl', $moduleName);
            
            $stylesheet  = file_get_contents('layouts/v7/modules/PortfolioInformation/css/HoldingsReport.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/GroupAccounts.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/TableOfContents.css');
            $stylesheet .= file_get_contents('layouts/v7/modules/PortfolioInformation/css/pdf/MonthOverMonth.css');
            
            if (!is_dir($fileDir)) {
                mkdir($fileDir);
            }
            
            $name = GetClientNameFromRecord($calling_record) . "_" . $printed_date . "_MonthOverMonth";
            
            $bodyFileName = $fileDir.'/body_'.$name.'.html';
            $fb = fopen($bodyFileName, 'w');
            $b = '<html><style>'.$stylesheet.'</style>'.$pdf_content.'</html>';
            fwrite($fb, $b);
            fclose($fb);
            
            $footer ="<!doctype html>
			<html>
				<head>
					<meta charset='utf-8'>
					<script>
						function substitutePdfVariables() {
                            
							function getParameterByName(name) {
								var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
								return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
							}
                            
							function substitute(name) {
								var value = getParameterByName(name);
								var elements = document.getElementsByClassName(name);
                            
								for (var i = 0; elements && i < elements.length; i++) {
									elements[i].textContent = value;
								}
							}
                            
							['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection']
								.forEach(function(param) {
									substitute(param);
								});
						}
					</script>
				</head>
				<body onload='substitutePdfVariables()'>
					<div style='width:100%;'>
						<div style='width:40%; float:left;vertical-align:middle;line-height:30px;'>
							<p style='color:black;font-family:arial,  Sans-Serif, font-size:15px;padding-top:30px;'>
								Page <span class='page'></span> of <span class='topage'></span> <span style='font-size:12px;'>Disclosures are on the final two pages</span>
							</p>
						</div>
						<!-- <div style='float:right; width:60%;'>
							<img class='pdf_crm_logo' src='" . $site_URL . "" . $logo . "' style='float:right;'  width='40%'/>
						</div> -->
					</div>
				</body>
			</html>";
            $footerFileName = $fileDir.'/footer_'.$name.'.html';
            $ff = fopen($footerFileName, 'w');
            $f = $footer;
            fwrite($ff, $f);
            fclose($ff);
            
            $whtmltopdfPath = $fileDir.'/'.$name.'.pdf';
            
            $output = shell_exec("wkhtmltopdf --javascript-delay 4000 -T 10.0 -B 25.0 -L 5.0 -R 5.0  --footer-html ".$footerFileName." --footer-font-size 10 ". $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
            
            unlink($bodyFileName);
            unlink($footerFileName);
            
            $filePath[] = $whtmltopdfPath;
                        
        }
        
        if(!$request->get('sendEmail')){
            $this->GeneratePDF($filePath);
        }else if ($request->get('sendEmail')){
            $this->SendEmail($filePath, $request->get('userEmail'));
        }
        
    }
    
}