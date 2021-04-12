<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 3:36 PM
 */
class PortfolioInformation_ConvertCustodian_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request){
		$date = $request->get('date');
		$custodian = $request->get('custodian');
		$convert_table = $request->get('convert_table');
		$account_number = $request->get('account_number');
		switch($convert_table){
			case "portfolios":
#				PortfolioInformation_ConvertCustodian_Model::ConvertCustodian($custodian, $date, "=", $account_number);
				break;
			case "update_portfolios":
				switch($custodian){
					/*case "fidelity":
						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFidelity($date, $account_number);
						echo "Fidelity Portfolios Updated";
						break;
					case "pershing":
						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesPershing($date, $account_number);
						echo "Pershing Portfolios Updated";
						break;
					case "td":
						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date, $account_number);
						echo "TD Portfolios Updated";
						break;
                    case "schwab":
                        PortfolioInformation_ConvertCustodian_Model::UpdateAllSchwabPortfoliosWithLatestInfoForAccount();
                        echo "Schwab updated to LATEST values.  Date had no effect if entered";
                        break;*/
				}
			break;
			case "assign_portfolios":
				PortfolioInformation_ConvertCustodian_Model::AssignPortfoliosBasedOnContactLink($account_number);
				echo "Portfolio Re-Assignment Complete";
				break;
			case "calculate_portfolios":
				switch($custodian){/*
					case stristr($custodian, 'schwab'):
						PortfolioInformation_ConvertCustodian_Model::UpdateAllSchwabPortfoliosWithLatestInfoForAccount($account_number);
					break;
					case stristr($custodian, 'fidelity'):
						PortfolioInformation_ConvertCustodian_Model::UpdateAllFidelityPortfoliosWithLatestInfoForAccount($account_number);
					break;
					default:
						PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $account_number);
					break;*/
				}
#				echo "Portfolio Totals Updated for {$custodian} {$account_number}";
				break;
			case "link_portfolios":
				PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios();
				PortfolioInformation_ConvertCustodian_Model::LinkHouseholdsToPortfolios();
				echo "Contacts and Households Linked";
				break;
			case "integrity_check":
				$date = $request->get('date');
				$values = PortfolioInformation_ConvertCustodian_Model::IntegrityCheck($custodian, $date);
				PortfolioInformation_ConvertCustodian_Model::UpdateIntegrityHistory($values['good'] + $values['bad'],
																			        $values['good'],
																					$values['bad'],
																					$custodian,
																					$date);
				echo json_encode($values);
				break;
			case "update_portfolio_center":
				$accounts = PortfolioInformation_Module_Model::GetAllDashlessAndCustodian();
				foreach($accounts AS $k => $v){
					PortfolioInformation_ConvertCustodian_Model::UpdatePCCustodian($v['custodian'], $v['account_number']);
				}
				echo "PC Updated";
				break;
			case "update_balances":
				PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud($custodian, $account_number, $date);
				echo "Balances Updated for {$custodian}";
				break;
		}
	}
}