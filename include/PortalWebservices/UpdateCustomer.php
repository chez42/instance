<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_update_customer($element, $user){

    global $log, $adb;
    
    $id = $element['id'];
	
	if(isset($element['password'])){
		
		$adb->pquery("update vtiger_contactdetails set portal_password = ? where contactid = ?",
		array($element['password'], $id));
	
	} else {
		
		$obj = CRMEntity::getInstance("Contacts");
		
		$obj->id = $id;
		
		$obj->retrieve_entity_info($id, "Contacts");
		
		foreach($element as $key => $data){
			$obj->column_fields[$key] = $data;
		}
		
		$obj->mode = "edit";
		
		$obj->save("Contacts");
		
	}
	
	return array("success" => true);
	
}
?>