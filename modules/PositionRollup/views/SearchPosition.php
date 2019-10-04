<?php
class PositionRollup_List_View extends Vtiger_List_View {
        /**
         * Calculates the global summary for the list view
         * @global type $current_user
         * @param Vtiger_Request $request
         * @param type $display
         * @return type
         */
        public function preProcess(Vtiger_Request $request, $display = true) {
            parent::preProcess($request, $display);
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $viewer = $this->getViewer($request);
            
//            echo $request->set('module', 'PositionRollup');
        }

        public function process(Vtiger_Request $request) {
//            echo $request->set('module', 'PositionRollup');
            echo "HERE";
//            parent::process($request);
        }
        
        public function postProcess(Vtiger_Request $request) {
            parent::postProcess($request);
        }
                
        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.positionrollup", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
        
}
?>