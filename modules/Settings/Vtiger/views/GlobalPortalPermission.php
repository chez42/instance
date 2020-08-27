<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_GlobalPortalPermission_View extends Settings_Vtiger_Index_View {
    
    
    public function process (Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!$mode)
            $mode = 'detail';
        
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);
        
        $viewer->assign('USER_MODEL', $currentUserModel);
        $viewer->assign('MODULE',$moduleName);
        
        global $adb;
        $selectedPortalModulesInfo = array();
        
        $selectedPortalInfo = $adb->pquery("SELECT * FROM vtiger_default_portal_permissions WHERE userid = ?",array('0'));
        if($adb->num_rows($selectedPortalInfo)){
            $selectedPortalModulesInfo = $adb->query_result_rowdata($selectedPortalInfo);
        }
        
        $viewer->assign('mode', $mode);
        
        $url = "index.php?module=Vtiger&parent=Settings&view=GlobalPortalPermission";
        
        $viewer->assign('URL', $url);
        
        $viewer->assign('SELECTED_PORTAL_MODULES', $selectedPortalModulesInfo);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->view('GlobalPortalInfoBlock.tpl', $qualifiedName);
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Global Portal Permissions',$qualifiedModuleName);
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
            "modules.Settings.$moduleName.resources.GlobalPortalBlock"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    
}