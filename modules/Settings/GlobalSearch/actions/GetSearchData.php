<?php

class Settings_GlobalSearch_GetSearchData_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
    	$selectedModule = $request->get('selected_module');
        
    	$fieldList = array();
    	$alreadySavedData = array();
    	
    	if($selectedModule != ''){
    		
	        $recordModel = Vtiger_Record_Model::getCleanInstance($selectedModule);			
	        $allFieldList = $recordModel->getModule()->getFields();
			if( !empty($allFieldList) ){
				foreach($allFieldList as $fieldname => $fieldModel){
					$fieldList[$fieldname] = $fieldModel->get('label');
				}
			}
			
			$searchRecordModel = Settings_GlobalSearch_Record_Model::getInstance($selectedModule);
			if(!empty($searchRecordModel)){
				$recordData = $searchRecordModel->getData();
				$alreadySavedData['fieldnames'] = explode(',', $recordData['fieldnames']);
				$alreadySavedData['allow_global_search'] = $recordData['allow_global_search'];
			}
			
			
			
    	}
    	
    	$response = new Vtiger_Response();
        try{            
            $response->setResult( array('all_fields' => $fieldList, 'savedData' => $alreadySavedData) );
        }catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(Vtiger_Request $request) {
        //$request->validateWriteAccess();
    }
    
}