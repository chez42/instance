<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class PortfolioInformation_ManualWidget_View extends Vtiger_Detail_View {        
    public function process(Vtiger_Request $request) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');
        if($calling_module == "Accounts")
            $calling_module = "household";

        $totals = array();
        $values = PortfolioInformation_ManualPortfolios_Model::GetPortfolioValuesFromParentInfo($calling_module, $calling_record, $totals);

        $viewer = $this->getViewer($request);
        $viewer->assign("VALUES", $values);
        $viewer->assign("TOTALS", $totals);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->view('ManualPortfoliosList.tpl', 'PortfolioInformation', false);
    }
        
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "modules.$moduleName.resources.ManualWidget", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
}