<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
 
class Notifications_Detail_View extends Vtiger_Detail_View {

    function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
        if(!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        
        if ($recordId) {
            if ('Notifications' !== $moduleName) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
        }
        
        return true;
    }
    
    public function preProcess(Vtiger_Request $request) {
        return parent::preProcess($request);
    }

    public function process(Vtiger_Request $request){
        return parent::process($request);
    }

}
