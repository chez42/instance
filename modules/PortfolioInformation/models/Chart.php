<?php 

class PortfolioInformation_Chart_Model extends Vtiger_Module {

	function getAssetAllocationChartData(){
		
		$customView = new CustomView();

		$moduleName = "PortfolioInformation";
		
		$viewid = $customView->getViewId($moduleName);
		 
		$global_summary = new PortfolioInformation_GlobalSummary_Model();
            
		$pie_values = $global_summary->GetTrailingFilterPieTotalsFromListViewID($viewid);
        
		$pie = array();
		
		if(!empty($pie_values)){
		
			if($pie_values['Equities'] == 0 && $pie_values['Cash'] == 0 && $pie_values['Fixed Income'] == 0)
				return array();
				
			foreach($pie_values as $k => $v){
				
				$color = PortfolioInformation::GetChartColorForTitle($k);
				
				if($color){
					$pie[] = array(
						"title"=>$k, 
						"value"=>$v,
						"color"=>$color
					);
				} else {
					$pie[] = array(
						"title"=>$k,
						"value"=>$v
					);
				}
			}
		}
		return $pie;
	}
	
	function getAccountActivityChartData(){
		    
		$customView = new CustomView();

		$moduleName = "PortfolioInformation";
		
		$viewid = $customView->getViewId($moduleName);
		 
		$global_summary = new PortfolioInformation_GlobalSummary_Model();
                    
		$query = null;
		
		$active_values = $global_summary->GetTrailingAccountsCountFromListViewID($viewid, $query, 1);
		
		$new_accounts = $global_summary->GetTrailingNewAcccountsFromListViewID($viewid, $query, 0);
		
		$closed_accounts = $global_summary->GetTrailingClosedAcccountsFromListViewID($viewid, $query, 0);
		
		$account_activity = array();
		
		foreach($active_values AS $k => $v){
			$tmp = array();
			$tmp['date'] = $v['date'];
			$tmp['value'] = $v['value'];
			$tmp['new_accounts'] = ($new_accounts[$k]['new_accounts'] == null)?0:$new_accounts[$k]['new_accounts'];
			$tmp['closed_accounts'] = ($closed_accounts[$k]['closed_accounts'] == null)?0:$closed_accounts[$k]['closed_accounts'];
			$account_activity[] = $tmp;
		}
		
		return $account_activity;
	}
	
	static public function getTrailing12RevenueChartData($start_date, $end_date){
		
		$customView = new CustomView();

		$moduleName = "PortfolioInformation";

		$viewid = $customView->getViewId($moduleName);
		 
		$global_summary = new PortfolioInformation_GlobalSummary_Model();
         
		$revenue_values = $global_summary->GetTrailing12RevenueFromListViewID($viewid, $query, $start_date, $end_date);
		
		return $revenue_values;
    }

    static public function getTrailing12ZoomRevenueChartData($start_date, $end_date){

        $customView = new CustomView();

        $moduleName = "PortfolioInformation";

        $viewid = $customView->getViewId($moduleName);

        $global_summary = new PortfolioInformation_GlobalSummary_Model();

        $revenue_values = $global_summary->GetTrailing12ZoomRevenueFromListViewID($viewid, $query, $start_date, $end_date);

        return $revenue_values;
    }

    static public function getTrailingBalancesChartData(){
//	    $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForLoggedInUser(false);//Get all account numbers that ever belonged to the user, not just currently open ones
        $global_summary = new PortfolioInformation_GlobalSummary_Model();
//        $revenue_values = $global_summary->GetTrailingBalancesForAccounts($account_numbers);
        $revenue_values = $global_summary->GetTrailingBalancesForAccountsUsingTotalsTable();
        return $revenue_values;
    }

    static public function getAssetAllocationData(){
	    $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForLoggedInUser(true);//Get all account numbers that ever belonged to the user, not just currently open ones
        $global_summary = new PortfolioInformation_GlobalSummary_Model();
        $revenue_values = $global_summary->GetAssetAllocationDataForAccountsFromCalculatedUserTable();
        PortfolioInformation_Chart_Model::assignRandomColorToColorFieldInArray($revenue_values);
        return $revenue_values;
    }

    static public function getAccountActivity(){
//        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForLoggedInUser(false);//Get all account numbers that ever belonged to the user, not just currently open ones
        $global_summary = new PortfolioInformation_GlobalSummary_Model();
        $revenue_values = $global_summary->GetTrailingBalancesForAccountsUsingTotalsTable();
//        $revenue_values = $global_summary->GetTrailingBalancesForAccounts($account_numbers);
        return $revenue_values;
    }

    static public function getPieChartForRecord($record, $group_type){
	    $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromCrmid($record);
        PortfolioInformation_HoldingsReport_Model::GenerateDefinedTables($account_numbers, $group_type);
        return PortfolioInformation_Reports_Model::GetPieFromTable();
    }

    static public function getPieChartAsOfDateForAccounts($accounts, $as_of_date){
        $account_numbers = explode(",", $accounts);
        $account_numbers = array_unique($account_numbers);
        if (sizeof($account_numbers) > 0) {
            PortfolioInformation_Reports_Model::GeneratePositionsValuesTable($account_numbers, $as_of_date);
            $new_pie = PortfolioInformation_Reports_Model::GetPositionValuesPie();
            return $new_pie;
        }
        return 0;
    }

    static public function assignRandomColorToColorFieldInArray(&$array){
        require_once("libraries/Reporting/ReportCommonFunctions.php");
	    foreach($array AS $k => $v){
	        if($v['color'] == null){
	            $tmp = $v;
	            $tmp['color'] = '#' . random_color();
	            $array[$k] = $tmp;
            }
        }
        return $array;
    }

    static public function getHoldingsWidgetDatasetsForRecord($record){
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromCrmid($record);
        $datasets = array();
        PortfolioInformation_HoldingsReport_Model::GenerateDefinedTables($account_numbers, 'aclass');
        $datasets[] = PortfolioInformation_Reports_Model::GetPieFromTable();
        PortfolioInformation_HoldingsReport_Model::GenerateDefinedTables($account_numbers, 'securitytype');
        $datasets[] = PortfolioInformation_Reports_Model::GetPieFromTable();
        PortfolioInformation_HoldingsReport_Model::GenerateDefinedTables($account_numbers, 'security_symbol');
        $datasets[] = PortfolioInformation_Reports_Model::GetPieFromTable();

        foreach($datasets AS $k => $v){
            $result = self::assignRandomColorToColorFieldInArray($v);
            $datasets[$k] = $result;
        }

        return $datasets;
    }


    function getHeatMapData(){
		
		$adb = PearDatabase::getInstance();
		
		$moduleName = "PortfolioInformation";
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	
		$headerColumns = array("state", "stated_value_date", "accountclosed");
		
		$queryGenerator->setFields( $headerColumns );

		$listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
		
		$fieldModelList = $moduleModel->getFields();
		
		if(isset($fieldModelList['state'])){
			
			$fieldModel = $fieldModelList['state'];
			
			if($fieldModel->getPermissions())
				$queryGenerator->addCondition("state", "", "ny");
		}
		
		$queryGenerator->addCondition("accountclosed", "0", "e","AND");
		 
		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
        $last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		
		$queryGenerator->addCondition("stated_value_date", "$last7days,$today", "bw","AND");
		
		
		$stateColumnName = $fieldModelList['state']->getCustomViewColumnName();
		$accountClosedColumnName = $fieldModelList['accountclosed']->getCustomViewColumnName();
		$statedValueColumnName = $fieldModelList['stated_value_date']->getCustomViewColumnName();
		
		$last7days = getValidDisplayDate($last7days);
		$today = getValidDisplayDate($today);
		
		$filterList = array(array("columns" => array(
            //array("columnname" => $stateColumnName,"comparator" => "ny","value" => '', "column_condition" => "and"),
			array("columnname" => $statedValueColumnName,"comparator" => "last7days","value" => "$last7days,$today", "column_condition" => "and"),
			array("columnname" => $accountClosedColumnName,"comparator" => "e","value" => 0, "column_condition" => "and"),
		)));
		
		$query = $queryGenerator->getQuery();
		
		$whereFields = $queryGenerator->getWhereFields();
		
		if(!in_array("state", $whereFields))
			$query .= "AND (vtiger_portfolioinformationcf.state IS NOT NULL AND vtiger_portfolioinformationcf.state !=  '') ";
		
		$query = str_replace('SELECT', 'SELECT COUNT(vtiger_portfolioinformationcf.state) as state_count, ', $query);
		
		$query .= " GROUP BY vtiger_portfolioinformationcf.state";
		
		$result = $adb->pquery($query,array());
		
		$state_data = array();
		
		if($adb->num_rows($result)){
			
			while($row = $adb->fetchByAssoc($result)){
				
				$state_data[] = array(
					"id" => "US-".$row['state'],
					"value" => $row['state_count'],
					"urlTarget" => "_blank",
					"url" => $this->generateLink($stateColumnName, $row['state'], $filterList)
				);
			}
		}
		
		return $state_data;
	}
	
	/**
	 * Function generate links
	 * @param <String> $field - fieldname
	 * @param <Decimal> $value - value
	 * @return <String>
	 */
	function generateLink($field, $value,$filter) {
		
		$moduleName = "PortfolioInformation";
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		// Special handling for date fields
		$comparator = 'e';
		$dataFieldInfo = @explode(':', $field);
		if(($dataFieldInfo[4] == 'D' || $dataFieldInfo[4] == 'DT') && !empty($dataFieldInfo[5])) {
			$dataValue = explode(' ',$value);
			if(count($dataValue) > 1) {
				$comparator = 'bw';
				$value = date('Y-m-d H:i:s' ,strtotime($value)).','.date('Y-m-d' ,strtotime('last day of'.$value)).' 23:59:59';
			} else {
				$comparator = 'bw';
				$value = date('Y-m-d H:i:s' ,strtotime('first day of JANUARY '.$value)).','.date('Y-m-d' ,strtotime('last day of DECEMBER '.$value)).' 23:59:59';
			}
		} elseif($dataFieldInfo[4] == 'DT') {
			$value = Vtiger_Date_UIType::getDisplayDateTimeValue($value);
		}

		if(empty($value)) {
			$comparator = 'empty';
		}
		
		//Step 1. Add the filter condition for the field
		$filter[0]['columns'][] = array(
									'columnname' => $field,
									'comparator' => $comparator,
									'value' => $value,
									'column_condition' => ''
								);

		//Step 2. Convert report field format to normal field names
		foreach($filter as $index => $filterInfo) {
			foreach($filterInfo['columns'] as $i => $column) {
				if($column) {
					$fieldInfo = @explode(':', $column['columnname']);
					$filter[$index]['columns'][$i]['columnname'] = $fieldInfo[2];
				}
			}
		}

		//Step 3. Convert advanced filter format to list view search format
		$listSearchParams = array();
		$i=0;
		if($filter) {
			foreach($filter as $index => $filterInfo) {
				foreach($filterInfo['columns'] as $j => $column) {
					if($column) {
						$listSearchParams[$i][] = array($column['columnname'], $column['comparator'], $column['value']);
					}
				}
				$i++;
			}
		}
		
		//Step 4. encode and create the link
		$baseModuleListLink = $moduleModel->getListViewUrlWithAllFilter();
		$baseModuleListLink = str_ireplace("view=List", "view=GraphFilterList", $baseModuleListLink);
		
		return $baseModuleListLink.'&search_params='. json_encode($listSearchParams);
	}
}