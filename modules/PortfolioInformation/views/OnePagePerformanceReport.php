<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");

include_once "libraries/custodians/cCustodian.php";
require_once("libraries/Reporting/PerformanceReport.php");

require_once("libraries/Reporting/ReportHistorical.php");
require_once("libraries/reports/pdf/cMpdf7.php");
require_once("libraries/reports/new/holdings_report.php");
require_once("modules/PortfolioInformation/models/NameMapper.php");
include_once("modules/PortfolioInformation/models/PrintingContactInfo.php");

class PortfolioInformation_OnePagePerformanceReport_View extends Vtiger_Index_View{

    public function postProcess(Vtiger_Request $request) {
		
        if($request->get("pdf") == 1){
			
		} else {
			parent::postProcess($request);
		}
	}
	
	public function preProcessTplName(Vtiger_Request $request) {
		
		if($request->get("pdf") == 1){
			return '';
		} else {
			return parent::preProcessTplName($request);
		}
 	}
	
	public  function DetermineIntervalStartDate($account_number, $sdate){
		
		global $adb;
		
		$questions = generateQuestionMarks($account_number);

		$query = "SELECT DATE_ADD(MAX(intervalbegindate), INTERVAL 1 DAY) AS begin_date
              FROM intervals_daily 
              WHERE accountnumber IN ({$questions}) AND intervalbegindate <= ?";
			  
		$result = $adb->pquery($query, array($account_number, $sdate));
		
		if($adb->num_rows($result) > 0){
			
			$result = $adb->query_result($result, 0, 'begin_date');
			
			if(is_null($result))
				return $sdate;
				
			return $result;
		
		}
		
		return $sdate;
	
	}
    
    function process(Vtiger_Request $request) {
        
		global $adb, $current_user;
		
		$viewer = $this->getViewer($request);
		
		if($request->get("account_number") > 0){
			
            $accounts = explode(",", $request->get("account_number"));
            
			$accounts = array_unique($accounts);

            $portfolio_result = $adb->pquery("select * from vtiger_portfolioinformation 
			inner join vtiger_contactdetails  on contact_link = contactid 
			inner join vtiger_contactaddress on contactaddressid = contactid
			
			where account_number = ?", array($accounts[0]));
			
			if($adb->num_rows($portfolio_result)){
				
				$viewer->assign("PREPARED_FOR", 
					
					$adb->query_result($portfolio_result, 0, "firstname") . ' ' . 
					$adb->query_result($portfolio_result, 0, "lastname")
					
				);
			}
			
			
			//Calculate Intervals
			
			PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($accounts, null, null, true);
			
			if(strlen($request->get('report_start_date')) > 1) {
               $start_date = date("Y-m-d", strtotime($request->get("report_start_date")));
			} else {
               $start_date =  date("Y-01-01");
            }
			$viewer->assign("START_DATE", $start_date);	
			
            if(strlen($request->get('report_end_date')) > 1) {
                $end_date = date("Y-m-d", strtotime($request->get("report_end_date")));
			} else {
				$end_date =  date("Y-12-31");
			}
			
			$viewer->assign("END_DATE", $end_date);	
			
			$viewer->assign("REPORT_PERIOD", $start_date . ' - ' .  $end_date);
			
			//$start_date = date("Y-m-d", strtotime($start_date));
			//$end_date = date("Y-m-d", strtotime($end_date));
			
			$selected_period_performance = new PerformanceReport_Model($accounts, 
			$this->DetermineIntervalStartDate($accounts, $start_date), $end_date);
			
			$result = $adb->pquery("select * from vtiger_portfolioinformation where 
			account_number in (?)", array($request->get("account_number")));
			$inception_date = $adb->query_result($result, 0, "inceptiondate");
			$since_inception_performance = new PerformanceReport_Model($accounts, 
			$this->DetermineIntervalStartDate($accounts, $inception_date),$end_date);
			
			$result = $adb->pquery("SELECT DATE_SUB(?, INTERVAL 12 MONTH) as last_month_date", array($end_date));			
			$last_12month_date = $adb->query_result($result, 0, "last_month_date");
			$last_12month_performance = new PerformanceReport_Model($accounts, 
			$this->DetermineIntervalStartDate($accounts, $last_12month_date), $end_date);
			
			$result = $adb->pquery("SELECT DATE_SUB(?, INTERVAL 3 MONTH) as last_month_date", array($end_date));			
			$last_3month_date = $adb->query_result($result, 0, "last_month_date");
			$last3_month_performance = new PerformanceReport_Model($accounts, $this->DetermineIntervalStartDate($accounts, $last_3month_date), $end_date);
            
			$ytd_performance = new PerformanceReport_Model($accounts, 
			$this->DetermineIntervalStartDate($accounts, GetDateStartOfYear($end_date)), $end_date);
			
			$index_return_data = array();
			
			//Last 12 Month Index Return
			$index_return_data[] = array(
				'GSPC' => $this->GetIndex("GSPC", $request->get("account_number"), $last_12month_date, $end_date),
				'AGG' => $this->GetIndex("AGG", $request->get("account_number"), $last_12month_date, $end_date)
			);
			
			
			//YTD Index Return
			$index_return_data[] = array(
				'GSPC' => $this->GetIndex("GSPC", $request->get("account_number"), 
				GetDateStartOfYear($end_date), $end_date),
				'AGG' => $this->GetIndex("AGG", $request->get("account_number"), 
				GetDateStartOfYear($end_date), $end_date)
			);
			
			//Since Inception
			$index_return_data[] = array(
				'GSPC' => $this->GetIndex("GSPC", $request->get("account_number"), 'since_inception', $end_date),
				'AGG' => $this->GetIndex("AGG", $request->get("account_number"), 'since_inception', $end_date),
			);
			
			//Last 3 Months Index Return
			$index_return_data[] = array(
				'GSPC' => $this->GetIndex("GSPC", $request->get("account_number"), $last_3month_date, $end_date),
				'AGG' => $this->GetIndex("AGG", $request->get("account_number"), $last_3month_date, $end_date),
			);
			
			$index_return_data[2]['50/50'] = ($index_return_data[2]['GSPC'] + $index_return_data[2]['AGG']) /2;
			$index_return_data[1]['50/50'] = ($index_return_data[1]['GSPC'] + $index_return_data[1]['AGG']) /2;
			$index_return_data[0]['50/50'] = ($index_return_data[0]['GSPC'] + $index_return_data[0]['AGG'])/2;
			$index_return_data[3]['50/50'] = ($index_return_data[3]['GSPC'] + $index_return_data[3]['AGG'])/2;
			
			$viewer->assign("INDEX_RETURN_DATA", $index_return_data);
			
			$viewer->assign("SELECTED_PERIOD_PERFORMANCE", $selected_period_performance);
			$viewer->assign("YTD_PERFORMANCE", $ytd_performance);
			$viewer->assign("LAST_3_MONTHS_PERFORMANCE", $last3_month_performance);
			
			
			
			$viewer->assign("SINCE_INCEPTION_PERFORMANCE", $since_inception_performance);
			$viewer->assign("LAST_12_MONTHS_PERFORMANCE", $last_12month_performance);
			
			
			
			
			$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            $viewer->assign("ACCOUNT_NUMBER", $request->get("account_number"));

			global $site_URL;
			
			$ispdf = $request->get('pdf');
			
			if($ispdf) {
				$viewer->assign("IS_PDF", $ispdf);
			}
			
			$screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/OnePagePerformanceReport.tpl', "PortfolioInformation");
			
			if($ispdf) {
				
				
				
				$stylesheet  = '<link type="text/css" rel="stylesheet" href = "' . $site_URL . 'layouts/v7/lib/todc/css/bootstrap.min.css">';
				
				$screen_content = $stylesheet . $screen_content;
				
				$fileDir = 'cache/PerformanceReport';
				
				if (!is_dir($fileDir)) {
					mkdir($fileDir);
				}
				
				$bodyFileName = $fileDir.'/PerformanceReport.html';
				
				$fb = fopen($bodyFileName, 'w');
				
				fwrite($fb, $screen_content);
				
				fclose($fb);
				
				$whtmltopdfPath = $fileDir.'/PerformanceReport.pdf';
				
				
				
				 $footer ="<!doctype html>
				<html>
				
					<head>
						<meta charset='utf-8'>
						<script>
						function subst() {
								var vars = {};
								var x = document.location.search.substring(1).split('&');
								for (var i in x) {
									var z = x[i].split('=', 2);
									vars[z[0]] = unescape(z[1]);
								}
								var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
								
								for (var i in x) {
									
									var y = document.getElementsByClassName(x[i]);
									
									for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];

									if (vars['page'] == 1) {
										document.getElementById('FakeHeaders').style.display = 'none';
									}
								}
						 }
						 </script>
					</head>
					
					<body onload='subst()'>
						<div style='width:100%;'>
							<div style='width:100%; float:left;vertical-align:middle;line-height:30px;' id = 'FakeHeaders'>
								<p>
									Market values are obtained from sources believed to be reliable but are not guaranteed. No representation is made as to this review's accuracy or completeness.<br/>
									The performance data quoted represents past performance and does not guarantee future 
									results. The investment return and principal value of an investment will fluctuate thus an investor's shares, when redeemed, may be worth more or less than return data quoted herein.
								</p>
							</div>
						</div>
					</body>
				</html>";
                $footerFileName = $fileDir.'/footer_PR.html';
                $ff = fopen($footerFileName, 'w');
                $f = $footer;
                fwrite($ff, $f);
                fclose($ff);
				
				shell_exec("wkhtmltopdf -O landscape --javascript-delay 2000 -T 10.0 -B 25.0 -L 5.0 -R 5.0 --footer-html ".$footerFileName." --footer-font-size 10 " . $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
				
				header("Content-Type: application/octet-stream");
				
				header('Content-Disposition: attachment; filename="PerformanceReport.pdf"');
				
				readfile($whtmltopdfPath);
				
			} else {
				echo $screen_content;
			}
		
		} else { 
            return "<div class='ReportBottom'></div>";
		}
    }


    public function GetIndex($index, $account_number, $start_date = 'since_inception', $end_date){
        
		global $adb;
		
		if($start_date == 'since_inception'){
			
			$result = $adb->pquery("select * from vtiger_portfolioinformation where 
			account_number in (?)", array($account_number));
			
			$inception_date = $adb->query_result($result, 0, "inceptiondate");
			
			$start_date = date("Y-m-d", strtotime($inception_date . " - 1 DAY"));
		}
		
		return round($this->getReferenceReturn($index, $start_date, $end_date), 2);
	
	}
	
	
	function getReferenceReturn($symbol,$startDate,$endDate) {
		global $adb;

		$end = $start = array();
		$symbol = html_entity_decode($symbol);

		$result = $adb->pquery("SELECT to_days(date) as to_days, date AS price_date, close AS price from 
		   vtiger_prices_index where date <= ?
		   AND symbol = ? 
		   order by date DESC limit 1",array($startDate,$symbol));

		if($adb->num_rows($result) <= 0)
			return 0;

		while($v = $adb->fetchByAssoc($result))
			$start = $v;

		  $query = "SELECT to_days(date) as to_days, date AS price_date, close AS price 
					  FROM vtiger_prices_index WHERE date <= ?
					  AND symbol = ?
					  order by price_date desc limit 1";
			$end_result = $adb->pquery($query,array($endDate,$symbol));

		if($adb->num_rows($end_result) <= 0)
			return 0;

		while($v = $adb->fetchByAssoc($end_result))
			$end = $v;

		$intervalDays = $end['to_days'] - $start['to_days'];

		$guess = $end['price'] / $start['price'] - 1;

		if ($intervalDays >= 365)
			$irr = pow((1+$guess),(365/$intervalDays)) - 1;
		else
			$irr = $guess;

		return $irr * 100;
	}
	
	public function getHeaderScripts(Vtiger_Request $request) {
        
		$headerScriptInstances = parent::getHeaderScripts($request);
        
		$moduleName = $request->getModule();
        
		$jsFileNames = array(
            "modules.PortfolioInformation.resources.DateSelection",
        );
		
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

}