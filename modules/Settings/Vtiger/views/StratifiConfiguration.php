<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_StratifiConfiguration_View extends Settings_Vtiger_Index_View {
    
    public function process (Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        
        if(!$mode)
            $mode = 'detail';
            
        $viewer = $this->getViewer($request);
        
        $moduleName = $request->getModule();
        
        $qualifiedName = $request->getModule(false);
        
        global $adb;
        
        $userPick = array();
        
        $userQuery = $adb->pquery("SELECT CONCAT(vtiger_users.first_name,' ',vtiger_users.last_name) AS fullname, vtiger_users.advisor_control_number FROM vtiger_users");
        if($adb->num_rows($userQuery)){
            for($u=0;$u<$adb->num_rows($userQuery);$u++){
                $userPick[$adb->query_result($userQuery, $u, 'advisor_control_number')] = $adb->query_result($userQuery, $u, 'fullname');
            }
        }
        
        $viewer->assign('USERPICK', $userPick);
        
        $viewer->assign('USER_MODEL', $currentUserModel);
        
        $viewer->assign('MODULE',$moduleName);
        
        $result = $adb->pquery("SELECT * FROM vtiger_stratifi_configuration");
        
        if($adb->num_rows($result)){
            $viewer->assign('REP_CODES',$adb->query_result($result, 0, "rep_codes"));
        }
        
        $viewer->assign('mode', $mode);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $url = "index.php?module=Vtiger&parent=Settings&view=StratifiConfiguration";
        $viewer->assign('URL', $url);
        
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->view('StratifiConfiguration.tpl', $qualifiedName);
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Stratifi Configuration',$qualifiedModuleName);
    }
    
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        
        $jsFileNames = array(
            "modules.Settings.$moduleName.resources.StratifiConfiguration"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    
}