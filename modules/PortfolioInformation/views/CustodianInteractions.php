<?php

#use League\Csv\Reader;
#require 'libraries/csv/vendor/autoload.php';

class PortfolioInformation_CustodianInteractions_View extends Vtiger_Index_View{
	function process(Vtiger_Request $request) {
		$dateTime = new DateTime("first day of last month");
		$accounts = PortfolioInformation_CustodianInteractions_Model::GetCSVAccountList();
		$bad_accounts = PortfolioInformation_CustodianInteractions_Model::GetBadCSVPortfolioAccountList();
		$date = $dateTime->format("Y-m-d");

		$viewer = $this->getViewer($request);
		$viewer->assign("HISTORICAL_DATE", $date);
		$viewer->assign("ACCOUNTS", $accounts);
		$viewer->assign("BAD_ACCOUNTS", $bad_accounts);
		$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));

		$viewer->view('CustodianInteractions.tpl', "PortfolioInformation", false);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"modules.PortfolioInformation.resources.CustodianInteractions", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
}
?>