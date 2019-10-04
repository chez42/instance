<?php
include_once "include/utils/omniscientCustom.php";
include_once("libraries/Stratifi/StratifiAPI.php");

class PortfolioInformation_Module_Model extends Vtiger_Module_Model
{
    /**
     * Sets all production numbers that are currently null or empty
     * @global type $adb
     */
    static public function SetAllProductionNumbers()
    {
        global $adb;
        $query = "DROP TABLE IF EXISTS tmp_production;";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE tmp_production
        SELECT p.account_number, adv.pc_name FROM vtiger_portfolioinformation p
        JOIN vtiger_portfolioinformationcf pcf ON p.portfolioinformationid = pcf.portfolioinformationid
        JOIN vtiger_portfolios por ON p.account_number = por.portfolio_account_number
        JOIN vtiger_pc_advisors adv ON adv.pc_id = por.advisor_id
        WHERE pcf.production_number = '' 
        OR pcf.production_number IS NULL;";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_portfolioinformation p
        JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
        JOIN tmp_production t ON p.account_number = t.account_number
        SET cf.production_number = t.pc_name
        WHERE p.account_number = t.account_number;";
        $adb->pquery($query, array());
    }

    static public function TestPositionsAgainstTotalForAccount($account_number)
    {
        $total = PositionInformation_Module_Model::GetTotalvalueForAccountNumberUsingPositions($account_number);
        $id = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
        $record = PortfolioInformation_Record_Model::getInstanceById($id);
#        if($total >= $record->get('total_value')+10 || $total <= $record->get('total_value')-10){
        if ($total != $record->get('total_value')) {
            $positions = PositionInformation_Module_Model::GetPositionsForAccountNumber($account_number);
            $origination = $record->get('origination');
            $symbols = array();
            foreach ($positions AS $k => $v) {
                $symbols[] = $v['security_symbol'];
            }
            switch ($origination) {
                case stristr($origination, 'schwab'):
                    if (sizeof($symbols) > 0)
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsSchwab($symbols);
                    break;
                case stristr($origination, 'fidelity'):
                    ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsFidelity($symbols, true);
                    break;
            }
//                ModSecurities_ConvertCustodian_Model::UpdateSecurityType($record->get('origination'), $v['security_symbol']);
//            }
        }
    }

    static public function AssignPortfolioBasedOnRepCodes($account_number)
    {
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $params = array();


        $query = "DROP TABLE IF EXISTS UpdatePortfolios";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE UpdatePortfolios
                    SELECT crmid, production_number, master_production_number, omniscient_control_number, 0 AS user_id
                    FROM vtiger_crmentity e
                    JOIN vtiger_portfolioinformation p ON p.portfolioinformationid = e.crmid
                    JOIN vtiger_portfolioinformationcf cf ON cf.portfolioinformationid = e.crmid
                    WHERE (e.smownerid = 1 OR p.origination = 'td' OR p.origination = 'schwab' OR e.smownerid = '')
                    AND e.deleted = 0
                    AND (CHAR_LENGTH(production_number) > 1 OR CHAR_LENGTH(master_production_number) > 1 OR CHAR_LENGTH(omniscient_control_number) > 1)";
        $adb->pquery($query, array());
#WHERE advisor_control_number RLIKE (CONCAT('[[:<:]]',p.production_number,'[[:>:]]'))


        $query = "UPDATE UpdatePortfolios p
                    SET user_id = (SELECT id FROM vtiger_users u
                               WHERE omniscient_control_number LIKE (CONCAT('%',p.omniscient_control_number,'%'))
                               AND CHAR_LENGTH(omniscient_control_number) > 1 LIMIT 1)
                    WHERE p.user_id = 0
                    AND CHAR_LENGTH(p.omniscient_control_number) > 1";
        $adb->pquery($query, array());

        $query = "UPDATE UpdatePortfolios p
                    SET user_id = (SELECT id FROM vtiger_users u
                               WHERE advisor_control_number LIKE (CONCAT('%',p.production_number,'%'))
                               AND CHAR_LENGTH(advisor_control_number) > 1 LIMIT 1)
                    WHERE CHAR_LENGTH(p.production_number) > 1";
        $adb->pquery($query, array());
#WHERE advisor_control_number RLIKE (CONCAT('[[:<:]]',p.master_production_number,'[[:>:]]'))

        $query = "UPDATE UpdatePortfolios p
                    SET user_id = (SELECT id FROM vtiger_users u
                               WHERE advisor_control_number LIKE (CONCAT('%',p.master_production_number,'%'))
                               AND CHAR_LENGTH(advisor_control_number) > 1 LIMIT 1)
                    WHERE p.user_id = 0
                    AND CHAR_LENGTH(p.master_production_number) > 1";
        $adb->pquery($query, array());

        $query = "UPDATE vtiger_crmentity e
                    JOIN UpdatePortfolios up ON e.crmid = up.crmid
                    SET e.smownerid = up.user_id
                    WHERE up.user_id > 0";
        $adb->pquery($query, array());

    }

    static public function InvalidatePortfolioAndSetDeleted($pid){
        global $adb;
        $query = "UPDATE vtiger_portfolios SET isvalid = 0, account_closed = 1 WHERE portfolio_id = ?";
        $adb->pquery($query, array($pid));
    }

    static public function UpdatePortfolioTableSSNFromAccountNumber($ssn, $account_number){
        global $adb;
        $query = "UPDATE vtiger_portfolios SET portfolio_tax_id = ? WHERE portfolio_account_number = ?";
        $adb->pquery($query, array($ssn, $account_number));
    }

    static public function GetAccountNumbersFromCrmid($crmid)
    {
        global $adb;
        $instance = Vtiger_Record_Model::getInstanceById($crmid);
        $params = array();
        $ssn = array();
        $params[] = $crmid;
        $params[] = $crmid;
        $params[] = $crmid;

        switch ($instance->getModuleName()) {
            case "Contacts":
                $ssn[] = $instance->get('ssn');
                $params[] = $ssn;
                $questions = generateQuestionMarks($ssn);
                $or = " OR (tax_id IN ({$questions}) AND tax_id != '' AND tax_id != 0) ";
                break;
            case "Accounts":
                $ssn[] = GetSSNsForHousehold($crmid);
                $params[] = $ssn;
                $questions = generateQuestionMarks($ssn);
                $or = " OR (tax_id IN ({$questions}) AND tax_id != '' AND tax_id != 0) ";
                break;
        }

        $query = "SELECT p.account_number FROM vtiger_portfolioinformation p 
				  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                  JOIN vtiger_crmentity e ON p.portfolioinformationid = e.crmid
                  WHERE (e.crmid = ? OR p.contact_link = ? OR household_account = ? {$or})
                  AND e.deleted = 0";

        $result = $adb->pquery($query, $params);
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $accounts[] = $v['account_number'];
            }
            return $accounts;
        }
        return 0;
    }

    static public function GetAccountNumberFromCrmid($crmid)
    {
        global $adb;
        $instance = Vtiger_Module_Model::getInstance($crmid);

        $query = "SELECT p.account_number FROM
                  vtiger_portfolioinformation p 
                  JOIN vtiger_crmentity e ON p.portfolioinformationid = e.crmid
                  WHERE e.crmid = ? OR p.contact_link = ? OR household_account = ?
                  AND e.deleted = 0 AND p.accountclosed = 0";
        $result = $adb->pquery($query, array($crmid, $crmid, $crmid));
        if ($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'account_number');
        return 0;
    }

    static public function UpdateCustodianNameDirectly($account_number, $custodian)
    {
        global $adb;
        $query = "UPDATE vtiger_portfolioinformation SET origination = ? WHERE account_number = ?";
        $adb->pquery($query, array($custodian, $account_number));
    }

    static public function GetHouseholdEntityFromAccountNumber($account_number)
    {
        if (is_array($account_number))
            $account_number = $account_number[0];

        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
        if ($crmid) {
            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
            $household_id = $p->get('household_account');
            if ($household_id) {
                $household_instance = Accounts_Record_Model::getInstanceById($household_id);
                return $household_instance;
            }
        }
        return 0;
    }

    static public function GetContactEntityFromAccountNumber($account_number)
    {
        if (is_array($account_number))
            $account_number = $account_number[0];

        $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($account_number);
        if ($crmid) {
            $p = PortfolioInformation_Record_Model::getInstanceById($crmid);
            $contact_id = $p->get('contact_link');
            if ($contact_id) {
                $contact_instance = Contacts_Record_Model::getInstanceById($contact_id);
                return $contact_instance;
            }
        }
        return 0;
    }

    static public function CheckCloudForAccountNumber($custodian, $custodianDB, $account_number)
    {
        global $adb;
        $query = "SELECT account_number FROM {$custodianDB}.custodian_portfolios_{$custodian} WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number)) or die(mysql_error());
        if ($adb->num_rows($result) > 0)
            return 1;
        return 0;
    }

    static public function GetCrmidFromAccountNumber($account_number, $ignore_closed = false)
    {
        global $adb;
        if ($ignore_closed)
            $and = " AND accountclosed = 0 ";

        $query = "SELECT e.crmid 
                  FROM vtiger_crmentity e
                  JOIN vtiger_portfolioinformation p ON p.portfolioinformationid = e.crmid
                  WHERE p.account_number = ?
                  AND e.deleted = 0 {$and} ";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'crmid');
        return 0;
    }

    static public function GetCustodianFromAccountNumber($account_number)
    {
        global $adb;
        $query = "SELECT origination 
                  FROM vtiger_portfolioinformation p
                  WHERE p.account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'origination');
        return 0;
    }

    static public function CheckIfAccountExists($account_number)
    {
        global $adb;
        $account_number = str_replace("-", "", $account_number);
        $query = "SELECT * FROM vtiger_portfolioinformation WHERE REPLACE(account_number, '-', '') = ?";
        $r = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($r) > 0)
            return $adb->num_rows($r);
        return 0;
    }

    static public function SetAccountAsDeleted($accounts)
    {
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $query = "UPDATE vtiger_crmentity e
                  JOIN vtiger_portfolioinformation p ON p.portfolioinformationid = e.crmid
                  SET e.deleted = 1, p.accountclosed=1
                  WHERE p.account_number IN ({$questions})";
        $adb->pquery($query, array($accounts));
    }

    static public function SetAccountAsUnDeleted($accounts)
    {
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $query = "UPDATE vtiger_crmentity e
                  JOIN vtiger_portfolioinformation p ON p.portfolioinformationid = e.crmid
                  SET e.deleted = 0, p.accountclosed = 0
                  WHERE p.account_number IN ({$questions})";
        $adb->pquery($query, array($accounts));
    }

    static public function SetAccountTaxID($account_number, $tax_id)
    {
        global $adb;
        $account_number = str_replace('-', '', $account_number);
        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                  SET cf.tax_id = ?
                  WHERE p.account_number = ?";
        $adb->pquery($query, array($tax_id, $account_number));
    }

    static public function GetAllActiveAccountNumbers($manual_only = true)
    {
        global $adb;
        if ($manual_only)
            $and = "AND p.account_number LIKE ('M%')";
        else
            $and = "AND origination NOT IN ('fidelity', 'schwab', 'pershing', 'td', 'tdameritrade', 'tda')";

        $query = "SELECT account_number FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 {$and}";
        $result = $adb->pquery($query, array());
        $accounts = array();
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }

    static public function GetAccountsWithoutLastMonthIntervalCalculated(){
        global $adb;

        $query = "SELECT account_number FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND accountclosed = 0 
                  AND p.account_number NOT IN ( SELECT AccountNumber FROM intervals_daily 
                                                WHERE intervaltype = 'monthly'
                                                AND IntervalEndDate >= CURRENT_DATE() - INTERVAL 1 MONTH)";
        $result = $adb->pquery($query, array());
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }

    static public function GetAllOpenAccountNumbers()
    {
        global $adb;

        $query = "SELECT account_number FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND accountclosed = 0 ";
        $result = $adb->pquery($query, array());
        $accounts = array();
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }

    static public function GetAccountsToCalculateTWR()
    {
        global $adb;

        $query = "SELECT account_number FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND accountclosed = 0 
                  AND year(last_twr_calculated) <= year(curdate()) 
                  AND month(last_twr_calculated) < month(curdate())";
        $result = $adb->pquery($query, array());
        $accounts = array();
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $accounts[] = $v['account_number'];
            }
        }
        return $accounts;
    }

    static public function GetFreezeDateForAccount($account_number)
    {
        global $adb;
        $query = "SELECT stated_value_date FROM vtiger_portfolioinformationcf JOIN vtiger_portfolioinformation USING (portfolioinformationid) WHERE account_number = ? AND frozen = 1";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            $date = $adb->query_result($result, 0, 'stated_value_date');
            return $date;
        }
        return 0;
    }

    static public function ResetPortfolioValues($accounts)
    {
        global $adb;
        $accounts = RemoveDashes($accounts);
        $questions = generateQuestionMarks($accounts);
        $query = "UPDATE vtiger_portfolioinformation JOIN vtiger_portfolioinformationcf USING (portfolioinformationid)
			      SET total_value = 0, market_value = 0, cash_value = 0, cash = 0, equities = 0, fixed_income = 0, unsettled_cash = 0, 
			      	  other_value = 0, unclassified_value = 0, dividend_accrual = 0, short_market_value = 0, short_balance = 0, securities = 0
			      WHERE dashless IN ({$questions})";

        $adb->pquery($query, array($accounts));
    }

    static public function GetAllDashlessAndCustodian()
    {
        global $adb;
        echo "This needs to be done better, otherwise the script times out";
        exit;
        $query = "SELECT dashless, origination FROM vtiger_portfolioinformation p WHERE origination IN ('millenium', 'fidelity', 'schwab', 'td', 'pershing')";
        $result = $adb->pquery($query, array());
        $accounts = array();
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $accounts[] = array("account_number" => $v['dashless'],
                    "custodian" => $v['origination']);
            }
        }
        return $accounts;
    }

    static public function UpdateAllPortfolioInceptionDates()
    {
        global $adb;
        $query = "DROP TABLE IF EXISTS tmp_inception;";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE tmp_inception
        SELECT p.portfolio_account_number, t.portfolio_id, t.trade_date
        FROM vtiger_pc_transactions t FORCE INDEX (TradeDate)
        JOIN vtiger_portfolios p ON t.portfolio_id = p.portfolio_id
        WHERE t.portfolio_id IN (select portfolio_id from vtiger_portfolioinformation pin
                                                         JOIN vtiger_portfolios p ON p.portfolio_account_number = pin.account_number)
        group by t.portfolio_id
        ORDER BY t.trade_date ASC;";
        $adb->pquery($query, array());

        $query = "update vtiger_portfolioinformation p
        JOIN tmp_inception i ON p.account_number = i.portfolio_account_number
        SET p.inceptiondate = i.trade_date
        WHERE p.account_number = i.portfolio_account_number;";
        $adb->pquery($query, array());

        return 1;
    }

    static public function FindAndFixEmptyInceptionDates()
    {
        global $adb;
        $query = "SELECT p.account_number
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE p.inceptiondate is null";
        $result = $adb->pquery($query, array());

        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                self::UpdateInceptionDate($v['account_number']);
            }
        }
    }

    static public function UpdateInceptionDate($account_number)
    {
        global $adb;
        $query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            $portfolio_id = $adb->query_result($result, 0, 'portfolio_id');
            $query = "SELECT trade_date from vtiger_pc_transactions
                      WHERE portfolio_id = ?
                      ORDER BY trade_date ASC";
            $result = $adb->pquery($query, array($portfolio_id));
            if ($adb->num_rows($result) > 0) {
                $trade_date = $adb->query_result($result, 0, 'trade_date');
                $query = "UPDATE vtiger_portfolioinformation SET inceptiondate = ? WHERE account_number = ?";
                $adb->pquery($query, array($trade_date, $account_number));
                return 1;
            }
        }
        return 0;
    }

    static public function GetAccountNumbersFromSSN($ssn)
    {
        global $adb;
        if (!is_array($ssn))
            $ssns[] = $ssn;
        $ssns = $ssn;
        foreach ($ssns AS $k => $v)
            $ssns[$k] = str_replace('-', '', $v);

        $questions = generateQuestionMarks($ssns);

        $query = "SELECT account_number FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid 
                  WHERE REPLACE(cf.tax_id, '-', '') IN ({$questions}) AND REPLACE(cf.tax_id, '-', '') != '' AND accountclosed = 0";
        $result = $adb->pquery($query, array($ssns));
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $t[] = $v['account_number'];
            }
            return $t;
        }
        return 0;
    }

    static public function GetAccountNumbersFromOmniscientControlNumber($ccn, $limit = null)
    {
        global $adb;
        if (strlen($limit) > 0)
            $limit = " LIMIT {$limit} ";
        $questions = generateQuestionMarks($ccn);

        $query = "SELECT account_number FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid 
                  WHERE omniscient_control_number IN ({$questions}) AND e.deleted = 0 AND p.accountclosed = 0 AND stated_value_date >= '2018-11-01' {$limit}";
        $result = $adb->pquery($query, array($ccn));
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $t[] = $v['account_number'];
            }
            return $t;
        }
        return 0;
    }

	static public function GetChartColorForTitle($title){
		global $adb;
		$query = "SELECT color FROM vtiger_chart_colors WHERE title = ?";
		$result = $adb->pquery($query, array($title));
		if($adb->num_rows($result) > 0){
			return $adb->query_result($result, 0, 'color');
		}
		return 0;
	}

    static public function GetRecordIDFromAccountNumber($account_number)
    {
        global $adb;
        $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, 'portfolioinformationid');
        }
        return 0;
    }

    /**
     * Update the specified PortfolioInformation field.  Months represents the number of months to calculate
     * @param $field
     * @param $months
     */
    static public function UpdatePortfolioInformationIntervalValue($field, $num_months)
    {
        global $adb;
        $query = "DROP TABLE IF EXISTS CalculatedIntervals";
        $adb->pquery($query, array());
        $query = "DROP TABLE IF EXISTS LatestIntervals";
        $adb->pquery($query, array());
        $query = "DROP TABLE IF EXISTS TrailingIntervals";
        $adb->pquery($query, array());
        $query = "DROP TABLE IF EXISTS CalculatedTrailing";
        $adb->pquery($query, array());
        $query = "DROP TABLE IF EXISTS TrailingPercent";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE TrailingIntervals
                  SELECT i.* FROM intervals i
                  WHERE i.IntervalEndDate >= DATE_SUB(NOW(), INTERVAL {$num_months} MONTH) 
                  AND i.IntervalEndDate <= NOW()
                  GROUP BY AccountNumber, IntervalEndDate
                  ORDER BY AccountNumber";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE CalculatedTrailing
                  SELECT AccountNumber, IntervalEndDate, (1 + NetReturnAmount/100) AS NetReturnAmount FROM TrailingIntervals";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE TrailingPercent 
                  SELECT AccountNumber, ((exp(sum(log(coalesce(NetReturnAmount,1))))) - 1) * 100 AS TrailingPercent FROM CalculatedTrailing GROUP BY AccountNumber";
        $adb->pquery($query, array());

        $query = "UPDATE TrailingPercent i
                  JOIN vtiger_portfolioinformation p ON p.account_number = i.AccountNumber
                  JOIN vtiger_portfolioinformationcf cf ON cf.portfolioinformationid = p.portfolioinformationid
                  SET cf.{$field} = i.TrailingPercent";
        $adb->pquery($query, array());
    }


    static public function GetAccountSumTotals($accounts)
    {
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $params[] = $accounts;

        $query = "SELECT SUM(total_value) AS total_value, SUM(securities) AS securities_total, SUM(cash) AS cash_total 
				  FROM vtiger_portfolioinformation p
				  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
				  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
				  WHERE account_number IN ({$questions})
				  AND e.deleted = 0 AND (p.accountclosed = 0 OR p.accountclosed IS NULL)";
        $result = $adb->pquery($query, $params);
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $totals = array("total" => $v['total_value'],
                    "securities_total" => $v['securities_total'],
                    "cash_total" => $v['cash_total']);
            }
            return $totals;
        }
        return 0;
    }

    static public function GetAccountIndividualTotals($accounts)
    {
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $params[] = $accounts;

        $query = "SELECT * FROM vtiger_portfolioinformation p
				  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
				  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
				  WHERE account_number IN ({$questions})
				  AND e.deleted = 0 AND (p.accountclosed = 0 OR p.accountclosed IS NULL)";
        $result = $adb->pquery($query, $params);
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $account_info[] = array("account_number" => $v['account_number'],
                    "total" => $v['total_value'],
                    "securities" => $v['securities'],
                    "cash" => $v['cash']);
            }
            return $account_info;
        }
        return 0;
    }

    static public function GetAllChartColors()
    {
        global $adb;
        $query = "SELECT title, color FROM vtiger_chart_colors";
        $result = $adb->pquery($query, array());
        $colors = array();
        if ($adb->num_rows($result) > 0) {
            foreach ($result AS $k => $v) {
                $colors[$v['title']] = $v['color'];
            }
            return $colors;
        }
        return 0;
    }

    static public function RecalculatePortfolio($account_number)
    {
        $crmid = self::GetCrmidFromAccountNumber($account_number);
        $record = Vtiger_Record_Model::getInstanceById($crmid, 'PortfolioInformation');
        if (strlen($record->get('origination') == 0)) {
            $custodian = PortfolioInformation_ConvertCustodian_Model::DetermineCustodian($record->get('account_number'));
            if ($custodian != "")
                PortfolioInformation_Module_Model::UpdateCustodianNameDirectly($record->get('account_number'), $custodian);
        }

        $nodata = 0;
        $asset_allocation = new PortfolioInformation_AssetAllocation_Action();
        $custodian = $record->get("origination");
        $date = date("Y-m-d", strtotime("today - 1 Weekday"));
        $clist = array("fidelity", "schwab", "td", "pershing", "manual");

        if (!$record->get('contact_link') && $record->get('account_number') != '') {//A contact hasn't been set, check if one exists
            PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios($record->get('account_number'));
            PortfolioInformation_ConvertCustodian_Model::LinkHouseholdsToPortfolios($record->get('account_number'));
        }
        //contact_link    household_account
#		print_r($data);
#		echo $record->get('contact_link');

        if (in_array(strtolower($custodian), $clist)) {
            if ($custodian) {
                if (PositionInformation_ConvertCustodian_Model::IsTherePositionDataForDate($custodian, $date, $record->get('account_number')) == 0) {
#					$date = date("Y-m-d", strtotime("today - 2 Weekday"));
                    $date = date("Y-m-d", strtotime("today - 2 Weekday"));
                    if (PositionInformation_ConvertCustodian_Model::IsTherePositionDataForDate($custodian, $date, $account_number) == 0) {
                        $date = date("Y-m-d", strtotime("today - 3 Weekday"));
                        if (PositionInformation_ConvertCustodian_Model::IsTherePositionDataForDate($custodian, $date, $account_number) == 0) {
                            $nodata = 1;
                        }
                    }
                }

                $freeze = PortfolioInformation_Module_Model::GetFreezeDateForAccount($account_number);
                if ($freeze) {
                    $date = $freeze;
                    $nodata = 0;
                }

                if ($nodata) {
                    PositionInformation_Module_Model::ResetPositionValues($record->get('account_number'));
                }

                switch (true) {
                    case stristr($custodian, 'Fidelity'):
                        if (!$nodata) {
                            PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
                            PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFidelity($date, $record->get('account_number'));
                            $symbols = PositionInformation_Module_Model::GetSymbolsForAccountNumber($record->get('account_number'));
                            if (sizeof($symbols) > 0)
                                ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsFidelity($symbols, true);
//                            PortfolioInformation_ConvertCustodian_Model::UpdateAllFidelityPortfoliosWithLatestInfoForAccount($record->get('account_number'));
                            PositionInformation_ConvertCustodian_Model::UpdatePositionInformationFidelity($date, $record->get('account_number'), 1);
//							PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
                        }
                        break;
                    case stristr($custodian, "td"):
                        if (!$nodata) {
                            PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
                            PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date, $record->get('account_number'));
                            $symbols = PositionInformation_Module_Model::GetSymbolsForAccountNumber($record->get('account_number'));
                            if (sizeof($symbols) > 0)
                                ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsTD($symbols, true);
                            PositionInformation_ConvertCustodian_Model::UpdatePositionInformationTD($date, $record->get('account_number'), 1);
//							PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
                        }
                        break;
                    case stristr($custodian, "schwab"):
                        if (!$nodata) {
                            PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
                            #					PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesSchwab(null, $record->get('account_number'));
                            $symbols = PositionInformation_Module_Model::GetSymbolsForAccountNumber($record->get('account_number'), 8);
                            if (sizeof($symbols) > 0)
                                ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsSchwab($symbols);
                            $symbols = PositionInformation_Module_Model::GetSymbolsForAccountNumber($record->get('account_number'));
                            if (sizeof($symbols) > 0)
                                ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsSchwab($symbols);
                            PositionInformation_ConvertCustodian_Model::UpdatePositionInformationSchwab($date, $record->get('account_number'), 1);
//							PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
                            PortfolioInformation_ConvertCustodian_Model::UpdateAllSchwabPortfoliosWithLatestInfoForAccount($record->get('account_number'));
                        }
                        break;
                    case stristr($custodian, "pershing"):
                        if (!$nodata) {
                            /*							PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
                                                        PositionInformation_Module_Model::ResetPositionValues($record->get('account_number'));
                                                        $symbols = PositionInformation_Module_Model::GetSymbolsForAccountNumber($record->get('account_number'));
                                                        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesPershing($date, $record->get('account_number'));
                                                        if(sizeof($symbols) > 0)
                                                            ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsPershing($symbols, true);
                                                        PositionInformation_ConvertCustodian_Model::UpdatePositionInformationPershing($date, $record->get('account_number'), 1);*/
#							PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
                        }
                        break;
                    case stristr($custodian, "manual"):
                        PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
                        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
                        break;
                    default:
                        if ($asset_allocation->IsInPC($record->get('account_number')))
                            $asset_allocation->UpdateIndividualAccount($crmid, $record->get('account_number'));
                        break;
                }
            }
        } else {
            PortfolioInformation_Module_Model::ResetPortfolioValues($record->get('account_number'));
            PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFromPositions($custodian, $record->get('account_number'));
            if ($asset_allocation->IsInPC($record->get('account_number'))) {
#				echo "TESTING!";
#				exit;
                $asset_allocation->UpdateIndividualAccount($crmid, $record->get('account_number'));
            }
        }
    }

    /**
     * Function to get relation query for particular module with function name
     * @param <record> $recordId
     * @param <String> $functionName
     * @param Vtiger_Module_Model $relatedModule
     * @return <String>
     */
    public function getRelationQuery($recordId, $functionName, $relatedModule)
    {

        $relatedModuleName = $relatedModule->get('name');

        $query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
        return $query;///BOTTOM SECTION NO LONGER NEEDED SINCE VTIGER 7 (left in place just in case)
        /*		if($relatedModuleName == "PositionInformation"){

                    $query = explode("FROM", $query);

                    $selectedColumns = array_map('trim',explode(",",$query[0]));

                    $qtyKey = array_search('vtiger_positioninformation.quantity', $selectedColumns);

                    if($qtyKey)
                        $selectedColumns[$qtyKey] = 'SUM(vtiger_positioninformation.quantity) AS quantity';
                    else
                        $selectedColumns[] = 'SUM(vtiger_positioninformation.quantity) AS quantity';

                    $current_value_key = array_search('vtiger_positioninformation.current_value', $selectedColumns);

                    if($current_value_key)
                        $selectedColumns[$current_value_key] = 'SUM(vtiger_positioninformation.current_value) AS current_value';
                    else
                        $selectedColumns[] = 'SUM(vtiger_positioninformation.current_value) AS current_value';

                    $costKey = array_search('vtiger_positioninformation.cost_basis', $selectedColumns);

                    if($costKey)
                        $selectedColumns[$costKey] = 'SUM(vtiger_positioninformation.cost_basis) AS cost_basis';
                    else
                        $selectedColumns[] = 'SUM(vtiger_positioninformation.cost_basis) AS cost_basis';

                    $glKey = array_search('vtiger_positioninformation.unrealized_gain_loss', $selectedColumns);

                    if($glKey)
                        $selectedColumns[$glKey] = 'SUM(vtiger_positioninformation.unrealized_gain_loss) AS unrealized_gain_loss';
                    else
                        $selectedColumns[] = 'SUM(vtiger_positioninformation.unrealized_gain_loss) AS unrealized_gain_loss';

                    $wtKey =  array_search('vtiger_positioninformation.weight', $selectedColumns);

                    if($wtKey)
                        $selectedColumns[$wtKey] = 'SUM(current_value)/@global_total*100 AS weight';
                    else
                        $selectedColumns[] = 'SUM(current_value)/@global_total*100 AS weight';

                    $query[0] = implode(",", $selectedColumns);

                    $query = implode(" FROM ", $query);
                }*/

        return $query;
    }

    static public function GetEndValuesForAccounts($accounts, $start = null, $end = null, $intervalType = 'Monthly')
    {
        global $adb;
        $params = array();
        $and = "";
        $questions = generateQuestionMarks($accounts);
        $params[] = $accounts;

        if ($start) {
            $and .= " AND IntervalEndDate >= ? ";
            $params[] = $start;
        }
        if ($end) {
            $and .= " AND IntervalEndDate <= ? ";
            $params[] = $end;
        }

        $query = "DROP TABLE IF EXISTS IntervalTemp";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE IntervalTemp SELECT i.* FROM intervals_daily i WHERE AccountNumber IN ({$questions}) AND IntervalType = ?";
        $adb->pquery($query, array($accounts, $intervalType));

//        $query = "SELECT DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDate, SUM(IntervalEndValue) AS IntervalEndValue, SUM(NetFlowAmount) AS NetFlowAmount, SUM(IntervalEndValue) - (SUM(IntervalBeginValue) + SUM(NetFlowAmount)) AS InvestmentReturn
        $query = "SELECT DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDate, SUM(IntervalEndValue) AS IntervalEndValue, SUM(NetFlowAmount) AS NetFlowAmount, SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue) AS InvestmentReturn, (SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue)) / SUM(IntervalBeginValue) * 100 AS periodreturn
                  FROM IntervalTemp
                  WHERE AccountNumber IN ({$questions}) {$and} GROUP BY IntervalEndDate";
        $result = $adb->pquery($query, $params);
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $intervals[] = array(
                    "end_date" => $v['intervalenddate'],
                    "end_value" => $v['intervalendvalue'],
                    "net_flow" => $v['netflowamount'],
                    "investment_return" => $v['investmentreturn'],
                    "period_return" => $v['periodreturn']);
            }
            return $intervals;
        }
        return 0;
    }

    static public function CreateIntervalTempTable($accounts, $start = null, $end = null, &$and, $intervaltype)
    {
        global $adb;
        $params = array();
        $and = " AND intervaltype = '{$intervaltype}' ";
        $questions = generateQuestionMarks($accounts);
        $params[] = $accounts;

        if ($start) {
            $and .= " AND IntervalEndDate >= ? ";
            $params[] = $start;
        }
        if ($end) {
            $and .= " AND IntervalEndDate <= ? ";
            $params[] = $end;
        }

        $query = "DROP TABLE IF EXISTS IntervalTemp";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS HitAgainst";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE IntervalTemp SELECT i.* FROM Intervals_daily i WHERE AccountNumber IN ({$questions}) {$and} ";
        $adb->pquery($query, $params);

        $query = "CREATE TEMPORARY TABLE HitAgainst SELECT * FROM IntervalTemp";
        $adb->pquery($query, array());

        return $params;
    }

    static private function GetIntervalBeginValue()
    {
        global $adb;
        $query = "SELECT SUM(IntervalBeginValue) AS BeginValue, DATE_FORMAT(IntervalBeginDate, '%m-%d-%Y') AS IntervalBeginDate FROM IntervalTemp WHERE IntervalEndDate = (SELECT MIN(IntervalEndDate) FROM HitAgainst)";
        $result = $adb->pquery($query, array());
        $begin = array("begin_value" => $adb->query_result($result, 0, 'beginvalue'),
            "begin_date" => $adb->query_result($result, 0, 'intervalbegindate'));
        return $begin;
    }

    static private function GetFlowAmount()
    {
        global $adb;
        $query = "SELECT SUM(NetFlowAmount) AS netflowamount FROM IntervalTemp";
        $result = $adb->pquery($query, array());
        return $adb->query_result($result, 0, 'netflowamount');
    }

    static private function GetInvestmentReturn()
    {
        return (self::GetIntervalEndValue()['end_value'] - self::GetFlowAmount() - self::GetIntervalBeginValue()['begin_value']);
/*      FLAW WITH THE LOGIC BELOW... It is summing all of the begin and end values in the table when we need the start and end dates only
        global $adb;
        $query = "SELECT SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue) AS InvestmentReturn FROM IntervalTemp";
        $result = $adb->pquery($query, array());
        $r = $adb->query_result($result, 0, 'investmentreturn');
        echo $r;exit;*/
    }

    static private function GetIntervalEndValue()
    {
        global $adb;
        $query = "SELECT SUM(IntervalEndValue) AS EndValue, DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDate FROM IntervalTemp WHERE IntervalEndDate = (SELECT MAX(IntervalEndDate) FROM HitAgainst)";
        $result = $adb->pquery($query, array());
        $end = array("end_value" => $adb->query_result($result, 0, 'endvalue'),
            "end_date" => $adb->query_result($result, 0, 'intervalenddate'));
        return $end;
    }

    static public function GetSummerizedIntervalInfo(array $accounts, $start = null, $end = null, $intervaltype = 'monthly')
    {
        $unused = null;
        self::CreateIntervalTempTable($accounts, $start, $end,$unused, $intervaltype);

        $begin = self::GetIntervalBeginValue();
        $flow = self::GetFlowAmount();
        $investment = self::GetInvestmentReturn();
        $end = self::GetIntervalEndValue();

        $summary = array("begin_value" => $begin['begin_value'],
            "begin_date" => $begin['begin_date'],
            "flow_value" => $flow,
            "investment_return_value" => $investment,
            "end_value" => $end['end_value'],
            "end_date" => $end['end_date']);
        return $summary;
    }

    static public function ReturnValidAccountsFromArray(array $accounts)
    {
        global $adb;
        $account_numbers = array();
        $params = array();
        $params[] = $accounts;
        $questions = generateQuestionMarks($accounts);
        $query = "SELECT account_number FROM vtiger_portfolioinformation WHERE account_number IN ({$questions}) AND accountclosed=0";
        $result = $adb->pquery($query, $params);
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $account_numbers[] = $v['account_number'];
            }
            return $account_numbers;
        }
        return 0;
    }

    static public function CalculateMonthlyIntervalsForAccounts(array $accounts, $start = null, $end = null)
    {
        global $adb;
        if (!$start)
            $start = '1900-01-01';
        if (!$end)
            $end = date("Y-m-d");

        foreach ($accounts AS $k => $v) {
            $custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v);
            $query = "CALL CALCULATE_MONTHLY_INTERVALS_LOOP(?, ?, ?, ?, ?)";
#            CALL CALCULATE_MONTHLY_INTERVALS_LOOP("34300882", "1900-01-01", "2017-10-12", "schwab", "live_omniscient");
            $adb->pquery($query, array($v, $start, $end, $custodian, 'live_omniscient'));
        }
    }

    static public function CalculateDailyIntervalsForAccounts(array $accounts, $start = null, $end = null)
    {
        global $adb;
        if (!$start)
            $start = '1900-01-01';
        if (!$end)
            $end = date("Y-m-d");

        foreach ($accounts AS $k => $v) {
            $custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v);
            $query = "CALL CALCULATE_DAILY_INTERVALS_LOOP(?, ?, ?, ?, ?)";
#            CALL CALCULATE_MONTHLY_INTERVALS_LOOP("34300882", "1900-01-01", "2017-10-12", "schwab", "live_omniscient");
            $adb->pquery($query, array($v, $start, $end, $custodian, 'live_omniscient'));
        }
    }

    static public function GetIntervalsForAccounts(array $accounts, $start = null, $end = null)
    {
        global $adb;
        $and = "";
        $questions = generateQuestionMarks($accounts);
        $params = array();
        /*
                $params = self::CreateIntervalTempTable($accounts, $start, $end, $and);

                $query = "SELECT AccountNumber, DATE_FORMAT(IntervalBeginDate, '%m-%d-%Y') AS IntervalBeginDateFormatted, DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDateFormatted, SUM(IntervalBeginValue) AS IntervalBeginValue, SUM(IntervalEndValue) AS IntervalEndValue, SUM(NetFlowAmount) AS NetFlowAmount, SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue) AS InvestmentReturn, (SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue)) / SUM(IntervalBeginValue) * 100 AS periodreturn
                          FROM IntervalTemp
                          WHERE AccountNumber IN ({$questions}) {$and} GROUP BY IntervalEndDate ORDER BY IntervalEndDate DESC";*/
        $query = "CALL MONTHLY_INTERVALS_CALCULATED(\"{$questions}\")";
        $adb->pquery($query, array($accounts));

        if (strlen($start) > 0 && strlen($end) > 0) {
            $where = " WHERE IntervalEndDate >= ? AND IntervalEndDate <= ? ";
            $params[] = $start;
            $params[] = $end;
        }
        $query = "SELECT AccountNumber, DATE_FORMAT(IntervalBeginDate, '%m-%d-%Y') AS IntervalBeginDateFormatted, 
                         DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDateFormatted, IntervalBeginValue, IntervalEndValue, NetFlowAmount, 
                         InvestmentReturn, periodreturn 
                  FROM calculated_intervals {$where} 
                  GROUP BY IntervalEndDate 
                  ORDER BY IntervalEndDate DESC";
        $result = $adb->pquery($query, $params);

        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $intervals[] = array("account_number" => $v['accountnumber'],
                    "begin_date" => $v['intervalbegindateformatted'],
                    "end_date" => $v['intervalenddateformatted'],
                    "begin_value" => $v['intervalbeginvalue'],
                    "end_value" => $v['intervalendvalue'],
                    "net_flow" => $v['netflowamount'],
                    "period_return" => $v['periodreturn'],
                    "investment_return" => $v['investmentreturn']);
            }
            return $intervals;
        }

        return 0;
    }

    static public function GetDailyIntervalsForAccountsWithDateFilter(array $accounts, $start = null, $end = null)
    {
        global $adb;
        $and = "";
        $questions = generateQuestionMarks($accounts);
        $params = array();
        $where = "WHERE AccountNumber IN ({$questions}) ";
        $params[] = $accounts;
        if (strlen($start) > 0 && strlen($end) > 0) {
            $where .= " AND IntervalEndDate >= ? AND IntervalEndDate <= ? ";
            $params[] = $start;
            $params[] = $end;
        }

        $where .= " AND IntervalType = 'Daily' ";
        $query = "SELECT AccountNumber, DATE_FORMAT(IntervalBeginDate, '%m-%d-%Y') AS IntervalBeginDateFormatted, 
                         DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDateFormatted, IntervalBeginValue, IntervalEndValue, NetFlowAmount, 
                         InvestmentReturn, (SUM(IntervalEndValue) - SUM(NetFlowAmount) - SUM(IntervalBeginValue)) / SUM(IntervalBeginValue) * 100 AS periodreturn 
                  FROM intervals_daily {$where} 
                  GROUP BY IntervalEndDate 
                  ORDER BY IntervalEndDate DESC";
        $result = $adb->pquery($query, $params);

        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $intervals[] = array("account_number" => $v['accountnumber'],
                    "begin_date" => $v['intervalbegindateformatted'],
                    "end_date" => $v['intervalenddateformatted'],
                    "begin_value" => $v['intervalbeginvalue'],
                    "end_value" => $v['intervalendvalue'],
                    "net_flow" => $v['netflowamount'],
                    "period_return" => $v['periodreturn'],
                    "investment_return" => $v['investmentreturn']);
            }
            return $intervals;
        }

        return 0;
    }

    static public function GetDailyIntervalsForAccounts(array $accounts)
    {
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;

        $query = "SELECT intervalbegindate, 
                         intervalenddate, 
                         DATE_FORMAT(intervalbegindate, '%m-%d-%Y') AS intervalbegindate_formatted, 
                         DATE_FORMAT(intervalenddate, '%m-%d-%Y') AS intervalenddate_formatted, 
                         intervalbeginvalue, intervalendvalue, netflowamount, netreturnamount, grossreturnamount, expenseamount, 
                         incomeamount, journalamount, tradeamount, investmentreturn
                  FROM intervals_daily 
                  WHERE AccountNumber IN ({$questions}) AND IntervalType = 'daily'
                  GROUP BY intervalenddate 
                  ORDER BY intervalenddate DESC";
        $result = $adb->pquery($query, $params);

        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $intervals[] = array("account_number" => $v['accountnumber'],
                    "begin_date" => $v['intervalbegindate_formatted'],
                    "end_date" => $v['intervalenddate_formatted'],
                    "begin_value" => $v['intervalbeginvalue'],
                    "end_value" => $v['intervalendvalue'],
                    "net_flow" => $v['netflowamount'],
                    "net_return" => $v['netreturnamount'],
                    "gross_return" => $v['grossreturnamount'],
                    "expense_amount" => $v['expenseamount'],
                    "incomeamount" => $v['incomeamount'],
                    "journalamount" => $v['journalamount'],
                    "tradeamount" => $v['tradeamount'],
                    "investmentreturn" => $v['investmentreturn']);
            }
            return $intervals;
        }

        return 0;
    }


    static public function GetMonthlyIntervalDatesStartDate(&$start_date, &$end_date)
    {
        global $adb;
        if (strlen($start_date) > 0 && strlen($end_date) > 0) {
            $query = "SELECT MIN(IntervalBeginDate) AS IntervalBeginDate, MAX(IntervalEndDate) AS IntervalEndDate  
                      FROM CALCULATED_INTERVALS 
                      WHERE IntervalEndDate >= ? 
                      AND IntervalEndDate <= ?";
            $result = $adb->pquery($query, array($start_date, $end_date));
            if ($adb->num_rows($result) > 0) {
                $start_date = $adb->query_result($result, 0, 'intervalbegindate');
                $end_date = $adb->query_result($result, 0, 'intervalenddate');
                return;
            }
        }
        $start_date = 0;
        $end_date = 0;
    }

    function getTop10AUMPortfolios($headerColumns)
    {

        $db = PearDatabase::getInstance();

        $moduleName = $this->getName();

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);

        $headerColumns = array_merge($headerColumns, array("id"));

        $queryGenerator->setFields($headerColumns);

        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);

        if (in_array('total_value', $headerColumns))
            $queryGenerator->addCondition("total_value", "", "ny");

        $query = $queryGenerator->getQuery();

        $query .= " AND DATE_FORMAT( vtiger_crmentity.modifiedtime, '%Y-%m-%d' ) <= DATE_FORMAT(NOW() - INTERVAL 1 DAY, '%Y-%m-%d')";

        $query .= ' ORDER BY vtiger_portfolioinformation.total_value DESC LIMIT 0, 10';

        $query = str_replace(" FROM ", ",vtiger_crmentity.crmid as id FROM ", $query);

        $result = $db->pquery($query, array());

        $moduleFocus = CRMEntity::getInstance($moduleName);

        $entries = $listviewController->getListViewRecords($moduleFocus, $moduleName, $result);

        $listviewRecords = array();
        $index = 0;
        foreach ($entries as $id => $record) {
            $rawData = $db->query_result_rowdata($result, $index++);
            $record['id'] = $id;
            $listviewRecords[$id] = $this->getRecordFromArray($record, $rawData);
        }

        return $listviewRecords;
    }

    function getTop10RevenuePortfolios($headerColumns)
    {

        $db = PearDatabase::getInstance();

        $moduleName = $this->getName();

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);

        $headerColumns = array_merge($headerColumns, array("id"));

        $queryGenerator->setFields($headerColumns);

        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);

        $query = $queryGenerator->getQuery();

        $query .= " AND vtiger_transactionscf.transaction_type = 'Expense' AND vtiger_transactionscf.transaction_activity = 'Management fee'";

        $query .= ' GROUP BY vtiger_transactions.account_number ORDER BY annual_management_fee DESC LIMIT 0, 10';

        $query = str_replace(" FROM vtiger_portfolioinformation", ",vtiger_crmentity.crmid as id, 
		SUM(vtiger_transactionscf.net_amount) as annual_management_fee FROM vtiger_portfolioinformation
		INNER JOIN vtiger_transactions ON vtiger_transactions.account_number = vtiger_portfolioinformation.account_number 
		INNER JOIN vtiger_transactionscf ON vtiger_transactions.transactionsid = vtiger_transactionscf.transactionsid ", $query);

        $result = $db->pquery($query, array());

        $moduleFocus = CRMEntity::getInstance($moduleName);

        $entries = $listviewController->getListViewRecords($moduleFocus, $moduleName, $result);

        $listviewRecords = array();
        $index = 0;
        foreach ($entries as $id => $record) {
            $rawData = $db->query_result_rowdata($result, $index++);
            $record['id'] = $id;
            $listviewRecords[$id] = $this->getRecordFromArray($record, $rawData);
        }

        return $listviewRecords;
    }

    static public function GetActivityPicklistValues()
    {
        global $adb;
        $query = "SELECT transaction_activity FROM vtiger_transaction_activity";
        $result = $adb->pquery($query, array());
        $activities = array();
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $activities[] = $v['transaction_activity'];
            }
            return $activities;
        }
        return 0;
    }

    static public function GetMappedAccountType($custodian, $type)
    {
        global $adb;

        $query = "SELECT account_type FROM vtiger_accounttype_mapping WHERE custodian = ? AND custodian_account_type = ?";
        $result = $adb->pquery($query, array($custodian, $type));
        if ($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, 'account_type');
        }
        return 0;
    }

    /**
     * Update the TD portfolio type based on mapping.
     * The account type code is fed into Omniscient VIA the API and is NOT in the cloud, so omniscient itself is responsible for filling this in.
     */
    static public function UpdatePortfolioTypeTDOnly()
    {return;
        global $adb;
        $query = "UPDATE vtiger_portfolioinformation p
	             JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
	             JOIN custodian_omniscient.custodian_portfolios_td ptd ON ptd.account_number = p.account_number
	             JOIN portfolios_mapping_td map ON ptd.account_type = map.td_type 	             
	             SET cf.cf_2549 = CASE WHEN map.omniscient_type != '' AND map.omniscent_type IS NOT NULL THEN map.omniscient_type ELSE ptd.account_type END
	             WHERE origination = 'TD'";
        $adb->pquery($query, array());
    }

    static public function UpdatePortfolioTDInfo()
    {
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $increment = 300;
        $counter = 1;
        $x = 1;
        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, 1, 1);
        $max = $tmp['model']['getAccountsJson']['responseInfo']['totalSize'];
        $query = "UPDATE vtiger_portfolioinformation 
                  JOIN vtiger_portfolioinformationcf USING (portfolioinformationid) 
                  SET custodian_inception = ?, production_number = ?, accountclosed = ?, account_type_code = ?
                  WHERE account_number = ?";
        while ($counter < $max) {
            $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, $counter, $counter + $increment);
            foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
                if ($v['accountStatus'] == 'CLOSED')
                    $closed = 1;
                else
                    $closed = 0;

                $params = array($v['dateOpened'], $v['repCode'], $closed, $v['accountCategoryCode'], $v['accountNumber']);
                $adb->pquery($query, $params);
            }
            $counter += $increment;
        }
    }

    static public function UpdatePortfolioTDInfoBackup()//The new version above removes accountType so it can be handled by Java
    {
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $increment = 300;
        $counter = 1;
        $x = 1;
        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, 1, 1);
        $max = $tmp['model']['getAccountsJson']['responseInfo']['totalSize'];
        $query = "UPDATE vtiger_portfolioinformation 
                  JOIN vtiger_portfolioinformationcf USING (portfolioinformationid) 
                  SET custodian_inception = ?, production_number = ?, account_type = ?, accountclosed = ?, account_type_code = ?
                  WHERE account_number = ?";
        while ($counter < $max) {
            $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, $counter, $counter + $increment);
            foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
                if ($v['accountStatus'] == 'CLOSED')
                    $closed = 1;
                else
                    $closed = 0;

                $params = array($v['dateOpened'], $v['repCode'], $v['accountType'], $closed, $v['accountCategoryCode'], $v['accountNumber']);
                $adb->pquery($query, $params);
            }
            $counter += $increment;
        }
    }

    static public function AuditTDRepCodes()
    {
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $increment = 300;
        $counter = 1;

        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, 1, 1);
        $max = $tmp['model']['getAccountsJson']['responseInfo']['totalSize'];

        $accounts = array();
        $reps = array();
        while ($counter < $max) {
            $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, $counter, $counter + $increment);
            foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
                $accounts[$v['accountNumber']]['rep_code'] = $v['repCode'];
                $accounts[$v['accountNumber']]['status'] = $v['accountStatus'];
                $reps[$v['repCode']] += 1;
            }
            $counter += $increment;
        }

        $query = "INSERT INTO rep_code_counts (rep_code, account_count, custodian) VALUES (?, ?, 'td') ON DUPLICATE KEY UPDATE account_count = VALUES(account_count), custodian = VALUES(custodian)";
        foreach ($reps AS $k => $v) {
            $adb->pquery($query, array($k, $v));
        }

        $query = "INSERT INTO account_rep_codes (account_number, rep_code, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rep_code = VALUES(rep_code), status = VALUES(status)";
        foreach ($accounts AS $k => $v) {
            $adb->pquery($query, array($k, $v['rep_code'], $v['status']));
        }
    }

    static public function UpdateTDAccountTypes()
    {
        return;
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $increment = 300;
        $counter = 1;
        $x = 1;
        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, 1, 1);
        $max = $tmp['model']['getAccountsJson']['responseInfo']['totalSize'];
        $query = "INSERT INTO portfolio_mapping_td (td_type)  
                  VALUES (?)
                  ON DUPLICATE KEY SET td_type = VALUES(td_type)";
        $types = array();

        while ($counter < $max) {
            $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, $counter, $counter + $increment);
            foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
                print_r($v);
                exit;
                $types[$v['accountCategoryCode']] = 1;
            }
            $counter += $increment;
        }
        foreach ($types AS $k => $v) {
            $adb->pquery($query, array($k));
        }
    }

    static public function MarkInceptionIntervalsDone($account_number){
        global $adb;

        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING(portfolioinformationid)
                  SET daily_since_inception = 1 WHERE account_number = ?";
        $adb->pquery($query, array($account_number));
    }

    static public function GetAccountsThatInceptionIntervalsHaveNotRun($limit){
        global $adb;

        if(strlen($limit) > 0){
            $limit = " LIMIT {$limit} ";
        }
        $query = "SELECT account_number 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND p.accountclosed = 0
                  AND cf.daily_since_inception = 0
                  {$limit}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $account_numbers[] = $v['account_number'];
            }
            return $account_numbers;
        }
        return 0;
    }

    static public function GetAccountsThatDontHaveIntervalForDate($date, $limit){
        global $adb;

        if(strlen($limit) > 0){
            $limit = " LIMIT {$limit} ";
        }
        $query = "SELECT account_number 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND p.accountclosed = 0
                  AND account_number NOT IN (SELECT accountnumber FROM intervals_daily WHERE intervalenddate = ?)
                  {$limit}";
        $result = $adb->pquery($query, array($date));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $account_numbers[] = $v['account_number'];
            }
            return $account_numbers;
        }
        return 0;
    }

    static public function GetAccountsPCHasNotTransferred($limit){
        global $adb;

        if(strlen($limit) > 0)
            $limit = " LIMIT " . $limit;

        $query = "SELECT account_number 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE pc_transactions_transferred != 1 OR pc_transactions_transferred IS NULL
                  AND e.deleted = 0 
                  AND p.accountclosed = 0
                  {$limit}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $account_numbers[] = $v['account_number'];
            }
            return $account_numbers;
        }
        return 0;
    }

    static public function HavePCTransactionsBeenTransferred($account_number){
        global $adb;

        $query = "SELECT pc_transactions_transferred FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            if ($adb->query_result($result, 0, 'pc_transactions_transferred') == 1)
                return 1;
            return 0;
        }
        return 0;
    }

    static public function SetPCTransactionsTransferredToNo($account_number)
    {
        global $adb;
        $query = "UPDATE vtiger_portfolioinformation p 
	              JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
	              SET pc_transactions_transferred = 0 
	              WHERE account_number = ? ";
	    $adb->pquery($query, array($account_number));
    }

    static public function CopyTransactionsFromPCTableToCloud($account_number){
        global $adb;
        $query = "INSERT IGNORE INTO custodian_omniscient.custodian_transactions_pc
                  SELECT t.*, o.custodian AS custodian FROM live_omniscient.vtiger_pc_transactions t
                  JOIN live_omniscient.vtiger_portfolios p ON t.portfolio_id = p.portfolio_id AND p.portfolio_account_number = ?
                  JOIN live_omniscient.vtiger_pc_originations o ON o.id = t.origination_id
                  WHERE t.activity_id != 30 AND t.symbol_id != 0";
        $adb->pquery($query, array($account_number));
    }

    static public function CopyTransactionsFromCloudToCRM($account_number){
        global $adb;

        $query = "DROP TABLE IF EXISTS PCTransactions";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE PCTransactions 
                  SELECT cloud_transaction_id 
                  FROM live_omniscient.vtiger_transactions JOIN live_omniscient.vtiger_transactionscf USING (transactionsid) 
                  WHERE pc_transferred = 1 AND account_number = ?";
        $adb->pquery($query, array($account_number));

        $query = "DROP TABLE IF EXISTS CreateTransactions";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE CreateTransactions 
SELECT 0 AS crmid, 0000000.00000 AS price, m.omniscient_category, m.omniscient_activity, transaction_id, portfolio_id, sell_lot_id, trade_lot_id, link_id, custodian_id, symbol_id, activity_id, money_id, broker_id, report_as_type_id, quantity, total_value, conversion_value, accrued_interest, yield_at_purchase, advisor_fee, amount_per_share, other_fee, net_amount, settlement_date, trade_date, origina_trade_date, entry_date, link_date, odd_income_payment_flag, long_position_flag, reinvest_gains_flag, reinvest_income_flag, keep_fractional_shares_flag, taxable_prev_year_flag, complete_transaction_flag, is_reinvested_flag, notes, principal, add_sub_status_type_id, contribution_type_id, matching_method_id, custodian_account, original_link_account, origination_id, last_modified_date, trans_link_id, status_type_id, last_modified_user_id, dirty_flag, invalid_cost_basis_flag, cost_basis_adjustment, security_split_flag, reset_cost_basis_flag, deleted, account_number, data_set_id, symbol, custodian, m.operation 
FROM custodian_omniscient.custodian_transactions_pc t 
JOIN live_omniscient.pcmapping m ON m.id = t.activity_id AND m.rat = t.report_as_type_id AND m.add_sub_status_type = t.add_sub_status_type_id WHERE t.transaction_id NOT IN (SELECT cloud_transaction_id FROM PCTransactions) AND t.status_type_id = 100
        AND t.account_number = ?
GROUP BY transaction_id";
        $adb->pquery($query, array($account_number));

        $query = "UPDATE CreateTransactions t JOIN live_omniscient.vtiger_securities s ON t.symbol_id = s.security_id 
JOIN live_omniscient.vtiger_portfolios p ON t.portfolio_id = p.portfolio_id 
SET t.symbol = s.security_symbol, t.account_number = REPLACE(p.portfolio_account_number, '-', ''), operation = CASE WHEN operation IS NULL THEN '' ELSE operation END";
        $adb->pquery($query, array());

        $query = "UPDATE CreateTransactions t
SET net_amount = CASE WHEN net_amount = 0 THEN total_value ELSE net_amount END";
        $adb->pquery($query, array());

        $query = "UPDATE CreateTransactions t SET price = COALESCE(ABS(net_amount / CASE WHEN quantity > 0 THEN quantity ELSE net_amount END), 0.0)";
        $adb->pquery($query, array());

        $query = "UPDATE CreateTransactions SET crmid = live_omniscient.IncreaseAndReturnCrmEntitySequence()";
        $adb->pquery($query, array());

        $query = "INSERT INTO live_omniscient.vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label) SELECT crmid, 1, 1, 1, 'Transactions', NOW(), NOW(), notes FROM CreateTransactions";
        $adb->pquery($query, array());

        $query = "INSERT INTO live_omniscient.vtiger_transactions (transactionsid, account_number, security_symbol, security_price, quantity, trade_date, origination, cloud_transaction_id, operation) SELECT crmid, account_number, symbol, price, ABS(quantity), trade_date, custodian, transaction_id, operation FROM CreateTransactions";
        $adb->pquery($query, array());

        $query = "INSERT INTO live_omniscient.vtiger_transactionscf (transactionsid, custodian, transaction_type, transaction_activity, net_amount, principal, comment, pc_transferred) SELECT crmid, custodian, omniscient_category, omniscient_activity, ABS(net_amount), ABS(principal), notes, 1 FROM CreateTransactions";
        $adb->pquery($query, array());
    }

    static public function CreateTransactionsFromPCCloudUsingJava($custodian, $account_number){
        $url = "http://lanserver24.concertglobal.com:8085/OmniServ/AutoParse?tenant=Omniscient&user=syncuser&password=Concert222&connection=192.168.100.224&dbname=custodian_omniscient&operation=createtransactions&vtigerDBName=live_omniscient&custodian={$custodian}&account_number={$account_number}";
        file_get_contents($url);
    }

    static public function CreateTransactionsFromPCCloud($custodian, $account_number)
    {
        $url = "http://lanserver24.concertglobal.com:8085/OmniServ/AutoParse?tenant=Omniscient&user=syncuser&password=Concert222&connection=192.168.100.224&dbname=custodian_omniscient&operation=createtransactions&vtigerDBName=live_omniscient&custodian={$custodian}&account_number={$account_number}";
        file_get_contents($url);
    }


    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams)
    {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => 'LBL_DASHBOARD',
            'linkurl' => $this->getDashBoardUrl(),
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if ($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $parentQuickLinks;
    }

    static public function CreateDailyIntervalsForAccounts(array $accounts, $date)
    {
        global $adb;
        $query = "CALL CALCULATE_DAILY_INTERVALS_LOOP(?, ?, ?, ?, 'live_omniscient');";
        foreach ($accounts AS $k => $v) {
            $custodian = self::GetCustodianFromAccountNumber($v);
            $adb->pquery($query, array($v, $date, $date, $custodian));
        }
    }

    static public function IsPerformanceDisabled($account_number)
    {
        global $adb;

        $query = "SELECT disable_performance FROM vtiger_portfolioinformation p JOIN vtiger_portfolioinformationcf cf USING(portfolioinformationid) WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, 'disable_performance');
        }
        return 0;
    }

    static public function GetAccountNameFromAccountNumber($account_number)
    {
        global $adb;

        $query = "SELECT description FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  WHERE account_number=?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'description');
        return ' -- ';
    }

    static public function GetAccountTypeFromAccountNumber($account_number)
    {
        global $adb;

        $query = "SELECT cf_2549 FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  WHERE account_number=?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'cf_2549');
        return ' -- ';
    }

    static public function GetPreparedForNameByRecordID($record_id)
    {
        if ($record_id) {
            $record = Vtiger_Record_Model::getInstanceById($record_id);
            $data = $record->getData();
            $module = $record->getModule();
            switch ($module->getName()) {
                case "PortfolioInformation":
                    if (strlen($data['statement_title']) > 0) {
                        return $data['statement_title'];
                    }
                    if (strlen($data['contact_link']) > 0 && $data['contact_link'] != '0') {
                        $contact_record = Contacts_Record_Model::getInstanceById($data['contact_link']);
                        $name = $contact_record->getName();
                        return $name;
                    }
                        return $data['last_name'];
                    break;
                case "Contacts":
                    $contact_record = Contacts_Record_Model::getInstanceById($record_id);
                    $name = $contact_record->getName();
                    return $name;
                    break;
                case "Accounts":
                    if (strlen($data['statement_title']) > 0)
                        return $data['statement_title'];
                    $account_record = Accounts_Record_Model::getInstanceById($record_id);
                    $name = $account_record->getName();
                    return $name;
                    break;
            }
        }
    }

    static public function GetPreparedByNameByRecordID($record_id)
    {
        if ($record_id) {
            $record = VTiger_Record_Model::getInstanceById($record_id);
            $data = $record->getData();
            $module = $record->getModule();
            $current_user = Users_Record_Model::getCurrentUserModel();
            switch ($module->getName()) {
                case "PortfolioInformation":
                    if (strlen($data['prepared_by']) > 0) {
                        return $data['prepared_by'];
                    }
                    return $current_user->getName();
                    break;
                case "Contacts":
                    return $current_user->getName();
                    break;
                case "Accounts":
                    if (strlen($data['preparer']) > 0)
                        return $data['preparer'];
                    return $current_user->getName();
                    break;
            }
        }
    }

    static public function UpdateOmniInceptionDate(){
        global $adb;

        $query = "UPDATE vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  SET p.inceptiondate = e.createdtime 
                  WHERE p.inceptiondate IS NULL OR p.inceptiondate = '0000-00-00';";
        $adb->pquery($query, array());
    }

    static public function UpdateContactFees(){
        global $adb;
        $query = "CALL UPDATE_CONTACT_MANAGEMENT_FEES()";
        $adb->pquery($query, array());
    }

    static public function UpdateHouseholdFees()
    {
        global $adb;
        $query = "CALL UPDATE_HOUSEHOLD_MANAGEMENT_FEES()";
        $adb->pquery($query, array());
    }

    static public function UpdateYTDManagementFees()
    {
        global $adb;
        $query = "CALL UPDATE_PORTFOLIO_MANAGEMENT_FEES_FIELD(DATE_FORMAT(NOW(),'%Y-01-01'), NOW(), 'ytd_management_fees')";
        $adb->pquery($query, array());
    }

    static public function UpdateTrailing12ManagementFees()
    {
        global $adb;
        $query = "CALL UPDATE_PORTFOLIO_MANAGEMENT_FEES_FIELD(NOW() - INTERVAL 1 YEAR, NOW(), 'annual_management_fee')";
        $adb->pquery($query, array());
    }

    static public function GetReportSelectionOptions($report_name)
    {
        global $adb;
        $query = "SELECT * FROM vtiger_report_options WHERE report_name = ? ORDER BY sort_order ASC";
        $result = $adb->pquery($query, array($report_name));
        $values = array();
        if ($adb->num_rows($result) > 0) {
            while ($v = $adb->fetchByAssoc($result)) {
                $v['date'] = self::ReportValueToDate($v['option_value']);
                if($v['default'] == 1)
                    $v['date']['default'] = 1;
                $values[] = $v;
            }
            return $values;
        }
        return 0;
    }

    /**
     * Converts the option value to an actual date to be filled in
     * @param $option_value
     */
    static public function ReportValueToDate($option_value, $month_only = false)
    {
        $day = "";
        if (!$month_only)
            $day = "d/";

        switch ($option_value) {
            case "current":
                $dateReturn['end'] = date("m/{$day}Y");
                break;
            case "last_month":
                $dateReturn['end'] = date("m/{$day}Y", strtotime("last day of previous month"));
                break;
            case "last_year":
                $dateReturn['end'] = date("m/{$day}Y", strtotime("last year December 31st"));
                break;
            case "last_year_start":
                $dateReturn['end'] = date("m/{$day}Y", strtotime("last year January 1st"));
                break;
            case "ytd":
                $dateReturn['start'] = date("Y-m", strtotime("January 1st " . date('Y')));
                $dateReturn['end'] = date("Y-m", strtotime("last day of previous month"));
                break;
            case "2017":
                $dateReturn['start'] = date("Y-m", strtotime("January 1st 2017"));
                $dateReturn['end'] = date("Y-m", strtotime("December 31st 2017"));
                break;
            case "2018":
                $dateReturn['start'] = date("Y-m", strtotime("January 1st 2018"));
                $dateReturn['end'] = date("Y-m", strtotime("December 31st 2018"));
                break;
            case "trailing_12":
                $dateReturn['start'] = date("Y-m", strtotime("first day of this month -1 year"));
                $dateReturn['end'] = date("Y-m", strtotime("last day of previous month"));
                break;
            case "trailing_6":
                $dateReturn['start'] = date("Y-m", strtotime("first day of this month -6 months"));
                $dateReturn['end'] = date("Y-m", strtotime("last day of previous month"));
                break;
            case "custom":
                $dateReturn['start'] = "";
                $dateReturn['end'] = "";
                break;
            default:
                $dateReturn['end'] = date("m/{$day}Y");
        }

        return $dateReturn;
    }

    static public function SetStratifiID($id, $account_number)
    {
        global $adb;

        $query = "UPDATE vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  SET cf.stratid = ? WHERE p.account_number = ?";
        $adb->pquery($query, array($id, $account_number));
    }

    static public function GetStratifiData($account_number)
    {
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $query = "SELECT stratid, p.account_number, CONCAT('POR',p.portfolioinformationid) AS stratname, security_symbol, pos.weight, pos.current_value
                  FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid 
                  JOIN vtiger_positioninformation pos ON pos.account_number = p.account_number
                  JOIN vtiger_positioninformationcf poscf ON poscf.positioninformationid = pos.positioninformationid
                  WHERE e.deleted = 0 AND p.accountclosed = 0 AND pos.quantity != 0 AND p.account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number));

        if ($adb->num_rows($result) > 0) {
            $account_info = array();
            while ($v = $adb->fetchByAssoc($result)) {
                $tmp = array();
                $tmp['security_symbol'] = $v['security_symbol'];
                $tmp['weight'] = $v['weight'];
                $tmp['current_value'] = $v['current_value'];
                $account_info['symbol_data'][] = $tmp;
            }
            $account_info['stratid'] = $adb->query_result($result, 0, 'stratid');
            $account_info['stratname'] = $adb->query_result($result, 0, 'stratname');
            $account_info['account_number'] = $adb->query_result($result, 0, 'account_number');
            return $account_info;
        }
        return 0;
    }

    static public function GetAccountNumbersWithoutStratifiID($number_to_get)
    {
        global $adb;

        $query = "SELECT p.account_number 
                  FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  JOIN vtiger_positioninformation pos ON pos.account_number = p.account_number
                  JOIN vtiger_positioninformationcf poscf ON poscf.positioninformationid = pos.positioninformationid
                  JOIN vtiger_modsecurities m ON m.security_symbol = pos.security_symbol
                  WHERE (cf.stratid IS NULL OR cf.stratid = 0)
                  AND e.deleted = 0 
                  AND p.accountclosed = 0 
                  AND pos.quantity > 0 LIMIT {$number_to_get}";
        $result = $adb->pquery($query, array());
        if ($adb->num_rows($result) > 0) {
            while ($x = $adb->fetchByAssoc($result)) {
                $account_numbers[] = $x['account_number'];
            }
        }

        return $account_numbers;
    }

    static public function DoesAccountHaveStratifiID($account_number)
    {
        global $adb;
        $query = "SELECT stratid 
                  FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  WHERE account_number = ?";
        $result = $adb->pquery($query, array($account_number));
        if ($adb->num_rows($result) > 0) {
            $val = $adb->query_result($result, 0, 'stratid');
            if ($val == 0 || $val == '') {
                return 0;
            } else {
                return $val;
            }
        }
        return 0;
    }

    /**
     * Gets account numbers without stratifi ID's and creates them in Stratifi
     */
    static public function StratifiCreationLoop()
    {
        $strat = new StratifiAPI();

        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersWithoutStratifiID(25);
        foreach ($account_numbers AS $k => $v) {
            $stratify_data[] = PortfolioInformation_Module_Model::GetStratifiData($v);
        }

        foreach ($stratify_data AS $k => $v) {
            $result = json_decode($strat->CreateNewStratifiAccount($v['stratname']));
            $v['stratid'] = $result->id;
            PortfolioInformation_Module_Model::SetStratifiID($result->id, $v['account_number']);
            $result = $strat->SendPositionsToStratifi($v);
        }
    }

    /**
     * @param $omniID
     * @param $stratifiID
     * Updates the portfolio in omniscient with the Stratifi ID
     */
    static public function UpdateStratifiID($omniID, $stratifiID)
    {
        global $adb;
        $query = "UPDATE vtiger_portfolioinformationcf 
                  SET stratid = ? WHERE portfolioinformationid = ?";
        $adb->pquery($query, array($stratifiID, $omniID));

    }

    /**
     * @param $omniID
     * Creates a new Stratifi account on their end.  Uses a Portfolio record number
     */
    static public function CreateStratifiPortfolioAccount($omniID)
    {
        global $adb;
        $stratifi = new StratifiAPI();
        $result = json_decode($stratifi->CreateNewStratifiAccount(""));
        if ($result->id) {
            self::UpdateStratifiID($omniID, $result->id);
        }
    }

    static public function UpdateHouseholdLinkForContact($contact_id, $new_household_id)
    {
        global $adb;
        $query = "UPDATE vtiger_portfolioinformation SET household_account = ? WHERE contact_link = ?";
        $adb->pquery($query, array($new_household_id, $contact_id));
    }

    /**
     * Get list of account numbers for the logged in user
     * @return array|int
     */
    static public function GetAccountNumbersForLoggedInUser($open_only = true)
    {
        if ($open_only) {
            $where = " WHERE vtiger_crmentity.deleted=0 ";
        }
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $adb = PearDatabase::getInstance();

        $query = "SELECT account_number FROM vtiger_portfolioinformation
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid ";

        if (!$currentUser->isAdminUser()) {
            $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('PortfolioInformation');
        }
        $query .= $where;
        $result = $adb->pquery($query, array());

        if ($adb->num_rows($result) > 0) {
            $account_numbers = array();
            while ($x = $adb->fetchByAssoc($result)) {
                $account_numbers[] = $x['account_number'];
            }
            return $account_numbers;
        }
        return false;
    }

    /**
     * This is specifically for an individual transaction, NOT AN ARRAY
     * @param $account_number
     */
    static public function CreateTransactionsForPositionsThatHaveNone($account_number){
        global $adb;
        $query = "SELECT * FROM vtiger_positioninformation p
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  WHERE account_number IN (?)
                  AND quantity <> 0
                  AND p.security_symbol NOT IN (SELECT security_symbol FROM vtiger_transactions t 
                                                JOIN vtiger_transactionscf cf USING (transactionsid) 
                                                WHERE account_number IN (?) AND transaction_type IN ('Trade', 'Flow') 
                                                GROUP BY security_symbol)";
        $result = $adb->pquery($query, array($account_number, $account_number));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $t = Vtiger_Record_Model::getCleanInstance("Transactions");
                $data = $t->getData();
                $data['security_symbol'] = $v['security_symbol'];
                $data['description'] = 'System Generated Transaction';
                $data['account_number'] = $v['account_number'];
                $data['quantity'] = 0;
                $data['net_amount'] = 0;
                $data['transaction_type'] = 'Trade';
                $data['trade_date'] = date("Y-m-d");
                $data['system_generated'] = 1;
                $t->set('mode','create');
                $t->setData($data);
                $t->save();
            }
        }
    }

    static public function AutoGenerateTransactionsForGainLossReport($account_number){
        global $adb;
        PortfolioInformation_Module_Model::CreateTransactionsForPositionsThatHaveNone($account_number);
        PortfolioInformation_GainLoss_Model::CreateGainLossTables($account_number);

        $query = "SELECT * FROM COMPARISON";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                if($v['reconcile'] != 0){//If we need to reconcile
                    $transaction_id = Transactions_Module_Model::GetGeneratedTransactionID($account_number, $v['security_symbol']);//Find a transaction that already exists for the given symbol
                    if($transaction_id == 0){//The symbol doesn't exist already for this account, create it
                        $t = Vtiger_Record_Model::getCleanInstance("Transactions");
                        $data = $t->getData();
                        $data['security_symbol'] = $v['security_symbol'];
                        $data['description'] = 'System Generated Transaction';
                        $data['account_number'] = $account_number;
                        $tmp_quantity = $v['reconcile'];
                        $data['quantity'] = ABS($v['reconcile']);
                        $data['net_amount'] = ABS($v['reconcile']);
                        $data['transaction_type'] = 'Trade';
                        if($tmp_quantity < 0)
                            $data['operation'] = '-';
                        $data['trade_date'] = date("Y-m-d");
                        $data['system_generated'] = 1;
                        $t->set('mode','create');
                        $t->setData($data);
                        $t->save();
##                        echo 'created for ' . $v['security_symbol'] . '<br />';
                    }else{//A system generated transaction already exists, so update the quantity accordingly
                        $recordModel = Vtiger_Record_Model::getInstanceById($transaction_id, 'Transactions');
                        $data = $recordModel->getData();
                        $tmp_quantity = $data['quantity'] + $v['reconcile'];
                        $data['quantity'] = ABS($tmp_quantity);
#                        print_r($v); echo "<br />";
#                        print_r($data); echo "<br />";exit;
                        $price = ModSecurities_Module_Model::GetSecurityPrice($v['security_symbol']);
                        $data['security_price'] = $price;
                        $data['net_amount'] = ABS($tmp_quantity * $v['security_price_adjustment'] * $price);
                        if($tmp_quantity < 0)
                            $data['operation'] = '-';
                        else
                            $data['operation'] = '';
                        $recordModel->setData($data);
                        $recordModel->set('mode','edit');
                        $recordModel->save();
##                        echo 'updated for ' . $v['security_symbol'] . '<br />';
                    }
##                    echo 'check for ' . $v['security_symbol'];exit;
                }
            }
        }
    }

    /**
     * Get list of account numbers for the specified user
     * @return array|int
     */
    static public function GetAccountNumbersForSpecificUser($user_id, $open_only = true)
    {
        $user = new Users();
        $user = $user->retrieve_entity_info($user_id, 'Users');
        $user = Users_Record_Model::getInstanceFromUserObject($user);

        if ($open_only) {
            $where = " WHERE vtiger_crmentity.deleted=0 ";
        }
        $adb = PearDatabase::getInstance();

        $query = "SELECT account_number FROM vtiger_portfolioinformation
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid ";

        if (!$user->isAdminUser()) {
            $query .= getNonAdminAccessControlQuery("PortfolioInformation", $user);
        }
        $query .= $where;

        $result = $adb->pquery($query, array());

        if ($adb->num_rows($result) > 0) {
            $account_numbers = array();
            while ($x = $adb->fetchByAssoc($result)) {
                $account_numbers[] = $x['account_number'];
            }
            return $account_numbers;
        }
        return false;
    }

    static public function GetEntityIDFromStratifiID($stratifiid){
        global $adb;

        $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid) 
                  WHERE cf.stratid = ?";
        $result = $adb->pquery($query, array($stratifiid));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'portfolioinformationid');
        }
        return 0;
    }

    static public function WriteTrailingTWRToPortfolio($account_number, $trailing_num_months, $field){
        global $adb;
        $sdate = GetFirstDayMinusNumberOfMonthsFromEndOfLastMonth($trailing_num_months);
        $edate = GetLastDayLastMonth();
        $type = "monthly";
        $query = "CALL TWR_TRAILING_FOR_ACCOUNT(?, ?, ?, ?, @twr)";
        $twr = 0.00;
        $adb->pquery($query, array($account_number, $sdate, $edate, $type));
        $query = "SELECT @twr AS twr";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $twr = $adb->query_result($result, 0, 'twr');
            $query = "UPDATE vtiger_portfolioinformation p
                      JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                      SET {$field} = ?, last_twr_calculated = NOW()
                      WHERE account_number = ?";
            $adb->pquery($query, array($twr, $account_number));
        }
    }

    static public function UpdatePortfolioDataInCloudForTDByRepCode(array $rep_codes){
        include_once("modules/Trading/models/Ameritrade.php");
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $result = $trade->GetAllAccountsForRepCode("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", $rep_codes);
        $query = "INSERT INTO custodian_omniscient.custodian_portfolios_td (account_number, first_name, last_name, street, address2, city, 
                              state, zip, phone_number, advisor_id, rep_code)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), street = VALUES(street), address2 = VALUES(address2),
                                          city = VALUES(city), state = VALUES(state), zip = VALUES(zip),  
                                          phone_number = VALUES(phone_number), advisor_id = VALUES(advisor_id), rep_code = VALUES(rep_code)";
        foreach($result['model']['getAccountsJson']['account'] AS $k => $v){
            $adb->pquery($query, array($v['accountNumber'], $v['firstName'], $v['lastName'], $v['address1'], $v['address2'], $v['city'], $v['state'],
                $v['zip'], $v['secondaryPhone'], $v['repCode'], $v['repCode']));
        }
    }

    static public function UpdatePortfolioDataInCloudForTDByRepCodeBackup(array $rep_codes){//The original had accounttype in it, the version above no longer does
        include_once("modules/Trading/models/Ameritrade.php");
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $result = $trade->GetAllAccountsForRepCode("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", $rep_codes);
        $query = "INSERT INTO custodian_omniscient.custodian_portfolios_td (account_number, first_name, last_name, street, address2, city, 
                              state, zip, account_type, phone_number, advisor_id, rep_code)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), street = VALUES(street), address2 = VALUES(address2),
                                          city = VALUES(city), state = VALUES(state), zip = VALUES(zip), account_type = VALUES(account_type), 
                                          phone_number = VALUES(phone_number), advisor_id = VALUES(advisor_id), rep_code = VALUES(rep_code)";
        foreach($result['model']['getAccountsJson']['account'] AS $k => $v){
            $adb->pquery($query, array($v['accountNumber'], $v['firstName'], $v['lastName'], $v['address1'], $v['address2'], $v['city'], $v['state'],
                $v['zip'], $v['accountType'], $v['secondaryPhone'], $v['repCode'], $v['repCode']));
        }
    }

}