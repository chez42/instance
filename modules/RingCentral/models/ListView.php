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
 * Vtiger ListView Model Class
 */
class RingCentral_ListView_Model extends Vtiger_ListView_Model {

    public function getSideBarLinks($linkParams) {
        $moduleLinks = array();
		return $moduleLinks;
	}

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
	    $links = array();
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
	    $advancedLinks = array();
		return $advancedLinks;
	}

	/*
	 * Function to get Setting links
	 * @return array of setting links
	 */
	public function getSettingLinks() {
		return $this->getModule()->getSettingLinks();
	}

	/*
	 * Function to get Basic links
	 * @return array of Basic links
	 */
	public function getBasicLinks(){
		$basicLinks = array();
		return $basicLinks;
	}



	public function isImportEnabled() {
		return false;
	}
	
}
