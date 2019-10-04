<?php

class AdvisorDirect_Sidebar_View extends Vtiger_Index_View {
        // We are overriding the default SideBar UI to list our feeds.
        public function preProcess(Vtiger_Request $request, $display = true) {
                return parent::preProcess($request, $display);
        }
        
        public function process(Vtiger_Request $request) {
            global $current_user;
            $model = new AdvisorDirect_Module_Model();
            $custodians = $model->GetCustodianList();
            $user = Users_Record_Model::getCurrentUserModel();
            $name = $user->getName();
            $email = $user->get('email1');

            $attachment_id = $request->get("record");

            $viewer = $this->getViewer($request);
            $viewer->assign("CUSTODIANS", $custodians);
            $viewer->assign("ATTACHMENT_ID", $attachment_id);
            $viewer->assign("EMAIL", $email);
            $viewer->assign("USER_NAME", $name);
            $viewer->assign("AUTHORIZED", 1);
            $viewer->view('CustomSideBar.tpl', $request->getModule());
            
            parent::process($request);
        }

        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.advisordirect", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
}

?>
