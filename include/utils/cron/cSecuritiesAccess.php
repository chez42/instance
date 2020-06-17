<?php
include_once("include/utils/cron/cPortfolioCenter.php");
include_once("include/utils/cron/cTransactionsAccess.php");

class cSecuritiesAccess{
	private $pc;
	private $last_modified;
	private $reset;
	private $datasets;

	public function __construct() {
		global $adb;
		$this->pc = new cPortfolioCenter();
		$query = "SELECT modified_date FROM vtiger_securities GROUP BY modified_date DESC LIMIT 1";
		$result = $adb->pquery($query, array());
		$this->last_modified = $adb->query_result($result, 0, "modified_date");
		$this->reset = 500;
		$this->datasets = $this->pc->GetDatasets();//"1, 28";
	}

	/**Convert the sql date to a proper format*/
	public function ConvertDate($date)
	{
		$time = strtotime($date);
		$time = date('Y-m-d 00:00:00', $time);
		return $time;
	}

	/**
	 * Returns pairing of security_id and security_data_set_id
	 * @global type $adb
	 * @param type $symbol
	 * @return int
	 */
	static public function GetSecurityIDsBySymbol($symbol){
		global $adb;
		$query = "SELECT security_id, security_data_set_id FROM vtiger_securities WHERE security_symbol = ? ORDER BY security_data_set_id ASC";
		$result = $adb->pquery($query, array($symbol));
		$info = array();
		if($adb->num_rows($result) > 0){
			foreach ($result AS $k => $v){
				$info[] = $v;
			}
			return $info;
		}
		return 0;
	}

	/**
	 * Calculate the Position Summary rows (set last price, weight, etc)
	 * @global type $adb
	 * @param type $date
	 */
	public function CalculatePositionsSummary($account_number=null, $date=null){
		global $adb;
		if(strlen($account_number) > 1)
			$where = " WHERE ps.account_number = '{$account_number}' ";

		echo "Updating Current Price in Summary Table " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		/*        $query = "UPDATE vtiger_position_summary ps SET last_price=(SELECT price FROM vtiger_pc_security_prices
																			WHERE security_id=ps.symbol_id
																			AND price_date <= NOW()
																			GROUP BY security_id
																			ORDER BY price_date DESC)";*/
		$query = "UPDATE vtiger_position_summary ps
                  LEFT JOIN vtiger_securities s ON s.security_id = ps.symbol_id
                  SET last_price = (SELECT price FROM vtiger_pc_security_prices 
                                                                     WHERE price_date = (SELECT max(price_date) FROM vtiger_pc_security_prices WHERE security_id=ps.symbol_id AND price > 0) 
                                                                     AND security_id=ps.symbol_id) 
                                                                        * CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * s.security_factor
                                                                                                            ELSE s.security_price_adjustment END
                  {$where}";

		$adb->pquery($query, array());
		echo "Done Updating Current Price in Summary Table " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$query = "UPDATE vtiger_position_summary ps SET last_price = 1, cost_basis = quantity WHERE security_type = 'Cash'";
		$adb->pquery($query, array());
		echo "Done Updating Current Prices " . date('Y-m-d H:i:s') . "<br />\r\n";

		echo "Calculating Current Value " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();

		$query = "UPDATE vtiger_position_summary ps SET current_value = quantity*last_price {$where}";
		$adb->pquery($query, array());
		echo "Finished calculating current value " . date('Y-m-d H:i:s') . "<br />\r\n";

		$query = "DROP TABLE if exists summed";
		$adb->pquery($query, array());

		echo "Creating temporary table to hold account values " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$query = "CREATE TEMPORARY TABLE summed(
                  account_number VARCHAR(50) NOT NULL,
                  total_value DECIMAL(12,2) NULL DEFAULT 0.00)";
		$adb->pquery($query, array());
		echo "Done creating temporary table " . date('Y-m-d H:i:s') . "<br />\r\n";

		echo "Inserting summarized values to temporary table " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$query = "INSERT INTO summed(account_number, total_value)
                  SELECT ps.account_number, SUM(ps.current_value) 
                          FROM vtiger_position_summary ps GROUP BY account_number";

		$adb->pquery($query, array());
		echo "Done inserting into temporary table " . date('Y-m-d H:i:s') . "<br />\r\n";

		echo "Calculating weight, unrealized gain/loss, gain/loss percent " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$query = "UPDATE vtiger_position_summary ps
                  SET ps.weight = ps.current_value / (SELECT total_value FROM summed s WHERE s.account_number = ps.account_number) * 100,
                      ps.unrealized_gain_loss = ps.current_value - ps.cost_basis,
                      ps.gain_loss_percent = ((ps.current_value - ps.cost_basis)/ps.cost_basis)*100 {$where}";

		$adb->pquery($query, array());
		echo "Done calculating weight, unrealized gain/loss, gain/loss percent " . date('Y-m-d H:i:s') . "<br />\r\n";
		echo "Updating asset class and Security Type " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();

		$query = "UPDATE vtiger_position_summary ps
                  JOIN vtiger_securities s ON s.security_id = ps.symbol_id
                  JOIN vtiger_security_types st on s.security_type_id = st.security_type_id
                  JOIN vtiger_pc_security_codes sc on s.security_id = sc.security_id AND sc.code_type_id in (20)
                  JOIN vtiger_pc_codes c on c.code_id = sc.code_id
                  SET ps.security_type = st.security_type_name,
                  ps.asset_class = c.code_description";
		$adb->pquery($query, array());
		echo "Done updating asset class and security type " . date('Y-m-d H:i:s') . "<br />\r\n";
	}

	/**
	 * Gets everything from the summary table that doesn't already exist in the vtiger_positioninformation table
	 * @global type $adb
	 * @param type $date
	 * @return type
	 */
	public function GetPositionsToInsertFromSummaryTable($account_number = null, $date = null){
		global $adb;
		if(strlen($account_number) > 1)
			$and = " AND vps.account_number = '{$account_number}' ";
		/*        $query = "SELECT vps.*, vpi.household_account, vpi.contact_link FROM vtiger_position_summary vps
						  left outer join vtiger_positioninformation vpi ON vpi.account_number = vps.account_number AND vpi.symbol_id = vps.symbol_id
						  WHERE vpi.account_number is null
						  AND vps.account_number != '' {$and}";*/
		$query = "SELECT vps.*, vpi.household_account, vpi.contact_link 
                      FROM vtiger_position_summary vps
                      left outer join vtiger_positioninformation vpi ON vpi.account_number = vps.account_number AND vpi.symbol_id = vps.symbol_id
                      WHERE vps.account_number != ''
                      AND vps.account_number IS NOT NULL
                      AND vps.symbol_id NOT IN (SELECT symbol_id FROM vtiger_positioninformation WHERE account_number = vps.account_number)
                      {$and}";

		$result = $adb->pquery($query, array());
		return $result;
	}

	private function ExecuteQuery($query, $feedback=''){
		global $adb;

		if(strlen($feedback) > 1)
			$feedback .= date('Y-m-d H:i:s') . "<br />\r\n";

		echo $feedback;
		ob_flush();
		flush();

		$adb->pquery($query, array());

	}
	/**
	 * Update the position information module from the summary table
	 * @global type $adb
	 * @param type $date
	 */
	public function UpdatePositionInformationModule($account_number=null, $date=null){return;
		global $adb;
		return;
		echo "Retrieving Positions to Insert from Summary Table " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$result = $this->GetPositionsToInsertFromSummaryTable($account_number, $date);

		$num_results = $adb->num_rows($result);
		if($num_results > 0){
			$query = "UPDATE vtiger_crmentity_seq SET id = id + 1";
			$adb->pquery($query, array());
		}

		$query = "SELECT id FROM vtiger_crmentity_seq";
		$entity_seq_result = $adb->pquery($query, array());//we now have our new crmid
		$crmid = $adb->query_result($entity_seq_result, 0, "id");

		echo "Positions retrieved with {$num_results} to create " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();

		if($num_results > 0){
			$query = "UPDATE vtiger_crmentity_seq SET id = id + {$num_results}";
			$adb->pquery($query, array());
		}

		$query_entity = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, viewedtime, presence) VALUES ";
		$query_positioninformation = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, household_account, advisor_id, quantity,
                                      last_price, current_value, weight, cost_basis, unrealized_gain_loss, gain_loss_percent, contact_link, symbol_id) VALUES ";
		$query_positioninformationcf = "INSERT INTO vtiger_positioninformationcf (positioninformationid, asset_class, security_type) VALUES ";

		$count = 0;
		$reset = 0;
		$query_entity_extension = "";
		$query_positioninformation_extension = "";
		$query_positioninformationcf_extension = "";
		foreach($result AS $k => $v){
			$advisor_id = $v['advisor_id'];
			$symbol = mysql_real_escape_string($v['symbol']);
			$description = mysql_real_escape_string($v['description']);
			$tmp_account_number = mysql_real_escape_string($v['account_number']);
			$query_entity_extension .= "('{$crmid}', 0, 0, 0, 'PositionInformation', NOW(), NOW(), NOW(), 1)";
			$query_positioninformation_extension .= "('{$crmid}', '{$symbol}', '{$description}', '{$tmp_account_number}', '0', '{$v['advisor_id']}', '{$v['quantity']}',
                                    '{$v['last_price']}', '{$v['current_value']}', '{$v['weight']}', '{$v['cost_basis']}', '{$v['unrealized_gain_loss']}', '{$v['gain_loss_percent']}',
                                    '{$v['contact_id']}', '{$v['symbol_id']}')";
			$query_positioninformationcf_extension .= "('{$crmid}', '{$v['asset_class']}', '{$v['security_type']}')";
			$count++;
			$reset++;
			if($count < $num_results && $reset < $this->reset){//If we need to reset, don't add a comma
				$query_entity_extension .= ",";
				$query_positioninformation_extension .= ",";
				$query_positioninformationcf_extension .= ",";
			}
			$crmid++;

			if($reset >= $this->reset)
			{
				$reset = 0;//Reset the query insert
				$this->ExecuteQuery($query_entity . $query_entity_extension, "Inserting into entities table ");
				$this->ExecuteQuery($query_positioninformation . $query_positioninformation_extension, "Inserting into position information table ");
				$this->ExecuteQuery($query_positioninformationcf . $query_positioninformationcf_extension, "Inserting into position information cf table ");
				$query_entity_extension = '';
				$query_positioninformation_extension = '';
				$query_positioninformationcf_extension = '';
			}
		}

		$this->ExecuteQuery($query_entity . $query_entity_extension, "Inserting into entities table -- Final  ");
		$this->ExecuteQuery($query_positioninformation . $query_positioninformation_extension, "Inserting into position information table -- Final ");
		$this->ExecuteQuery($query_positioninformationcf . $query_positioninformationcf_extension, "Inserting into position information cf table -- Final ");
		/*
				{
		////https://www.advisorviewdev.com/vcrm/index.php?module=PositionInformation&action=copy_positions
					$q3 = "UPDATE vtiger_crmentity_seq SET id = id + 1";
					$adb->pquery($q3, array());
					$q4 = "SELECT id FROM vtiger_crmentity_seq";
					$r2 = $adb->pquery($q4, array());
					$crmid = $adb->query_result($r2, 0, "id");//we now have our new crmid

					$q5 = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, viewedtime, presence)
						   VALUES(?, ?, ?, ?, 'PositionInformation', NOW(), NOW(), NOW(), 1)";
					$adb->pquery($q5, array($crmid, $v['assigned_to'], $v['assigned_to'], $v['assigned_to']));
					$q6 = "INSERT INTO vtiger_positioninformation (positioninformationid, security_symbol, description, account_number, household_account, advisor_id, quantity,
									   last_price, current_value, weight, cost_basis, unrealized_gain_loss, gain_loss_percent, contact_link, symbol_id)
						   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
					$adb->pquery($q6, array($crmid, $v['symbol'], $v['description'], $v['account_number'], $account_id, $v['advisor_id'], $v['quantity'],
											$v['last_price'], $v['current_value'], $v['weight'], $v['cost_basis'], $v['unrealized_gain_loss'], $v['gain_loss_percent'],
											$v['contact_id'], $v['symbol_id']));
					$q7 = "INSERT INTO vtiger_positioninformationcf (positioninformationid, asset_class, security_type) VALUES (?, ?, ?)";
					$adb->pquery($q7, array($crmid, $v['asset_class'], $v['security_type']));
				}
		 */
		$where = "";
		if(strlen($account_number) > 1)
			$where = " WHERE pinfo.account_number = '{$account_number}' ";
		$query = "UPDATE vtiger_positioninformation AS pinfo
                  JOIN vtiger_positioninformationcf cf ON pinfo.positioninformationid = cf.positioninformationid
                  LEFT JOIN vtiger_position_summary ps ON pinfo.account_number = ps.account_number AND pinfo.symbol_id = ps.symbol_id
                  LEFT JOIN vtiger_crmentity e ON e.crmid = pinfo.positioninformationid
                  SET pinfo.cost_basis = ps.cost_basis,
                  pinfo.current_value = ps.current_value,
                  pinfo.last_price = ps.last_price,
                  pinfo.gain_loss_percent = ps.gain_loss_percent,
                  pinfo.unrealized_gain_loss = ps.unrealized_gain_loss,
                  pinfo.weight = ps.weight,
                  pinfo.quantity = ps.quantity, 
                  cf.asset_class = ps.asset_class,
                  cf.security_type = ps.security_type,
                  e.smownerid = ps.assigned_to
                  {$where}";
		echo "About to run this query (A source of possible issue...: {$query} " . date('Y-m-d H:i:s') . "<br />\r\n";
		ob_flush();
		flush();
		$this->ExecuteQuery($query, "Updating the positioninformation table");
		$adb->pquery($query, array());
	}

	/**
	 * This inserts into the Positions Summary table.  It gets the positions quantity/cost basis totals and slaps em in there.
	 * @global type $adb
	 * @param type $date
	 */
	public function UpdatePositionsSummary($pids=null, $date=null){
		global $adb;

		$result = $this->GetPositionsQuantityAndCostBasis($pids, $date);
		echo "About to insert to summary table.. If we die here, or it goes REALLY fast it is due to memory issues (too much data sent through the socket). " . date('Y-m-d H:i:s') . "<br />";
		ob_flush();
		flush();
		if($adb->num_rows($result) > 0){
			$count = 0;
			$reset = 0;
			$query = "INSERT INTO vtiger_position_summary (symbol_id, symbol, description, account_number, cost_basis, quantity, advisor_id, last_modified, assigned_to, security_type) VALUES ";
			$extension = "";
			$update = " ON DUPLICATE KEY UPDATE cost_basis = VALUES(cost_basis), symbol = VALUES(symbol), description = VALUES(description), quantity = VALUES(quantity), advisor_id=VALUES(advisor_id), last_modified=VALUES(last_modified), assigned_to=VALUES(assigned_to), security_type=VALUES(security_type)";
			$accounts = array();
			foreach($result AS $k => $v)
				$accounts[$v['portfolio_account_number']] = 1;
			foreach($accounts AS $k => $v)
				$to_delete[] = $k;
			$to_delete = SeparateArrayWithCommasAndSingleQuotes($to_delete);
			$delete_query = "DELETE FROM vtiger_position_summary WHERE account_number IN ({$to_delete})";
			$adb->pquery($delete_query);

			foreach($result AS $k => $v){
				$symbol = mysql_real_escape_string($v['security_symbol']);
				$description = mysql_real_escape_string($v['security_description']);
				$account_number = mysql_real_escape_string($v['portfolio_account_number']);
				$extension .= "('{$v['symbol_id']}',
                            '{$symbol}',
                            '{$description}',
                            '{$account_number}',
                            '{$v['cost_basis']}',
                            '{$v['quantity']}',
                            '{$v['advisor_id']}',
                             NOW(),
                            '{$v['assigned_to']}',
                            '{$v['security_type']}')";
				$count++;
				$reset++;
				if($count < $adb->num_rows($result) && $reset < $this->reset)
					$extension .= ",";

				if($reset >= $this->reset)
				{
					$reset = 0;//Reset the query insert
					$this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_position_summary ");
					$extension = "";
				}
			}

			$this->ExecuteQuery($query . $extension . $update, "Inserting into vtiger_position_summary -- Final insert ");
		}
		echo "Finished inserting to the summary table (passed the death gate)" . date('Y-m-d H:i:s') . "<br /><br />\r\n";
		ob_flush();
		flush();
	}
//        $trans = new cTransactionsAccess();
//        $trans->GetPositionsQuantityAndCostBasis();

	/**
	 * Find the last modified date from transactions to get our last transaction date
	 * @global type $adb
	 * @param type $portfolio_id
	 */
	public function GetLastModifiedDate($portfolio_id=null){
		return $this->last_modified;
	}

	/**
	 * Get portfolio positions as of the date specified
	 * @global type $adb
	 * @param type $portfolio_ids
	 * @param type $date
	 * @return type
	 */
	public function GetPositionsAsOfDate($portfolio_ids, $date){
		global $adb;
		$query = "SELECT *, t1.quantity*t1.price AS security_value FROM 
                (SELECT SUM(t.quantity) AS quantity, SUM(t.cost_basis_adjustment) AS cost_basis, s.security_symbol, s.security_description, t.symbol_id, t.portfolio_id, p.portfolio_account_number, p.advisor_id,
                                                                        st.security_type_name AS security_type, COUNT(*),
                (SELECT CASE WHEN (st.security_type_name = 'Cash') THEN 1 ELSE (SELECT price 
                * CASE WHEN (s.security_factor > 0) THEN s.security_price_adjustment * s.security_factor
                ELSE s.security_price_adjustment END
                FROM vtiger_pc_security_prices 
                 WHERE price_date = (SELECT max(price_date) FROM vtiger_pc_security_prices WHERE security_id=s.security_id AND price > 0 AND price_date <= ?) 
                 AND security_id=s.security_id) END) AS price
                  FROM vtiger_pc_transactions t
                  LEFT JOIN vtiger_pc_transactions_pricing tp ON t.transaction_id = tp.transaction_id
                  LEFT JOIN vtiger_securities s ON t.symbol_id = s.security_id
                  LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                  LEFT JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE s.security_data_set_id IN ({$this->datasets})
                  AND t.status_type_id = 100
                  AND p.portfolio_account_number != ''
                  AND p.portfolio_id IN ({$portfolio_ids})
                  AND t.trade_date <= ?
                  GROUP BY t.portfolio_id, t.symbol_id) AS t1
                WHERE t1.quantity != 0";

		$result = $adb->pquery($query, array($date, $date));

		return $result;
	}
	/**
	 *
	 * @global type $adb
	 * @param type $portfolio_id
	 * @param type $date
	 */
	public function GetPositionsQuantityAndCostBasis($portfolio_id=null, $date=null){
		global $adb;//THIS IS THE FUNCTION WE MAY BE ABLE TO SPEED THINGS UP EVEN MORE WITH.... If we take only the latest modified transactions, we can add it to our current summary
		//straight on (quantity and cost basis) -- Then we calculate that entire table afterwards.
		if(strlen($date) < 2)
			$date = $this->GetLastModifiedDate($portfolio_id);

		$condition = '';
		if(strlen($portfolio_id) > 1)
			$condition .= " AND p.portfolio_id IN ({$portfolio_id}) ";

//        $condition .= " AND t.last_modified_date >= '{$date}' ";

		$query = "SELECT SUM(t.quantity) AS quantity, SUM(t.cost_basis_adjustment) AS cost_basis, s.security_symbol, s.security_description, t.symbol_id, t.portfolio_id, p.portfolio_account_number, p.advisor_id,
                    (SELECT user_id FROM vtiger_pc_advisor_linking WHERE pc_id = p.advisor_id LIMIT 1) AS assigned_to, st.security_type_name AS security_type, COUNT(*) 
                  FROM vtiger_pc_transactions t
                  LEFT JOIN vtiger_pc_transactions_pricing tp ON t.transaction_id = tp.transaction_id
                  LEFT JOIN vtiger_securities s ON t.symbol_id = s.security_id
                  LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                  LEFT JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE s.security_data_set_id IN ({$this->datasets})
                  AND t.status_type_id = 100
                  AND p.portfolio_account_number != ''
                  {$condition}
                  GROUP BY t.portfolio_id, t.symbol_id ";

		$result = $adb->pquery($query, array());

		return $result;
	}

	/**
	 * If no date is supplied, we take the last known date in the CRM's securities and grab from there
	 * @global type $adb
	 * @param type $security_id
	 * @param type $date
	 * @return string
	 */
	public function GetSecuritiesFromPCByDate($security_id=null, $date=null){
		global $adb;

		if(strlen($date) < 2){
			$date = $this->GetLastModifiedDate();
		}

		if(!$this->pc->connect())
			return "Error Connecting to PC";

		$condition = " AND s.DataSetID IN ({$this->datasets}) ";
		if($security_id)
			$condition .= " AND s.SecurityID={$security_id} ";

		$query = "SELECT DISTINCT s.SecurityID, s.SecurityTypeID, s.DataSetID, s.Symbol, S.Description, pr2.Price, st2.PriceAdjustmentValue, pr2.Factor, s.MaturityDate, s.NextDividendDate,
                                s.NextDividendDate, s.NextDividendAmount, s.AnnualIncomeRate, s.IncomeFrequencyID, s.CreatedOn, s.CreatedBy, s.LastModifiedDate, s.LastModifiedUserID 
                  FROM Securities s
                  CROSS APPLY (SELECT TOP 1 pr.* FROM SecurityPrices pr WHERE pr.SecurityID = s.SecurityID AND pr.DataSetID = s.DataSetID) pr2
                  CROSS APPLY (SELECT TOP 1 st.* FROM DatasetSecurityTypes st WHERE s.SecurityTypeID = st.SecurityTypeID AND s.DataSetID = st.DataSetID) st2
                  WHERE s.LastModifiedDate >= '{$date}' {$condition} AND s.DataSetID IN ({$this->datasets})";

		$results = mssql_query($query);
		if($results)
			while($row = mssql_fetch_array($results)){
				$securities[] = $row;
			}

		return $securities;
	}

	public function GetSecuritiesNotInList($security_list){
		global $adb;

		$query = "SELECT security_id FROM vtiger_securities";
		$result = $adb->pquery($query, array());
		$omni_list = array();
		if($adb->num_rows($result) > 0) {
			foreach ($result AS $k => $v) {
				$omni_list[] = $v['security_id'];
			}
			$difference = array_diff($security_list, $omni_list);
			return $difference;
		}
		return 0;
	}

	public function GetAllSecuritiesFromPC(){
		if(!$this->pc->connect())
			return "Error Connecting to PC";

		$query = "SELECT DISTINCT s.SecurityID
                  FROM PortfolioCenter.dbo.Securities s
                  WHERE s.DataSetID IN ({$this->datasets})";

		$results = mssql_query($query);
		if($results)
			while($row = mssql_fetch_array($results)){
				$securities[] = $row['SecurityID'];
			}

		return $securities;
	}

	public function GetSecuritiesFromPCBySymbol($symbol){
		global $adb;

		if(!$this->pc->connect())
			return "Error Connecting to PC";

		$query = "SELECT DISTINCT s.SecurityID, s.SecurityTypeID, s.DataSetID, s.Symbol, S.Description, pr2.Price, st2.PriceAdjustmentValue, pr2.Factor, s.MaturityDate, s.NextDividendDate,
                                s.NextDividendDate, s.NextDividendAmount, s.AnnualIncomeRate, s.IncomeFrequencyID, s.CreatedOn, s.CreatedBy, s.LastModifiedDate, s.LastModifiedUserID 
                  FROM PortfolioCenter.dbo.Securities s
                  OUTER APPLY (SELECT TOP 1 pr.* FROM PortfolioCenter.dbo.SecurityPrices pr WHERE pr.SecurityID = s.SecurityID AND pr.DataSetID = s.DataSetID) pr2
                  CROSS APPLY (SELECT TOP 1 st.* FROM PortfolioCenter.dbo.DatasetSecurityTypes st WHERE s.SecurityTypeID = st.SecurityTypeID AND s.DataSetID = st.DataSetID) st2
                  WHERE s.Symbol = '{$symbol}' AND s.DataSetID IN ({$this->datasets})";

		$results = mssql_query($query);
		if($results)
			while($row = mssql_fetch_array($results)){
				$securities[] = $row;
			}

		return $securities;
	}

	public function GetSecuritiesFromPCBySecurityId($security_id){
		global $adb;

		if(!$this->pc->connect())
			return "Error Connecting to PC";

		$condition = " AND s.DataSetID IN ({$this->datasets}) ";

		$query = "SELECT DISTINCT s.SecurityID, s.SecurityTypeID, s.DataSetID, s.Symbol, S.Description, pr2.Price, st2.PriceAdjustmentValue, pr2.Factor, s.MaturityDate, s.NextDividendDate,
                                s.NextDividendDate, s.NextDividendAmount, s.AnnualIncomeRate, s.IncomeFrequencyID, s.CreatedOn, s.CreatedBy, s.LastModifiedDate, s.LastModifiedUserID 
                  FROM PortfolioCenter.dbo.Securities s
                  CROSS APPLY (SELECT TOP 1 pr.* FROM PortfolioCenter.dbo.SecurityPrices pr WHERE pr.SecurityID = s.SecurityID) pr2
                  CROSS APPLY (SELECT TOP 1 st.* FROM PortfolioCenter.dbo.DatasetSecurityTypes st WHERE s.SecurityTypeID = st.SecurityTypeID AND s.DataSetID = st.DataSetID) st2
                  WHERE s.SecurityID IN ({$security_id}) AND s.DataSetID IN ({$this->datasets})";
		$results = mssql_query($query);

		if(mssql_num_rows($results) == 0) {
			$query = "SELECT DISTINCT s.SecurityID, s.SecurityTypeID, s.DataSetID, s.Symbol, S.Description, 1 AS Price, st2.PriceAdjustmentValue, 0 AS Factor, s.MaturityDate, s.NextDividendDate,
                                s.NextDividendDate, s.NextDividendAmount, s.AnnualIncomeRate, s.IncomeFrequencyID, s.CreatedOn, s.CreatedBy, s.LastModifiedDate, s.LastModifiedUserID 
                  FROM PortfolioCenter.dbo.Securities s
                  CROSS APPLY (SELECT TOP 1 st.* FROM PortfolioCenter.dbo.DatasetSecurityTypes st WHERE s.SecurityTypeID = st.SecurityTypeID AND s.DataSetID = st.DataSetID) st2
                  WHERE s.SecurityID IN ({$security_id}) AND s.DataSetID IN ({$this->datasets})";
			$results = mssql_query($query);
		}

		if($results)
			while($row = mssql_fetch_array($results)){
				$securities[] = $row;
			}

		return $securities;
	}

	/**
	 * Update the vtiger_securities table with info straight from PC.  We don't worry about pricing, etc here, this is just security information like name, symbol id, etc..
	 * @global type $adb
	 * @param type $date
	 */
	public function UpdateCRMSecurities($date=null){
		global $adb;
		$securities = $this->GetSecuritiesFromPCByDate('', $date);

		$query = "INSERT INTO vtiger_securities (vtiger_securities.security_id, vtiger_securities.security_type_id, vtiger_securities.security_data_set_id, vtiger_securities.security_symbol, vtiger_securities.security_description, vtiger_securities.security_last_price, vtiger_securities.security_price_adjustment,
                                                          vtiger_securities.security_factor, vtiger_securities.security_maturity_date, vtiger_securities.security_next_dividend_date, vtiger_securities.security_next_dividend_amount, vtiger_securities.security_annual_income_rate, vtiger_securities.security_income_frequency_id, vtiger_securities.security_service_provider_id,
                                                          vtiger_securities.security_client_organization_id, vtiger_securities.created_date, vtiger_securities.created_by, vtiger_securities.modified_date, vtiger_securities.modified_by) VALUES ";

		$count = 0;
		foreach($securities AS $k => $v){
			$security_id = $v['SecurityID'];
			$security_type_id = $v['SecurityTypeID'];
			$security_data_set_id = $v['DataSetID'];
			$security_symbol = $v['Symbol'];
			$security_description = mysql_real_escape_string($v['Description']);
			$security_last_price = $v['Price'];
			$security_price_adjustment = $v['PriceAdjustmentValue'];
			$security_factor = $v['Factor'];
			$security_maturity_date = $this->ConvertDate($v['MaturityDate']);
			$security_next_dividend_date = $this->ConvertDate($v['NextDividendDate']);
			$security_next_dividend_amount = $v['NextDividendAmount'];
			$security_annual_income_rate = $v['AnnualIncomeRate'];
			$security_income_frequency_id = $v['IncomeFrequencyID'];
			$security_service_provider_id = 0;
			$security_client_organization_id = 0;
			$created_date = $this->ConvertDate($v['CreatedOn']);
			$created_by = $v['CreatedBy'];
			$modified_date = $this->ConvertDate($v['LastModifiedDate']);
			$modified_by = $v['LastModifiedUserID'];

			$query .= "('{$security_id}',
                        '{$security_type_id}',
                        '{$security_data_set_id}',
                        '{$security_symbol}',
                        '{$security_description}',
                        '{$security_last_price}',
                        '{$security_price_adjustment}',
                        '{$security_factor}',
                        '{$security_maturity_date}',
                        '{$security_next_dividend_date}',
                        '{$security_next_dividend_amount}',
                        '{$security_annual_income_rate}',
                        '{$security_income_frequency_id}',
                        '{$security_service_provider_id}',
                        '{$security_client_organization_id}',
                        '{$created_date}',
                        '{$created_by}',
                        '{$modified_date}',
                        '{$modified_by}')";
			$count++;
			if($count < sizeof($securities))
				$query .= ",";
		}

		$query .= " ON DUPLICATE KEY UPDATE vtiger_securities.security_last_price = VALUES(vtiger_securities.security_last_price), vtiger_securities.security_maturity_date = VALUES(vtiger_securities.security_maturity_date), 
                                            vtiger_securities.security_next_dividend_date = VALUES(vtiger_securities.security_next_dividend_date), vtiger_securities.security_next_dividend_amount = VALUES(vtiger_securities.security_next_dividend_amount),
                                            vtiger_securities.modified_date = VALUES(vtiger_securities.modified_date), vtiger_securities.modified_by = VALUES(vtiger_securities.modified_by)";

		$adb->pquery($query, array());
	}

	/**
	 * Update the vtiger_securities table with info straight from PC.  We don't worry about pricing, etc here, this is just security information like name, symbol id, etc..
	 * @global type $adb
	 * @param type $date
	 */
	public function UpdateCRMSecuritiesByID($security_id){
		global $adb;
		$securities = $this->GetSecuritiesFromPCBySecurityId($security_id);

		$query = "INSERT INTO vtiger_securities (vtiger_securities.security_id, vtiger_securities.security_type_id, vtiger_securities.security_data_set_id, vtiger_securities.security_symbol, vtiger_securities.security_description, vtiger_securities.security_last_price, vtiger_securities.security_price_adjustment,
                                                          vtiger_securities.security_factor, vtiger_securities.security_maturity_date, vtiger_securities.security_next_dividend_date, vtiger_securities.security_next_dividend_amount, vtiger_securities.security_annual_income_rate, vtiger_securities.security_income_frequency_id, vtiger_securities.security_service_provider_id,
                                                          vtiger_securities.security_client_organization_id, vtiger_securities.created_date, vtiger_securities.created_by, vtiger_securities.modified_date, vtiger_securities.modified_by) VALUES ";

		$count = 0;
		foreach($securities AS $k => $v){
			$security_id = $v['SecurityID'];
			$security_type_id = $v['SecurityTypeID'];
			$security_data_set_id = $v['DataSetID'];
			$security_symbol = $v['Symbol'];
			$security_description = mysql_real_escape_string($v['Description']);
			$security_last_price = $v['Price'];
			$security_price_adjustment = $v['PriceAdjustmentValue'];
			$security_factor = $v['Factor'];
			$security_maturity_date = $this->ConvertDate($v['MaturityDate']);
			$security_next_dividend_date = $this->ConvertDate($v['NextDividendDate']);
			$security_next_dividend_amount = $v['NextDividendAmount'];
			$security_annual_income_rate = $v['AnnualIncomeRate'];
			$security_income_frequency_id = $v['IncomeFrequencyID'];
			$security_service_provider_id = 0;
			$security_client_organization_id = 0;
			$created_date = $this->ConvertDate($v['CreatedOn']);
			$created_by = $v['CreatedBy'];
			$modified_date = $this->ConvertDate($v['LastModifiedDate']);
			$modified_by = $v['LastModifiedUserID'];

			$query .= "('{$security_id}',
                        '{$security_type_id}',
                        '{$security_data_set_id}',
                        '{$security_symbol}',
                        '{$security_description}',
                        '{$security_last_price}',
                        '{$security_price_adjustment}',
                        '{$security_factor}',
                        '{$security_maturity_date}',
                        '{$security_next_dividend_date}',
                        '{$security_next_dividend_amount}',
                        '{$security_annual_income_rate}',
                        '{$security_income_frequency_id}',
                        '{$security_service_provider_id}',
                        '{$security_client_organization_id}',
                        '{$created_date}',
                        '{$created_by}',
                        '{$modified_date}',
                        '{$modified_by}')";
			$count++;
			if($count < sizeof($securities))
				$query .= ",";
		}

		$query .= " ON DUPLICATE KEY UPDATE vtiger_securities.security_last_price = VALUES(vtiger_securities.security_last_price), vtiger_securities.security_maturity_date = VALUES(vtiger_securities.security_maturity_date), 
                                            vtiger_securities.security_next_dividend_date = VALUES(vtiger_securities.security_next_dividend_date), vtiger_securities.security_next_dividend_amount = VALUES(vtiger_securities.security_next_dividend_amount),
                                            vtiger_securities.modified_date = VALUES(vtiger_securities.modified_date), vtiger_securities.modified_by = VALUES(vtiger_securities.modified_by)";

		$adb->pquery($query, array());
	}

	public function UpdateCRMSecuritiesBySymbol($symbol){
		global $adb;
		$securities = $this->GetSecuritiesFromPCBySymbol($symbol);

		$query = "INSERT INTO vtiger_securities (vtiger_securities.security_id, vtiger_securities.security_type_id, vtiger_securities.security_data_set_id, vtiger_securities.security_symbol, vtiger_securities.security_description, vtiger_securities.security_last_price, vtiger_securities.security_price_adjustment,
                                                          vtiger_securities.security_factor, vtiger_securities.security_maturity_date, vtiger_securities.security_next_dividend_date, vtiger_securities.security_next_dividend_amount, vtiger_securities.security_annual_income_rate, vtiger_securities.security_income_frequency_id, vtiger_securities.security_service_provider_id,
                                                          vtiger_securities.security_client_organization_id, vtiger_securities.created_date, vtiger_securities.created_by, vtiger_securities.modified_date, vtiger_securities.modified_by) VALUES ";

		$count = 0;
		if(sizeof($securities) < 1)
			return 0;

		foreach($securities AS $k => $v){
			$security_id = $v['SecurityID'];
			$security_type_id = $v['SecurityTypeID'];
			$security_data_set_id = $v['DataSetID'];
			$security_symbol = $v['Symbol'];
			$security_description = mysql_real_escape_string($v['Description']);
			$security_last_price = $v['Price'];
			$security_price_adjustment = $v['PriceAdjustmentValue'];
			$security_factor = $v['Factor'];
			$security_maturity_date = $this->ConvertDate($v['MaturityDate']);
			$security_next_dividend_date = $this->ConvertDate($v['NextDividendDate']);
			$security_next_dividend_amount = $v['NextDividendAmount'];
			$security_annual_income_rate = $v['AnnualIncomeRate'];
			$security_income_frequency_id = $v['IncomeFrequencyID'];
			$security_service_provider_id = 0;
			$security_client_organization_id = 0;
			$created_date = $this->ConvertDate($v['CreatedOn']);
			$created_by = $v['CreatedBy'];
			$modified_date = $this->ConvertDate($v['LastModifiedDate']);
			$modified_by = $v['LastModifiedUserID'];

			$query .= "('{$security_id}',
                        '{$security_type_id}',
                        '{$security_data_set_id}',
                        '{$security_symbol}',
                        '{$security_description}',
                        '{$security_last_price}',
                        '{$security_price_adjustment}',
                        '{$security_factor}',
                        '{$security_maturity_date}',
                        '{$security_next_dividend_date}',
                        '{$security_next_dividend_amount}',
                        '{$security_annual_income_rate}',
                        '{$security_income_frequency_id}',
                        '{$security_service_provider_id}',
                        '{$security_client_organization_id}',
                        '{$created_date}',
                        '{$created_by}',
                        '{$modified_date}',
                        '{$modified_by}')";
			$count++;
			if($count < sizeof($securities))
				$query .= ",";
		}

		$query .= " ON DUPLICATE KEY UPDATE vtiger_securities.security_last_price = VALUES(vtiger_securities.security_last_price), vtiger_securities.security_maturity_date = VALUES(vtiger_securities.security_maturity_date), 
                                            vtiger_securities.security_next_dividend_date = VALUES(vtiger_securities.security_next_dividend_date), vtiger_securities.security_next_dividend_amount = VALUES(vtiger_securities.security_next_dividend_amount),
                                            vtiger_securities.modified_date = VALUES(vtiger_securities.modified_date), vtiger_securities.modified_by = VALUES(vtiger_securities.modified_by),
                                            vtiger_securities.security_symbol = VALUES(vtiger_securities.security_symbol)";

		$adb->pquery($query, array());
		return 1;
	}
}