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
 * Vtiger Menu Model Class
 */
class Vtiger_Menu_Model extends Vtiger_Module_Model {

	/**
	 * Static Function to get all the accessible menu models with/without ordering them by sequence
	 * @param <Boolean> $sequenced - true/false
	 * @return <Array> - List of Vtiger_Menu_Model instances
	 */
	public static function getAll($sequenced = false) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$restrictedModulesList = array('Emails', 'ProjectMilestone', 'ProjectTask', 'ModComments', 'Integration', 'PBXManager', 'Dashboard', 'Home');

		$allModules = parent::getAll(array('0','2'));
		$menuModels = array();
		$moduleSeqs = Array();
		$moduleNonSeqs = Array();
		foreach($allModules as $module){
			if($module->get('tabsequence') != -1){
				$moduleSeqs[$module->get('tabsequence')] = $module;
			}else {
				$moduleNonSeqs[] = $module;
			}
		}
		ksort($moduleSeqs);
		$modules = array_merge($moduleSeqs, $moduleNonSeqs);

		foreach($modules as $module) {
			if (($userPrivModel->isAdminUser() ||
					$userPrivModel->hasGlobalReadPermission() ||
					$userPrivModel->hasModulePermission($module->getId()))& !in_array($module->getName(), $restrictedModulesList) && $module->get('parent') != '') {
					$menuModels[$module->getName()] = $module;

			}
		}

		return $menuModels;
	}

	/**
	 * Static Function to get all the accessible module model for Quick Create
	 * @return <Array> - List of Vtiger_Menu_Model instances
	 */
	public static function getAllForQuickCreate() {
	    global $adb;
		$userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		
        $restrictedModulesList = array('Emails', 'ModComments', 'Integration', 'PBXManager', 'Dashboard', 'Home', 'Google', 'Transactions', 'DocumentFolder', 'PositionInformation', 'PortfolioInformation', 'ModSecurities', 'Reports', 'RingCentral');
		
		$menuModels = array();
		$result = $adb->pquery("SELECT * FROM vtiger_tab WHERE presence IN ('0','2') 
            AND isentitytype = 1 AND (quick_create_seq IS NOT NULL || quick_create_seq != '')
            ORDER by quick_create_seq ASC",array());
		
		$noOfModules = $adb->num_rows($result);
		for($i=0; $i<$noOfModules; ++$i) {
		    $row = $adb->query_result_rowdata($result, $i);
		    $allModules[$row['tabid']] = parent::getInstanceFromArray($row);
		}
		
		foreach ($allModules as $module) {
		    
			if (($userPrivModel->isAdminUser() || $userPrivModel->hasGlobalReadPermission() || $userPrivModel->hasModulePermission($module->getId())) && !in_array($module->getName(), $restrictedModulesList)) {
		        $menuModels[$module->getName()] = $module;
			}
		}
		
		if(empty($menuModels)){
		    
		    $allModules = parent::getAll(array('0', '2'));
		    $menuModels = array();
		    $sortMenuModels = array();
		    
		    foreach ($allModules as $module) {
		        if($module->isQuickCreateSupported()){
    		        if (($userPrivModel->isAdminUser() || $userPrivModel->hasGlobalReadPermission() || $userPrivModel->hasModulePermission($module->getId())) && !in_array($module->getName(), $restrictedModulesList) && $module->get('parent') != '') {
                        if($module->getName() == 'Campaigns' || $module->getName() == 'ProjectTask' || $module->getName() == 'ProjectMilestone'){
                            $sortMenuModels[$module->getName()] = $module;
                            continue;
                        }    
    		            $menuModels[$module->getName()] = $module;
    		        }
		        }
		    }
		    
		    uksort($menuModels, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
		    
		    $menuModels = array_merge($menuModels,$sortMenuModels);
		}
		
		return $menuModels;
	}

}
