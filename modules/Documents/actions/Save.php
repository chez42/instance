<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Save_Action extends Vtiger_Save_Action {

	public function checkPermission(Vtiger_Request $request) {
	    $record = $request->get('record');
	    if($record){
	        $check = Documents_Record_Model::checkPermission($request->get('action'),$record);
    		if(!$check)
    		    throw new AppException('LBL_PERMISSION_DENIED');
	    }else{
	        parent::checkPermission($request);
	    }
	}
	
	
}
