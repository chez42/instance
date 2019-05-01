<?php

class PositionInformation_Chart_Model extends Vtiger_Module {

	function getAssetAllocationChartData(){
		
		$global_summary_port = new PortfolioInformation_GlobalSummary_Model();

		$pie_values = $global_summary_port->GetTrailingFilterPieTotalsFromListViewID(1353);

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
						"color"=>$color,
					);
				} else {
					$pie[] = array(
						"title"=>$k,
						"value"=>$v,
					);
				}
			}
		}
		
		return $pie;
	}
	
	function getTrailing12AUMChartData(){
		
		$global_summary = new PortfolioInformation_GlobalSummary_Model();
         
		$asset_values = $global_summary->GetTrailingAUMFromListViewID(1353);
		
		return $asset_values;
    }
	
	function getSecurityClassesChartData(){
		
		$adb = PearDatabase::getInstance();
		
		$moduleName = "PositionInformation";
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	
		$headerColumns = array("base_asset_class");
		
		$queryGenerator->setFields( $headerColumns );

		$listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
		
		$fieldModelList = $moduleModel->getFields();
		
		if(isset($fieldModelList['base_asset_class'])){
			
			$fieldModel = $fieldModelList['base_asset_class'];
			
			if($fieldModel->getPermissions())
				$queryGenerator->addCondition("base_asset_class", "", "ny");
		}
		
		$aclassColumnName = $fieldModelList['base_asset_class']->getCustomViewColumnName();
		
		$aclassColumnName = $fieldModelList['base_asset_class']->getCustomViewColumnName();
		$closedAccountColumnName = $fieldModelList['closed_account']->getCustomViewColumnName();
		$currentValueColumnName = $fieldModelList['current_value']->getCustomViewColumnName();
		$cf_2662ColumnName = $fieldModelList['cf_2662']->getCustomViewColumnName();
		$lastUpdateColumnName = $fieldModelList['last_update']->getCustomViewColumnName();
		
		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
        $last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		
		$advFilterList = Array(
			Array( 
				'columns' => Array(
					Array(
						'columnname' => $lastUpdateColumnName,
						'comparator' => 'last7days',
						'value' => "$last7days,$today",
						'column_condition' => ''
					)
				)
			)
		);
		$advFilterList1 = Array(
			Array(
				'columns' => Array(
					Array(
							'columnname' => $closedAccountColumnName,
							'comparator' => 'e',
							'value' => 0,
							'column_condition' => 'and'
						),
					Array(
							'columnname' => $currentValueColumnName,
							'comparator' => 'n',
							'value' => 0,
							'column_condition' => ''
						)
				),
				'condition' => 'and'
			),
			Array(
				'columns' => Array(
					Array(
						'columnname' => $cf_2662ColumnName,
						'comparator' => 'e',
						'value' => 1,
						'column_condition' => 'or'
					),
					Array(
						'columnname' => $lastUpdateColumnName,
						'comparator' => 'last7days',
						'value' => "$last7days,$today",
						'column_condition' => ''
					)
				),
				'condition' => ''
			)
		);

		if($queryGenerator->conditionInstanceCount > 0)
			$queryGenerator->addConditionGlue('AND');
		
		$queryGenerator->parseAdvFilterList($advFilterList);
		
		$query = $queryGenerator->getQuery();
		
		$query = explode("SELECT", $query);
		
		$query = "SELECT vtiger_positioninformation.positioninformationid, SUM(vtiger_positioninformation.current_value) as base_asset_class_sum, vtiger_chart_colors.color, ".$query[1].
		" AND vtiger_positioninformationcf.base_asset_class IN ('Bonds','Other','Cash','Stocks') GROUP BY vtiger_positioninformationcf.base_asset_class WITH ROLLUP 
		HAVING vtiger_positioninformation.positioninformationid > 0";
		
		$query = explode("WHERE", $query);
		
		$query = $query[0]." LEFT JOIN vtiger_chart_colors on vtiger_chart_colors.title = vtiger_positioninformationcf.base_asset_class WHERE ".$query[1];
		
		$result = $adb->pquery($query,array());
		
		$data = array();
		
		if($adb->num_rows($result)){
			
			$i = 0;
			
			while($row = $adb->fetchByAssoc($result)){
				
				if($row['base_asset_class'] == '')
					$data['grand_total'] = $row['base_asset_class_sum'];
				else {
					$data['chartData'][$i] = array(
						"title" => $row['base_asset_class'],
						"value" => $row['base_asset_class_sum'],
						"url" => $this->generateLink($aclassColumnName, $row['base_asset_class'], $advFilterList)
					);
					
					if($row['color'] != '' && $row['color'] != 'NULL')
						$data['chartData'][$i]["color"] = $row['color'];
				}
				$i++;
			}
		}
		
		return $data;
	}
	
	
	/**
	 * Function generate links
	 * @param <String> $field - fieldname
	 * @param <Decimal> $value - value
	 * @return <String>
	 */
	function generateLink($field, $value,$filter) {
		
		$moduleName = "PositionInformation";
		
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