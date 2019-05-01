<?php

class PortfolioInformation_Top10RevenuePortfolioInformation_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		$viewer = $this->getViewer($request);
		
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$headerFields = array('contact_link' => "Name", 'account_number' => "Account No", 'annual_management_fee' => "Revenue");
		
		$fieldModelList = $moduleModel->getFields();
		
		foreach($headerFields as $fieldName => $fieldLabel){
			
			$fieldModel = $fieldModelList[$fieldName];
		
			if(!$fieldModel->getPermissions())
				unset($headerFields[$fieldName]);
			else {
				$fieldModel->set("field_label", $fieldLabel);
				$headerFields[$fieldName] = $fieldModel;
			}
		}
		
		if(!empty($headerFields)){
		
			$data = $moduleModel->getTop10RevenuePortfolios(array_keys($headerFields));
		
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
			$viewer->view('dashboards/TopPortfolioInformationContent.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/TopPortfolioInformation.tpl', $moduleName);
		}
	}
	
}
