<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_CkEditorImages_View extends Settings_Vtiger_Index_View {
    
    function process(Vtiger_Request $request) {
        
        global $site_URL;
        
        $qualifiedModuleName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        
        $viewer->assign('MODULE', $request->getModule());
        
        $url = rtrim($site_URL,'/')."/ckfinder/ckfinder.html?type=Images&CKEditorFuncNum=1&langCode=en";
        
        $viewer->assign('URL', $url);
        
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        
        $viewer->view('CkEditorImages.tpl', $qualifiedModuleName);
            
    }
    
    function getPageTitle(Vtiger_Request $request) {
        $qualifiedModuleName = $request->getModule(false);
        return vtranslate('Ck Editor Images',$qualifiedModuleName);
    }
    
}