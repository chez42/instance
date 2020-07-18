<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_OauthConfiguration_View extends Settings_Vtiger_Index_View {
    
    function process(Vtiger_Request $request) {
        
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        
        $mode = $request->get('mode');
        if(!$mode)
            $mode = 'detail';
        $result = array();
        global $adb,$current_user;
        
        $query = $adb->pquery("SELECT * FROM vtiger_oauth_configuration");
        
        if($adb->num_rows($query)){
            for($i=0;$i<$adb->num_rows($query);$i++){
                $clientId = $adb->query_result($query, $i, 'client_id');
                $clienSecret = $adb->query_result($query, $i, 'client_secret');
                $redirectUri = $adb->query_result($query, $i, 'redirect_url');
                $type = $adb->query_result($query, $i, 'type');
                
                $result[$type] = array(
                    'clientid' => $clientId,
                    'clientsecret' => $clienSecret,
                    'redirecturi' => $redirectUri,
                    'type' => $type
                );
            }
            
        }
        
        $viewer->assign('DATA', $result);
        
        $viewer->assign('mode', $mode);
        
        $viewer->assign('MODULE', $request->getModule());
        
        $url = "index.php?module=Vtiger&parent=Settings&view=OauthConfiguration";
        
        $viewer->assign('URL', $url);
        
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
        $viewer->view('OauthConfiguration.tpl', $qualifiedModuleName);
    
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Oauth Configuration',$qualifiedModuleName);
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
            "modules.Settings.$moduleName.resources.OauthConfiguration"
        );
        
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}