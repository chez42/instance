<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 1:48 PM
 */
include_once("include/utils/omniscientCustom.php");

class PositionInformation_ConvertCustodian_Model extends Vtiger_Module_Model{
	static $tenant = "custodian_omniscient";

	static public function GetSecurityTypeMapping(){
		global $adb;
		$query = "SELECT code, asset_class, type FROM vtiger_security_mapping";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[$v['code']] = $v;
			}
			return $tmp;
		}
		return 0;
	}

	/**
	 * Gets the positions from Fidelity for the specified date and enters them into the system
	 * @param $date
	 */
	static public function PullNewPositionsFidelity($date){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());

		//WHERE NOT EXISTS (SELECT 1 FROM vtiger_positioninformation p WHERE f.symbol = p.security_symbol AND REPLACE(f.account_number, '-', '') = p.dashless)   old WHERE that works, new should be faster
		$query = "CREATE TEMPORARY TABLE temp_positions
					SELECT 0 AS crmid, f.*, map.*, SUM(f.trade_date_quantity) AS sum_quantity FROM {$tenant}.custodian_positions_fidelity f
					JOIN vtiger_security_mapping map ON f.security_type_code = map.code
					WHERE (f.account_number, f.symbol) NOT IN (SELECT dashless, security_symbol FROM vtiger_positioninformation)
					AND f.as_of_date = ?
					GROUP BY account_number, symbol";
		$adb->pquery($query, array($date));

		$query = "UPDATE temp_positions SET crmid = IncreaseAndReturnCrmEntitySequence()";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, setype, createdtime, modifiedtime, presence, label)
				  SELECT crmid, 1 AS smcreatorid, 1 AS smownerid, 'PositionInformation' AS setype, NOW() AS createdtime, NOW() AS modifiedtime, 1 AS presence, description AS label FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, quantity, last_price, current_value, dashless)
				  SELECT crmid, symbol, description, account_number, sum_quantity, close_price, sum_quantity * close_price AS current_value, REPLACE(account_number, '-', '') AS dashless FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformationcf (positioninformationid, security_type, asset_class)
				  SELECT crmid, type, asset_class FROM temp_positions";
		$adb->pquery($query, array());
	}

	static public function PullNewPositionsSchwab($date){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());

		//WHERE NOT EXISTS (SELECT 1 FROM vtiger_positioninformation p WHERE f.symbol = p.security_symbol AND REPLACE(f.account_number, '-', '') = p.dashless)   old WHERE that works, new should be faster
		$query = "CREATE TEMPORARY TABLE temp_positions
				  SELECT 0 AS crmid, f.*, s.description1 AS description, pr.security_type, SUM(f.quantity) AS sum_quantity, pr.price
				  FROM {$tenant}.custodian_positions_schwab f
				  LEFT JOIN {$tenant}.custodian_securities_schwab s ON s.symbol = f.symbol
				  LEFT JOIN {$tenant}.custodian_prices_schwab pr ON pr.symbol = LEFT(f.symbol, 8) 
				  			AND pr.date = (SELECT MAX(date) FROM custodian_omniscient.custodian_prices_schwab WHERE symbol = f.symbol)
				  WHERE (f.account_number, f.symbol) NOT IN (SELECT dashless, security_symbol FROM vtiger_positioninformation)
				  AND f.date = ?
				  AND f.symbol NOT IN ('CASH02','CASH03','CASH04','CASH05','CASH06','CASH08','CASH09','CASH10','CASH11','CASH12','CASH13','CASH14','CASH15','CASH16','CASH17','CASH18','CASH19','CASH20','CASH21')
				  GROUP BY account_number, symbol";
		$adb->pquery($query, array($date));

		$query = "UPDATE temp_positions SET symbol = 'SCASH', description = 'SCASH', price=1 WHERE symbol IN ('CASH01', 'CASH07')";
		$adb->pquery($query, array());

		$query = "DELETE FROM temp_positions WHERE (account_number, symbol) IN (SELECT dashless, security_symbol FROM vtiger_positioninformation)";
		$adb->pquery($query, array());

		$query = "DELETE FROM temp_positions WHERE original_symbol = 'CASH07'";
		$adb->pquery($query, array());

		$query = "UPDATE temp_positions SET crmid = IncreaseAndReturnCrmEntitySequence()";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, setype, createdtime, modifiedtime, presence, label)
				  SELECT crmid, 1 AS smcreatorid, 1 AS smownerid, 'PositionInformation' AS setype, NOW() AS createdtime, NOW() AS modifiedtime, 1 AS presence, description AS label FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, quantity, last_price, current_value, dashless)
				  SELECT crmid, symbol, description, account_number, sum_quantity, price, sum_quantity * price AS current_value, REPLACE(account_number, '-', '') AS dashless FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformationcf (positioninformationid, security_type)
				  SELECT crmid, security_type FROM temp_positions";
		$adb->pquery($query, array());
	}

	static public function PullNewPositionsTD($date){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());
//					WHERE NOT EXISTS (SELECT 1 FROM vtiger_positioninformation p WHERE f.symbol = p.security_symbol AND f.account_number = p.account_number)  OLD WHERE that works, new should be faster
		$query = "CREATE TEMPORARY TABLE temp_positions
					SELECT 0 AS crmid, f.*, map.*, SUM(f.quantity) AS sum_quantity, cptd.factor, cptd.price FROM {$tenant}.custodian_positions_td f
					JOIN vtiger_security_mapping_td map ON f.security_type = map.code
					LEFT JOIN custodian_omniscient.custodian_prices_td cptd ON cptd.symbol = f.symbol AND cptd.date = ?
					WHERE (f.account_number, f.symbol) NOT IN (SELECT account_number, security_symbol FROM vtiger_positioninformation)
					AND f.date = ?
					GROUP BY account_number, symbol";
		$adb->pquery($query, array($date, $date));

        $query = "UPDATE temp_positions SET symbol = 'TDCASH' WHERE symbol = 'CASH'";
        $adb->pquery($query, array());

        $query = "DELETE FROM temp_positions WHERE (account_number, symbol) IN (SELECT dashless, security_symbol FROM vtiger_positioninformation);";
        $adb->pquery($query, array());

		$query = "UPDATE temp_positions SET crmid = IncreaseAndReturnCrmEntitySequence()";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, setype, createdtime, modifiedtime, presence, label)
				  SELECT crmid, 1 AS smcreatorid, 1 AS smownerid, 'PositionInformation' AS setype, NOW() AS createdtime, NOW() AS modifiedtime, 1 AS presence, symbol AS label FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, quantity, last_price, current_value, dashless)
				  SELECT crmid, symbol, symbol, account_number, sum_quantity, price, sum_quantity * price AS current_value, REPLACE(account_number, '-', '') AS dashless FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformationcf (positioninformationid, security_type, asset_class)
				  SELECT crmid, type, asset_class FROM temp_positions";
		$adb->pquery($query, array());
	}

	static public function DeleteDashedAccountPositions($accounts){
		global $adb;
		if(is_array($accounts))
			$account_numbers = $accounts;
		else
			$account_numbers = $accounts;

		$questions = generateQuestionMarks($account_numbers);

		$query = "DROP TABLE IF EXISTS to_delete_entities";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE to_delete_entities
				  SELECT positioninformationid FROM vtiger_positioninformation 
				  WHERE account_number LIKE ('%-%') AND REPLACE (account_number, '-', '') IN 
					(SELECT account_number FROM custodian_omniscient.custodian_positions_fidelity WHERE as_of_date = (CURDATE() - INTERVAL 1 DAY) GROUP BY account_number)
				  AND account_number IN ({$questions})";
		$adb->pquery($query, array($account_numbers));


	}

	/**
	 * Gets the positions from Fidelity for the specified date and enters them into the system
	 * @param $date
	 */
	static public function PullNewPositionsPershing($date){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE temp_positions
		SELECT 0 AS crmid, f.*, CONCAT(f.quantity_sign, 1)*f.quantity/100000 AS sum_quantity, CONCAT(f.position_value_sign, 1)*f.position_value/1000 AS sum_value, 0 AS close_price FROM {$tenant}.custodian_positions_pershing f
		WHERE NOT EXISTS (SELECT 1 FROM vtiger_positioninformation p WHERE CASE WHEN f.symbol = 'USD999997' THEN 'PCASH' ELSE f.symbol END = p.security_symbol AND REPLACE(f.account_number, '-', '') = p.dashless)
		AND f.position_date = ?
		GROUP BY account_number, symbol";
		$adb->pquery($query, array($date));

		$query = "UPDATE temp_positions SET symbol = 'PCASH' WHERE symbol = 'USD999997'";
		$adb->pquery($query, array());

        $query = "DELETE FROM temp_positions WHERE (account_number, symbol) IN (SELECT dashless, security_symbol FROM vtiger_positioninformation)";
        $adb->pquery($query, array());

        $query = "INSERT INTO temp_positions(account_number, symbol, quantity, position_value, position_date, cusip, sum_quantity, sum_value, close_price)
                  SELECT account_number, fund_mnemonic, principal, principal, date, cusip, principal/100, principal/100, 1
                  FROM {$tenant}.custodian_money_pershing 
                  WHERE (account_number, fund_mnemonic) NOT IN (SELECT dashless, security_symbol FROM vtiger_positioninformation)
                  AND date = ?";
        $adb->pquery($query, array($date));

		$query = "UPDATE temp_positions SET crmid = IncreaseAndReturnCrmEntitySequence(), close_price = sum_value / sum_quantity";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, setype, createdtime, modifiedtime, presence, label)
				  SELECT crmid, 1 AS smcreatorid, 1 AS smownerid, 'PositionInformation' AS setype, NOW() AS createdtime, NOW() AS modifiedtime, 1 AS presence, symbol AS label FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, quantity, last_price, current_value, dashless)
				  SELECT crmid, symbol, symbol AS description, account_number, sum_quantity, close_price, sum_value, REPLACE(account_number, '-', '') AS dashless FROM temp_positions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_positioninformationcf (positioninformationid, security_type, asset_class)
				  SELECT crmid, 'unknown' AS type, 'unknown' AS class FROM temp_positions";
		$adb->pquery($query, array());
	}
	/**
	 *
	 * @param $custodian
	 * @param $date
	 * @param $comparitor
	 * @return array|int
	 */
	static public function GetNewPositions($custodian, $date, $comparitor){
		global $adb;
		$tenant = self::$tenant;
//		$query = "SELECT * FROM {$tenant}.custodian_positions_{$custodian} WHERE as_of_date {$comparitor} ?";
		$query = "SELECT * FROM {$tenant}.custodian_positions_{$custodian} f WHERE f.symbol NOT IN (SELECT security_symbol FROM vtiger_positioninformation WHERE REPLACE(account_number, '-', '') = REPLACE(f.account_number, '-', '') ) AND f.as_of_date = ? LIMIT 2000";
		$result = $adb->pquery($query, array($date));
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[] = $v;
			}
			return $tmp;
		}
		return 0;
	}

	/**
	 * Check if the security symbol already exists
	 * @param $original_id
	 * @param $custodian
	 */
	static public function DoesPositionAlreadyExist($account_number, $symbol){
		global $adb;
		$query = "SELECT positioninformationid FROM vtiger_positioninformation p JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid WHERE REPLACE(account_number, '-', '') = ? AND security_symbol = ? AND e.deleted = 0";
		$result = $adb->pquery($query, array($account_number, $symbol));
		if($adb->num_rows($result) > 0){
			return $adb->query_result($result, 0, 'positioninformationid');
		}
		return 0;
	}

	static private function UseTDRules(&$custodian, &$security_type_map, &$cloudData, &$data){
		$data['security_name'] = $cloudData['description'];
		$data['security_symbol'] = $cloudData['symbol'];
		$data['aclass'] = $security_type_map[$cloudData['type']]['asset_class'];
		$data['securitytype'] = $security_type_map[$cloudData['type']]['type'];
#		$data['cusip'] = $cloudData['cusip'];
		$data['interest_rate'] = $cloudData['interest_rate'];
		$data['maturity_date'] = $cloudData['maturity'];
	}

	static private function UseFidelityRules(&$custodian, &$security_type_map, &$cloudData, &$data){
		$data['account_number'] = $cloudData['account_number'];
		$data['description'] = $cloudData['description'];
		$data['security_symbol'] = $cloudData['symbol'];
		$data['quantity'] = $cloudData['trade_date_quantity'];
		$data['last_price'] = $cloudData['close_price'];
		$data['current_value'] = $cloudData['quantity'] * $cloudData['close_price'];
		$data['asset_class'] = $security_type_map[$cloudData['type']]['asset_class'];
		$data['security_type'] = $security_type_map[$cloudData['type']]['type'];
	}

	/**
	 * Maps the Custodian data to be compatible with the Transactions module
	 * @param $custodian
	 * @param $$security_type_map
	 * @param $cloudData
	 * @param $data
	 */
	static private function MapCloudToModuleData(&$custodian, &$security_type_map, &$cloudData, &$data){
		switch($custodian){
			case "td":
			case "millenium":
				self::UseTDRules($custodian, $security_type_map, $cloudData, $data);
				break;
			case "fidelity":
				self::UseFidelityRules($custodian, $security_type_map, $cloudData, $data);
				break;
			case "omniscient":
				self::UseOmniscientRules($custodian, $security_type_map, $cloudData, $data);
				break;
		}
	}

	/**
	 * Reset the quantity, value, etc of the positions for the specified account
	 * @param $account_number
	 */
	static public function ClearPositionInfoByAccount($account_number){
		global $adb;
		$account_number = RemoveDashes($account_number);
		$questions = generateQuestionMarks($account_number);
		$query = "UPDATE vtiger_positioninformation p 
				  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0, e.deleted = 0
				  WHERE dashless IN ({$questions})";
		$adb->pquery($query, array($account_number));
	}
	
	/**
	 * Zero's out all position values for the given custodian
	 * @param $custodian
	 * @param $date
	 * @param null $account_number
	 */
	static public function ClearPositionInfoByCustodian($custodian, $date, $account_number=null){
		global $adb;
		if(strlen($account_number) > 5)
			$and = " AND replace(account_number, '-', '') = replace(?, '-', '')";
		$tenant = self::$tenant;

		$query = "UPDATE IGNORE vtiger_positioninformation SET dashless = REPLACE(account_number, '-', '') WHERE dashless is null";
		$adb->pquery($query, array());

		$query = "DROP TABLE IF EXISTS SetZero;";
		$adb->pquery($query, array());

		if(strlen($account_number) > 5)
			$where = "WHERE dashless = REPLACE(?, '-', '')";
		else
			$where = "WHERE dashless IN (SELECT account_number from {$tenant}.custodian_positions_{$custodian} WHERE as_of_date = ?) ";
		$query = "CREATE TEMPORARY TABLE SetZero
			  	  SELECT p.positioninformationid
				  FROM vtiger_positioninformation p
				  {$where}";
		if(strlen($account_number) > 5)
			$adb->pquery($query, array($account_number));
		else
			$adb->pquery($query, array($date));

		$query = "UPDATE vtiger_positioninformation p
				  JOIN SetZero z USING (positioninformationid)
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0";
		$adb->pquery($query, array());
	}

	static public function UpdatePositionInformationTD($date, $account_number=null, $checkmultidate=0){
		global $adb;
		$tenant = self::$tenant;
		if($date == null){
			$date = date( "Y-m-d", strtotime("today -1 Weekday") );
		}
		$params = array();

		//If there are no positions for the date in question, we assume a bad date.  IE: after 5:00PM, today -1 weekday thinks it is today not yesterday
		if(self::IsTherePositionDataForDate("td", $date) == 0) {
			if($checkmultidate){
				$date = date( "Y-m-d", strtotime("today -2 Weekday") );
				if(self::IsTherePositionDataForDate("td", $date) == 0)
					return;
			}else
				return;
		}
		$params[] = $date;
		$params[] = $date;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());

#		$account_number = str_replace('-', '', $account_number);
		if($account_number) {
			$account_number = RemoveDashes($account_number);
			$questions = generateQuestionMarks($account_number);
			$and = " AND REPLACE(f.account_number, '-', '') IN ({$questions})";
			self::ClearPositionInfoByAccount($account_number);
			$params[] = $account_number;
		}

		$query = "CREATE TEMPORARY TABLE temp_positions
		SELECT f.*, cptd.price, map.*, p.dashless, CASE WHEN (factor = 0) THEN 1 WHEN factor != 0 THEN factor END AS cf, CASE WHEN (quantity = 0) THEN SUM(f.amount) ELSE SUM(f.quantity) END AS sum_quantity 
		FROM custodian_omniscient.custodian_positions_td f
		LEFT JOIN custodian_omniscient.custodian_prices_td cptd ON f.symbol = cptd.symbol AND cptd.date = ?
		JOIN vtiger_portfolioinformation p ON p.dashless = REPLACE(f.account_number, '-', '')
		JOIN vtiger_security_mapping_td map ON f.security_type = map.code
		WHERE f.date = ? {$and}
		GROUP BY f.account_number, f.symbol";
		$adb->pquery($query, $params);

		$query = "UPDATE temp_positions SET symbol = 'TDCASH' WHERE symbol = 'CASH'";
		$adb->pquery($query, array());

		$query = "UPDATE temp_positions SET price = 1, cf = 1 WHERE price is null";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_positioninformation p 
				  JOIN temp_positions tp ON p.dashless = tp.dashless
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_positioninformation p
				  JOIN temp_positions f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  SET p.quantity = f.sum_quantity, p.last_price = m.security_price * cf.security_price_adjustment, p.current_value = f.sum_quantity * m.security_price * cf.security_price_adjustment";

		$adb->pquery($query, array());
	}

	static public function SetDashless(){
		global $adb;
		$query = "UPDATE IGNORE vtiger_positioninformation SET dashless = replace(account_number, '-', '')";
		$adb->pquery($query, array());
	}

	static public function RemoveDupes($accounts=null)
	{
		global $adb;
		$query = "DROP TABLE IF EXISTS to_delete_entities";
		$adb->pquery($query, array());
		if ($accounts) {
			if (is_array($accounts))
				$account_numbers = $accounts;
			else
				$account_numbers[] = $accounts;

			$account_numbers = RemoveDashes($account_numbers);
			$questions = generateQuestionMarks($account_numbers);

			$query = "CREATE TEMPORARY TABLE to_delete_entities
					  SELECT p1.positioninformationid FROM vtiger_positioninformation p1, vtiger_positioninformation p2 
					  WHERE p1.positioninformationid > p2.positioninformationid AND p1.dashless = p2.dashless AND p1.security_symbol = p2.security_symbol
					  AND p1.dashless IN ({$questions})";
			$adb->pquery($query, array($account_numbers));
		} else {
			$query = "CREATE TEMPORARY TABLE to_delete_entities
					  SELECT p1.positioninformationid FROM vtiger_positioninformation p1, vtiger_positioninformation p2 
					  WHERE p1.positioninformationid > p2.positioninformationid AND p1.dashless = p2.dashless AND p1.security_symbol = p2.security_symbol";
			$adb->pquery($query, array());
		}

		$query = "DELETE FROM vtiger_positioninformation WHERE positioninformationid IN (SELECT positioninformationid FROM to_delete_entities)";
		$adb->pquery($query, array());

		$query = "DELETE FROM vtiger_positioninformationcf WHERE positioninformationid IN (SELECT positioninformationid FROM to_delete_entities)";
		$adb->pquery($query, array());

		$query = "DELETE FROM vtiger_crmentity WHERE positioninformationid IN (SELECT positioninformationid FROM to_delete_entities) AND setype = 'positioninformation'";
		$adb->pquery($query, array());
	}

	/**
	 * Check if there are any positions in the custodian positions table for the given date
	 * @param $date
	 */
	static public function IsTherePositionDataForDate($custodian, $date, $accounts=null){
		global $adb;
		$tenant = self::$tenant;
		$params[] = $date;
		$questions = generateQuestionMarks($accounts);
		if($accounts){
			$acc = RemoveDashes($accounts);
			$and = " AND account_number IN ({$questions}) ";
			$params[] = $acc;
		}
		$datefield = "as_of_date";
		switch($custodian){
			case "fidelity":
				$datefield = "as_of_date";
				break;
			case "td":
			case "schwab":
				$datefield = "date";
				break;
			case "pershing":
				$datefield = "position_date";
				break;
		}
		$query = "SELECT COUNT(*) AS count FROM {$tenant}.custodian_positions_{$custodian} f WHERE f.{$datefield} = ? {$and}";
		$result = $adb->pquery($query, $params);
		if($adb->num_rows($result) > 0 )
			return $adb->query_result($result, 0, "count");
		return 0;
	}

	/**
	 * Clears the position information table for the given accounts
	 * @param $accounts
	 */
	static public function ClearPositionsForAccounts($accounts){
		global $adb;
		if($accounts) {
			$accounts = RemoveDashes($accounts);
			$questions = generateQuestionMarks($accounts);

			$params = $accounts;
			$query = "UPDATE vtiger_positioninformation p 
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0
				  WHERE account_number IN ({$questions})";
			$adb->pquery($query, $params);
		}
	}
	/**
	 * Update PositionInformation from Fidelity based on the given date.  If no date given (null), then the last working day is used
	 * @param $date
	 * @param null $account_number
	 */
	static public function UpdatePositionInformationFidelity($date, $account_number=null, $checkmultidate=0){
		global $adb;
		$tenant = self::$tenant;
		if($date == null){
			$date = date( "Y-m-d", strtotime("today -1 Weekday") );
		}
		$params = array();

		//If there are no positions for the date in question, we assume a bad date.  IE: after 5:00PM, today -1 weekday thinks it is today not yesterday
		if(self::IsTherePositionDataForDate("fidelity", $date) == 0) {
			if($checkmultidate){
				$date = date( "Y-m-d", strtotime("today -2 Weekday") );
				if(self::IsTherePositionDataForDate("fidelity", $date) == 0)
					return;
			}else
				return;
		}
		$params[] = $date;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());

#		$account_number = str_replace('-', '', $account_number);
		if($account_number) {
			$account_number = RemoveDashes($account_number);
			$questions = generateQuestionMarks($account_number);
			$and = " AND REPLACE(f.account_number, '-', '') IN ({$questions})";
			self::ClearPositionInfoByAccount($account_number);
			$params[] = $account_number;
		}

		$query = "CREATE TEMPORARY TABLE temp_positions
		SELECT f.*, map.*, p.dashless, CASE WHEN (current_factor = 0) THEN 1 WHEN current_factor != 0 THEN 1 END AS cf, SUM(f.trade_date_quantity) AS sum_quantity 
		FROM {$tenant}.custodian_positions_fidelity f
		JOIN vtiger_portfolioinformation p ON p.dashless = REPLACE(f.account_number, '-', '')
		JOIN vtiger_security_mapping map ON f.security_type_code = map.code
		WHERE f.as_of_date = ? {$and}
		GROUP BY account_number, symbol";
		$adb->pquery($query, $params);
		$query = "UPDATE vtiger_positioninformation p 
				  JOIN temp_positions tp ON p.dashless = tp.dashless
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_positioninformation p
				  JOIN temp_positions f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  SET p.quantity = f.sum_quantity, p.last_price = m.security_price * cf.security_price_adjustment, p.current_value = f.sum_quantity * m.security_price * cf.security_price_adjustment";
		$adb->pquery($query, array());

/*		if($account_number)
			$where = " WHERE REPLACE(account_number, '', '') = ?";
		$query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  JOIN temp_positions tp ON p.dashless = tp.dashless
				  SET p.last_price = p.last_price * cf.security_price_adjustment, p.current_value = p.quantity * p.last_price
				  {$where}";
		if($account_number)
			$adb->pquery($query, array($account_number));
		else
			$adb->pquery($query, array());
*/
		/*
		 * IN CASE FACTOR COMES INTO PLAY.. FIDELITY DOESN'T USE IT!
		CASE WHEN (cf.factor > 0) THEN cf.security_price_adjustment * cf.factor * f.trade_date_quantity * f.close_price
		ELSE cf.security_price_adjustment * f.trade_date_quantity * f.close_price END
		 */
	}

	/**
	 * Update PositionInformation from Schwab based on the given date.  If no date given (null), then the last working day is used
	 * @param $date
	 * @param null $account_number
	 * @param int $checkmultidate
	 */
	static public function UpdatePositionInformationSchwab($date, $account_number=null, $checkmultidate=0){
		global $adb;
		$tenant = self::$tenant;
		if($date == null){
			$date = date( "Y-m-d", strtotime("today -1 Weekday") );
		}
		$params = array();

		//If there are no positions for the date in question, we assume a bad date.  IE: after 5:00PM, today -1 weekday thinks it is today not yesterday
		if(self::IsTherePositionDataForDate("schwab", $date) == 0) {
			if($checkmultidate){
				$date = date( "Y-m-d", strtotime("today -2 Weekday") );
				if(self::IsTherePositionDataForDate("schwab", $date) == 0)
					return;
			}else
				return;
		}
		$params[] = $date;
		$params[] = $date;

		$query = "DROP TABLE IF EXISTS temp_positions";
		$adb->pquery($query, array());
		$query = "DROP TABLE IF EXISTS temp_positions_copy";
		$adb->pquery($query, array());

#		$account_number = str_replace('-', '', $account_number);
		if($account_number) {
			$account_number = RemoveDashes($account_number);
			$questions = generateQuestionMarks($account_number);
			$and = " AND REPLACE(f.account_number, '-', '') IN ({$questions})";
			self::ClearPositionInfoByAccount($account_number);
			$params[] = $account_number;
		}

		$query = "CREATE TEMPORARY TABLE temp_positions
				  SELECT 0 AS crmid, f.symbol,
                  f.account_number, f.account_type, f.long_short, f.quantity, f.date, f.filename, f.original_symbol, f.record_type, f.custodian_id, f.master_account_number, 
                  f.master_account_name, f.business_date, f.product_code, f.product_category_code, f.tax_code, f.legacy_security_type, f.ticker_symbol, f.industry_ticker_symbol, 
                  f.schwab_security_number, f.item_issue_id, f.rule_set_suffix_id, f.isin, f.sedol, f.options_display_symbol, f.security_description_line1, 
                  f.security_description_line2, f.security_description_line3, f.security_description_line4, f.underlying_ticker_symbol, f.underlying_industry_ticker_symbol, 
                  f.underlying_cusip, f.underlying_schwab_security, f.underlying_item_issue_id, f.underlying_rule_set_suffix_id, f.underlying_isin, f.underlying_sedol, 
                  f.money_market_code, f.dividend_reinvest, f.capital_gains_reinvest, f.closing_price, f.security_price_update_date, f.long_short_indicator, 
                  f.market_value_settled_and_unsettled, f.accounting_rule_code, f.quantity_settled, f.quantity_unsettled_long, f.quantity_unsettled_short, f.version_marker1, 
                  f.tips_factor, f.asset_backed_factor, f.version_marker2, f.closing_price_unfactored, f.factor, f.factor_date, f.file_date, f.insert_date, 
                  f.quantity_settled_and_unsettled, SUM(f.quantity) AS sum_quantity, pr.price, 1 AS cf, p.dashless, sec.cusip AS cusip, sec.sec_nbr, CASE WHEN sec.symbol != '' THEN sec.symbol ELSE sec.cusip END AS symbolOverride
				  FROM custodian_omniscient.custodian_positions_schwab f
				  LEFT JOIN custodian_omniscient.custodian_securities_schwab sec ON (sec.symbol = f.symbol OR sec.cusip = f.symbol OR sec.sec_nbr = f.symbol)
				  JOIN vtiger_portfolioinformation p ON p.dashless = REPLACE(f.account_number, '-', '')
				  LEFT JOIN custodian_omniscient.custodian_prices_schwab pr ON pr.symbol = LEFT(f.symbol, 8) AND pr.date = ?
				  WHERE f.date = ?
				  AND f.symbol NOT IN ('CASH02','CASH04','CASH05','CASH06','CASH08','CASH09','CASH10','CASH11','CASH12','CASH13','CASH14','CASH15','CASH16','CASH17','CASH18','CASH19','CASH20','CASH21')
				  {$and}
				  GROUP BY account_number, symbol";
		$adb->pquery($query, $params);

		$query = "UPDATE temp_positions SET symbol = 'SCASH', price=1 WHERE symbol IN ('CASH01', 'CASH03', 'CASH07')";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE temp_positions_copy
				  SELECT * FROM temp_positions";
		$adb->pquery($query, array());

		$query = "UPDATE temp_positions tp SET sum_quantity = (SELECT SUM(sum_quantity) FROM temp_positions_copy c WHERE symbol IN ('SCASH') AND tp.account_number = c.account_number) WHERE symbol = 'SCASH'";
		$adb->pquery($query, array());

		$query = "UPDATE temp_positions tp
                  SET symbol = CASE WHEN sec_nbr is not null THEN symbolOverride
	              ELSE symbol END";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_positioninformation p 
				  JOIN temp_positions tp ON p.dashless = tp.dashless
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0";
		$adb->pquery($query, array());

		$query = "UPDATE IGNORE vtiger_positioninformation p
                  JOIN temp_positions f ON p.dashless = REPLACE(f.account_number, '-', '')
                  SET p.security_symbol = f.symbol
                  WHERE p.security_symbol != f.symbol AND p.security_symbol = f.original_symbol;";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_positioninformation p
				  JOIN temp_positions f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  SET p.quantity = f.sum_quantity, 
				  p.last_price = m.security_price * cf.security_price_adjustment,
				  p.current_value = m.security_price * cf.security_price_adjustment * f.sum_quantity";

//#				  p.last_price = CASE WHEN (cf.factor = 0) THEN 1 ELSE cf.factor END * f.price * cf.security_price_adjustment,
//#				  p.current_value = CASE WHEN (cf.factor = 0) THEN 1 ELSE cf.factor END * f.price * f.sum_quantity * cf.security_price_adjustment";
//CASE WHEN (current_factor = 0) THEN 1 WHEN current_factor != 0 THEN 1 END AS cf
		$adb->pquery($query, array());
	}

	/**
	 * This function hits pershing balances table and pulls the cash amount
	 * @param $account_number
	 * @param $date
	 */
	static public function GetCashCloudPershing($account_number, $date){
		global $adb;
		$tenant = self::$tenant;

		$query = "SELECT total_usde/100 AS amount FROM {$tenant}.custodian_balances_pershing WHERE account_number=? AND date=?";
		$result = $adb->pquery($query, array($account_number, $date));
		if($adb->num_rows($result) > 0){
			$amount = $adb->query_result($result, 0, 'amount');
		}
		return $amount;
	}

	/**
	 * Returns the record ID of the position based on symbol/account number
	 * @param $symbol
	 * @param $account_number
	 * @return int|mixed|string
	 */
	static public function GetPositionIDBySymbol($symbol, $account_number){
		global $adb;
		$query = "SELECT positioninformationid FROM vtiger_positioninformation WHERE security_symbol = ? AND (account_number = ? OR dashless = ?)";
		$result = $adb->pquery($query, array($symbol, $account_number, $account_number));
		if($adb->num_rows($result) > 0)
			return $adb->query_result($result, 0, 'positioninformationid');
		return 0;
	}

	static public function UpdatePositionInformationPershing($date, $account_number=null, $checkmultidate=0){
        global $adb;
        $tenant = self::$tenant;
        if($date == null){
            $date = date( "Y-m-d", strtotime("today -1 Weekday") );
        }
        $params = array();

        //If there are no positions for the date in question, we assume a bad date.  IE: after 5:00PM, today -1 weekday thinks it is today not yesterday
        if(self::IsTherePositionDataForDate("pershing", $date) == 0) {
            if($checkmultidate){
                $date = date( "Y-m-d", strtotime("today -2 Weekday") );
                if(self::IsTherePositionDataForDate("pershing", $date) == 0)
                    return;
            }else
                return;
        }
        $params[] = $date;

        $query = "DROP TABLE IF EXISTS temp_positions";
        $adb->pquery($query, array());

#		$account_number = str_replace('-', '', $account_number);
        if($account_number) {
            $account_number = RemoveDashes($account_number);
            $questions = generateQuestionMarks($account_number);
            $and = " AND REPLACE(f.account_number, '-', '') IN ({$questions})";
            self::ClearPositionInfoByAccount($account_number);
            $params[] = $account_number;
        }

        $query = "CREATE TEMPORARY TABLE temp_positions
                  SELECT 0 AS crmid, f.*, CONCAT(f.quantity_sign, 1)*f.quantity/100000 AS sum_quantity, CONCAT(f.position_value_sign, 1)*f.position_value/1000 AS sum_value,
                  (CONCAT(f.position_value_sign, 1)*f.position_value/1000) / (CONCAT(f.quantity_sign, 1)*f.quantity/100000) AS close_price, REPLACE(f.account_number, '-', '') AS dashless
                  FROM {$tenant}.custodian_positions_pershing f
                  JOIN vtiger_portfolioinformation p ON p.dashless = REPLACE(f.account_number, '-', '')
                  WHERE f.position_date = ? {$and}
                  GROUP BY account_number, symbol";
        $adb->pquery($query, $params);

        $query = "INSERT INTO temp_positions(account_number, symbol, quantity, position_value, position_date, cusip, sum_quantity, sum_value, close_price)
                  SELECT account_number, fund_mnemonic, principal, principal, date, cusip, principal/100, principal/100, 1
                  FROM custodian_omniscient.custodian_money_pershing f 
                  WHERE date = ? {$and}";
        $adb->pquery($query, $params);

        $query = "UPDATE temp_positions SET symbol = 'PCASH', close_price=1 WHERE symbol IN ('USD999997')";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE temp_positions_copy
				  SELECT * FROM temp_positions";
        $adb->pquery($query, array());

        $query = "UPDATE temp_positions tp SET sum_quantity = (SELECT SUM(sum_quantity) FROM temp_positions_copy c WHERE symbol IN ('PCASH') AND tp.account_number = c.account_number) WHERE symbol = 'PCASH'";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_positioninformation p 
				  JOIN temp_positions tp ON p.dashless = tp.dashless
				  SET p.quantity=0, p.last_price=0, p.current_value=0, p.weight=0, p.cost_basis=0, p.unrealized_gain_loss=0, p.gain_loss_percent=0";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_positioninformation p
				  JOIN temp_positions f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  SET p.quantity = f.sum_quantity, 
				  p.last_price = m.security_price * cf.security_price_adjustment,
				  p.current_value = m.security_price * cf.security_price_adjustment * f.sum_quantity";
        $adb->pquery($query, array());
	}

	static public function UpdatePositionInfoByCustodianAllAccounts($custodian, $date){
		global $adb;
		$tenant = self::$tenant;

		$query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_modsecurities s ON s.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON s.modsecuritiesid = cf.modsecuritiesid
				  JOIN {$tenant}.custodian_positions_{$custodian} f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  SET p.quantity = (SELECT SUM(trade_date_quantity) FROM {$tenant}.custodian_positions_{$custodian} WHERE symbol = f.symbol AND account_number = f.account_number AND as_of_date = f.as_of_date), p.last_price = f.close_price,
				  p.current_value = cf.security_price_adjustment * (SELECT SUM(trade_date_quantity) FROM {$tenant}.custodian_positions_{$custodian} WHERE symbol = f.symbol AND p.dashless = REPLACE(f.account_number, '-', '') AND as_of_date = f.as_of_date) * f.close_price
				  WHERE f.as_of_date = ?";
/*
 * IN CASE FACTOR COMES INTO PLAY.. FIDELITY DOESN'T USE IT!
CASE WHEN (cf.factor > 0) THEN cf.security_price_adjustment * cf.factor * f.trade_date_quantity * f.close_price
ELSE cf.security_price_adjustment * f.trade_date_quantity * f.close_price END
 */
		$adb->pquery($query, array($date));
	}

	static public function UpdatePositionInfoByCustodianIndividualAccount($custodian, $date, $account_number){
		global $adb;
		$tenant = self::$tenant;
		$query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_modsecurities s ON s.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON s.modsecuritiesid = cf.modsecuritiesid
				  JOIN {$tenant}.custodian_positions_{$custodian} f ON p.dashless = REPLACE(f.account_number, '-', '') AND p.security_symbol = f.symbol
				  SET p.quantity = (SELECT SUM(trade_date_quantity) FROM {$tenant}.custodian_positions_{$custodian} WHERE symbol = f.symbol AND account_number = f.account_number AND as_of_date = f.as_of_date), p.last_price = f.close_price,
				  p.current_value = cf.security_price_adjustment * (SELECT SUM(trade_date_quantity) FROM {$tenant}.custodian_positions_{$custodian} WHERE symbol = f.symbol AND REPLACE(account_number, '-', '') = REPLACE(f.account_number, '-', '') AND as_of_date = f.as_of_date) * f.close_price
				  WHERE f.as_of_date = ? AND REPLACE(f.account_number, '-', '') = REPLACE(?, '-', '')";
		$adb->pquery($query, array($date, $account_number));
	}

	static public function UpdatePositionInfoFromCloud(&$custodian, &$date, $account_number){
		switch($custodian){
			case "fidelity":
				self::ClearPositionInfoByCustodian($custodian, $date, $account_number);
				break;
		}
	}

	static public function ConvertCustodian($custodian, $date, $comparitor, $account_number){
		self::CloudToModuleConversion($custodian, $date, $comparitor, $account_number);
	}

	static public function UpdatePriceAdjustment(){
		global $adb;
/*		$query = "UPDATE vtiger_modsecurities s
				  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
				  SET security_price_adjustment = 0.01, s.security_price = 1
				  WHERE aclass IN ('Fixed Income')";
		$adb->pquery($query, array());
*/

		$query = "UPDATE vtiger_modsecurities s
				  JOIN vtiger_modsecuritiescf cf USING (modsecuritiesid)
				  SET security_price_adjustment = 1
				  WHERE security_price_adjustment IN (0, '')";
		$adb->pquery($query, array());
	}

	/**
	 * Sets the position ownership to whoever the matching Portfolio is assigned to if the position is assigned to admin
	 */
	static public function SetPositionOwnerShip($admin_only = false){
		global $adb;
		$query = "DROP TABLE IF EXISTS PositionOwners";
		$adb->pquery($query, array());

		if($admin_only)
		    $where = " WHERE e2.smownerid = 1 ";

		$query = "CREATE TEMPORARY TABLE PositionOwners
  				  SELECT p.account_number AS port_account, e.smownerid AS port_owner, pos.account_number AS position_account,  e2.smownerid AS position_owner, e2.crmid AS position_id FROM vtiger_portfolioinformation p
				  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
				  JOIN vtiger_positioninformation pos ON pos.account_number = p.account_number
				  JOIN vtiger_crmentity e2 ON e2.crmid = pos.positioninformationid
				  {$where}";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_crmentity 
				  JOIN PositionOwners ON position_id = crmid
				  SET smownerid = port_owner";
		$adb->pquery($query, array());
	}
	
	static public function UpdateAllPrices(){
		global $adb;
		$query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  SET p.last_price = m.security_price";
		$adb->pquery($query, array());
/*		global $adb;
		$query = "DROP TABLE IF EXISTS tmp_prices";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE tmp_prices
		select * from vtiger_custodian_prices t1
		where trade_date = (select max(trade_date) from vtiger_custodian_prices t2 where t1.symbol = t2.symbol)";
		$adb->pquery($query, array());

		$query = "UPDATE vtiger_modsecurities sec
		  		  JOIN tmp_prices pri ON sec.security_symbol = pri.symbol
		  		  SET sec.security_price = pri.price";
		$adb->pquery($query, array());
*/
	}

	static private function CloudToModuleConversion($custodian, $date, $comparitor, $account_number){
		$security_type_map = self::GetSecurityTypeMapping();
		$positions = self::GetNewPositions($custodian, $date, $comparitor);
		$count = 0;
		if($positions){
			set_time_limit ( 0 );
			foreach($positions AS $k => $v){
#				echo "START OF LOOP: " . memory_get_peak_usage() . " - Count: " . $count . PHP_EOL;
				$record = self::DoesPositionAlreadyExist($v['account_number'], $v['symbol']);
				if($record){//If the record exists, use it instead
					$tmp = Vtiger_Record_Model::getInstanceById($record, "PositionInformation");
					$tmp->set('mode', 'edit');
#					echo "EDIT<br />";
				}else{
					$tmp = Vtiger_Record_Model::getCleanInstance("PositionInformation");
					$tmp->set('mode', 'create');
#					echo "NEW<br />";
					$count++;
				}

				$data = $tmp->getData();
				self::MapCloudToModuleData($custodian, $security_type_map, $v, $data);
				$tmp->setData($data);
				$tmp->save();
			}
		}

		self::ClearPositionInfoByCustodian($custodian, $date, $account_number);
//		self::UpdatePositionInfoFromCloud($custodian, $date, '638-221089');
		self::UpdatePriceAdjustment();
		self::UpdateAllPrices();
		if(strlen($account_number) > 5)
			self::UpdatePositionInfoByCustodianIndividualAccount($custodian, $date, $account_number);
		else
			self::UpdatePositionInfoByCustodianAllAccounts($custodian, $date);

		echo "{$count} Positions Added.  All positions updated to specified date of {$date}";
	}

	static public function SetPositionsDeletedForClosedAccounts(){
	    global $adb;
	    $query = "UPDATE vtiger_crmentity e
                  SET e.deleted = 1 WHERE crmid IN (
                    SELECT positioninformationid
                    FROM vtiger_positioninformation p
                    WHERE account_number IN (SELECT account_number FROM vtiger_portfolioinformation WHERE accountclosed = 1))";
	    $adb->pquery($query, array());
    }
}