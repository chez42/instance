<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_TabColumnView_Module_Model extends Vtiger_Module_Model {

	public static $supportedModules = false;

	/**
	 * Function returns all the blocks for the module
	 * @return <Array of Vtiger_Block_Model> - list of block models
	 */
	public function getBlocks() {
		if(empty($this->blocks)) {
			$blocksList = array();
			$moduleBlocks = Settings_LayoutEditor_Block_Model::getAllForModule($this);
			foreach($moduleBlocks as $block){
				if(!$block->get('label')) {
					continue;
				}
				if($this->getName() == 'HelpDesk' && $block->get('label') == 'LBL_COMMENTS'){
					continue;
				}

				if($block->get('label') != 'LBL_RELATED_PRODUCTS') {
					$blocksList[$block->get('label')] = $block;
				}
			}
			//To handle special case for invite users block
			if($this->getName() == 'Events') {
				$blockModel = new Settings_LayoutEditor_Block_Model();
				$blockModel->set('id','EVENT_INVITE_USER_BLOCK_ID');
				$blockModel->set('label','LBL_INVITE_USER_BLOCK');
				$blockModel->set('module', $this);
				$blocksList['LBL_INVITE_USER_BLOCK'] = $blockModel;
			}
			$this->blocks = $blocksList;
		}
		return $this->blocks;
	}




	public static function getSupportedModules() {
		if(empty(self::$supportedModules)) {
		   self::$supportedModules = self::getEntityModulesList();
		}
		return self::$supportedModules;
	}


	public static function getInstanceByName($moduleName) {
		$moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
		$objectProperties = get_object_vars($moduleInstance);
		$selfInstance = new self();
		foreach($objectProperties as $properName=>$propertyValue){
			$selfInstance->$properName = $propertyValue;
		}
		return $selfInstance;
	}

	/**
	 * Function to get Entity module names list
	 * @return <Array> List of Entity modules
	 */
	public static function getEntityModulesList() {
		$db = PearDatabase::getInstance();
		self::preModuleInitialize2();

		$presence = array(0, 2);
		$restrictedModules = array('Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter');

		$query = 'SELECT name FROM vtiger_tab WHERE
						presence IN ('. generateQuestionMarks($presence) .')
						AND isentitytype = ?
						AND name NOT IN ('. generateQuestionMarks($restrictedModules) .')';
		$result = $db->pquery($query, array($presence, 1, $restrictedModules));
		$numOfRows = $db->num_rows($result);

		$modulesList = array();
		for($i=0; $i<$numOfRows; $i++) {
			$moduleName = $db->query_result($result, $i, 'name');
			$modulesList[$moduleName] = vtranslate($moduleName, $moduleName);
			//Calendar needs to be shown as TODO so we are translating using Layout editor specific translations
			if ($moduleName == 'Calendar') {
				$modulesList[$moduleName] = vtranslate($moduleName, 'Settings:LayoutEditor');
			}
		}
		// If calendar is disabled we should not show events module too
		// in layout editor
		if(!array_key_exists('Calendar', $modulesList)) {
			unset($modulesList['Events']);
		}
		return $modulesList;
	}

	/**
	 * Function to check field is editable or not
	 * @return <Boolean> true/false
	 */
	public function isSortableAllowed() {
		$moduleName = $this->getName();
		if (in_array($moduleName, array('Calendar', 'Events'))) {
			return false;
		}
		return true;
	}

	/**
	 * Function to check blocks are sortable for the module
	 * @return <Boolean> true/false
	 */
	public function isBlockSortableAllowed($blockName) {
		$moduleName = $this->getName();
		if (in_array($moduleName, array('Calendar', 'Events'))) {
			return false;
		}

		if (($blockName === 'LBL_INVITE_USER_BLOCK')
				|| (in_array($moduleName, getInventoryModules()) && $blockName === 'LBL_ITEM_DETAILS')) {
			return false;
		}
		return true;
	}

	
	public function checkTabBlockAndFields($tabName){
	    
	    $moduleName = $this->getName();
	    $db = PearDatabase::getInstance();
	    
	    $block = $db->pquery("SELECT * FROM vtiger_module_tab
 		INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab.id = vtiger_module_tab_blocks.tabid
        INNER JOIN vtiger_field ON vtiger_field.block = vtiger_module_tab_blocks.blockid
        WHERE vtiger_module_tab.tab_name = ? AND vtiger_module_tab.module_name = ?",
	        array($tabName, $moduleName));
	    
	    if($db->num_rows($block)){
	        for($i=0;$i<$db->num_rows($block);$i++){
	            $blockId = $db->query_result($block,$i,'blockid');
	            $blocks =  Vtiger_Block_Model::getInstance($blockId,$this);
	            
	            $fields = $blocks->getFields();
	            foreach($fields as $fieldName=>$fieldModel) {
	                if($fieldModel->isViewable()) {
	                    return true;
	                }
	            }
	        }
	    }
	    
	    return false;
	}
	

}
