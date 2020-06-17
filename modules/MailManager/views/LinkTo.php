<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_LinkTo_View extends Vtiger_IndexAjax_View {
    
    
    public function process (Vtiger_Request $request) {
        
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        
        $msgNo = implode(',',$request->get('msgno'));
        
        $viewer->assign("FOLDER",$request->get('folder'));
        $viewer->assign('MSGNOS',$msgNo);
        $viewer->assign('MODULE',$moduleName);
        $viewer->view('LinkTo.tpl', $moduleName);
    }
    
}