<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");

class Timecontrol_List_View extends Vtiger_List_View {

	function preProcess(Vtiger_Request $request, $display=true) {
        $moduleModel = Vtiger_Module_Model::getInstance("Timecontrol");

        $className = "\\TimeControl\\S"."WE"."xt"."ension\\cd10"."7ad732d2304"."f9118bc9f6892f"."1110643e5469";
        $as2df = new $className("Timecontrol", $moduleModel->version);
		/*
        if(!$as2df->g814b18adf5aa857e83e72e05669060e9b72dd078()) {
            header('Location:index.php?module=Timecontrol&view=LicenseManager&parent=Settings');
            exit();
        }
		*/
		parent::preProcess($request, $display);
	}
	
	public function getRecordActionsFromModule($moduleModel) {
	    $editPermission = $deletePermission = 0;
	    if ($moduleModel) {
	        $editPermission	= $moduleModel->isPermitted('EditView');
	        $deletePermission = $moduleModel->isPermitted('Delete');
	    }
	    
	    $recordActions = array();
	    //$recordActions['edit'] = $editPermission;
	    $recordActions['delete'] = $deletePermission;
	    
	    return $recordActions;
	}
}



