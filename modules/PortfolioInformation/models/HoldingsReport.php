<?php
require_once("libraries/reports/new/holdings_report.php");


class PortfolioInformation_HoldingsReport_Model extends Vtiger_Module {
    static $tenant = "custodian_omniscient";

	static public function GenerateReportFromAccounts(array $accounts, $group_primary='aclass', $group_secondary='securitytype', $order_primary='aclass', $order_secondary='securitytype'){
		#Generate the temporary tables in mysql
		cHoldingsReport::GenerateTablesByAccounts($accounts, $group_primary, $group_secondary, $order_primary, $order_secondary);
	}

    /**
     * Returns the margin balance amount total for the passed in accounts
     * @param $accounts
     * @return int|mixed|string
     */
    static public function GetNetCreditDebitTotal($accounts){
        global $adb;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;

        $query = "SELECT SUM(net_credit_debit) AS net_credit_Debit FROM vtiger_portfolioinformation JOIN vtiger_portfolioinformationcf USING (portfolioinformationid) WHERE account_number IN ({$questions}) AND accountclosed = 0";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'net_credit_debit');
        else
            return 0;
    }

    /**
     * Returns the SUM of the field name passed in
     * @param $accounts
     * @return int|mixed|string
     */
    static public function GetDynamicFieldTotal($accounts, $field_name){
        global $adb;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;

        $query = "SELECT SUM({$field_name}) AS {$field_name} FROM vtiger_portfolioinformation JOIN vtiger_portfolioinformationcf USING (portfolioinformationid) WHERE account_number IN ({$questions}) AND accountclosed = 0";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, $field_name);
        else
            return 0;
    }

    static public function GetCustodianTotalAsOfDate($custodian, $accounts, $field_name, $as_of_date){
        global $adb;
        $tenant = self::$tenant;
        $questions = generateQuestionMarks($accounts);
        $params = array();

        $params[] = $accounts;
        $params[] = $accounts;
        $params[] = $as_of_date;

        $date_field = Omniscient_CustodianInteractions_Model::GetPositionCustodianFieldName($custodian, 'as_of_date');
        $custodian_field_name  = Omniscient_CustodianInteractions_Model::GetPositionCustodianFieldName($custodian, $field_name);

        $query = "SELECT SUM({$custodian_field_name}) AS {$field_name} 
                  FROM {$tenant}.custodian_balances_{$custodian} f 
                  WHERE account_number IN ({$questions}) 
                  AND {$date_field} = (SELECT MAX({$date_field}) FROM {$tenant}.custodian_balances_{$custodian} WHERE account_number IN ({$questions}) AND {$date_field} <= ?)";
        $result = $adb->pquery($query, $params);

        if($adb->num_rows($result) > 0) {
            return $adb->query_result($result, 0, $field_name);
        }
        else
            return 0;
    }

    /**
     * Returns the SUM of the field name passed in
     * @param $accounts
     * @return int|mixed|string
     */
    static public function GetFidelityFieldTotalAsOfDate($accounts, $field_name, $as_of_date){
        global $adb;
        $tenant = self::$tenant;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;
        $params[] = $accounts;
        $params[] = $as_of_date;

        $query = "SELECT SUM({$field_name}) AS {$field_name} 
                  FROM {$tenant}.custodian_balances_fidelity f 
                  WHERE account_number IN ({$questions}) 
                  AND as_of_date = (SELECT MAX(as_of_date) FROM {$tenant}.custodian_balances_fidelity WHERE account_number IN ({$questions}) AND as_of_date <= ?)";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, $field_name);
        else
            return 0;
    }

    /**
     * Returns the unsettled cash amount total for the passed in accounts
     * @param $accounts
     * @return int|mixed|string
     */
    static public function GetUnsettledCashTotal($accounts){
        global $adb;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;

        $query = "SELECT SUM(unsettled_cash) AS unsettled_cash FROM vtiger_portfolioinformation JOIN vtiger_portfolioinformationcf USING (portfolioinformationid) WHERE account_number IN ({$questions}) AND accountclosed = 0";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'unsettled_cash');
        else
            return 0;
    }

    /**
     * Returns the margin balance amount total for the passed in accounts
     * @param $accounts
     * @return int|mixed|string
     */
	static public function GetMarginBalanceTotal($accounts){
	    global $adb;

	    $questions = generateQuestionMarks($accounts);
	    $params = array();
	    $params[] = $accounts;

	    $query = "SELECT SUM(margin_balance) AS margin_balance FROM vtiger_portfolioinformation WHERE account_number IN ({$questions}) AND accountclosed = 0";
	    $result = $adb->pquery($query, $params);
	    if($adb->num_rows($result) > 0)
	        return $adb->query_result($result, 0, 'margin_balance');
	    else
	        return 0;
    }

    /**
     * Returns the available to pay balance amount total for the passed in accounts
     * @param $accounts
     * @return int|mixed|string
     */
    static public function GetAvailableToPayTotal($accounts){
        global $adb;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $accounts;

        $query = "SELECT SUM(available_to_pay) AS available_to_pay FROM vtiger_portfolioinformation WHERE account_number IN ({$questions}) AND accountclosed = 0";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'available_to_pay');
        else
            return 0;
    }


    static public function GeneratePositionsTable($accounts){
        global $adb;

        $query = "DROP TABLE IF EXISTS ReportPositions";
        $adb->pquery($query, array());


    }

    /**
     * @param $accounts
     * Generates the positions based on the base asset class
     */
    static public function GenerateAssetClassTables($accounts){
        global $adb;

        $query = "DROP TABLE IF EXISTS Estimator";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS PieTable";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS TotalTable";
        $adb->pquery($query, array());

        $accounts = RemoveDashes($accounts);

        if($accounts) {
            $questions = generateQuestionMarks($accounts);

            $query = "CREATE TEMPORARY TABLE Estimator
                     SELECT p.security_symbol, p.account_number, mscf.cusip, p.description, p.quantity, p.last_price, p.weight, p.current_value, aclass 
                     FROM vtiger_positioninformation p
                     JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                     JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
                     JOIN vtiger_modsecuritiescf mscf ON mscf.modsecuritiesid = ms.modsecuritiesid
                     JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
                     WHERE account_number IN ({$questions})
                     AND current_value != 0 AND e.deleted = 0 AND cf.closed_account != 1";
            $adb->pquery($query, $accounts);

            $query = "SET @global_total = (SELECT SUM(current_value) FROM Estimator)";
            $adb->pquery($query, array());

            $query = "UPDATE Estimator SET weight = current_value/@global_total*100";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE PieTable
                      SELECT aclass AS title, SUM(current_value) AS value, c.color
                      FROM Estimator
                      LEFT JOIN vtiger_chart_colors c ON c.title = aclass
                      GROUP BY aclass";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE TotalTable
                      SELECT SUM(current_value) AS current_value, SUM(weight) AS weight FROM Estimator";
            $adb->pquery($query, array());
        }
    }

    /**
     * @param $accounts'
     * Generates a table based on the defined field
     */
    static public function GenerateDefinedTables($accounts, $field_name){
        global $adb;

        $query = "DROP TABLE IF EXISTS Estimator";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS PieTable";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS TotalTable";
        $adb->pquery($query, array());

        $accounts = RemoveDashes($accounts);

        if($accounts) {
            $questions = generateQuestionMarks($accounts);

            $query = "CREATE TEMPORARY TABLE Estimator
                     SELECT p.security_symbol, p.account_number, mscf.cusip, p.description, p.quantity, p.last_price, p.weight, p.current_value, aclass, ms.securitytype 
                     FROM vtiger_positioninformation p
                     JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                     JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
                     JOIN vtiger_modsecuritiescf mscf ON mscf.modsecuritiesid = ms.modsecuritiesid
                     WHERE account_number IN ({$questions})
                     AND current_value != 0";
            $adb->pquery($query, $accounts);

            $query = "SET @global_total = (SELECT SUM(current_value) FROM Estimator)";
            $adb->pquery($query, array());

            $query = "UPDATE Estimator SET weight = current_value/@global_total*100";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE PieTable
                      SELECT `{$field_name}` AS title, SUM(current_value) AS value, c.color
                      FROM Estimator
                      LEFT JOIN vtiger_chart_colors c ON c.title = `{$field_name}`
                      GROUP BY `{$field_name}`";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE TotalTable
                      SELECT SUM(current_value) AS current_value, SUM(weight) AS weight FROM Estimator";
            $adb->pquery($query, array());
        }
    }

    /**
     * Generates the positions based on the estimated categories IE:  51% equity and 49% bonds would give it an Equity category
     * @param $accounts
     */
	static public function GenerateEstimateTables($accounts){
	    global $adb;

        $query = "DROP TABLE IF EXISTS Estimator";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS EstimatorTmp";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS PieTable";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS TotalTable";
        $adb->pquery($query, array());

        $accounts = RemoveDashes($accounts);

        if($accounts) {
            $questions = generateQuestionMarks($accounts);

            $query = "CREATE TEMPORARY TABLE EstimatorTmp
                     SELECT p.*, ms.securitytype, mscf.*, us_stock + intl_stock AS 'EquityTotal', 
                                  us_bond + intl_bond + preferred_net AS 'BondTotal', 
                                  cash_net AS 'CashTotal',
                                  convertible_net + other_net + unclassified_net AS 'OtherTotal'
                     FROM vtiger_positioninformation p
                     JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                     JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
                     JOIN vtiger_modsecuritiescf mscf ON mscf.modsecuritiesid = ms.modsecuritiesid
                     WHERE account_number IN ({$questions})
                     AND current_value != 0";
            $adb->pquery($query, $accounts);

            $query = "CREATE TEMPORARY TABLE Estimator
                      SELECT *, case GREATEST(EquityTotal, BondTotal, CashTotal, OtherTotal)
                                WHEN EquityTotal THEN 'Stocks'
                                WHEN BondTotal THEN 'Bonds'
                                WHEN CashTotal THEN 'Cash'
                                WHEN OtherTotal THEN 'Other' END AS EstimatedType
                      FROM EstimatorTmp";
            $adb->pquery($query, array());

            $query = "SET @global_total = (SELECT SUM(current_value) FROM Estimator)";
            $adb->pquery($query, array());

            $query = "UPDATE Estimator SET weight = current_value/@global_total*100";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE PieTable
                      SELECT EstimatedType AS title, SUM(current_value) AS value, c.color
                      FROM Estimator
                      LEFT JOIN vtiger_chart_colors c ON c.title = EstimatedType
                      GROUP BY EstimatedType";
            $adb->pquery($query, array());

            $query = "CREATE TEMPORARY TABLE TotalTable
                      SELECT SUM(current_value) AS current_value, SUM(weight) AS weight FROM Estimator";
            $adb->pquery($query, array());
        }
    }
}
?>