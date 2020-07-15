<?php

require_once "vtlib/Vtiger/Net/Client.php";

class Instances_InstanceSave_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        global $current_user;
        
        $result = array();
        
        $record = $request->get('record');
        
        $users = $request->get('users');
        
        $custFieldValue = array();
        
        foreach ($users as $user){
            $custFieldValue[$user] = $request->get($user);
        }
        
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($record);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
        
        $httpc = new Vtiger_Net_Client($domain);
        
        $element = array('mode' => 'cust_update', 'cust_value' => $custFieldValue);
        
        $single_params = array(
            "operation" => 'manageinstanceusersrepcode',
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