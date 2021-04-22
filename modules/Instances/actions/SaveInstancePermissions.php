<?php

require_once "vtlib/Vtiger/Net/Client.php";

class Instances_SaveInstancePermissions_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        global $current_user;
        
        $result = array();
        
        $record = $request->get('record');
		
        if($request->get('portfolio_reports')){
			$portfolio_reports = 1;
        } else {
			$portfolio_reports = 0;
		}
        
		$parentRecordModel = Vtiger_Record_Model::getInstanceById($record);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
        
        $httpc = new Vtiger_Net_Client($domain);
        
        $element = array('mode' => 'save_permissions', 'portfolio_reports' => $portfolio_reports);
       
		
        $single_params = array(
            "operation" => 'manageinstancepermissions',
            "element" => json_encode($element)
        );
        
        $single_response = $httpc->doPost($single_params);
      
        $single_result = json_decode($single_response,true);
        
        $usersResult = $single_result['result'];
        
        if($usersResult['success']){
            $result = array('success' => true);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
    
}