<?php

class Accounts_Top10AUMAccounts_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		$viewer = $this->getViewer($request);
		
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$fieldModelList = $moduleModel->getFields();
			
		$headerFields = array('accountname' => "Account", 'household_total' => "AUM");
		
		foreach($headerFields as $fieldName => $fieldLabel){
			
			$fieldModel = $fieldModelList[$fieldName];
		
			if(!$fieldModel->getPermissions())
				unset($headerFields[$fieldName]);
			else {
				$fieldModel->set("field_label", $fieldLabel);
				$headerFields[$fieldName] = $fieldModel;
			}
		}
		
		$pageLimit = $request->get('household_limit');
		
		if(!$pageLimit)
			$pageLimit = 5;
		
		if(!empty($headerFields)){
			
			$data = $moduleModel->getTopAUMAccounts(array_keys($headerFields), $pageLimit);
		
		} else {
			
			$viewer->assign('MESSAGE', "Not Accessible");
			
			$data = array();
		}
		
		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
        
		$viewer->assign('HEADERS', $headerFields);
        $viewer->assign('DATA', $data);
        
		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/TopHouseholdContent.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/TopHousehold.tpl', $moduleName);
		}
	}
	
}
