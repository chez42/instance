<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Group_Detail_View extends Vtiger_Detail_View {

	function process(Vtiger_Request $request) {
	    $this->showItemBlocks($request); 
        parent::process($request);
	}

	public function showItemBlocks(Vtiger_Request $request){
	    
	    global $adb;
	    
	    $moduleName = $request->getModule();
	    
	    $record = $request->get("record");
	    
	    $final_details = array();
	    
	    if($record){
	        
	        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
	        
            $relatedItems = $recordModel->getRelatedItemsDetails();
	        
            if(!empty($relatedItems)){
	            
	            $index = 1;
	            
	            foreach($relatedItems as $item_detail){
	                $portDetailUrl = '';
	                $billSpecDetailUrl = '';
	                if($item_detail['portfolioid']){
	                   $portfolioModel = Vtiger_Record_Model::getInstanceById($item_detail['portfolioid']);
	                   $portDetailUrl = $portfolioModel->getDetailViewUrl();
	                }
                   if($item_detail['billingspecificationid']){
	                   $billingSpecificationModel = Vtiger_Record_Model::getInstanceById($item_detail['billingspecificationid']);
	                   $billSpecDetailUrl = $billingSpecificationModel->getDetailViewUrl();
                   }
	                $final_details[$index] = array(
	                    'itemid'.$index => $item_detail['itemid'],
	                    'portfolioid'.$index => $item_detail['portfolioid'],
	                    'portfolioname'.$index => $portDetailUrl ?"<a href='".$portDetailUrl."'>".$item_detail['portfolioname']."</a>" : $portDetailUrl,
	                    'billingspecificationid'.$index => $item_detail['billingspecificationid'],
	                    'billingspecificationname'.$index => $billSpecDetailUrl ? "<a href='".$billSpecDetailUrl."'>".$item_detail['billingspecificationname']."</a>" : $billSpecDetailUrl,
	                    'active'.$index => $item_detail['active']?'Yes':'No',
	                );
	                
	                $index++;
	            }
	        }
    	        
    	    
    	    $viewer = $this->getViewer($request);
    	    $viewer->assign('RELATED_ITEMS', $final_details);
	    }
	}
	
}
