<?php

class MDI_List_View extends Vtiger_List_View {
        var $generator;//The query generator

        public function process(Vtiger_Request $request) {
            $viewer = $this->getViewer ($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                        
            $viewer->assign("SCRIPTS_CUSTOM", $this->getCustomScripts($request));
            $viewer->assign("RESULT_VALUES", $result_values);
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->view('MdiImport.tpl', $moduleName);
        }
        
        public function postProcess(Vtiger_Request $request) {

            parent::postProcess($request);
            
        }
        
        // Injecting custom javascript resources
        public function getCustomScripts(Vtiger_Request $request) {
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.mdi", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                return $jsScriptInstances;
        }
}
?>
