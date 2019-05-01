<?php

class OmniMail_Contact_View extends Vtiger_BasicAjax_View{
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $contact_info = new OmniMail_Contact_Model();
        $contact_info->LoadInfo($request);
        
        $viewer->assign("CONTACT_ID", $contact_info->contact_id);
        $viewer->assign("FIRSTNAME", $contact_info->fname);
        $viewer->assign("LASTNAME", $contact_info->lname);
        $viewer->assign("MODULE", $contact_info->module);
        $viewer->assign("ACTION", $contact_info->action);
        $viewer->assign("METHOD", $contact_info->method);
        $viewer->assign("SUBJECT", $contact_info->subject);
        $viewer->assign("FROMNAME", $contact_info->fromname);
        $viewer->assign("FROM", $contact_info->from);
        $viewer->assign("TONAME", $contact_info->toname);
        $viewer->assign("TO", $contact_info->to);
        $viewer->assign("BODY", $contact_info->body);
        
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $output = $viewer->view('Contact.tpl', "OmniMail", true);
        return $output;
    }
    
    /**DOES NOT WORK WITHIN OMNIMAIL**/
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.OmniMail.resources.Contact", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}

?>