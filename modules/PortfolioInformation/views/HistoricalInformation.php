<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once("libraries/reports/new/nCommon.php");
include_once("libraries/reports/new/nCombinedAccounts.php");

/* ===== START : Felipe Project Run Changes ===== */

include_once("include/utils/omniscientCustom.php");

/* ===== END : Felipe Project Run Changes ===== */

class PortfolioInformation_HistoricalInformation_View extends Vtiger_Detail_View {

	public function process(Vtiger_Request $request) {
		$calling_module = $request->get('calling_module');
		$calling_record = $request->get('calling_record');
		$setype = GetSettypeFromID($request->get('calling_record'));
        if(strlen($request->get('calling_record') ) == 0) {
            echo "Records unable to be determined";
            return;
        }

		if($setype == "PortfolioInformation"){
			$account_numbers = array(PortfolioInformation_Module_Model::GetAccountNumberFromCrmid($request->get('calling_record')));
		}
		else
			$account_numbers = GetAccountNumbersFromRecord($request->get('calling_record'));

		$account_numbers = array_unique($account_numbers);
/*		foreach($account_numbers AS $k => $v){
            $tmp = new CustodianToOmni($v);
            $tmp->UpdatePortfolios();
            $tmp->UpdatePositions();
#            $tmp->UpdateTransactions();
        }*/

		$margin_balance = PortfolioInformation_HoldingsReport_Model::GetMarginBalanceTotal($account_numbers);
		$net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetNetCreditDebitTotal($account_numbers);
		$unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetDynamicFieldTotal($account_numbers, "unsettled_cash");

/*		$combined_accounts = new nCombinedAccounts();
		if(is_array($account_numbers)){
			foreach($account_numbers AS $k => $v){
				$tmp = new nAccount($v);
				$tmp->SetAssetPie();
				$tmp->SetTrailing12Revenue();
				$tmp->SetTrailing12AUM();
				$combined_accounts->AddAccount($tmp);//Add the account to the combined
			}
		} else{
			$hide = true;
		}
		$combined_accounts->CombineAssetPie();
		$combined_accounts->CombineTrailing12Revenue();
		$combined_accounts->CombineTrailing12AUM();

//        $asset_pie = $combined_accounts->GetAssetPie();
//        $pie = FormatPieForDisplay($asset_pie);
*/
#		PortfolioInformation_HoldingsReport_Model::GenerateReportFromAccounts($account_numbers);
/*		$positions = cHoldingsReport::GetWeightedPositions();
		$positions = cHoldingsReport::CategorizePositions($positions);
		$pie = cHoldingsReport::CreatePieFromPositions($positions);

		$trailing_12_revenue = $combined_accounts->GetTrailing12Revenue();
		$trailing_12_aum = $combined_accounts->GetTrailing12AUM();
*/
/*
 ********          COMMENTED OUT FOR SPEED PURPOSES!!   THIS MAY NEED TO BE RE-IMPLEMENTED IF TRANSACTIONS ARE AN ISSUE FOR PERFORMANCE**********
        foreach($account_numbers AS $k => $v){
            if(PortfolioInformation_Module_Model::HavePCTransactionsBeenTransferred($v) != 1){
                $custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v);
                PortfolioInformation_Module_Model::CreateTransactionsFromPCCloud($custodian, $v);
            }
        }
*/
        PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($account_numbers);
        $pie = PortfolioInformation_Reports_Model::GetPieFromTable();
		$viewer = $this->getViewer($request);
		$viewer->assign("CURRENT_USER", Users_Record_Model::getCurrentUserModel());
		$viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->assign('CSS', $this->getHeaderCss($request));
		$viewer->assign("RECORD", $calling_record);
		$viewer->assign("ASSET_PIE", json_encode($pie));
//		$viewer->assign("TRAILING_12_REVENUE", json_encode($trailing_12_revenue));
//		$viewer->assign("TRAILING_12_AUM", json_encode($trailing_12_aum));
        $viewer->assign("CALLING_RECORD", $calling_record);
        $viewer->assign("ACCOUNTS", json_encode($account_numbers));
		$viewer->assign("HIDE_CHARTS", $hide);
		$viewer->assign("MARGIN_BALANCE", $margin_balance);
        $viewer->assign("NET_CREDIT_DEBIT", $net_credit_debit);
        $viewer->assign("UNSETTLED_CASH", $unsettled_cash);
		$viewer->assign("SOURCE_MODULE", $calling_module);

		echo $viewer->view('HistoricalInformation.tpl', $request->get('module'), true);
	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/serial.js",
//			"~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.min.js",
#			"modules.PortfolioInformation.resources.HoldingsReport",

#			"~/libraries/amcharts/amcharts/amcharts.js",
#			"~/libraries/amcharts/amcharts/pie.js",
#			"~/libraries/amcharts/amcharts/serial.js",
#			"~/libraries/amcharts/amcharts/plugins/export/export.min.js",
            "~/libraries/amcharts4_9/themes/dark.js",
            "modules.PortfolioInformation.resources.PortfolioList", // . = delimiter
			"modules.PortfolioInformation.resources.HistoricalInformation", // . = delimiter
		);
		if($moduleName == "PortfolioInformation")
            $jsFileNames[] = "modules.PortfolioInformation.resources.PositionsWidget";
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~/libraries/amcharts/amstockchart/plugins/export/export.css",
            '~/layouts/v7/modules/PortfolioInformation/css/HistoricalInformation.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

}
