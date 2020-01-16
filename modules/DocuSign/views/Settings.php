<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class DocuSign_Settings_View extends Settings_Vtiger_Index_View {
    
    function process(Vtiger_Request $request) {
        
        $qualifiedModuleName = $request->getModule();
        $viewer = $this->getViewer($request);
        
        $mode = $request->get('mode');
        if(!$mode)
            $mode = 'detail';
        
        global $adb,$current_user;
        
        $conQuery = $adb->pquery("SELECT * FROM vtiger_document_designer_auth_settings");
        
        if($adb->num_rows($conQuery)){
            
            $clientId = $adb->query_result($conQuery, 0, 'clientid');
            $cliensecret = $adb->query_result($conQuery, 0, 'clientsecret');
            $server = $adb->query_result($conQuery, 0, 'server');
            
            $viewer->assign('CLIENTID', $clientId);
            $viewer->assign('CLIENTSECRET', $cliensecret);
            $viewer->assign('SERVER', $server);
            
        }
        
        $viewer->assign('mode', $mode);
        $viewer->assign('MODULE', $request->getModule());
        
        $url = "index.php?module=DocuSign&parent=Settings&view=Settings";
        $viewer->assign('URL', $url);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('DocuSignSettings.tpl', $qualifiedModuleName);
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
            "modules.$moduleName.resources.Settings"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
}