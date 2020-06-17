<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MenuEditor_Module_Model extends Settings_Vtiger_Module_Model {

	var $name = 'MenuEditor';

	/**
	 * Function to save the menu structure
	 */
	public function saveMenuStruncture() {
		$db = PearDatabase::getInstance();
		$selectedModulesList = $this->get('selectedModulesList');

		$updateQuery = "UPDATE vtiger_tab SET tabsequence = CASE tabid ";

		foreach ($selectedModulesList as $sequence => $tabId) {
			$updateQuery .= " WHEN $tabId THEN $sequence ";
		}
		$updateQuery .= "ELSE -1 END";

		$db->pquery($updateQuery, array());
	}

	/**
	 * Function to get all the modules which are hidden for an app
	 * @param <string> $appName
	 * @return <array> $modules
	 */
	public static function getHiddenModulesForApp($appName) {
		$db = PearDatabase::getInstance();
		$modules = array();
		$menu = $db->pquery("SELECT menu_id FROM vtiger_menu WHERE menuname = ?",array($appName));
		if($db->num_rows($menu)){
		    $menuId = $db->query_result($menu, 0, 'menu_id');
    		$result = $db->pquery('SELECT tabid FROM vtiger_tab 
            WHERE vtiger_tab.tabid NOT IN 
            (SELECT vtiger_menu_tab.tabid FROM vtiger_menu_tab WHERE menuid = ?)', array($menuId));
    		$count = $db->num_rows($result);
    		if ($count > 0) {
    			for ($i = 0; $i < $count; $i++) {
    				$tabid = $db->query_result($result, $i, 'tabid');
    				$moduleName = getTabModuleName($tabid);
    				$ignoredModules = Vtiger_MenuStructure_Model::getIgnoredModules();
    				if (!in_array($moduleName, $ignoredModules)) {
        				$moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        				if ($moduleInstance->isActive() && $moduleInstance->isEntityModule()) {
        					$modules[$moduleName] = $moduleName;
        				}
    				}
    			}
    		}
    		
    		$quotingQuery = $db->pquery("SELECT vtiger_menu_tab.tabid FROM vtiger_menu_tab 
            WHERE menuid = ? AND tabid = ?",array($menuId,getTabid('QuotingTool')));
    		
    		if(!$db->num_rows($quotingQuery)){
    		    $quotingModuleInstance = Vtiger_Module_Model::getInstance('QuotingTool');
    		    if ($quotingModuleInstance->isActive()) {
    		        $modules['QuotingTool'] = 'QuotingTool';
    		    }
    		}
		}

		return $modules;
	}

	public static function getAllVisibleModules() {
		$modules = array();
		$presence = array('0', '2');
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_menu.menuname as appname, vtiger_menu_tab.sequence as sequence, 
        vtiger_menu_tab.tabid as tabid FROM vtiger_menu
        INNER JOIN vtiger_menu_tab ON vtiger_menu_tab.menuid = vtiger_menu.menu_id
        ORDER BY vtiger_menu.menu_id, vtiger_menu_tab.sequence', array());
		$count = $db->num_rows($result);
		$userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$tabid = $db->query_result($result, $i, 'tabid');
				$moduleName = getTabModuleName($tabid);
				$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
				if (empty($moduleModel)) {
					continue;
				}

				$sequence = $db->query_result($result, $i, 'sequence');
				$appname = $db->query_result($result, $i, 'appname');
				$moduleModel->set('app2tab_sequence', $sequence);
				if (($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
					$modules[$appname][$moduleName] = $moduleModel;
				}
			}
		}

		return $modules;
	}

	public static function addModuleToApp($moduleName, $parent) {
		if (empty($moduleName) || empty($parent)) return;

		$db = PearDatabase::getInstance();
		$parent = strtoupper($parent);
		$oldToNewAppMapping = Vtiger_MenuStructure_Model::getOldToNewAppMapping();
		if (!empty($oldToNewAppMapping[$parent])) {
			$parent = $oldToNewAppMapping[$parent];
		}

		$ignoredModules = Vtiger_MenuStructure_Model::getIgnoredModules();
		if (!in_array($moduleName, $ignoredModules)) {
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
			$result = $db->pquery('SELECT * FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array($moduleModel->getId(), $parent));

			$sequence = self::getMaxSequenceForApp($parent) + 1;
			if ($db->num_rows($result) == 0) {
				$db->pquery('INSERT INTO vtiger_app2tab(tabid,appname,sequence) VALUES(?,?,?)', array($moduleModel->getId(), $parent, $sequence));
			}
		}
	}

	public static function updateModuleApp($moduleName, $parent, $oldParent = false) {
		$db = PearDatabase::getInstance();

		$parent = strtoupper($parent);
		$oldToNewAppMapping = Vtiger_MenuStructure_Model::getOldToNewAppMapping();
		if (!empty($oldToNewAppMapping[$parent])) {
			$parent = $oldToNewAppMapping[$parent];
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$query = "UPDATE vtiger_app2tab SET appname=? WHERE tabid=?";
		$params = array($parent, $moduleModel->getId());
		if ($oldParent) {
			$query .= ' AND appname=?';
			array_push($params, strtoupper($oldParent));
		}
		$db->pquery($query, $params);
	}

	/**
	 * Function to get the max sequence number for an app
	 * @param <string> $appName
	 * @return <integer>
	 */
	public static function getMaxSequenceForApp($appName) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT MAX(sequence) AS maxsequence FROM vtiger_app2tab WHERE appname=?', array($appName));
		$sequence = 0;
		if ($db->num_rows($result) > 0) {
			$sequence = $db->query_result($result, 0, 'maxsequence');
		}

		return $sequence;
	}

}
