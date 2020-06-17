<?php

class Settings_GlobalSearch_Save_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
    	$selectedModule = $request->get('modulename');
        $fieldList = $request->get('fieldnames');
    	
        if(is_array($fieldList)){
        	$fieldList = implode(',',$fieldList);
        }
    	
        $result = array();
        
    	if($selectedModule != ''){
	    	$record = $request->get('record');
	    	
	        if(empty($record)) {
				$recordModel = Settings_GlobalSearch_Record_Model::getInstance($request->get('modulename'));
	            if(empty($recordModel)) {
					$recordModel = new Settings_GlobalSearch_Record_Model();
				}
			} else {
	            $recordModel = Settings_GlobalSearch_Record_Model::getInstance($record);
	        }
        
        	$recordModel->set('modulename',$request->get('modulename'));
        	if($request->get('allow_global_search')){
        		$recordModel->set('allow_global_search','1');
        	} else {
        		$recordModel->set('allow_global_search','0');        	
        	}
    		$recordModel->set('fieldnames',$fieldList);
    	
    		$id = $recordModel->save();
            $recordModel = Settings_GlobalSearch_Record_Model::getInstance($id);
            $result = array_merge($recordModel->getData(),array('record'=> $recordModel->getId()));
        }
    	
    	$response = new Vtiger_Response();
        try{
            
            $response->setResult($result);
        }catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
}