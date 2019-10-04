<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory ListView Model Class
 */
class Emails_ListView_Model extends Vtiger_ListView_Model {

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
		
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
				'linkicon' => ''
			);
		}

		
		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
		    $massActionLinks[] = array(
		        'linktype' => 'LISTVIEWMASSACTION',
		        'linklabel' => 'Resend Emails',
		        'linkurl' => 'javascript:Emails_List_Js.triggerResendEmail("index.php?module='.$moduleModel->get('name').'&view=MassSaveAjax&mode=resendEmails");',
		        'linkicon' => ''
		    );
		}

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}
	
	
	/*
	 * Function to give advance links of a module
	 *	@RETURN array of advanced links
	 */
	public function getAdvancedLinks(){
	    $moduleModel = $this->getModule();
	    $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'CreateView');
	    $advancedLinks = array();
	 
	    
	    return $advancedLinks;
	}
	
	
	function getQuery() {
	    $queryGenerator = $this->get('query_generator');
	    $listQuery = $queryGenerator->getQuery();
	    
	    $pos = strpos($listQuery, "SELECT");
	    if ($pos !== false) {
	        $listQuery = substr_replace($listQuery, "SELECT DISTINCT vtiger_activity.activityid, ", $pos, strlen("SELECT"));
	    }
	    
	    $listQuery = str_replace("vtiger_email_track.access_count","email_track.access_count",$listQuery);
	    $listQuery = str_replace("vtiger_email_track.click_count","email_track.click_count",$listQuery);
	    
	    $listQuery = str_replace("INNER JOIN vtiger_email_track ON vtiger_activity.activityid = vtiger_email_track.mailid",
	        'INNER JOIN (select sum(access_count) as access_count, sum(click_count) as click_count, mailid from vtiger_email_track
		group by mailid) as email_track on vtiger_crmentity.crmid = email_track.mailid',$listQuery);
	    
	    return $listQuery;
	}
	
}