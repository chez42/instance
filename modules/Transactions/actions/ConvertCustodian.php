<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 3:36 PM
 */
class Transactions_ConvertCustodian_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request){
		$date = $request->get('date');
		$comparitor = $request->get('comparitor');
		$custodian = $request->get('custodian');
		$convert_table = $request->get('convert_table');
		$newonly = $request->get('newonly');
		$comparitor = html_entity_decode($comparitor);
		$account_number = $request->get('account_number');

		switch($convert_table){
			case "transactions":
				Transactions_ConvertCustodian_Model::ConvertCustodian($custodian, $date, $comparitor, $newonly);
				break;
			case "prices":
				Transactions_ConvertCustodianPrices_Model::ConvertCustodian($custodian, $date, $comparitor, $newonly);
				break;
			case "update_symbol":
				Transactions_ConvertCustodianPrices_Model::AddSymbolToPricingTable();
				break;
            case "assign_transactions":
                Transactions_ConvertCustodian_Model::ReassignTransactions($account_number);
                echo "Transactions Re-Assigned";
                break;
		}
	}
}