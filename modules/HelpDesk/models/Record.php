<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to get the Display Name for the record
	 * @return <String> - Entity Display Name for the record
	 */
	public function getDisplayName() {
		return Vtiger_Util_Helper::getRecordName($this->getId());
	}

	/**
	 * Function to get URL for Convert FAQ
	 * @return <String>
	 */
	public function getConvertFAQUrl() {
		return "index.php?module=".$this->getModuleName()."&action=ConvertFAQ&record=".$this->getId();
	}

	/**
	 * Function to get Comments List of this Record
	 * @return <String>
	 */
	public function getCommentsList() {
		$db = PearDatabase::getInstance();
		$commentsList = array();

		$result = $db->pquery("SELECT commentcontent AS comments FROM vtiger_modcomments WHERE related_to = ?", array($this->getId()));
		$numOfRows = $db->num_rows($result);

		for ($i=0; $i<$numOfRows; $i++) {
			array_push($commentsList, $db->query_result($result, $i, 'comments'));
		}

		return $commentsList;
	}
	
	public function checkPermission($view='',$record=''){
	    
	    
	    $current_user = Users_Record_Model::getCurrentUserModel();
	    $moduleName = 'HelpDesk';
	    $recordId = $record;
	    
	    if($view == 'Edit'){
	       
    	    $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId);
    	    
    	    if(!$recordPermission) {
    	        
    	        $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);
    	        
    	        $creatorId = $recordModel->get('creator');
    	        
    	        $ownerId = $recordModel->get('assigned_user_id');
    	        
    	        $financialAdvisor = $recordModel->get('financial_advisor');
    	        
    	        if(($creatorId == $current_user->id || $financialAdvisor == $current_user->id) && $ownerId != $current_user->id)
    	            return true;
    	            
    	    }else if($recordPermission){
    	        
    	        return true;
    	        
    	    }
        
    	    
	    }elseif($view == 'Detail'){
	        
	        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
	        
	        if(!$recordPermission) {
	            
	            if(HelpDesk_Sharing_Model::IsUserPartOfSharingPermission($recordId))
	                return true;
	                
                if(HelpDesk_Sharing_Model::DoesTicketBelongToUsersGroup($recordId))
                    return true;
	                    
            }else if($recordPermission){
                return true;
            }
	        
	    }elseif($view == 'Save' || $view == 'SaveAjax'){
	        
	        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'Save', $recordId);
	        
	        if(!$recordPermission) {
	            
	            $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);
	            
	            $creatorId = $recordModel->get('creator');
	            
	            $ownerId = $recordModel->get('assigned_user_id');
	            
	            $financialAdvisor = $recordModel->get('financial_advisor');
	            
	            if(($creatorId == $current_user->id || $financialAdvisor == $current_user->id)&& $ownerId != $current_user->id)
	                return true;
	                
	        }else if($recordPermission) {
	            return true;
	        }
	    }
	}
	
}