<?php
/*
Subject
Start
Contacts
Description
 */
class OmniCal_Postpone_View extends Vtiger_BasicAjax_View{
    
    public function process(Vtiger_Request $request) {
        $postpone = new OmniCal_Postpone_Model($request);
        $reminders = $postpone->getReminders();

        if(sizeof($reminders) > 0){
            $viewer = $this->getViewer($request);
            $viewer->assign('REMINDERS', $reminders);
            $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
            $viewer->assign("STYLES", $this->getHeaderCss($request));
            $output = $viewer->view('Postpone.tpl', "OmniCal", true);//False makes it echo
        }
        else
            $output = null;
        return $output;
    }
    
    public function preProcess(Vtiger_Request $request, $display = true) {

    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                    "modules.$moduleName.resources.Activities", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                    '~/layouts/vlayout/modules/OmniCal/css/Reminders.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?>
