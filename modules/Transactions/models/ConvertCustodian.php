<?php
/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-03-29
 * Time: 1:48 PM
 */
class Transactions_ConvertCustodian_Model extends Vtiger_Module_Model{
	static $tenant = "custodian_omniscient";

	static public function GetTransactionTypeMapping(){
		global $adb;
		$query = "SELECT code, transaction_type, transaction_activity, report_as_type FROM vtiger_transaction_type_mapping";
		$result = $adb->pquery($query, array());
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[$v['code']] = array("type" => $v['transaction_type'],
										 "activity" => $v['transaction_activity'],
										 "rat" => $v['report_as_type']);
			}
			return $tmp;
		}
		return 0;
	}

	/**
	 * Comparitor is used with the $date variable.  >= will select all transactions >= the given date.  = is the default pulling only for the date provided
	 * @param $custodian
	 * @param $date
	 * @param $comparitor
	 * @return array|int
	 */
	static public function GetTransactions($custodian, $date, $comparitor="=", $limit = 1000){
		global $adb;

		$tenant = self::$tenant;
		$comparitor = str_replace("&gt;", ">", $comparitor);
		$query = "SELECT * FROM {$tenant}.custodian_transactions_{$custodian} WHERE trade_date {$comparitor} ? LIMIT {$limit}";
		$result = $adb->pquery($query, array($date));
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[] = $v;
			}
			return $tmp;
		}
		return 0;
	}

	static public function GetNewTransactions($custodian, $date, $comparitor="=", $limit = 2000){
		global $adb;

		$tenant = self::$tenant;
		$comparitor = str_replace("&gt;", ">", $comparitor);
		if($custodian == "td")
			$custodian = "TD Ameritrade";

		$query = "SELECT * FROM {$tenant}.custodian_transactions_{$custodian} WHERE trade_date {$comparitor} ? AND transaction_id NOT IN (SELECT original_id FROM vtiger_transactionscf WHERE custodian = ?) LIMIT {$limit}";
		$result = $adb->pquery($query, array($date, $custodian));
		if($adb->num_rows($result) > 0){
			foreach($result AS $k => $v){
				$tmp[] = $v;
			}
			return $tmp;
		}
		return 0;
	}

	/**
	 * Returns the transactions from the omniscient transactions table
	 * @param $custodian
	 * @param $date
	 * @param string $comparitor
	 * @return array|int
	 */
	static public function GetOmniscientTransactions($date, $comparitor="="){
		global $adb;

		$tenant = self::$tenant;
		$comparitor = str_replace("&gt;", ">", $comparitor);
		$query = "SELECT * FROM transactions_transfer_delete_me WHERE trade_date {$comparitor} ? AND transaction_id NOT IN (SELECT original_id FROM vtiger_transactionscf) LIMIT 2000";
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
	 * We need to pass in the custodian because transactions start at 1 for each custodian
	 * @param $original_id
	 * @param $custodian
	 */
	static public function DoesTransactionAlreadyExist($original_id, $custodian){
		global $adb;
		if($custodian == "td")
			$custodian = "TD Ameritrade";
		$query = "SELECT transactionsid FROM vtiger_transactionscf WHERE custodian = ? AND original_id = ?";
		$result = $adb->pquery($query, array($custodian, $original_id));
		if($adb->num_rows($result) > 0){
			return $adb->query_result($result, 0, 'transactionsid');
		}
		return 0;
	}

	/**
	 * Determine the activity information based on the passed in report as type
	 * @param $rat_id
	 */
	static public function DetermineTypeByRat($rat_id){
		global $adb;
		$query = "SELECT transaction_type, transaction_activity FROM vtiger_transaction_type_mapping WHERE report_as_type = ?";
		$result = $adb->pquery($query, array($rat_id));
		if($adb->num_rows($result) > 0){
			$type = $adb->query_result($result, 0, "transaction_type");
			$activity = $adb->query_result($result, 0, "transaction_activity");
			return array("type" => $type,
						 "activity" => $activity);
		}
		return 0;
	}

	/**
	 * The idea behind this function is to return the mapping code based on report as type which can be used to map against the table rather than having to pass back both the transaction type and activity
	 * @param $rat_id
	 * @return array|int
	 * @throws Exception
	 */
	static public function DetermineCodeByRat($rat_id){
		global $adb;
		$query = "SELECT code FROM vtiger_transaction_type_mapping WHERE report_as_type = ?";
		$result = $adb->pquery($query, array($rat_id));
		if($adb->num_rows($result) > 0)
			return $adb->query_result($result, 0, "code");

		return 0;
	}

	static private function UseTDRules(&$custodian, &$transaction_type_map, &$cloudData, &$data){
		$data['account_number'] = $cloudData['account_number'];
		$data['security_symbol'] = $cloudData['symbol'];
//				$data['security_price'] = '';
		$data['quantity'] = $cloudData['quantity'];
		$data['trade_date'] = $cloudData['trade_date'];
		$data['original_id'] = $cloudData['transaction_id'];
		$data['portfolio_id'] = $cloudData['original_id'];
		$data['net_amount'] = $cloudData['net_amount'];
		$data['principal'] = $cloudData['principal'];
		$data['broker_fee'] = $cloudData['broker_fee'];
		$data['other_fee'] = $cloudData['other_fee'];
		$data['rep_code'] = $cloudData['advisor_rep_code'];
		$data['cost_basis_adjustment'] = '';
		$data['security_price'] = Transactions_Module_Model::GetSymbolPriceForDate($cloudData['symbol'], $cloudData['trade_date']);
		$data['assigned_user_id'] = 1;
		if($custodian == "td")
			$data['custodian'] = "TD Ameritrade";
		else
			$data['custodian'] = $custodian;
		if($cloudData['cancel_status_flag'] == "Y")
			$data['cancelled'] = 1;
		$data['transaction_type'] = $transaction_type_map[$cloudData['transaction_code']]['type'];
		$data['transaction_activity'] = $transaction_type_map[$cloudData['transaction_code']]['activity'];
		$data['description'] = $cloudData['comment'];
		$data['filename'] = $cloudData['filename'];
	}

	static public function CreateCashTransaction(){

	}

	static private function UseFidelityRules(&$custodian, &$transaction_type_map, &$cloudData, &$data){
		//If the source is dvsplit, treat it like a regular buy
		//If the source is dvwash,
		//if type FC/MM/MF, create a cash withdrawal for the amount if it is of type buy.  If it is type sell, create a cash deposit for the amount.
		//If the source is cash, do not pay dividend
		$data['account_number'] = $cloudData['account_number'];
		$data['security_symbol'] = $cloudData['symbol'];
//				$data['security_price'] = '';
		$data['quantity'] = $cloudData['quantity'];
		$data['trade_date'] = str_replace(" 00:00:00", "", $cloudData['trade_date']);
		$data['original_id'] = $cloudData['transaction_id'];
		$data['net_amount'] = $cloudData['amount'];
		$data['broker_fee'] = $cloudData['commission'];
		$data['sec_fee'] = $cloudData['sec_fee'];
		$data['other_fee'] = $cloudData['service_charge_misc_fee'];
		$data['rep_code'] = "TBD";//$cloudData['advisor_rep_code'];
		$data['source'] = $cloudData['source'];
		$data['cost_basis_adjustment'] = "0";//$cloudData['cost_basis_adjustment'];
		$data['security_price'] = Transactions_Module_Model::GetSymbolPriceForDate($cloudData['symbol'], $data['trade_date']);
		$data['assigned_user_id'] = 1;
		$data['custodian'] = "Fidelity";
		$data['code'] = $cloudData['key_code'];
		$data['filename'] = $cloudData['filename'];
		if(strtoupper($cloudData['transaction_type']) == $cloudData['transaction_type'])
			$data['cancelled'] = 1;

		$data['transaction_type'] = $transaction_type_map[$cloudData['transaction_type']]['type'];
		$data['transaction_activity'] = $transaction_type_map[$cloudData['transaction_type']]['activity'];

		switch($cloudData['security_type']){
			case 'ex'://Expense
				$data['transaction_type'] = "Expense";
				$data['transaction_activity'] = "Other expenses";
		}
		$data['description'] = $cloudData['key_code_description'];
	}

	static private function UseOmniscientRules(&$custodian, &$transaction_type_map, &$cloudData, &$data){
		$data['account_number'] = $cloudData['account_number'];
		$data['security_symbol'] = $cloudData['security_symbol'];
//				$data['security_price'] = '';
		$data['quantity'] = $cloudData['quantity'];
		$data['trade_date'] = str_replace(" 00:00:00", "", $cloudData['trade_date']);
		$data['original_id'] = $cloudData['transaction_id'];
		$data['portfolio_id'] = $cloudData['portfolio_id'];
		$data['net_amount'] = $cloudData['net_amount'];
		$data['principal'] = $cloudData['principal'];
		$data['broker_fee'] = $cloudData['broker_fee'];
		$data['other_fee'] = $cloudData['other_fee'];
		$data['rep_code'] = "TBD";//$cloudData['advisor_rep_code'];
		$data['cost_basis_adjustment'] = $cloudData['cost_basis_adjustment'];
		$data['security_price'] = Transactions_Module_Model::GetPCPriceForDate($cloudData['security_symbol'], $data['trade_date']);
		$data['assigned_user_id'] = 1;
		$data['custodian'] = "Omniscient";
		if($cloudData['status_type_id'] != 100)
			$data['cancelled'] = 1;

		switch($cloudData['activity_id']){
			case 10://Flow
				if($data['quantity'] < 0)//Less than 0 is a withdrawal, otherwise deposit by default
					$cloudData['activity_id'] = "WITH";
				break;
			case 100://Amortization
				if($data['net_amount'] < 0)//Less than 0 is derease cost basis
					$cloudData['activity_id'] = "DECBA";
				break;
			case 160://Expense
			case 165://Income
				if($transaction_type_map[$cloudData['activity_id']]['rat'] > 0)
					$cloudData['activity_id'] = self::DetermineCodeByRat($transaction_type_map[$cloudData['activity_id']]['rat']);
				break;
		}

		$data['transaction_type'] = $transaction_type_map[$cloudData['activity_id']]['type'];
		$data['transaction_activity'] = $transaction_type_map[$cloudData['activity_id']]['activity'];
		$data['description'] = $cloudData['comment'];
	}

	/**
	 * Maps the Custodian data to be compatible with the Transactions module
	 * @param $custodian
	 * @param $transaction_type_map
	 * @param $cloudData
	 * @param $data
	 */
	static private function MapCloudToModuleData(&$custodian, &$transaction_type_map, &$cloudData, &$data){
		switch($custodian){
			case "td":
			case "millenium":
				self::UseTDRules($custodian, $transaction_type_map, $cloudData, $data);
				break;
			case "fidelity":
				self::UseFidelityRules($custodian, $transaction_type_map, $cloudData, $data);
				break;
			case "omniscient":
				self::UseOmniscientRules($custodian, $transaction_type_map, $cloudData, $data);
				break;
		}
	}

	//Assign rep codes to transactions based on the portfolio they belong to
	static public function UpdateRepCodes(){
	    global $adb;

        $query = "UPDATE vtiger_transactions t
                  JOIN vtiger_transactionscf tcf ON t.transactionsid = tcf.transactionsid
                  JOIN vtiger_portfolioinformation p ON p.account_number = t.account_number
                  JOIN vtiger_portfolioinformationcf pcf ON p.portfolioinformationid = pcf.portfolioinformationid
                  SET tcf.rep_code = pcf.omniscient_control_number, tcf.custodian_control_number = pcf.production_number, tcf.master_control_number = pcf.master_production_number, tcf.rep_codes_updated = 1
                  WHERE tcf.rep_codes_updated = '0'";

	    $adb->pquery($query, array());

        $query = "UPDATE vtiger_positioninformation po
                  JOIN vtiger_positioninformationcf pocf ON po.positioninformationid = pocf.positioninformationid
                  JOIN vtiger_portfolioinformation p ON p.account_number = po.account_number
                  JOIN vtiger_portfolioinformationcf pcf ON p.portfolioinformationid = pcf.portfolioinformationid
                  SET pocf.omniscient_control_number = pcf.omniscient_control_number, pocf.custodian_control_number = pcf.production_number";
        $adb->pquery($query, array());

    }

    static public function AssignTransactionsBasedOnPortfolio(){
        global $adb;

        $query = "CALL REASSIGN_ALL_TRANSACTIONS()";
        $adb->pquery($query, array());
    }

    static public function AssignTransactionsBasedOnPortfolioMinusDays($days){
	    global $adb;

	    $query = "CALL REASSIGN_ALL_TRANSACTIONS_MINUS_DAYS(?)";
        $adb->pquery($query, array($days));
    }

    static public function AssignPositionsBasedOnPortfolio(){
        global $adb;

        $query = "CALL REASSIGN_ALL_POSITIONS()";
        $adb->pquery($query, array());
    }

	static public function ReassignTransactions($account_number = null){
/*	    global $adb;

	    $params = array();

	    if($account_number) {
            $and = " AND r.account_number = ? ";
            $params[] = $account_number;
        }

	    $query = "DROP TABLE IF EXISTS PortfolioOwners";
	    $adb->pquery($query, array());

	    $query = "DROP TABLE IF EXISTS ReplacedTransactions";
	    $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE PortfolioOwners
                  SELECT p.dashless AS account_number, e.smownerid AS portfolio_owner
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE ReplacedTransactions (transactionsid INTEGER, 
					     account_number VARCHAR (255),
					     smownerid INTEGER,
					     INDEX ind(account_number))
                  SELECT transactionsid, account_number, smownerid 
                  FROM vtiger_transactions t
                  JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
                  WHERE e.setype = 'Transactions' AND e.smownerid=1";
        $adb->pquery($query, array());

        $query = "UPDATE ReplacedTransactions SET account_number = REPLACE(REPLACE(account_number, ' ', ''), '*', '')";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_crmentity e
                  JOIN ReplacedTransactions r ON r.transactionsid = e.crmid
                  JOIN PortfolioOwners o ON r.account_number = o.account_number
                  SET e.smownerid = o.portfolio_owner
                  WHERE e.smownerid=1 {$and}";
        $adb->pquery($query, $params);*/
    }

	static public function ConvertCustodian($custodian, $date, $comparitor, $newonly = 0){
		if($newonly == 1){
			switch ($custodian){
				case "fidelity":
					self::PullNewTransactionsFidelity($date, $comparitor);
					echo "Fidelity Pull finished where date {$comparitor} {$date}";
					break;
				case "pershing":
					self::PullNewTransactionsPershing($date, $comparitor);
					echo "Pershing Pull finished where date {$comparitor} {$date}";
					break;
			}
		}else
			self::CloudToModuleConversion($custodian, $date, $comparitor, $newonly);
	}

	static private function PullNewTransactionsPershing($trade_date, $comparitor){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS new_transactions";
		$adb->pquery($query, array());
//Quantity divided by 100,000
//net amount divided by 1,000
//price divided by 1,000,000,000
		$query = "CREATE TEMPORARY TABLE new_transactions
		SELECT account_number AS account_number, CASE WHEN (security_symbol != '') security_symbol ELSE cusip END AS security_symbol, CONCAT(price_in_trade_currency_sign, 1)*price_in_trade_currency/1000000000 AS security_price, CONCAT(quantity_sign, 1)*quantity/100000 AS quantity, trade_date AS trade_date, 0 AS cost_basis_adjustment,
		'pershing' AS custodian, transaction_id AS original_id, 'TBD' AS rep_code, cancel_code AS cancelled, CONCAT(net_amount_sign, 1)*net_amount/1000 AS net_amount, CONCAT(principal_sign, 1)*principal/1000 AS principal, CONCAT(commission/1000, 1)*commission AS broker_fee,
		CONCAT(misc_sign, 1)*misc_fee/1000 AS other_fee, filename AS filename, 0 AS crmid
		FROM {$tenant}.custodian_transactions_pershing f
		WHERE trade_date {$comparitor} ?
				AND transaction_id
		NOT IN (SELECT original_id FROM vtiger_transactionscf WHERE custodian = 'pershing')";

		$adb->pquery($query, array($trade_date));

#		$query = "UPDATE new_transactions
#		SET transaction_type = 'Expense', transaction_activity = 'Other expenses'
#		WHERE security_type = 'ex'";
#		$adb->pquery($query, array());

		$query = "UPDATE new_transactions SET crmid = IncreaseAndReturnCrmEntitySequence()";
		$adb->pquery($query, array());

		$query = "SELECT * FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
		SELECT crmid, 1, 1, 1, 'Transactions', NOW(), NOW(), security_symbol FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, cost_basis_adjustment)
		SELECT crmid, account_number, security_symbol, security_price, quantity, trade_date, cost_basis_adjustment FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, original_id, rep_code, description, cancelled, transaction_activity, net_amount, principal, broker_fee, other_fee, code, sec_fee, source, filename)
		SELECT crmid, custodian, transaction_type, original_id, rep_code, description, cancelled, transaction_activity, net_amount, principal, broker_fee, other_fee, code, sec_fee, source, filename FROM new_transactions";
		$adb->pquery($query, array());
	}

	static private function PullNewTransactionsFidelity($trade_date, $comparitor){
		global $adb;
		$tenant = self::$tenant;

		$query = "DROP TABLE IF EXISTS new_transactions";
		$adb->pquery($query, array());

		$query = "CREATE TEMPORARY TABLE new_transactions
		SELECT account_number AS account_number, symbol AS security_symbol, (SELECT price FROM vtiger_pc_security_prices pr WHERE pr.price_date = trade_date AND pr.symbol = security_symbol LIMIT 1) AS security_price, quantity AS quantity, trade_date AS trade_date, 0 AS cost_basis_adjustment,
		'Fidelity' AS custodian, map.transaction_type, transaction_id AS original_id, 'TBD' AS rep_code, key_code_description AS description, CASE f.transaction_type WHEN BINARY UPPER(f.transaction_type) THEN 1 ELSE 0 END AS cancelled, map.transaction_activity AS transaction_activity, amount AS net_amount, 0 AS principal, commission AS broker_fee,
		service_charge_misc_fee AS other_fee, key_code AS code, sec_fee AS sec_fee, source AS source, filename AS filename, f.security_type, 0 AS crmid
		FROM {$tenant}.custodian_transactions_fidelity f
		LEFT JOIN vtiger_transaction_type_mapping map ON map.code = f.transaction_type
		WHERE trade_date {$comparitor} ?
				AND transaction_id
		NOT IN (SELECT original_id FROM vtiger_transactionscf WHERE custodian = 'fidelity')";

		$adb->pquery($query, array($trade_date));

		$query = "UPDATE new_transactions
		SET transaction_type = 'Expense', transaction_activity = 'Other expenses'
		WHERE security_type = 'ex'";
		$adb->pquery($query, array());

		$query = "UPDATE new_transactions SET crmid = IncreaseAndReturnCrmEntitySequence()";
		$adb->pquery($query, array());

		$query = "SELECT * FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
		SELECT crmid, 1, 1, 1, 'Transactions', NOW(), NOW(), security_symbol FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, cost_basis_adjustment)
		SELECT crmid, account_number, security_symbol, security_price, quantity, trade_date, cost_basis_adjustment FROM new_transactions";
		$adb->pquery($query, array());

		$query = "INSERT INTO vtiger_transactionscf (transactionsid, custodian, transaction_type, original_id, rep_code, description, cancelled, transaction_activity, net_amount, principal, broker_fee, other_fee, code, sec_fee, source, filename)
		SELECT crmid, custodian, transaction_type, original_id, rep_code, description, cancelled, transaction_activity, net_amount, principal, broker_fee, other_fee, code, sec_fee, source, filename FROM new_transactions";
		$adb->pquery($query, array());
	}


	static private function CloudToModuleConversion($custodian, $date, $comparitor, $newonly = 0){
		$transaction_type_map = self::GetTransactionTypeMapping();
		if (strcasecmp($custodian, "omniscient") == 0)
			$transactions = self::GetOmniscientTransactions($date, $comparitor);
			if($newonly)
				$transactions = self::GetnewTransactions($custodian, $date, $comparitor);
			else
				$transactions = self::GetTransactions($custodian, $date, $comparitor);
		$count = 0;
		$record = 0;
		if($transactions){
			set_time_limit ( 0 );
			foreach($transactions AS $k => $v){
#				echo "START OF LOOP: " . memory_get_peak_usage() . " - Count: " . $count . PHP_EOL;
				if(!$newonly)
					$record = self::DoesTransactionAlreadyExist($v['transaction_id'], $custodian);
				if($record){//If the record exists, use it instead
					$tmp = Vtiger_Record_Model::getInstanceById($record, "Transactions");
					$tmp->set('mode', 'edit');
#					echo "EDIT<br />";
				}else{
					$tmp = Vtiger_Record_Model::getCleanInstance("Transactions");
					$tmp->set('mode', 'create');
					$count++;
#					echo "NEW<br />";
				}

				$data = $tmp->getData();
				self::MapCloudToModuleData($custodian, $transaction_type_map, $v, $data);
				//Create cash transaction if necessary HERE (OR AFTER SAVE MAY MAKE MORE SENSE)
				$tmp->setData($data);
#				$count++;
				$tmp->save();
			}
		}

		if($count > 0)
			echo "{$count} new transactions... Re-run with the same settings until this is 0";
		else
			echo "{$count} new transactions.  All up to date";
/*		echo '<script type="text/javascript" src="libraries/jquery/jquery.min.js"></script>';
		echo '<script type="text/javascript">$(document).ready(function(){
  			function Restart() {
  				location.reload();
			}
			setTimeout(Restart, 2000);
		});</script>';*/
	}
}