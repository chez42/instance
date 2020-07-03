<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $linkTypes, $linkParams);

		$basicLinks = $this->getBasicLinks();
		foreach($basicLinks as $basicLink) {
			$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
		}
        
        $links['LISTVIEW'] = array();
        $advancedLinks = $this->getAdvancedLinks();
		foreach($advancedLinks as $advancedLink) {
			$links['LISTVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($advancedLink);
		}
        
        $usersList = Users_Record_Model::getActiveAdminUsers();
        $settingLinks = array();
        if(count($usersList) ) {
            $changeOwnerLink = array(
                'linktype' => 'LISTVIEWSETTING',
				'linklabel' => 'LBL_CHANGE_OWNER',
				'linkurl' => $this->getModule()->getChangeOwnerUrl(),
				'linkicon' => ''
            );
            array_push($settingLinks, $changeOwnerLink);
        }

		$settingLinks = array_merge($settingLinks, $this->getSettingLinks());
        if(count($settingLinks) > 0) {
            foreach($settingLinks as $settingLink) {
                $links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingLink);
            }
        }

		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
	    
	    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    $moduleModel = $this->getModule();
	    
	    $linkTypes = array('LISTVIEWMASSACTION');
	    $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
	    
	    
	    $massActionLinks = array();
	    if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
	        $massActionLinks[] = array(
	            'linktype' => 'LISTVIEWMASSACTION',
	            'linklabel' => 'LBL_EDIT',
	            'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
	            'linkicon' => ''
	        );
	    }
	    
	    if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
	        $massActionLinks[] = array(
	            'linktype' => 'LISTVIEWMASSACTION',
	            'linklabel' => 'LBL_DELETE',
	            'linkurl' => 'javascript:Settings_Users_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&view=DeleteAjax&mode=MassDeleteUsers");',
	            'linkicon' => ''
	        );
	    }
	    foreach($massActionLinks as $massActionLink) {
	        $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
	    }
	    
	    return $links;
	    
	}

	/**
	 * Functions returns the query
	 * @return string
	 */
    public function getQuery() {
        
        $listQuery = parent::getQuery();
        
        if(Users_CustomView_Model::getNameBycvId($this->get('CVID')) == 'LBL_INACTIVE_USERS'){
            $listQueryComponents = explode(" WHERE vtiger_users.status='Active' AND", $listQuery);
        }else{
            $listQueryComponents = explode(" WHERE ", $listQuery);
        }
        $listQuery = implode(' WHERE ', $listQueryComponents);
        
/*14-Sep-2018*/
//         $listQuery = str_replace("vtiger_role.rolename", "vtiger_role.roleid", $listQuery);
//         $listQuery = str_replace("vtiger_users.is_admin = '1'", "vtiger_users.is_admin = 'on'", $listQuery);
        //$listQuery .= " AND (vtiger_users.user_name != 'admin' OR vtiger_users.is_owner = 1)";
        
        return $listQuery;
    }

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel, $status (Active or Inactive User). Default false
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		$queryGenerator = $this->get('query_generator');
                
		// Added as Users module do not have custom filters and id column is added by querygenerator.
		$fields = $queryGenerator->getFields();
		$fields[] = 'id';
		
		if(!in_array("status", $fields))
		    $fields[] = 'status';
	    if(!in_array("deleted", $fields))
	        $fields[] = 'deleted';
        
		$queryGenerator->setFields($fields);
		
		return parent::getListViewEntries($pagingModel);
	}
        
    /*
	 * Function to give advance links of Users module
	 * @return array of advanced links
	 */
	public function getAdvancedLinks(){
		$moduleModel = $this->getModule();
		$createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
		$advancedLinks = array();
		
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');
		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
		    $advancedLinks[] = array(
		        'linktype' => 'LISTVIEW',
		        'linklabel' => 'LBL_SEND_EMAIL',
		        'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
		        'linkicon' => ''
		    );
		}
		
		$importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
		if($importPermission && $createPermission) {
			$advancedLinks[] = array(
                'linktype' => 'LISTVIEW',
                'linklabel' => 'LBL_IMPORT',
                'linkurl' => $moduleModel->getImportUrl(),
                'linkicon' => ''
			);
			
		}
		
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		if ($currentUserModel->isAdminUser() && Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DetailView')) {
    		
		    $advancedLinks[] = array(
    		    'linktype' => 'LISTVIEW',
    		    'linklabel' => 'LBL_EXPORT',
    		    'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$this->getModule()->getExportUrl().'")',
    		    'linkicon' => ''
    		);
    		
		}
		
		if(Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Save') && $currentUserModel->isAdminUser()) {
		    
		    $advancedLinks[] = array(
		        'linktype' => 'LISTVIEW',
		        'linklabel' => 'LBL_REACTIVATE_USERS',
		        'linkurl' => 'javascript:Settings_Users_List_Js.triggerMassAction("index.php?module='.$moduleModel->get('name').'&action=MassSave&mode=changeStatus&status=Active");',
		        'linkicon' => ''
		    );
		    
		    $advancedLinks[] = array(
		        'linktype' => 'LISTVIEW',
		        'linklabel' => 'LBL_INACTIVATE_USERS',
		        'linkurl' => 'javascript:Settings_Users_List_Js.triggerMassAction("index.php?module='.$moduleModel->get('name').'&action=MassSave&mode=changeStatus&status=Inactive");',
		        'linkicon' => ''
		    );
		}
		return $advancedLinks;
	}
}
