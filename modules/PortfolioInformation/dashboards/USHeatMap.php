<?php

class PortfolioInformation_USHeatMap_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		$viewer = $this->getViewer($request);
		
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		
		$chartModel = new PortfolioInformation_Chart_Model();
		
		$data = $chartModel->getHeatMapData();

		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
        
		$viewer->assign('HEADERS', $headerFields);
        $viewer->assign('DATA', $data);
        
		$content = $request->get('content');
		
		if(!empty($content)) {
			$viewer->view('dashboards/USHeatMapContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/USHeatMap.tpl', $moduleName);
		}
	}
	
	function getWidgetData(Vtiger_Request $request){
		
		$adb = PearDatabase::getInstance();
		
		$moduleName = $request->getModule();
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	
		$headerColumns = array("mailingstate");
		
		$queryGenerator->setFields( $headerColumns );

		$listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
		
		$fieldModelList = $moduleModel->getFields();
		
		if(isset($fieldModelList['mailingstate'])){
			
			$fieldModel = $fieldModelList['mailingstate'];
			
			if($fieldModel->getPermissions())
				$queryGenerator->addCondition("mailingstate", "", "ny");
		}
		
		$query = $queryGenerator->getQuery();
		
		$whereFields = $queryGenerator->getWhereFields();
		
		if(!in_array("mailingstate", $whereFields))
			$query .= "AND (vtiger_contactaddress.mailingstate IS NOT NULL AND vtiger_contactaddress.mailingstate !=  '') ";
		
		$query = str_replace('SELECT', 'SELECT COUNT(vtiger_contactaddress.mailingstate) as state_count, ', $query);
		
		$query .= " GROUP BY vtiger_contactaddress.mailingstate";
		
		$result = $adb->pquery($query,array());
		
		$state_data = array();
		
		if($adb->num_rows($result)){
			
			while($row = $adb->fetchByAssoc($result)){
				
				$state_data[] = array(
					"id" => "US-".$row['mailingstate'],
					"value" => $row['state_count']
				);
			}
		}
		
		return $state_data;
	}
}
		