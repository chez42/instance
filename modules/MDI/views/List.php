<?php

class MDI_List_View extends Vtiger_List_View {
        var $generator;//The query generator

        public function process(Vtiger_Request $request) {
            $viewer = $this->getViewer ($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $fieldModel = new Vtiger_Field_Model();
            
            $viewer->assign("SCRIPTS", $this->getCustomScripts($request));
            $viewer->assign("RESULT_VALUES", $result_values);
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign("FIELD_MODEL", $fieldModel);
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->view('MdiImport.tpl', $moduleName);
        }
        
        public function preProcess(\Vtiger_Request $request, $display = true) {
            parent::preProcess($request, $display);
        }
        
        public function postProcess(Vtiger_Request $request) {

            parent::postProcess($request);
            
        }
        
        // Injecting custom javascript resources
        public function getCustomScripts(Vtiger_Request $request) {
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.MDI", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                return $jsScriptInstances;
        }
}
?>
