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

                $recordModel = Vtiger_Record_Model::getInstanceById( $recordId);

                $creatorId = $recordModel->get('creator');

                $ownerId = $recordModel->get('assigned_user_id');

                $financialAdvisor = $recordModel->get('financial_advisor');

                if(($creatorId == $current_user->id || $financialAdvisor == $current_user->id) && $ownerId != $current_user->id)
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
	
	function getRollupCommentsForModule($startIndex = 0, $pageLimit = 10) {
	    $rollupComments = array();
	    $modulename = $this->getModuleName();
	    $recordId = $this->getId();
	    $db = PearDatabase::getInstance();
	    
	    $relatedModuleRecordIds = $this->getCommentEnabledRelatedEntityIds($modulename, $recordId);
	    if($modulename == 'Accounts' || $modulename == 'Contacts'){
	        if($modulename == 'Accounts'){
	            $account = CRMEntity::getInstance($modulename);
	            $contacts = $account->getRelatedContactsIds($recordId);
	            array_push($contacts, $recordId);
	            $ticketId = $account->getRelatedTicketIds($contacts);
	            $relatedModuleRecordIds = array_merge($relatedModuleRecordIds,$ticketId);
	        }
	        $portfolioIds = $this->getRelatedPortfolioIds($modulename,$recordId);
	        $relatedModuleRecordIds = array_merge($relatedModuleRecordIds,$portfolioIds);
	    }
	    array_unshift($relatedModuleRecordIds, $recordId);
	    
	    if ($relatedModuleRecordIds) {
	        
	        $listView = Vtiger_ListView_Model::getInstance('ModComments');
	        $queryGenerator = $listView->get('query_generator');
	        $queryGenerator->setFields(array('parent_comments', 'createdtime', 'modifiedtime', 'related_to', 'assigned_user_id',
	            'commentcontent', 'creator', 'id', 'customer', 'reasontoedit', 'userid', 'from_mailconverter', 'is_private', 'customer_email'));
	        
	        $query = $queryGenerator->getQuery();
	        
	        $query .= " AND vtiger_modcomments.related_to IN (" . generateQuestionMarks($relatedModuleRecordIds)
	        . ") AND vtiger_modcomments.parent_comments=0 ORDER BY vtiger_crmentity.createdtime DESC LIMIT "
	            . " $startIndex,$pageLimit";
	            
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            if($recordModel->getModuleName() == 'HelpDesk'){
                
                global $current_user;
                $tabId = getTabid('ModComments');
                $creatorId = $recordModel->get('creator');
                $ownerId = $recordModel->get('assigned_user_id');
                $financialAdvisor = $recordModel->get('financial_advisor');
                
                $permission_result = $db->pquery("select * from vtiger_ticket_view_permission where ticketid = ?",array($parentRecordId));
                $viewUsers = array();
                if($db->num_rows($permission_result)){
                    for($h=0;$h<$db->num_rows($permission_result);$h++){
                        $viewUsers[] = $db->query_result($permission_result, $h, 'view_permission_id');
                    }
                }
                
                
                if( $creatorId == $current_user->id || $financialAdvisor == $current_user->id || $ownerId == $current_user->id || in_array($current_user->id, $viewUsers)){
                    $tableName = 'vt_tmp_u' . $current_user->id . '_t' . $tabId;
                    if(strpos($query,$tableName) !== FALSE){
                        $tableName = $tableName;
                    }else{
                        $tableName = 'vt_tmp_u' . $current_user->id;
                    }
                    $db->pquery("delete from $tableName");
                    $db->pquery("insert into $tableName select id from vtiger_users");
                }
            }
	            
            
            $result = $db->pquery($query, $relatedModuleRecordIds);
            if ($db->num_fields($result)) {
                for ($i = 0; $i < $db->num_rows($result); $i++) {
                    $rowdata = $db->query_result_rowdata($result, $i);
                    $recordInstance = new ModComments_Record_Model();
                    $rowdata['module'] = getSalesEntityType($rowdata['related_to']);
                    $recordInstance->setData($rowdata);
                    $rollupComments[] = $recordInstance;
                }
            }
	    }
	    
	    return $rollupComments;
	}
	
}