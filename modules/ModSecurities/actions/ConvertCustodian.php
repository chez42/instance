<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 3:36 PM
 */
class ModSecurities_ConvertCustodian_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request){
		$date = $request->get('date');
		$comparitor = $request->get('comparitor');
		$custodian = $request->get('custodian');
		$convert_table = $request->get('convert_table');
		$symbol = $request->get('symbol');
		$sdate = $request->get('sdate');
		$edate = $request->get('edate');

		switch($convert_table){
			case "securities":
				ModSecurities_ConvertCustodian_Model::ConvertCustodian($custodian, $date, $comparitor);
				break;
			case "update_prices":
				if(strlen($symbol) > 1)
					ModSecurities_ConvertCustodian_Model::UpdateIndividualPrice($symbol);
				else
					ModSecurities_ConvertCustodian_Model::UpdateAllPricesFromCloud($custodian);
				echo "Pricing Updated";
				break;
			case "update_symbol":
				Transactions_ConvertCustodianPrices_Model::AddSymbolToPricingTable();
				echo "Symbols added to pricing table";
				break;
			case "asset_type_update":
				ModSecurities_ConvertCustodian_Model::UpdateAllTypesAndAssetClass($custodian);
				echo "{$custodian} asset class and types updated";
				break;
			case "update_securities":
				switch($custodian){
					case "schwab": {
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsSchwab($symbol);
                    }
					break;
                    case "td": {
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsTD($symbol);
                    }
                    break;
					case "fidelity": {
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsFidelity($symbol, true);
                    }
					break;
                    case "pershing":{
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsPershing($symbol, true);
                    }
				}
//				ModSecurities_ConvertCustodian_Model::UpdateSecurityPrices($custodian, $date);
				echo "UPDATED SECURITIES";
				break;
			case "update_security_type":
				switch($custodian){
					case "schwab":
						ModSecurities_ConvertCustodian_Model::UpdateSecurityType($custodian, $symbol);
					break;
					case "fidelity":
						ModSecurities_ConvertCustodian_Model::UpdateSecurityType($custodian, $symbol);
					break;
				}
				echo "Updated Security Types {$symbol}";
				break;
			case "update_index":
				ModSecurities_ConvertCustodian_Model::UpdateIndexEOD($symbol, $sdate, $edate);
				echo "Index Updated for {$symbol}";
				break;
		}
	}
}