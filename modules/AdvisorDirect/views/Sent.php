<?php

class AdvisorDirect_Sent_View extends Vtiger_Index_View{
	function __construct() {
		parent::__construct();
	}    
        // We are overriding the default SideBar UI to list our feeds.
        public function preProcess(Vtiger_Request $request, $display = true) {

                parent::preProcess($request, $display);
        }
        
        public function process(Vtiger_Request $request) {
            $model = new AdvisorDirect_Module_Model();
            $custodians = $model->GetCustodianList();
            $result = $request->get('result');
            $viewer = $this->getViewer($request);
            $viewer->assign("CUSTODIANS", $custodians);
            $viewer->assign("RESULT", $result);
            $viewer->view('Sent.tpl', $request->getModule());
            parent::process($request);
        }

        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
/*                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.advisordirect", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;*/
        }
}

?>
