<?php
class OmniMail_List_View extends Vtiger_Index_View {
        // We are overriding the default SideBar UI to list our feeds.
    public function preProcess(Vtiger_Request $request, $display = true) {
//        parent::preProcess($request, $display);
    }
    
    public function process(Vtiger_Request $request) {
        global $current_user;
        $mail = OmniMail_Module_Model::getInstance($request->getModule());
        $url = $mail->getMailUrl();
        switch($request->get('qualify')){
            case "Contacts" :
                include ("libraries/OmniMail/plugins/qualify/contact.php");
                break;
            case "Leads" : 
                include ("libraries/OmniMail/plugins/qualify/leads.php");
                break;
        }

        $viewer = $this->getViewer($request);
        $viewer->assign("user_id", $current_user->id);
        $viewer->assign("URL", $url);

        parent::process($request);
    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
            $headerScriptInstances = parent::getHeaderScripts($request);
            $moduleName = $request->getModule();
            $jsFileNames = array(
                    "modules.$moduleName.resources.omnimail", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
            return $headerScriptInstances;
    }
}
?>