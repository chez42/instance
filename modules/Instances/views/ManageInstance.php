<?php

require_once "vtlib/Vtiger/Net/Client.php";

class Instances_ManageInstance_View extends Vtiger_Index_View {
    
    public function process (Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $parentId = $request->get('record');
        
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
        
        $httpc = new Vtiger_Net_Client($domain);
        
        
        $element = array();
        
        $single_params = array(
            "operation" => 'manageinstanceusersrepcode',
            "element" => json_encode($element)
        );
        
        $single_response = $httpc->doPost($single_params);
        
        $single_result = json_decode($single_response,true);
        
        $usersResult = $single_result['result'];
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        
        $viewer->assign('RECORD', $parentId);
        
        $viewer->assign('INSTANCEUSERS', $usersResult);
        
        $viewer->view('InstanceUsers.tpl', $moduleName);
        
    }
    
  
    
    
}