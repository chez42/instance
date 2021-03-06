<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Notifications_Module_Model extends Vtiger_Module_Model{

	/**
	 * Function to check whether the module is an entity type module or not
	 * @return <Boolean> true/false
	 */
	public function isQuickCreateSupported() {
		return false;
	}

	public function isPermitted($actionName) {
	    if($actionName == 'EditView')
	        return false;
	    return ($this->isActive() && Users_Privileges_Model::isPermitted($this->getName(), $actionName));
	}
	
	public function getModuleBasicLinks(){
	    if(!$this->isEntityModule() && $this->getName() !== 'Users') {
	        return array();
	    }
	    $createPermission = Users_Privileges_Model::isPermitted($this->getName(), 'CreateView');
	    $moduleName = $this->getName();
	    $basicLinks = array();
	    
	    return $basicLinks;
	}
	
	function isStarredEnabled() {
	    return false;
	}
	
	function isTagsEnabled() {
	    return false;
	}
	
	function getAcceptedValue($eventId){
	    
	    global $adb;
	   
	    $currentUser = Users_Record_Model::getCurrentUserModel();
	    
	    $accepted = false;
	    $eventQuery = $adb->pquery("SELECT * FROM vtiger_invitees WHERE activityid = ? AND inviteeid = ? AND  (status != 'accepted' AND status != 'rejected' )",
	        array($eventId, $currentUser->id));
	    
	    if(!$adb->num_rows($eventQuery)){
	        $accepted = true;
	    }
	    
	    return $accepted;
	}
	
}
?>
