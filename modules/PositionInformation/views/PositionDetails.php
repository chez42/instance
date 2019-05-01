<?php
include_once("libraries/yql/calls.php");

class PositionInformation_PositionDetails_View extends Vtiger_BasicAjax_View{

	function process(Vtiger_Request $request) {
		global $adb;
		$symbol = $request->get('symbol');
		$account = $request->get('account');

		$security_id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($symbol);
		$security_instance = Vtiger_Record_Model::getInstanceById($security_id, "ModSecurities");
		$security_data = $security_instance->getData();
		$position_id = PositionInformation_Module_Model::GetPositionEntityIDForAccountNumberAndSymbol($account, $symbol);

		if(!$position_id) {
			echo "Error Pulling Position";
			exit;
		}

        $equity_total = $security_data['us_stock'] + $security_data['intl_stock'];
        $bond_total = $security_data['us_bond'] + $security_data['intl_bond'] + $security_data['preferred_net'];
        $cash_total = $security_data['cash_net'];
        $other_total = $security_data['convertible_net'] + $security_data['other_net'];

		$position_instance = PositionInformation_Record_Model::getInstanceById($position_id);
		$position_data = $position_instance->getData();
/*
		$yql = json_decode(PortfolioInformation_yql_Model::GetSymbolQuotes($symbol));

		if(is_object($yql))
			$SYMBOL_INFO = $yql->query->results->quote;

		if(strlen($SYMBOL_INFO->MarketCapitalization) > 2) {
			$SYMBOL_INFO->symbol_type = 'equity';
			$equity_total = 100;
			PortfolioInformation_yql_Model::UpdateModSecurityEquity($security_id, $SYMBOL_INFO);
			$summary = new YQLCalls();
			$data = $summary->GetProfile($SYMBOL_INFO->symbol);
			if($data) {
				$sector = $data->query->results->td{1}->content;
				$industry = $data->query->results->td{2}->content;
				$summary = $data->query->results->p;
			}
			if(strlen($sector) > 0 && strlen($industry) > 0 && strlen($summary) > 0) {
				$security_data['sectorpl'] = $sector;
				$security_data['industrypl'] = $industry;
				$security_data['summary'] = $summary;
				PortfolioInformation_yql_Model::UpdateModSecurityProfile($security_id, $sector, $industry, $summary);
			}
		}else{
			$xignite_pr = PortfolioInformation_xignite_Model::GetFundProfileInformation($symbol);
			$xignite_aa = PortfolioInformation_xignite_Model::GetFundAssetAllocation($symbol);
			if($xignite_aa->Outcome === "Success") {
				$SYMBOL_INFO->symbol_type = 'fund';
				$security_data['aclass'] = "Fund";
				$summary = $xignite_pr->Profile->InvestmentSummary;
				$us_stock = $xignite_aa->StockAssetAllocation->USStockNetAllocation;
				$intl_stock = $xignite_aa->StockAssetAllocation->NonUSStockNetAllocation;
				$us_bond = $xignite_aa->BondAssetAllocation->USBondNetAllocation;
				$intl_bond = $xignite_aa->BondAssetAllocation->NonUSBondNetAllocation;
				$preferred = $xignite_aa->OtherAssetAllocation->PreferredNetAllocation;
				$convertible = $xignite_aa->OtherAssetAllocation->ConvertibleNetAllocation;
				$cash = $xignite_aa->OtherAssetAllocation->CashNetAllocation;
				$other = $xignite_aa->OtherAssetAllocation->OtherNetAllocation;

				$equity_total = $us_stock + $intl_stock;
				$bond_total = $us_bond + $intl_bond + $preferred;
				$cash_total = $cash;
				$other_total = $convertible + $other;

				$query = "UPDATE vtiger_modsecurities m JOIN vtiger_modsecuritiescf USING (modsecuritiesid) 
						  SET summary = ?, us_stock=?, intl_stock=?, us_bond=?, intl_bond=?, preferred_net=?, convertible_net=?, cash_net=?, other_net=?, unclassified_net = 0
						  WHERE modsecuritiesid = ? AND ignore_auto_update IN (0)";
				$adb->pquery($query, array($summary, $us_stock, $intl_stock, $us_bond, $intl_bond, $preferred, $convertible, $cash, $other, $security_id));
			}else{
				$equity_total = $security_data['us_stock'] + $security_data['intl_stock'];
				$bond_total = $security_data['us_bond'] + $security_data['intl_bond'] + $security_data['preferred_net'];
				$cash_total = $security_data['cash_net'];
				$other_total = $security_data['convertible_net'] + $security_data['other_net'];
			}
			$security_instance = Vtiger_Record_Model::getInstanceById($security_id, "ModSecurities");
			$security_data = $security_instance->getData();
		}*/
        $colors = PortfolioInformation_Module_Model::GetAllChartColors();

		$viewer = $this->getViewer($request);
		$viewer->assign('SCRIPTS', self::getHeaderScripts($request));
		$viewer->assign('STYLES', self::getHeaderCss($request));
		$viewer->assign("SECURITY", $security_data);
		$viewer->assign("POSITION", $position_data);
		$viewer->assign("SYMBOL_INFO", $SYMBOL_INFO);
		$viewer->assign("EQUITY_TOTAL", $equity_total);
		$viewer->assign("BOND_TOTAL", $bond_total);
		$viewer->assign("CASH_TOTAL", $cash_total);
		$viewer->assign("OTHER_TOTAL", $other_total);
        $viewer->assign("COLORS", $colors);
#		$viewer->assign("XIGNITE", $xignite);
		$viewer->view('PositionDetails.tpl', "PositionInformation");

	}

	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
/*			"~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
			"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			"~/libraries/amcharts/amstockchart_2.9.0/amcharts/amstock.js",*/
			"modules.PositionInformation.resources.PositionDetails", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);
		$cssFileNames = array(
			'~/layouts/vlayout/modules/PositionInformation/css/PositionDetails.css',
			// '~/libraries/amcharts/amstockchart_2.9.0/amcharts/style.css',
			'~/libraries/amcharts/amstockchart/style.css',
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $cssInstances;
	}

}

?>