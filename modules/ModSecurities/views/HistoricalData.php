<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ModSecurities_HistoricalData_View extends Vtiger_Detail_View {        
    public function process(Vtiger_Request $request, $display=true) {
        $calling_module = $request->get('calling_module');
        $calling_record = $request->get('calling_record');

        $width = $request->get('width') ? $request->get('width') : "640px";
        $height = $request->get('height') ? $request->get('height') : "400px";
        
        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("MODULE", $request->get('calling_module'));
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("MODULE_TITLE", $request->get('calling_module'));
        $viewer->assign("WIDTH", $width);
        $viewer->assign("HEIGHT", $height);
        
        if($calling_record) {
            $record = Vtiger_Record_Model::getInstanceById($calling_record);
            $data = $record->getData();
            $symbol = $data['security_symbol'];
            $prices = ModSecurities_HistoricalData_Model::GetHistoricalPricesForSymbol($symbol);

            $price_data = array();
//        echo "SECURITY ID: {$request->get('security_id')}<br />";
            foreach ($prices AS $k => $v) {
                $price_data[] = array("date" => $v['date'] . "T10:00:01",
                    "value" => $v['close'],
                    "volume" => $v['volume']);
            }
        }
        $price_data = json_encode($price_data);
        $viewer->assign("PRICE_DATA", $price_data);
        
        $widget_content = $viewer->view('HistoricalData.tpl', $request->get('module'), true);
        
        if($display)
            echo $widget_content;
        else
            return $widget_content;

/*        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("MODULE", $calling_module);
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("MODULE_TITLE", $calling_module);
        echo $viewer->view('ReportWidget.tpl', $request->get('module'), true);*/
    }
        
    public function GetHistoricalDataView(Vtiger_Request $request){
        $width = $request->get('width') ? $request->get('width') : "640px";
        $height = $request->get('height') ? $request->get('height') : "400px";
        
        $viewer = $this->getViewer($request);
        $viewer->assign("RECORD", $calling_record);
        $viewer->assign("WIDTH", $width);
        $viewer->assign("HEIGHT", $height);
        
        $prices = $request->get('advisor_prices') ? ModSecurities_HistoricalData_Model::GetHistoricalPricesWithVolumeAdvisor($request->get('security_id'), "ASC") : 
                                                    ModSecurities_SecurityBridge_Model::GetAllHistoricalPricesWithVolume($request->get('security_id'), "ASC");
        $price_data = array();
//        echo "SECURITY ID: {$request->get('security_id')}<br />";
        foreach($prices AS $k => $v){
            $price_data[] = array("date" => $v['stockFormat']. 'T10:00:01',
                                  "value" => $v['price'],
                                  "volume" => $v['volume']);
        }
        $price_data = json_encode($price_data);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign("PRICE_DATA", $price_data);

        if(strlen($request->get('template')) > 0)
                return $viewer->view($request->get('template'), 'ModSecurities', true);
        return $viewer->view('HistoricalData.tpl', 'ModSecurities', true);
    }
        
    public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			// "~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
			// "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			// "~/libraries/amcharts/amstockchart_2.9.0/amcharts/amstock.js",
			"~/libraries/amcharts/amcharts/amcharts.js",
			"~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
			"~/libraries/amcharts/amstockchart/amstock.js",
			"modules.$moduleName.resources.HistoricalDataChart",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
    }
}