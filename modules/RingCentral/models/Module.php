<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class RingCentral_Module_Model extends Vtiger_Module_Model {

    public function isSummaryViewSupported() {
        return false;
    }
    
	public function getSideBarLinks($linkParams) {
	    $links = array();
		return $links;
	}

	public function getModuleBasicLinks(){
	    $basicLinks = array();
		return $basicLinks;
	}
	
	function isStarredEnabled(){
	    return false;
	}
	
	public function isQuickPreviewEnabled(){
	    return false;
	}
	
	public function isExcelEditAllowed() {
	    return false;
	}
	
	/**
	 * Function returns Related Records
	 * @return Array
	 */
	public function getRelatedRecords($record_id) {
	    
	    global $adb;
	    
	    $relatedRecords = array();
	    
	    if($record_id){
	        
	        $relatedIds = $adb->pquery("SELECT vtiger_seringcentralrel.crmid FROM vtiger_seringcentralrel
            INNER JOIN vtiger_crmentity crm1 ON crm1.crmid = vtiger_seringcentralrel.crmid
            INNER JOIN vtiger_crmentity crm2 ON crm2.crmid = vtiger_seringcentralrel.ringcentralid
            WHERE crm1.deleted = 0 AND crm2.deleted = 0 AND vtiger_seringcentralrel.ringcentralid = ?",array($record_id));
	        
	        if($adb->num_rows($relatedIds)){
	            
	            for($r=0;$r<$adb->num_rows($relatedIds);$r++){
	                
	                $related_record = $adb->query_result($relatedIds,$r,'crmid');
	               
	                $recordPermission = Users_Privileges_Model::isPermitted(getSalesEntityType($related_record), 'DetailView', $related_record);
	               
                    if($recordPermission){
                        $recordInstance = Vtiger_Record_Model::getInstanceById($related_record);
                        $relatedRecords[] = "<a href='".$recordInstance->getDetailViewUrl()."' title='".$recordInstance->getModuleName()."'>".
                            $recordInstance->get('label')."</a>";
                    }
	            }
	            
	        }
	        
	    }
	    
	    return implode(',',$relatedRecords);
	}
	
	
	public function getRelatedNos($record_id) {
	    
	    global $adb;
	    
	    $relatedNo = array();
	    
	    if($record_id){
	        
	        $relatedIds = $adb->pquery("SELECT vtiger_seringcentralrel.to_number FROM vtiger_seringcentralrel
            INNER JOIN vtiger_crmentity crm1 ON crm1.crmid = vtiger_seringcentralrel.crmid
            INNER JOIN vtiger_crmentity crm2 ON crm2.crmid = vtiger_seringcentralrel.ringcentralid
            WHERE crm1.deleted = 0 AND crm2.deleted = 0 AND vtiger_seringcentralrel.ringcentralid = ?",array($record_id));
	        
	        if($adb->num_rows($relatedIds)){
	            
	            for($r=0;$r<$adb->num_rows($relatedIds);$r++){
	                $relatedNo[] = $adb->query_result($relatedIds,$r,'to_number');
	                
	            }
	            
	        }
	        
	    }
	    
	    return implode(',',$relatedNo);
	}
	

}
