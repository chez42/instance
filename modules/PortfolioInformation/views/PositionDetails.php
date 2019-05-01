<?php

class PortfolioInformation_PositionDetails_View extends Vtiger_BasicAjax_View{

	function process(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);

		$viewer->assign('SCRIPTS', self::getHeaderScripts($request));
		$viewer->assign('STYLES', self::getHeaderCss($request));
		$viewer->view('PositionDetails.tpl', "PortfolioInformation");

	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
#			"modules.PortfolioInformation.resources.PositionsWidget", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/layouts/vlayout/modules/PortfolioInformation/css/PositionDetails.css',
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $cssInstances;
	}

}

?>