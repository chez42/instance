<?php

require_once("modules/Billing/models/Billing.php");

class Billing_Index_View extends Vtiger_Index_View {
    // We are overriding the default SideBar UI to list our feeds.
    public function preProcess(Vtiger_Request $request, $display = true) {
/*        $feeds = MyRss_Record_Model::findAll();
        $viewer = $this->getViewer($request);
        $viewer->assign('FEEDS', $feeds);*/
        return parent::preProcess($request, $display);
    }

    public function Process(Vtiger_Request $request){
//        echo 'test';
        $billing = new Billing();
    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
#            "modules.$moduleName.resources.jquery_rss_min", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
