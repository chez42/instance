<?php

class Custodians_List_View extends Vtiger_List_View {
        /**
         * Calculates the global summary for the list view
         * @global type $current_user
         * @param Vtiger_Request $request
         * @param type $display
         * @return type
         */
        public function preProcess(Vtiger_Request $request, $display = true) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $global_summary = new PortfolioInformation_GlobalSummary_Model();
            
            if($currentUserModel->isAdminUser())
                $values = $global_summary->getAdminSummaryValues($request);
            else
                $values = $global_summary->getNonAdminSummaryValues($request);
            
            $viewer = $this->getViewer($request);
            $viewer->assign('GLOBAL_SUMMARY', $values);
            
            return parent::preProcess($request, $display);
        }

        public function postProcess(Vtiger_Request $request) {

            parent::postProcess($request);
            
        }
                
        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                        "modules.$moduleName.resources.portfolioinformation", // . = delimiter
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
}
?>
