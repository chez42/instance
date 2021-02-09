<?php
require_once "vtlib/Vtiger/Net/Client.php";

class Instances_SaveModulesList_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    function process(Vtiger_Request $request){
        
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        
        $activeModules = $request->get('activemodules');
		
        $allModules = $request->get('allmodules');
        
		$selectedModules = $request->get('select_modules');
        
        $disableModules = array_diff($activeModules, $selectedModules);
        
		$enableModules = array_diff($selectedModules, $activeModules);
       
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($recordId);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
        
        $httpc = new Vtiger_Net_Client($domain);
        
        $element = array('mode'=>'enableDisableModule', 'enable' => $enableModules, 'disable' => $disableModules);
       
        $single_params = array( 
			"operation" => 'managemodules',
            "element" => json_encode($element)
		);
        
        $single_response = $httpc->doPost($single_params);

        $single_result = json_decode($single_response,true);
            
        $response = new Vtiger_Response();
        $response->setResult($single_result['result']);
        $response->emit();
        
    }
    
}
