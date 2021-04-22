<?php
require_once "vtlib/Vtiger/Net/Client.php";

class Instances_ManageInstancePermissions_View extends Vtiger_Index_View {
    
    public function process (Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $parentId = $request->get('record');
        
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
        
        $httpc = new Vtiger_Net_Client($domain);
        
		$element = array();
        $element['mode'] = "get_permissions";
		
        $single_params = array(
            "operation" => 'manageinstancepermissions',
            "element" => json_encode($element)
        );
        
        $single_response = $httpc->doPost($single_params);
        
        $single_result = json_decode($single_response,true);
        
		
        $viewer = $this->getViewer($request);
		
		$viewer->assign("PORTFOLIO_REPORTS",  $single_result['result']['portfolio_reports']);
		
        $viewer->assign('MODULE', $moduleName);
        
        $viewer->assign('RECORD', $parentId);
        
        $viewer->view('ManageInstancePermissions.tpl', $moduleName);
        
    }
    
}