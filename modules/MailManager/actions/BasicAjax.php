<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MailManager_BasicAjax_Action extends Vtiger_Action_Controller {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('getMailRecordDetails');
    } 
    
	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
	    
	    $mode = $request->getMode();
	    if (!empty($mode)) {
	        echo $this->invokeExposedMethod($mode, $request);
	        return;
	    }
	    
		$searchValue = $request->get('search_value');
		
        $moduleModel = Vtiger_Module_Model::getInstance('Emails');
        
        $emailsResult = array();
        if ($searchValue) {
            $emailsResult = $moduleModel->searchEmails($searchValue);
        }
        
        $result = array();
        foreach($emailsResult as $moduleName=>$recordModels) {
            foreach($recordModels as $record => $recordModel) {
                foreach($recordModel as $resultRecord){
                    $result[] = array('label'=>strip_tags($resultRecord['label']), 'value'=>$resultRecord['name'], 'id'=>$record);
                }
            }
        }
        
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	public function getMailRecordDetails(Vtiger_Request $request){
	    
	    $response = new Vtiger_Response();
	    $mailBoxModel = MailManager_Mailbox_Model::activeInstance($request->get('account_id'));
	    
	    $data['id'] = $mailBoxModel->mId;
	    $data['type'] = $mailBoxModel->server();
	    
	    if(!empty($mailBoxModel->mId)){
	        $response->setResult(array('success'=>true, 'data'=>array_map('decode_html',$data)));
	    } else {
	        $response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
	    }
	    $response->emit();
	    
	}
	
}
