<?php
	if(!empty($_POST)){

		require_once('includes/config.php');
		require_once('includes/function.php'); 	
	
		$module = $_POST['module'];
		
		$action = $_POST['action'];

		if($module == 'Reports' && $action == 'loadPortalReports'){
			
			$loadReport = $_POST['loadReport'];
			
			//$account_number = $_POST['account_number'];
	
			$reportTypesDetail = getReportTypesData();
	
			$reportFunctionName = (isset($reportTypesDetail[$loadReport]))?$reportTypesDetail[$loadReport]['function_name']: false;
		
			$reportFilePath = isset($reportTypesDetail[$loadReport]) ? $reportTypesDetail[$loadReport]['filepath'] : "";

			if($reportFilePath){
				
				//$params = $account_number;
				
				if($loadReport == 'ghreport' || $loadReport == 'gh2report' || $loadReport == 'assetclassreport'){
					
					//$params = array("account_number" => $account_number);
					
					if(isset($_REQUEST['reportStartDate']) && $_REQUEST['reportStartDate'] != '')
						$params['report_start_date'] = $_REQUEST['reportStartDate'];
					
					if(isset($_REQUEST['reportEndDate']) && $_REQUEST['reportEndDate'] != '')
						$params['report_end_date'] = $_REQUEST['reportEndDate'];
					
					if(isset($_REQUEST['selectedDate']) && $_REQUEST['selectedDate'] != '')
						$params['selectedDate'] = $_REQUEST['selectedDate'];
				}
				
				$data = $reportFunctionName($params);
		
				//$data['account_number'] = $account_number;
				
				require_once($reportFilePath);  
				
			} else	
				require_once("not-authorized.php");	
		
		} else if($module == 'Reports' && $action == 'getReportAccounts'){
			
			$field = $_REQUEST['show_report'];
	
			$accountid = $_SESSION['accountid'];
			
			if(isset($_REQUEST['draw']) && $_REQUEST['draw'] > 0){
				$pageLimit = $_REQUEST['start'].", ".$_REQUEST['length'];
			} else 
				$pageLimit = 10;
			
			$order = $_REQUEST['order'][0]['column'];
			$orderBy = $_REQUEST['order'][0]['dir'];
			
			$sparams = array(
				'accountid'	=> $accountid, 
				'module'	=> "Reports",
				'contactid'	=> $_SESSION['ID'],
				'show_report' => $field,
				'page_limit' => $pageLimit,
				'order' => $order,
				'orderBy' => $orderBy,
			);

			$data = array();
			
			$data = get_reports($sparams);
	
			if(isset($_REQUEST['draw']) && $_REQUEST['draw'] > 0){
				$records = array();
				$data['allowAccDetail'] = $_REQUEST['viewAccountDetail'];
				$records["data"] = getReportsDataTableBodyData($data); 
				$records["footerData"] = $data['grandTotals'];
				$records["draw"] = $_REQUEST['draw'];
				$records["recordsTotal"] = $data['recordsTotal'];
				$records["recordsFiltered"] = $data['recordsTotal'];
				echo json_encode($records);
				exit; 
			}
		}
	}
	
	function getReportsDataTableBodyData($data){
		
		$allowedReports = $data['allowAccDetail'];
	
		$tbody = array();
		
		$summary_info = $data['summary_info'];
		
		foreach($summary_info as $reportFieldsInfo){
			
			if(!empty($allowedReports))
				$accountNumber = "<a href='portfolio_account_detail.php?account_number=".$reportFieldsInfo['account_number']."'>".$reportFieldsInfo['account_number']."</a>";
			else
				$accountNumber = $reportFieldsInfo['account_number'];
			
			$tableRow = array(
				$accountNumber,
				$reportFieldsInfo['contact_link'],
				$reportFieldsInfo['account_type'],
				"$".number_format($reportFieldsInfo['total_value'], "2"),
				"$".number_format($reportFieldsInfo['securities'], "2"),
				"$".number_format($reportFieldsInfo['cash'], "2"),
				$reportFieldsInfo['nickname']
			);
			
			$tbody[] = $tableRow;
		}
		
		return $tbody;
	}