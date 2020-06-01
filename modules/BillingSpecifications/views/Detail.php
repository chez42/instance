<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class BillingSpecifications_Detail_View extends Vtiger_Detail_View {

	function process(Vtiger_Request $request) {
	    $this->showScheduleBlocks($request); 
        parent::process($request);
	}

	public function showScheduleBlocks(Vtiger_Request $request){
	    
	    global $adb;
	    
	    $moduleName = $request->getModule();
	    
	    $record = $request->get("record");
	    
	    $final_details = array();
	    
	    if($record){
	        
	        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
	        
	        if($recordModel->get('billing_type') == 'Schedule'){
    	        $relatedcontacts = $recordModel->getRelatedScheduleDetails();
    	        
    	        if(!empty($relatedcontacts)){
    	            
    	            $index = 1;
    	            
    	            foreach($relatedcontacts as $contact_detail){
    	                
    	                $final_details[$index] = array(
    	                    'scheduleid'.$index => $contact_detail['rangeid'],
    	                    'from'.$index => $contact_detail['from'],
    	                    'to'.$index => $contact_detail['to'],
    	                    'type'.$index => $contact_detail['type'],
    	                    'value'.$index => $contact_detail['value'],
    	                );
    	                
    	                $index++;
    	            }
    	        }
    	        
    	    }
    	    
    	    $viewer = $this->getViewer($request);
    	    $viewer->assign('RELATED_SCHEDULE', $final_details);
	    }
	}
	
}
