<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 3:36 PM
 */
class PositionInformation_ConvertCustodian_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request){
		$date = $request->get('date');
		$custodian = $request->get('custodian');
		$convert_table = $request->get('convert_table');
		$account_number = $request->get('account_number');
		PositionInformation_ConvertCustodian_Model::SetDashless();
		switch($convert_table){
			case "remove_dupes":
				PositionInformation_ConvertCustodian_Model::RemoveDupes($account_number);
				echo "Dupes removed";
				break;
			case "update_positions":
				switch($custodian){
					case "fidelity":
						PositionInformation_ConvertCustodian_Model::UpdatePositionInformationFidelity($date, $account_number);
#						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $account_number);
						break;
					case "pershing":
						PositionInformation_ConvertCustodian_Model::UpdatePositionInformationPershing($date, $account_number);
						break;
					case "td":
						PositionInformation_ConvertCustodian_Model::UpdatePositionInformationTD($date, $account_number);
						break;
					case "schwab":
						PositionInformation_ConvertCustodian_Model::UpdatePositionInformationSchwab($date, $account_number);
#						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $account_number);
						break;
				}
				PositionInformation_ConvertCustodian_Model::SetPositionOwnerShip();
				echo "{$custodian} Positions Updated";
#				PositionInformation_ConvertCustodian_Model::ConvertCustodian($custodian, $date, "=", $account_number);
				break;
			case "new_positions":
				switch($custodian){
					case "fidelity":
						PositionInformation_ConvertCustodian_Model::PullNewPositionsFidelity($date);
						break;
					case "pershing":
						PositionInformation_ConvertCustodian_Model::PullNewPositionsPershing($date);
						break;
					case "td":
						PositionInformation_ConvertCustodian_Model::PullNewPositionsTD($date);
						break;
					case "schwab":
						PositionInformation_ConvertCustodian_Model::PullNewPositionsSchwab($date);
						break;
				}
				PositionInformation_ConvertCustodian_Model::SetPositionOwnerShip();
				echo "{$custodian} Positions Pulled";
				break;
		}
	}
}