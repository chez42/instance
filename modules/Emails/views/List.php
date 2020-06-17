<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Emails_List_View extends Vtiger_List_View {

	/*public function preProcess(Vtiger_Request $request) {
	}
*/
	/*public function process(Vtiger_Request $request) {
		header('Location: index.php?module=MailManager&view=List');
	}*/
    
    public function getRecordActionsFromModule($moduleModel) {
        $editPermission = $deletePermission = 0;
        if ($moduleModel) {
         //   $editPermission	= $moduleModel->isPermitted('EditView');
            $deletePermission = $moduleModel->isPermitted('Delete');
        }
        
        $recordActions = array();
       // $recordActions['edit'] = $editPermission;
        $recordActions['delete'] = $deletePermission;
        
        return $recordActions;
    }
}