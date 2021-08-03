<?php
require_once("libraries/Reporting/ReportCommonFunctions.php");

include_once "libraries/custodians/cCustodian.php";

require_once("libraries/Reporting/PerformanceReport.php");


class PortfolioInformation_OnePagePerformanceReport_View extends Vtiger_Index_View {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('viewForm');
		$this->exposeMethod('DownloadReport');
	}
	
	function viewForm(Vtiger_Request $request){
	    
	    $sourceModule = $request->getModule();
	    
	    $viewer = $this->getViewer($request);
		
		$cvId = $request->get('viewname');
		$selectedIds = $request->get('selected_ids');
		$excludedIds = $request->get('excluded_ids');
		
		$viewer->assign('CVID', $cvId);
		$viewer->assign('SELECTED_IDS', $selectedIds);
		$viewer->assign('EXCLUDED_IDS', $excludedIds);
		
		$searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
		$operator = $request->get('operator');
        if(!empty($operator)) {
			$viewer->assign('OPERATOR',$operator);
			$viewer->assign('ALPHABET_VALUE',$searchValue);
            $viewer->assign('SEARCH_KEY',$searchKey);
		}
		
		$searchParams = $request->get('search_params');
        if(!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS',$searchParams);
        }
	    
		$viewer->assign("TYPE", "OnePagePerformanceReport");
		
	    echo $viewer->view('PerformanceReportDownloadForm.tpl','PortfolioInformation',true);
	   
	}
	
	
	function DownloadReport(Vtiger_Request $request){
		set_time_limit(-1);

		global $adb;
		
		$record_ids = $this->getRecordsListFromRequest($request);
		
		$result = $adb->pquery("SELECT * FROM vtiger_portfolioinformation
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid
        WHERE vtiger_crmentity.deleted = 0 AND
        vtiger_portfolioinformation.portfolioinformationid IN (".generateQuestionMarks($record_ids).")",$record_ids);
		
		$viewer = $this->getViewer($request);
		
		$pdf_files = array();
		
		for($i = 0; $i < $adb->num_rows($result); $i++){
			
			$account_no = $adb->query_result($result, $i, "account_number");
			
			$account_numbers = array($account_no);
			
			PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($account_numbers, null, null, true);
			
			$file_content = $this->GenerateReport($account_no, $request);
		    
			file_put_contents("cache/$account_no" . '_' . date("Y-m-d") . ".pdf", $file_content);
			
			$pdf_files[]  = "cache/$account_no" . '_' . date("Y-m-d") . ".pdf";
		
		}
		
		$zipname  = 'cache/'.date('Y-m-d').'.zip';
        
		$files = implode("' '", $pdf_files);
		
		$files = "'" . $files . "'";
		
		$zip_password = strtotime(date("Y-m-d H:i:s"));
		
		@exec("zip -D -j $zipname $files");
		
		while(ob_get_level()) {
            ob_end_clean();
        }
		
        header('Content-Type: application/zip');
		
        header('Content-disposition: attachment; filename='.basename($zipname));
        
		readfile($zipname);
        
        foreach ($pdf_files as $file) {
            unlink($file);
        }
        
        unlink($zipname);
	
	}
	
	function preProcess(Vtiger_Request $request, $display=true) {
		$mode = $request->get('mode');
		
		if(!$mode){
			global $adb;
			
			parent::preProcess($request, false);
			
			$viewer = $this->getViewer($request);
			
			$portfolio_result = $adb->pquery("select * from vtiger_portfolioinformation
			inner join vtiger_portfolioinformationcf on 
			vtiger_portfolioinformationcf.portfolioinformationid = vtiger_portfolioinformation.portfolioinformationid			
			inner join vtiger_contactdetails  on contact_link = contactid 
			inner join vtiger_contactaddress on contactaddressid = contactid

			where account_number = ?", array($request->get("account_number")));
			
			$record_id = $adb->query_result($portfolio_result, 0, "portfolioinformationid");
			
			$recordModel = Vtiger_DetailView_Model::getInstance("PortfolioInformation", $record_id);
			
			$recordModel = $recordModel->getRecord();
			
			$viewer->assign('RECORD', $recordModel);
			
			$moduleName = $request->getModule();
			
			if($display) {
				$this->preProcessDisplay($request);
			}
		} else {
			return true;
		}
	}
	
	function postProcess(Vtiger_Request $request) {
		
		$mode = $request->get('mode');
		
		if(!$mode){
			parent::postProcess($request);	
		} else {
			return true;
		}
		
	}

	public  function DetermineIntervalStartDate($account_number, $sdate){
		
		global $adb;
		
		$questions = generateQuestionMarks($account_number);

		$query = "SELECT DATE_ADD(MAX(intervalbegindate), INTERVAL 1 DAY) AS begin_date
	    FROM intervals_daily  WHERE accountnumber IN ({$questions}) AND intervalbegindate <= ?";
	  
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
		
		$mode = $request->get('mode');
		
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
		
		$viewer = $this->getViewer($request);
		
		$account_no = $request->get("account_number");
		
		$account_numbers = array($account_no);
		PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($account_numbers, null, null, true);
		
		$file_content = $this->GenerateReport($account_no, $request);
		
		$viewer->assign("BLOB_CONTENT", base64_encode($file_content));
		
		$viewer->assign("USER_DATE_FORMAT", $current_user->date_format);
		
		$viewer->view('OnePagePerformanceReport.tpl', "PortfolioInformation");
		
    }
	
	
	function GenerateReport($account_no, Vtiger_Request $request){
		
		global $adb;
		
		$viewer = $this->getViewer($request);
		
		$report_start_date = DateTimeField::convertToDBFormat(str_replace("/", "-", $request->get("report_start_date")));
		
		$report_end_date = DateTimeField::convertToDBFormat(str_replace("/", "-", $request->get("report_end_date")));
		
		//PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($account_numbers, null, null, true);
		
		//foreach($account_numbers as $account_no){
			
		$portfolio_result = $adb->pquery("select * from vtiger_portfolioinformation
		inner join vtiger_portfolioinformationcf on 
		vtiger_portfolioinformationcf.portfolioinformationid = vtiger_portfolioinformation.portfolioinformationid			
		inner join vtiger_contactdetails  on contact_link = contactid 
		inner join vtiger_contactaddress on contactaddressid = contactid
		where account_number = ?", array($account_no));

		$viewer->assign("PREPARED_FOR", 
			$adb->query_result($portfolio_result, 0, "firstname") . ' ' . 
			$adb->query_result($portfolio_result, 0, "lastname")
		);
		
		$viewer->assign("PORTFOLIO_TYPE", $adb->query_result($portfolio_result, 0, "cf_2549"));
	
		if(strlen($report_start_date) > 1) {
		   $start_date = date("Y-m-d", strtotime($report_start_date));
		} else {
		   $start_date =  date("Y-01-01");
		}
		
		if(strlen($report_end_date) > 1) {
			$end_date = date("Y-m-d", strtotime($report_end_date));
		} else {
			$end_date =  date("Y-12-31");
		}
		
		$viewer->assign("REPORT_PERIOD", date("m/d/Y", strtotime($start_date)) . ' - ' .  date("m/d/Y", strtotime($end_date)));
		
		$date = new DateTimeField($start_date);
		$viewer->assign("START_DATE", $date->getDisplayDate());	
		
		$date = new DateTimeField($end_date);	
		$viewer->assign("END_DATE", $date->getDisplayDate());	
		
		$result = $adb->pquery("select * from vtiger_portfolioinformation where 
		account_number in (?)", array($account_no));
		$inception_date = $adb->query_result($result, 0, "inceptiondate");
		
		$accounts = array($account_no);
		
		$since_inception_performance = new PerformanceReport_Model($accounts, 
		$this->DetermineIntervalStartDate($accounts, $inception_date),$end_date);
		
		$selected_period_performance = new PerformanceReport_Model($accounts, 
		$this->DetermineIntervalStartDate($accounts, $start_date), $end_date);
		
		$result = $adb->pquery("SELECT LAST_DAY(DATE_SUB(?, INTERVAL 12 MONTH)) as last_month_date", array($end_date));			
		$last_12month_date = $adb->query_result($result, 0, "last_month_date");
		$last_12month_performance = new PerformanceReport_Model($accounts, 
		$this->DetermineIntervalStartDate($accounts, $last_12month_date), $end_date);
		
		$result = $adb->pquery("SELECT LAST_DAY(DATE_SUB(?, INTERVAL 3 MONTH)) as last_month_date", array($end_date));
		$last_3month_date = $adb->query_result($result, 0, "last_month_date");
		$last3_month_performance = new PerformanceReport_Model($accounts, $this->DetermineIntervalStartDate($accounts, $last_3month_date), $end_date);
		
		
		$ytd_performance = new PerformanceReport_Model($accounts, 
		$this->DetermineIntervalStartDate($accounts, GetDateStartOfYear($end_date)), $end_date);
		
		$index_return_data = array();
		
		if($inception_date > $last_12month_date){
			$calculated_start_date = $inception_date;
		} else {
			$calculated_start_date = $last_12month_date;
		}
				
				
		//Last 12 Month Index Return
		$index_return_data[] = array(
			'GSPC' => $this->GetIndex("GSPC", $account_no, $calculated_start_date, $end_date),
			'AGG' => $this->GetIndex("AGG", $account_no, $calculated_start_date, $end_date),
			"IRR" => $this->CalculateIRR($last_12month_performance, $calculated_start_date , $end_date, $account_no)
		);
			
				
		if($inception_date > GetDateStartOfYear($end_date)){
			$calculated_start_date = $inception_date;
		} else {
			$calculated_start_date = GetDateStartOfYear($end_date);
		}
			
		//YTD Index Return
		$index_return_data[] = array(
			'GSPC' => $this->GetIndex("GSPC", $request->get("account_number"), 
			$calculated_start_date, $end_date),
			'AGG' => $this->GetIndex("AGG", $request->get("account_number"), 
			$calculated_start_date, $end_date),
			"IRR" => $this->CalculateIRR($ytd_performance, $calculated_start_date , $end_date, $account_no)
		);
			
		$date1 = date_create($end_date);
		
		$date2 = date_create($inception_date);
		
		$diff = date_diff($date1, $date2);
		
		$total_days = $diff->days;
		
		$irr = $this->CalculateIRR($since_inception_performance, $inception_date, $end_date, $account_no, true);
		
		if($total_days > 365){
			$pow = pow( (1 + ($irr/ 100)) , 1 / ($total_days/365));
			$irr = round( ($pow - 1) * 100, 2);
		}
				
		//Since Inception
		$index_return_data[] = array(
			'GSPC' => $this->GetIndex("GSPC", $account_no, $inception_date, $end_date),
			'AGG' => $this->GetIndex("AGG", $account_no, $inception_date, $end_date),
			"IRR" => $irr
		);
				
		if($inception_date > $last_3month_date){
			$calculated_start_date = $inception_date;
		} else {
			$calculated_start_date = $last_3month_date;
		}
			
		//Last 3 Months Index Return
		$index_return_data[] = array(
			'GSPC' => $this->GetIndex("GSPC", $account_no, $calculated_start_date, $end_date),
			'AGG' => $this->GetIndex("AGG", $account_no, $calculated_start_date, $end_date),
			"IRR" => $this->CalculateIRR($last3_month_performance, $calculated_start_date, $end_date, $account_no)
		);
				
		$index_return_data[2]['50/50'] = round(($index_return_data[2]['GSPC'] + $index_return_data[2]['AGG']) /2, 2);
		$index_return_data[1]['50/50'] = round(($index_return_data[1]['GSPC'] + $index_return_data[1]['AGG']) /2, 2);
		$index_return_data[0]['50/50'] = round(($index_return_data[0]['GSPC'] + $index_return_data[0]['AGG'])/2, 2);
		$index_return_data[3]['50/50'] = round(($index_return_data[3]['GSPC'] + $index_return_data[3]['AGG'])/2, 2);
		
		$viewer->assign("INDEX_RETURN_DATA", $index_return_data);
		
		$viewer->assign("SELECTED_PERIOD_PERFORMANCE", $selected_period_performance);
		
		$viewer->assign("YTD_PERFORMANCE", $ytd_performance);
		
		$viewer->assign("LAST_3_MONTHS_PERFORMANCE", $last3_month_performance);
		
		$viewer->assign("LAST_12_MONTHS_PERFORMANCE", $last_12month_performance);
		
		$viewer->assign("MODULE", "PortfolioInformation");
		
		$viewer->assign("ACCOUNT_NUMBER", $account_no);

		global $site_URL;
			
		$ispdf = $request->get('pdf');
		
		$viewer->assign("IS_PDF", $ispdf);
		
		$screen_content = $viewer->fetch('layouts/v7/modules/PortfolioInformation/OnePagePerformanceReportContent.tpl', "PortfolioInformation");
		
		$stylesheet  = '<link type="text/css" rel="stylesheet" href = "' . $site_URL . 'layouts/v7/lib/todc/css/bootstrap.min.css">';
		
		$screen_content = $stylesheet . $screen_content;
			
		$fileDir = 'cache/Reports';
		
		if (!is_dir($fileDir)) {
			mkdir($fileDir);
		}
			
		$bodyFileName = $fileDir.'/PerformanceReport.html';
		
		$fb = fopen($bodyFileName, 'w');
		fwrite($fb, $screen_content);
		fclose($fb);
			
		$whtmltopdfPath = $fileDir.'/'. $request->get("account_number").'.pdf';
	
		shell_exec("wkhtmltopdf -O landscape --javascript-delay 2000 -T 10.0 -B 25.0 -L 5.0 -R 5.0 " . $bodyFileName.' '.$whtmltopdfPath.' 2>&1');
			
		return file_get_contents($whtmltopdfPath);
		
		//header("Content-Type: application/octet-stream");
		//header('Content-Disposition: attachment; filename="'.$request->get("account_number").'.pdf"');
		//readfile($whtmltopdfPath);
		
		unlink($whtmltopdfPath);
		
		unlink($bodyFileName);
	
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
	
	function CalculateIRR($performance_obj, $start_date, $end_date, $account_number, $since_inception = false){
		
		global $adb;
		
		$cashFlow = array();
		
		if($since_inception){
			$cashFlow[] = array("value" => 0, "day" => 0);
		} else {
			$cashFlow[] = array("value" => -$performance_obj->GetBeginningValuesSummed()->value, "day" => 0);
		}
		
		$date1 = date_create($end_date);
		
		$date2 = date_create($start_date);
		
		$diff = date_diff($date1, $date2);
		
		$total_days = $diff->days;
		
		$transaction_result = $adb->pquery("SELECT *, 
		SUM(vtiger_transactionscf.net_amount) as totalamount,
		CASE
			WHEN (
				vtiger_transactions.operation = '' 
			) then 'add'
			WHEN (
				vtiger_transactions.operation = '-' 
			) then 'minus'
		END  AS transaction_status
		FROM vtiger_transactions
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
		INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_transactions.account_number = ?
		AND 
		(
			vtiger_transactionscf.transaction_activity IN (
				'Transfer of funds', 
				'Deposit of funds', 
				'Withdrawal of funds', 
				'Moneylink Transfer',
				'Receipt of securities', 
				'Transfer of securities', 
				'Split or Share dividend',
				'Federal withholding',
				'Withdrawal Federal withholding',
				'Auto Cash Distribution'
			) or (
				vtiger_transactionscf.transaction_activity IN ('Check Transaction')
				AND transaction_type = 'Flow'
			) or (
				vtiger_transactionscf.transaction_activity IN ('Debit card transaction')
				AND transaction_type = 'Flow'
			)
		)
		
		AND (vtiger_transactions.trade_date >= ? AND vtiger_transactions.trade_date <= ?)
		GROUP BY vtiger_transactions.trade_date, transaction_status ORDER BY vtiger_transactions.trade_date ASC",
		array($account_number,$start_date, $end_date));
		
		for($i = 0; $i < $adb->num_rows($transaction_result);  $i++){
			
			$transaction_data = $adb->query_result_rowdata($transaction_result, $i);
                                
			$date1 = date_create($transaction_data['trade_date']);
			
			$date2 = date_create($start_date);
			
			$diff = date_diff($date1, $date2);
			
			$diffDays = $diff->days;
                                
			if(
				$transaction_data['transaction_status'] == 'minus' 
			){
				$totalAmount = $transaction_data['totalamount'];
			} else {
				$totalAmount = -$transaction_data['totalamount'];
			}
			$cashFlow[] = array("value" => $totalAmount, "day" => ($diffDays/$total_days));
		
		}
		
		$date1 = date_create($end_date);
		
		$date2 = date_create($start_date);
		
		$diff = date_diff($date1, $date2);
		
		$cashFlow[] = array(
			"value" => $performance_obj->GetEndingValuesSummed()->value, 
			"day" => ($diff->days/$total_days)
		);
		
		bcscale(11);

        $totalCashFlowItems = count($cashFlow);
		
        $maxIterationCount = 20000;
        
		$absoluteAccuracy = 10 ** -11;
        
		$x0 = 0.1;
		
        $i = 0;

        while ($i < $maxIterationCount) {
            
			$fValue = 0;
            
			$fDerivative = 0;

            for ($k = 0; $k < $totalCashFlowItems; $k++) {
				
				$div = bcdiv($cashFlow[$k]['value'], bcadd(1.0, $x0) **  $cashFlow[$k]['day']);
				
                $fValue = bcadd($fValue, $div);
                
				$fDerivative = bcadd($fDerivative, bcdiv(bcmul(-$k, $cashFlow[$k]['value']), bcadd(1.0, $x0) **  $cashFlow[$k]['day']));
            }

            $x1 = bcsub($x0, bcdiv($fValue, $fDerivative));
			
            if (abs($x1 - $x0) <= $absoluteAccuracy) {
				return round(($x1 *100), 2);
			}

            $x0 = $x1;
			
            $i++;
        }
		
        return null;
    }
	
	
	function getRecordsListFromRequest(Vtiger_Request $request) {
		$cvId = $request->get('viewname');
		$module = $request->get('module');
		if(!empty($cvId) && $cvId=="undefined"){
			$sourceModule = $request->get('sourceModule');
			$cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
		}
		$selectedIds = $request->get('selected_ids');
		$excludedIds = $request->get('excluded_ids');

		if(!empty($selectedIds) && $selectedIds != 'all') {
			if(!empty($selectedIds) && count($selectedIds) > 0) {
				return $selectedIds;
			}
		}

		$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
		if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }

            /**
			 *  Mass action on Documents if we select particular folder is applying on all records irrespective of
			 *  seleted folder
			 */
			if ($module == 'Documents') {
				$customViewModel->set('folder_id', $request->get('folder_id'));
				$customViewModel->set('folder_value', $request->get('folder_value'));
			}

			$customViewModel->set('search_params',$request->get('search_params'));
			return $customViewModel->getRecordIds($excludedIds,$module);
		}
	}
}