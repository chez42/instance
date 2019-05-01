<?php

class OmniMail_Lead_View extends Vtiger_BasicAjax_View{
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $lead_info = new OmniMail_Lead_Model();
        $lead_info->LoadInfo($request);
        
        $viewer->assign("LEAD_ID", $lead_info->lead_id);
        $viewer->assign("FIRSTNAME", $lead_info->fname);
        $viewer->assign("LASTNAME", $lead_info->lname);
        $viewer->assign("MODULE", $lead_info->module);
        $viewer->assign("ACTION", $lead_info->action);
        $viewer->assign("METHOD", $lead_info->method);
        $viewer->assign("SUBJECT", $lead_info->subject);
        $viewer->assign("FROMNAME", $lead_info->fromname);
        $viewer->assign("FROM", $lead_info->from);
        $viewer->assign("TONAME", $lead_info->toname);
        $viewer->assign("TO", $lead_info->to);
        $viewer->assign("BODY", $lead_info->body);
        
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $output = $viewer->view('Lead.tpl', "OmniMail", true);
        return $output;
    }
    
    /**DOES NOT WORK WITHIN OMNIMAIL**/
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.OmniMail.resources.Lead", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
}

?>