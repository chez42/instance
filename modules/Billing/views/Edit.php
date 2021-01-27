<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Billing_Edit_View extends Vtiger_Edit_View {
    
    public function checkPermission(Vtiger_Request $request) {
        throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
    }

	public function process(Vtiger_Request $request) {
		
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
	        
	        $relatedFlows = $recordModel->getRelatedCaptialFlowsDetails();
	        
	        if(!empty($relatedFlows)){
	            
	            $index = 1;
	            $finalAdujstment = 0;
	            foreach($relatedFlows as $flow_detail){
	                
	                $final_details[$index] = array(
	                    'capitalflowsid'.$index => $flow_detail['capitalflowsid'],
	                    'trade_date'.$index => $flow_detail['trade_date'],
	                    'diff_days'.$index => $flow_detail['diff_days'],
	                    'totalamount'.$index => number_format($flow_detail['totalamount'], 2),
	                    'totaldays'.$index => $flow_detail['totaldays'],
	                    'transactionamount'.$index => number_format($flow_detail['transactionamount'], 2),
	                    'transactiontype'.$index => $flow_detail['transactiontype'],
	                    'trans_fee'.$index => number_format($flow_detail['trans_fee'], 2),
	                    'totaladjustment'.$index => number_format($flow_detail['totaladjustment'], 2),
	                );
	                
	                $index++;
	                $finalAdujstment += $flow_detail['transactionamount'] * $flow_detail['totalamount'];
	            }
	        }
	        
	    }
	    
	    $viewer = $this->getViewer($request);
	    $viewer->assign('RELATED_FLOWS', $final_details);
	    $viewer->assign('FINAL_ADJUSTMENT', $finalAdujstment);
	}
	
}
