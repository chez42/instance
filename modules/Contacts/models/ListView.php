<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$massActionLinks = parent::getListViewMassActions($linkParams);

		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_SEND_EMAIL',
				'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
				'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		$SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
		if($SMSNotifierModuleModel && $currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_SEND_SMS',
				'linkurl' => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
				'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
		
		$moduleModel = $this->getModule();
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
				'linkurl' => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=transferOwnership")',
				'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
			
			$massActionLink = array(
			    'linktype' => 'LISTVIEWMASSACTION',
			    'linklabel' => 'Update Portal Permissions',
			    'linkurl' => 'javascript:Contacts_List_Js.updatePortalPermissions("index.php?module='.$moduleModel->getName().'&view=UpdatePortalPermission")',
			    'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
		
		$massActionLink = array(
		    'linktype' => 'LISTVIEWMASSACTION',
		    'linklabel' => 'Generate Reports',
		    'linkurl' => 'javascript:Vtiger_List_Js.triggerReportPdf("index.php?module='.$this->getModule()->getName().'&view=ReportPdf&mode=showSelectReportForm");',
		    'linkicon' => ''
		);
		$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		

		return $massActionLinks;
	}

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	function getListViewLinks($linkParams) {
		$links = parent::getListViewLinks($linkParams);

		$index=0;
		foreach($links['LISTVIEWBASIC'] as $link) {
			if($link->linklabel == 'Send SMS') {
				unset($links['LISTVIEWBASIC'][$index]);
			}
			$index++;
		}
		return $links;
	}
	
	function getQuery() {

	    $query = parent::getQuery();
	    
	    if($this->get('listmode') == 'connection'){
	        
	        $contactRecord = Vtiger_Record_Model::getInstanceById($this->get('src_record'));
	        
	        $connections = $this->getConnections($this->get('src_record'));
	        
	        $query .= ' AND vtiger_contactdetails.accountid != "'.$contactRecord->get('account_id').'" ';
	        
	        if(!empty($connections))
	            $query .= ' AND vtiger_contactdetails.contactid NOT IN ('.implode(',',$connections).') ';
	        
	        
	            $query .= " AND vtiger_contactdetails.contactid != ".$this->get('src_record')." ";
	    }

        $query = str_replace(" AND vtiger_contactdetails.contactid > 0", " ", $query);
        return $query;
	}
	
	function getConnections($recordId){

	    global $adb;
	    
	    $connections = array();
	    
	    $connectionQuery = $adb->pquery("SELECT * FROM vtiger_connection 
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_connection.connectionid
        WHERE vtiger_crmentity.deleted = 0 AND (vtiger_connection.parent_contact_id = ? 
        OR vtiger_connection.child_contact_id = ?)",array($recordId,$recordId));
	    
	    if($adb->num_rows($connectionQuery)){
	        for($i=0;$i<$adb->num_rows($connectionQuery);$i++){
	            $connections[] = $adb->query_result($connectionQuery,$i,'child_contact_id');
	            $connections[] = $adb->query_result($connectionQuery,$i,'parent_contact_id');
	        }
	    }
	    
	    return $connections;
	}
	
}