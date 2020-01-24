<?php

class OmniCal_CalendarNavigation_View extends Vtiger_BasicAjax_View {
    public function process(Vtiger_Request $request) {
        global $current_user;

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $output = $viewer->view('CalendarNavigation.tpl', "OmniCal", false);//False makes it echo
    }
    
    public function preProcess(Vtiger_Request $request, $display = true) {

    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "modules.OmniCal.resources.CalendarNavigation", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }    
}

?>
