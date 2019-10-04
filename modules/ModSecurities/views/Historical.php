<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once('libraries/reports/new/nCommon.php');

class ModSecurities_Historical_View extends Vtiger_Detail_View {

    function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $record = Vtiger_Record_Model::getInstanceById($request->get('record'), 'ModSecurities');
        $data = $record->getData();
        $pricing_history = ModSecurities_SecurityBridge_Model::GetAllHistoricalPrices($data['security_id']);

        $viewer->assign("EXTRA_SCRIPTS", $this->getCustomScripts($request));
        $viewer->assign("VIEW_MORE", 10);
        $viewer->assign("RECORD_ID", $record->get('id'));
        $viewer->assign('PRICING_HISTORY', $pricing_history);
        $viewer->assign("EXTRA_STYLES", $this->getExtraHeaderCss($request));
        $viewer->view('Historical.tpl', "ModSecurities");
    }
        
    // Injecting custom javascript resources
    public function getCustomScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "~/libraries/jquery/jquery.class.min.js",
                "modules.ModSecurities.resources.HistoricalPricing", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }

    public function getExtraHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/layouts/vlayout/modules/ModSecurities/css/Historical.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}