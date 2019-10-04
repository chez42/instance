<?php

class PortfolioInformation_PositionsWidget_View extends Vtiger_BasicAjax_View{

	function process(Vtiger_Request $request) {
		$module = $request->get('calling_module');
		$calling_record = $request->get('calling_record');

		if($module == "PortfolioInformation") {
			$p = PortfolioInformation_Record_Model::getInstanceById($calling_record);
			$account_number[] = $p->get('account_number');
##			PositionInformation_Module_Model::UpdatePositionInformationValuesUsingModSecuritiesSetting($account_number);
/*            foreach($account_number AS $k => $v){
                PortfolioInformation_Module_Model::RecalculatePortfolio($v);
            }
*/
			PositionInformation_Module_Model::UndeleteAllPositionsForAccounts($account_number);
			PositionInformation_Module_Model::CalculatePositionInformationWeightForAccountNumber($account_number);
//			$positions = PositionInformation_Module_Model::GetPositionsForAccountNumber($account_number);
            $positions = PositionInformation_Module_Model::GetPositionsAndCalculateDynamic($account_number);


            $viewer = $this->getViewer($request);

			$viewer->assign('POSITIONS', $positions);
			$viewer->assign('SCRIPTS', self::getHeaderScripts($request));
			$viewer->assign('STYLES', self::getHeaderCss($request));
			$viewer->assign("CALLING_RECORD", $calling_record);
			$viewer->assign("ACCOUNT", json_encode($account_number));
			$viewer->assign("SOURCE_MODULE", $module);
			$viewer->assign("SOURCE_RECORD", $calling_record);
			$viewer->view('PositionsWidget.tpl', "PortfolioInformation");
		}else
		if($module == "Contacts" || $module == "Accounts"){
			$i = Vtiger_Record_Model::getInstanceById($calling_record);
			if($module == "Accounts") {
				include_once("include/utils/omniscientCustom.php");
				$ssn = GetSSNsForHousehold($calling_record);
			}
			else
				$ssn[] = str_replace('-', '', $i->get('ssn'));
            if(!$ssn)
                return;

			$accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromSSN($ssn);
            PositionInformation_Module_Model::UndeleteAllPositionsForAccounts($accounts);
			$positions = PositionInformation_Module_Model::GetPositionsAndCalculateDynamic($accounts);
			$viewer = $this->getViewer($request);

			$viewer->assign('POSITIONS', $positions);
			$viewer->assign('SCRIPTS', self::getHeaderScripts($request));
			$viewer->assign('STYLES', self::getHeaderCss($request));
			$viewer->assign("CALLING_RECORD", $calling_record);
			$viewer->assign("ACCOUNT", json_encode($accounts));
			$viewer->assign("SOURCE_MODULE", $module);
			$viewer->assign("SOURCE_RECORD", $calling_record);
			$viewer->view('PositionsWidget.tpl', "PortfolioInformation");
		}
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			"~/libraries/jquery/qtip/jquery.qtip.js",
			"modules.PortfolioInformation.resources.PositionsWidget", // . = delimiter
			"~/libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu.js",
			"~/libraries/jquery/handsontable/jQuery-contextMenu/jquery.ui.position.js"
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/layouts/vlayout/modules/PortfolioInformation/css/PositionsWidget.css',
			'~/libraries/jquery/qtip/jquery.qtip.css',
			"~/libraries/jquery/handsontable/jQuery-contextMenu/jquery.contextMenu.css"
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $cssInstances;
	}

}

?>