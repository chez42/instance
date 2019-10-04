<?php

class PortfolioInformation_xignite_View extends Vtiger_Index_View{
	function process(Vtiger_Request $request) {
		$date = date("Y-m-d", strtotime('today'));
		$viewer = $this->getViewer($request);

		$viewer->assign("DATE", $date);
		$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
		$viewer->view('xignite.tpl', "PortfolioInformation", false);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.PortfolioInformation.resources.xignite", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
}
?>