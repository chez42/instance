<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-11-01
 * Time: 2:49 PM
 */

class PortfolioInformation_StratifiWidget_View extends Vtiger_Detail_View {
    public function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        if($calling_module == "Accounts")
            $calling_module = "household";

        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("MODULE", $request->get('calling_module'));
        $viewer->assign("CALLING_RECORD", $request->get('calling_record'));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("MODULE_TITLE", $request->get('calling_module'));
        $viewer->assign("CALLING_RECORD", $request->get('calling_record'));

        $portfolios_widget_content = $viewer->view('StratifiWidget.tpl', $request->get('module'), true);
        echo $portfolios_widget_content;
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
//            "~libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu.js",
//            "~libraries/jquery/handsontable/jQuery-contextMenu/jquery.ui.position.js",
//            "modules.$moduleName.resources.PortfolioList", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
//            "~/libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu2.css"
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }
}