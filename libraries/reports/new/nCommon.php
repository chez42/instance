<?php
include_once("include/utils/omniscientCustom.php");

function GetInceptionDate($account_number){
    global $adb;
    $query = "SELECT MIN(trade_date) AS inception_date
              FROM vtiger_pc_transactions t
              JOIN vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
              WHERE p.portfolio_account_number = ?";
    $result = $adb->pquery($query, array($account_number));
    if($adb->num_rows($result) > 0)
        return $adb->query_result($result, 0, 'inception_date');
}

function GetLastDayOfMonth($date){
    return date("Y-m-t", strtotime($inception_date));
}

/**
 * Returns all Portfolio Account Numbers from the given record id
 * @param type $crmid
 */
function GetAccountNumbersFromRecord($crmid){
    if(!$crmid)
        return;

    global $adb;
    $query = "SELECT 
                CASE (SELECT setype FROM vtiger_crmentity WHERE crmid=?)
                        WHEN 'Contacts' THEN (SELECT ssn FROM vtiger_contactscf WHERE contactid = ?)
                        WHEN 'PortfolioInformation' THEN (SELECT tax_id FROM vtiger_portfolioinformationcf WHERE portfolioinformationid = ?)
                        WHEN 'Accounts' THEN (SELECT GROUP_CONCAT(ssn) FROM vtiger_contactscf WHERE contactid IN 
                                                                        (SELECT contactid FROM vtiger_contactdetails WHERE accountid=?))
                        END AS ssn";
    $result = $adb->pquery($query, array($crmid, $crmid, $crmid, $crmid));
    if($adb->num_rows($result) > 0){
        $ssn_result = $adb->query_result($result, 0, "ssn");
        $ssn = explode(",", $ssn_result);
        $questions = generateQuestionMarks($ssn);//The original query
        $query = "SELECT account_number FROM vtiger_portfolioinformation p
        JOIN vtiger_portfolioinformationcf cf ON (p.portfolioinformationid = cf.portfolioinformationid)
        WHERE tax_id IN ({$questions}) AND tax_id != '' AND accountclosed = 0";
#        $query = "SELECT portfolio_account_number FROM vtiger_portfolios WHERE portfolio_tax_id IN ({$questions}) AND portfolio_tax_id != '' AND account_closed = 0";
        $res = $adb->pquery($query, array($ssn));
        if($adb->num_rows($res) > 0){
            foreach($res AS $k => $v){
                $accounts[] = $v['account_number'];
            }
        }

        $extra = GetPortfolioAccountNumbersFromSSN($ssn);
		if(is_array($extra))
			$accounts = array_merge($accounts, $extra);
    }

    if($crmid != null AND $crmid != 0) {
        $query = "SELECT account_number 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf ON (p.portfolioinformationid = cf.portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE contact_link = ? OR p.portfolioinformationid = ? OR p.household_account = ? AND p.accountclosed = 0 AND e.deleted = 0";// AND contact_link IS NOT NULL AND contact_link != ''";
        $result = $adb->pquery($query, array($crmid, $crmid, $crmid));
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetch_array($result))
                $accounts[] = $v['account_number'];
        }
    }

    $focus = CRMEntity::getInstance('Accounts');
    $entityIds = $focus->getRelatedContactsIds($crmid);

    $account_numbers_household = GetPortfolioAccountNumbersFromContactID($entityIds);
    if(!is_array($accounts))
        $accounts = array();
    if(is_array($account_numbers_household))
        $accounts = array_merge($accounts, $account_numbers_household);

    return $accounts;
}

/**
 * Calculates the months between two dates.  Jan 3 - Feb 4 for example will return January
 * @param DateTime $start
 * @param DateTime $end
 * @return type
 */
function GetMonthsBetween($start, $end){
    $start    = new DateTime($start);
    $start->modify('first day of this month');
    $end      = new DateTime($end);
    $end->modify('first day of this month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $months[] = $dt->format("Y-m-t");
    }
    return $months;
}

/**
 * Calculate Asset Allocations from scratch using actual transactions.  This does not use values from PortfolioInformation
 * @global type $adb
 * @param type $account_number
 * @param type $date
 * @return int
 */
function CalculateAssetAllocations($account_number, $date='', $update_positioninformation=0){
    global $adb;
    if(strlen($date) > 0){
        $price_date = " AND price_date <= '{$date}' ";
        $trade_date = " AND trade_date <= '{$date}'";
    }
/*
    $query = "
            SELECT *, SUM(value) AS total_value FROM
                (SELECT *,
                           CASE WHEN (factor > 0) 
                                          THEN security_price_adjustment * price * factor * quantity 
                                        WHEN (security_type_id = 11)
                                          THEN 1*quantity
                                        ELSE security_price_adjustment * price * quantity 
                                        END AS value
                FROM (SELECT symbol_id, SUM(quantity) AS quantity, s.security_type_id, s.security_price_adjustment, c.code_description, security_symbol, pr.price, pr.price_date, pr.factor 
                          FROM vtiger_pc_transactions t 
                          JOIN vtiger_securities s ON s.security_id = symbol_id
                          LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                                (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                          LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = 
                                (SELECT MAX(security_price_id) AS security_price_id FROM vtiger_pc_security_prices WHERE security_id = t.symbol_id {$price_date})
                          WHERE portfolio_id = 
                                (SELECT portfolio_id FROM vtiger_portfolios p WHERE p.portfolio_account_number=?)
                          AND t.status_type_id = 100
                          {$trade_date}
                          GROUP BY symbol_id) AS positions_tier2
                WHERE quantity != 0) AS positions
            GROUP BY code_description";

    $result = $adb->pquery($query, array($account_number));
    if($adb->num_rows($result) > 0){
        foreach($result AS $k => $v){
            $res[$v['code_description']] = $v;
        }

        return $res;
    }
    return 0;
 */
    
    $query = "DROP TABLE IF EXISTS before_prices;";
    $adb->pquery($query, array());

    $query = "CREATE TEMPORARY TABLE before_prices
              SELECT symbol_id,
              CASE WHEN (security_type_id = 8 AND activity_id=80) THEN SUM(quantity*-100)
									 WHEN (security_type_id = 8) THEN SUM(quantity*100)
									 WHEN (activity_id=80) THEN SUM(quantity*-1)
									 ELSE SUM(quantity) END AS quantity, t.activity_id,
              SUM(cost_basis_adjustment) AS cost_basis_adjustment, s.security_price_adjustment, case when (c.code_description is null) then 'unknown' else c.code_description end as code_description,
                case when (c.code_description = 'Fixed Income') then 2 else s.security_type_id end AS security_type_id, security_symbol, s.security_description
                          FROM vtiger_pc_transactions t 
                          JOIN vtiger_securities s ON s.security_id = symbol_id
                          LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                                (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
                          WHERE portfolio_id = 
                                (SELECT portfolio_id FROM vtiger_portfolios p WHERE p.portfolio_account_number=? AND account_closed = 0 ORDER BY data_set_id ASC LIMIT 1)
                          AND t.status_type_id = 100
                          {$trade_date}
                          GROUP BY symbol_id;";
    $adb->pquery($query, array($account_number));

    $query = "DROP TABLE IF EXISTS after_prices;";
    $adb->pquery($query, array());

/*    $query = "CREATE TEMPORARY TABLE after_prices
              SELECT bp.*, pr.price, pr.price_date, pr.factor 
              FROM before_prices bp
              LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = 
              (SELECT security_price_id 
                FROM vtiger_pc_security_prices 
                WHERE security_id = bp.symbol_id 
                {$price_date}
                ORDER BY price_date DESC LIMIT 1)
                WHERE (quantity >= 1 OR quantity <= -1)";*/
    
    $query = "CREATE TEMPORARY TABLE after_prices
                  SELECT bp.*, pr.price, pr.price_date, pr.factor, CASE WHEN (factor > 0) 
                                              THEN security_price_adjustment * price * factor * quantity 
                                            WHEN (security_type_id = 11)
                                              THEN 1*quantity
                                            ELSE security_price_adjustment * price * quantity 
                                            END AS security_value
                  FROM before_prices bp
                  LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = 
                  (SELECT security_price_id 
                    FROM vtiger_pc_security_prices 
                    WHERE security_id = bp.symbol_id 
                    {$price_date}
                    ORDER BY price_date DESC LIMIT 1)
                               WHERE (quantity >= 0.01 OR quantity <= -0.01);";

    $adb->pquery($query, array());

    if($update_positioninformation){
        $query = "DROP TABLE IF EXISTS positions;";
        $adb->pquery($query, array());
        $query = "CREATE TEMPORARY TABLE positions
                  SELECT *, ((security_value - cost_basis_adjustment)/cost_basis_adjustment * 100) AS gain_loss_percent,
                            (security_value - cost_basis_adjustment) AS gain_loss
                  FROM after_prices
                  GROUP BY symbol_id;";
        
        
        /* ===== START : Felipe Project Run Changes ===== */
        
        //$adb->pquery($query, array());
        $result = $adb->pquery($query, array());

        /* ===== END : Felipe Project Run Changes ===== */
        
        //Set all position information for this account to 0
        $query = "UPDATE vtiger_positioninformation 
                  SET quantity = 0, last_price = 0, current_value = 0, cost_basis = 0, unrealized_gain_loss = 0, gain_loss_percent = 0, weight = 0
                  WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
        
        $query = "SELECT * FROM positions p
                  WHERE p.security_symbol NOT IN (SELECT security_symbol FROM vtiger_positioninformation WHERE account_number = ?);";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0){
            $record_id = PortfolioInformation::GetPortfolioInformationRecordIDFromAccountNumber($account_number);
            if($record_id){
                $portfolio = PortfolioInformation_Record_Model::getInstanceById($record_id);
                $portfolio_data = $portfolio->getData();
                foreach($result AS $k => $v){
                    $t = Vtiger_Record_Model::getCleanInstance("PositionInformation");
                    $data = $t->getData();

                    $data['security_symbol'] = $v['security_symbol'];
                    $data['description'] = $v['security_description'];
                    $data['account_number'] = $account_number;
                    $data['advisor_id'] = $portfolio_data['advisor_id'];
                    $data['quantity'] = $v['quantity'];
                    $data['last_price'] = $v['price'];
                    $data['current_value'] = $v['security_value'];
                    $data['weight'] = 0;
                    $data['cost_basis'] = $v['cost_basis_adjustment'];
                    $data['unrealized_gain_loss'] = $v['gain_loss'];
                    $data['gain_loss_percent'] = $v['gain_loss_percent'];
                    $data['contact_link'] = $portfolio_data['contact_link'];
                    $data['household_account'] = $portfolio_data['household_account'];
                    $data['symbol_id'] = $v['symbol_id'];
                    $data['assigned_user_id'] = $portfolio_data['assigned_user_id'];

                    $t->set('mode','create');
                    $t->setData($data);
                    $t->save();
                }
            }
        }
        
        $query = "UPDATE vtiger_positioninformation pin
                  JOIN positions p ON p.security_symbol = pin.security_symbol
                  JOIN vtiger_crmentity e ON e.crmid = pin.positioninformationid
                  SET pin.quantity = p.quantity, 
                  e.deleted = 0, 
                  pin.last_price = p.price, 
                  pin.current_value = p.security_value, 
                  pin.cost_basis = p.cost_basis_adjustment,
                  pin.unrealized_gain_loss = p.gain_loss,
                  pin.gain_loss_percent = p.gain_loss_percent,
                  pin.weight = (security_value/(SELECT SUM(security_value) FROM after_prices)*100)            
                  WHERE pin.account_number = ?";
        $adb->pquery($query, array($account_number));
        
        $query = "UPDATE vtiger_positioninformation vpin
                  JOIN vtiger_crmentity e ON e.crmid = vpin.positioninformationid
                  SET e.deleted = 1
                  WHERE account_number = ?
                  AND e.deleted = 0
                  AND security_symbol NOT IN (select security_symbol from positions);";
        $adb->pquery($query, array($account_number));
    }

    $query = "SELECT *, SUM(value) AS total_value FROM
                (SELECT *,
                           CASE WHEN (factor > 0) 
                                          THEN security_price_adjustment * price * factor * quantity 
                                        WHEN (security_type_id = 11)
                                          THEN 1*quantity
                                        ELSE security_price_adjustment * price * quantity 
                                        END AS value
				  FROM after_prices) AS positions
				  WHERE quantity != 0
				  GROUP BY security_type_id;";
    
    $result = $adb->pquery($query);

    if($adb->num_rows($result) > 0){
        foreach($result AS $k => $v){
            $res[$v['security_type_id']] = $v;
        }
        return $res;
    }
    return 0;
}

function UpdatePositionInformation($account_number){
	global $adb;
	$query = "UPDATE vtiger_positioninformation 
                  SET quantity = 0, last_price = 0, current_value = 0, cost_basis = 0, unrealized_gain_loss = 0, gain_loss_percent = 0, weight = 0
                  WHERE account_number = ?";
	$adb->pquery($query, array($account_number));

	$query = "SELECT *, ((security_value - cost_basis_adjustment)/cost_basis_adjustment * 100) AS gain_loss_percent,
				 		(security_value - cost_basis_adjustment) AS gain_loss
			  FROM after_prices
			  GROUP BY symbol_id";
	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0){
		while($v = $adb->fetch_array($result)) {
//			$record_id = PortfolioInformation::GetPortfolioInformationRecordIDFromAccountNumber($account_number);
			$position_id = PositionInformation_Module_Model::GetPositionEntityIDForAccountNumberAndSymbol($account_number, $v['security_symbol']);
			if(!$position_id){//The ID doesn't exist, so create the position
				$t = Vtiger_Record_Model::getCleanInstance("PositionInformation");
				$t->set('mode', 'create');
			}else{
				$t = Vtiger_Record_Model::getInstanceById($position_id, "PositionInformation");
				$t->set('mode', 'edit');
			}
			$data = $t->getData();
			$data['security_symbol'] = $v['security_symbol'];
			$data['description'] = $v['security_description'];
			$data['account_number'] = $account_number;
#			$data['advisor_id'] = $portfolio_data['advisor_id'];
			$data['quantity'] = $v['quantity'];
			$data['last_price'] = $v['price'];
			$data['current_value'] = $v['security_value'];
			$data['weight'] = 0;
			$data['cost_basis'] = $v['cost_basis_adjustment'];
			$data['unrealized_gain_loss'] = $v['gain_loss'];
			$data['gain_loss_percent'] = $v['gain_loss_percent'];
#			$data['contact_link'] = $portfolio_data['contact_link'];
#			$data['household_account'] = $portfolio_data['household_account'];
			$data['symbol_id'] = $v['symbol_id'];
			$data['assigned_user_id'] = 1;//$portfolio_data['assigned_user_id'];
			$t->setData($data);
			$t->save();
#			echo "POSITION_ID for {$v['security_symbol']}: " . $position_id . "\r\n";
		}
	}
}

function FormatPieForDisplay($pie_data){
    include_once("modules/PortfolioInformation/PortfolioInformation.php");
    if(is_array($pie_data)){
        foreach($pie_data AS $k => $v){
            $color = PortfolioInformation::GetChartColorForTitle($k);
            if($color)
                $pie[] = array("title"=>$k, 
                               "value"=>$v,
                               "color"=>$color);
            else
                $pie[] = array("title"=>$k,
                               "value"=>$v);
        }
    }
    return $pie;
}

function SeparateArrayToQuoteAndCommas($values){
    $comma_separated = implode("','", $values);
    $comma_separated = "'".$comma_separated."'";
    return $comma_separated;
}
?>