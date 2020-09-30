<?php
require_once "vtlib/Vtiger/Net/Client.php";

class Instances_ModulesList_View extends Vtiger_Index_View {
    
    public function process(Vtiger_Request $request) {
        
        $moduleName = $request->getModule();
        
        $parentId = $request->get('record');
        
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId);
        
        $domain = $parentRecordModel->get('domain').'/webservice.php';
       
        $httpc = new Vtiger_Net_Client($domain);
        
        $element = array('mode' => 'modulesList');
        
        $single_params = array(
			"operation" => 'managemodules',
			"element" => json_encode($element)
		);
        
        $single_response = $httpc->doPost($single_params);

        $single_result = json_decode($single_response,true);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        
        $viewer->assign('INSMODULES', $single_result['result']['moduleList']);
        $viewer->assign('ACTIVEMODULES', $single_result['result']['activeModules']);
        $viewer->assign('ALLMODULES', $single_result['result']['allModules']);
        
        $viewer->view('ModulesList.tpl', $moduleName);
    }
    
}