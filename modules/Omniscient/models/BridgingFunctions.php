<?php
/*autoextract.sh
#!/bin/bash

cd /home/syncuser/tdcron/filehub
ls -l
unzip -n \*.zip -d /home/syncuser/tdcron/filehub/extracted && rm *.zip
cd /home/syncuser/tdcron/filehub/extracted
unzip -n \*F97*.ZIP -d /home/syncuser/tdcron/parse/tenant_stonebridge && rm *F97*.ZIP
exec /home/syncuser/tdcron/tdcron /home/syncuser/tdcron/parse/ tenant_stonebridge



 */
class Omniscient_BridgingFunctions_Model extends Vtiger_Module_Model{
	public static function CompareAccountData($jsonData){
		$compare_results = array();
		$results = array();
		foreach($jsonData AS $account_number => $v){
//			$omniPositions = PositionInformation_Module_Model::GetPositionsForAccountNumber($account_number);
//			$result = self::OmniPositionsComparison($account_number, $v->positionList);
			$result = self::WriteToAuditResultsTable($account_number, $v->positionList);
			$results = array_merge($results, $result);
		}
		$compare_results['positionList'] = $results;
		return $compare_results;
	}

	public static function WriteCSVToTable($jsonData){
		$compare_results = array();
		$results = array();

		foreach($jsonData AS $account_number => $v){
			$result = self::WriteCSVOnlyToResultsTable($account_number, $v->positionList);
			$results = array_merge($results, $result);
		}
		$compare_results['positionList'] = $results;
		return $compare_results;
	}

	public static function WriteCSVToPortfolioTable($jsonData){
		$compare_results = array();
		$results = array();

		foreach($jsonData AS $account_number => $v){
			$result = self::WritePortfolioCSVOnlyToResultsTable($account_number, $v->portfolioList);
			$results = array_merge($results, $result);
		}
		$compare_results['portfolioList'] = $results;
		return $compare_results;
	}

	private function CreateCompareTable($account_number, $positions){
		global $adb;
		$query = "DROP TABLE IF EXISTS OmniCompare";
		$adb->pquery($query, array());
		$query = "CREATE TEMPORARY TABLE OmniCompare (account_number VARCHAR(50),
													  symbol VARCHAR(50),
													  quantity double(10,0),
													  cash double(12,0))";
		$adb->pquery($query, array());

		foreach($positions AS $k => $v){
			$query = "INSERT INTO OmniCompare (account_number, symbol, quantity, cash)
					  VALUES (?, ?, ?, ?)";
			$adb->pquery($query, array($account_number, $v->sSymbol, $v->quantity, $v->value));
		}

	}

	private function WriteCSVOnlyToResultsTable($account_number, $positions){
		global $adb;
//		$query = "DELETE FROM vtiger_audit_results WHERE account_number = ?";
//		$adb->pquery($query, array());

		foreach($positions AS $k => $v) {
			$query = "INSERT INTO vtiger_audit_results (account_number, security_symbol, csv_quantity, csv_value, last_audit, filename, custodian_type, csv_position_value)
					  VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
			$adb->pquery($query, array($account_number, $v->sSymbol, $v->quantity, $v->value, $v->filename, $v->custodian_type, $v->position_value));
		}
	}

	private function WritePortfolioCSVOnlyToResultsTable($account_number, $portfolios){
		global $adb;

		foreach($portfolios AS $k => $v) {
			$query = "INSERT INTO vtiger_audit_portfolios (account_number, total_value, market_value, cash_value, last_audit, filename, custodian)
					  VALUES (?, ?, ?, ?, NOW(), ?, ?)
					  ON DUPLICATE KEY UPDATE total_value = VALUES(total_value), market_value = VALUES(market_value), cash_value = VALUES(cash_value), last_audit = VALUES(last_audit), filename=VALUES(filename), custodian=VALUES(custodian)";
			$adb->pquery($query, array($account_number, $v->total_value, $v->market_value, $v->cash_value, $v->filename, $v->custodian));
		}
	}

	private function WriteToAuditResultsTable($account_number, $positions){
		global $adb;
		self::CreateCompareTable($account_number, $positions);
		$query = "DELETE FROM vtiger_audit_results WHERE account_number = ?";
		$adb->pquery($query, array($account_number));

		$query = "INSERT INTO vtiger_audit_results
					  SELECT oc.account_number, oc.symbol AS security_symbol, oc.quantity AS CheckQuantity, oc.cash AS CheckCash, IFNULL(p.quantity,0) AS quantity, IFNULL(p.current_value,0) AS current_value, NOW()
					  FROM vtiger_positioninformation p
					  JOIN vtiger_positioninformationcf cf ON p.positioninformationid = cf.positioninformationid
					  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
					  RIGHT OUTER JOIN OmniCompare oc ON oc.symbol = p.security_symbol
					  WHERE p.account_number = ?
					  AND e.deleted = 0
					  AND p.quantity != 0
					  OR (oc.symbol NOT IN (SELECT security_symbol FROM vtiger_positioninformation p WHERE p.account_number = oc.account_number))
				  ON DUPLICATE KEY UPDATE csv_quantity = VALUES(csv_quantity), csv_value = VALUES(csv_value), omni_quantity = VALUES(omni_quantity), omni_value = VALUES(omni_value), last_audit = NOW()";
		$adb->pquery($query, array($account_number));
	}

	private function OmniPositionsComparison($account_number, $positions){
		global $adb;
		self::CreateCompareTable($account_number, $positions);

		$query = "SELECT oc.account_number, oc.symbol AS security_symbol, IFNULL(p.quantity,0) AS quantity, IFNULL(p.current_value,0) AS current_value, oc.quantity AS CheckQuantity, oc.cash AS CheckCash
				  FROM vtiger_positioninformation p
				  JOIN vtiger_positioninformationcf cf ON p.positioninformationid = cf.positioninformationid
				  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
				  RIGHT OUTER JOIN OmniCompare oc ON oc.symbol = p.security_symbol
				  WHERE p.account_number = ?
				  AND e.deleted = 0
				  AND p.quantity != 0
				  OR (oc.symbol NOT IN (SELECT security_symbol FROM vtiger_positioninformation p WHERE p.account_number = oc.account_number))";
		$result = $adb->pquery($query, array($account_number));
		if($adb->num_rows($result) > 0){
			$final_positions = array();
			foreach($result AS $k => $v){
				$final_positions[] = $v;
			}
			return $final_positions;
		}
		return 0;
	}
}

/*
 * 1-877-322-7849
 * Get access code
 */