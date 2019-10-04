<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class PortfolioInformation_ReportWidget_View extends Vtiger_Detail_View {        
    public function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        if($calling_module == "Accounts")
            $calling_module = "household";
        
        $report = new PortfolioInformation_ReportTopNavigation_View();

        $request->set('instance', strtolower($calling_module));
        $request->set('acct', $calling_record);
        $request->set('hide_links', true);

        $viewer = $this->getViewer($request);

		$account_numbers = array();
		$data = $report->process($request, $account_numbers);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
		$viewer->assign("STYLES", $this->getHeaderCss($request));
        $viewer->assign("MODULE", $request->get('calling_module'));
		$viewer->assign("CALLING_RECORD", $request->get('calling_record'));
		$viewer->assign("ACCOUNT", json_encode($account_numbers));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("MODULE_TITLE", $request->get('calling_module'));
		$viewer->assign("CALLING_RECORD", $request->get('calling_record'));
		$viewer->assign("ACCOUNT", json_encode($account_numbers));
        $portfolios_widget_content = $viewer->view('PortfolioList.tpl', $request->get('module'), true);


        $date .= "<br /><br />";
        echo $data . $portfolios_widget_content;
/*        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("MODULE", $calling_module);
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("MODULE_TITLE", $calling_module);
        echo $viewer->view('ReportWidget.tpl', $request->get('module'), true);*/
    }
        
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
            	"~libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu.js",
				"~libraries/jquery/handsontable/jQuery-contextMenu/jquery.ui.position.js",
                "modules.$moduleName.resources.PortfolioList", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			"~/libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu2.css"
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $cssInstances;
	}
}