<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class PositionInformation_Detail_View extends Vtiger_Detail_View {

	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
		$moduleName = 'Calendar';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			$moduleName = $request->getModule();
			$recordId = $request->get('record');

			$pageNumber = $request->get('page');
			if(empty ($pageNumber)) {
				$pageNumber = 1;
			}
			$pagingModel = new Vtiger_Paging_Model();
			$pagingModel->set('page', $pageNumber);
			$pagingModel->set('limit', 10);

			if(!$this->record) {
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
			}
			$recordModel = $this->record->getRecord();
			$moduleModel = $recordModel->getModule();

			$relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			$viewer->assign('PAGE_NUMBER', $pageNumber);
			$viewer->assign('ACTIVITIES', $relatedActivities);

			return $viewer->view('RelatedActivities.tpl', $moduleName, true);
		}
	}

	public function showModuleDetailView(Vtiger_Request $request) {
/*		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
                $data = $recordModel->getData();

                $historicalData = new ModSecurities_HistoricalData_View();
        //        echo "HELLO: {$request->get('security_symbol')}<br />";
                $trading = new Trading_Quote_View($request);
                $req = new Vtiger_Request(array());
                $req->set('symbol', $data['security_symbol']);
                $req->set('task', 'get_quote');
                $req->set('security_id', $data['symbol_id']);
                $req->set('width', '100%');
                $req->set('height', '480px');
                $req->set('template', 'HistoricalViewOnly.tpl');
                $trading_view = $trading->GetQuoteInformationTemplateOnly($req);

                $historical_view = $historicalData->GetHistoricalDataView($req);
                $req->set('calling_module', 'PortfolioInformation');
                $req->set('security_id', $data['symbol_id']);
                $req->set('module', 'ModSecurities');
                $req->set('advisor_prices', 1);
                /*
                $viewer->assign('HISTORICALDATA', $historicalData);
                $viewer->assign('HISTORICALSETTINGS', $req);*/
//                $v = $historicalData->process($req);
/*		$viewer = $this->getViewer($request);
//                $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
		$viewer->assign("TRADING_VIEW", $trading_view);
                $viewer->assign("HISTORICAL_VIEW", $historical_view);
*/
		return parent::showModuleDetailView($request);
}

        public function getHeaderScripts(\Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
/*
		$moduleName = $request->getModule();

		$jsFileNames = array(
			//"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
#           "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			//"~/libraries/amcharts/amstockchart_3.20.9/amcharts/amstock.js",
					
			"~/libraries/amcharts/amcharts/amcharts.js",
			"~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
			"~/libraries/amcharts/amstockchart/amstock.js",
			
            "modules.ModSecurities.resources.HistoricalDataChart",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);*/
		return $headerScriptInstances;
        }
}
