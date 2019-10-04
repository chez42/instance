<?php

class PortfolioInformation_CloudInteractions_View extends Vtiger_Index_View{
	function process(Vtiger_Request $request) {
		$date = date("Y-m-d", strtotime('today -1 Weekday'));
		$transaction_diagnosis = PortfolioInformation_CloudInteractions_Model::GetLatestDates();
		$files_run = PortfolioInformation_CloudInteractions_Model::GetFilesRunByCustodian(50);

		$viewer = $this->getViewer($request);

		$viewer->assign("DATE", $date);
		$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
		$viewer->assign("TRANSACTION_DIAGNOSIS", $transaction_diagnosis);
		$viewer->assign("FILES_RUN", $files_run);

		$viewer->view('CloudInteractions.tpl', "PortfolioInformation", false);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.PortfolioInformation.resources.CloudInteractions", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
}
?>