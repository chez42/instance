<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-10-28
 * Time: 6:13 PM
 */

class PositionInformation_Alert_View extends Vtiger_Index_View  {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$record = $request->get('record');
		$alert = "The position you are trying to create already exists.  A position cannot exist twice for an individual account.";
		$navigate = 1;

		$viewer->assign('RECORD', $record);
		$viewer->assign('NAVIGATE', $navigate);
		$viewer->assign('ALERT', $alert);

		return $viewer->view('Alert.tpl', "PositionInformation");
	}

	public function getHeaderScripts(\Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$moduleName = $request->getModule();

		$jsFileNames = array(
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
#           "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
//			"~/libraries/amcharts/amstockchart_3.20.9/amcharts/amstock.js",

			"~/libraries/amcharts/amcharts/amcharts.js",
			"~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
			"~/libraries/amcharts/amstockchart/amstock.js",
			
			"modules.ModSecurities.resources.HistoricalDataChart",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}
