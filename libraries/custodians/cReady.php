<?php

class ReadyData{
    public $account_list, $ready_data;
    public function __construct(){
        $ready_data = array();
        $account_list = array();
    }
}

class cReady
{
    public function __construct(){
    }

    public function RemoveReady(array $account_number){
        global $adb;
        $questions = generateQuestionMarks($account_number);
        $params = array();
        $params[] = $account_number;
        $query = "DELETE FROM custodian_omniscient.readyforupdate WHERE account_number IN ({$questions})";
        $adb->pquery($query, $params);
    }

    /**
     * Get all ready data via rep code
     * @param array $rep_code
     * @return int|ReadyData|void
     */
    public function GetReadyDataViaRepCode(array $rep_code)
    {
        global $adb;
        $data = new ReadyData();

        if (empty($rep_code))
            return;

        $params = array();
        $params[] = $rep_code;

        $questions = generateQuestionMarks($rep_code);
        $query = "SELECT rep_code, account_number, ready_type, custodian, timestamp
                  FROM custodian_omniscient.readyforupdate
                  WHERE rep_code IN ({$questions})";
        $result = $adb->pquery($query, $params, true);
        if ($adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $data->ready_data[] = $row;
                $data->account_list[] = $row['account_number'];
            }
            return $data;
        } else {
            return 0;
        }
    }

    /**
     * Get the ready module data via the rep code.  This hits the readyforupdate table and gets all info based on rep code and ready type (module)
     * @param array $rep_code
     * @param $module_id
     * @return int|ReadyData|void
     */
    public function GetReadyModuleDataViaRepCode(array $rep_code, $module_id)
    {
        if (empty($rep_code))
            return;

        global $adb;
        $data = new ReadyData();
        $params = array();
        $params[] = $rep_code;
        $params[] = $module_id;

        $questions = generateQuestionMarks($rep_code);
        $query = "SELECT rep_code, account_number, ready_type, custodian, timestamp
                  FROM custodian_omniscient.readyforupdate
                  WHERE rep_code IN ({$questions})
                  AND ready_type = ?";
        $result = $adb->pquery($query, $params, true);
        if ($adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $data->ready_data[] = $row;
                $data->account_list[] = $row['account_number'];
            }
            return $data;
        } else {
            return 0;
        }
    }

    /**
     * Get all ready data from the passed in account numbers (rep code, account number, ready type)
     * @param array $account_number
     * @return int|ReadyData|void
     */
    public function GetReadyDataViaAccountNumber(array $account_number)
    {
        if (empty($account_number))
            return;

        global $adb;
        $data = new ReadyData();
        $questions = generateQuestionMarks($account_number);
        $query = "SELECT rep_code, account_number, ready_type, custodian, timestamp 
                  FROM custodian_omniscient.readyforupdate
                  WHERE account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_number), true);
        if ($adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $data->ready_data[] = $row;
                $data->account_list[] = $row['account_number'];
            }
            return $data;
        } else {
            return 0;
        }
    }

    /**
     * When we know which data is ready specifically we are able to pull it into the CRM
     * setting $all to true will skip ready type entirely and pull everything for the passed in account
     * @param $account_number
     * @param $readyType
     * @param $all
     */
    public function PullDataTD($account_number, $readyType, $all = false)
    {
        $tdPortfolios = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
            "custodian_portfolios_td", "custodian_balances_td", array());
        $tdPortfolios->SetAccountNumbers(array($account_number));

        if (!PortfolioInformation_Module_Model::DoesAccountExist($account_number)) {//The account doesn't exist
            $tdPortfolios->CreateNewPortfoliosFromPortfolioData(array($account_number));//Create the account
        }

        switch (readyType) {
            case 1://Portfolios
                $tdPortfolios->UpdatePortfoliosFromPortfolioData($account_number);//Update the existing accounts with the latest data from the custodian
                break;
            case 2://Positions
                StatusUpdate::UpdateMessage("CRONUPDATER", "Pulling Custodian Positions (TD) for {$account_number}");
                $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
                    "custodian_portfolios_td", "custodian_positions_td", array(), array(), false);
                $positions->SetAccountNumbers(array($account_number));
                $missing_positions = $positions->GetMissingCRMPositions();
                $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
                if (!empty($missing_positions))
                    $positions->CreateNewPositionsFromPositionData($missing_positions);

                //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
                StatusUpdate::UpdateMessage("CRONUPDATER", "Starting Securities (TD)");
                $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
                    "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
                    "f.issue_date", "f.share_per_contact", "pr.factor");
                //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
                $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
                    "custodian_securities_td", $symbols, array(), $fields);
                $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
                if (!empty($missing_securities))
                    $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
                $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
                $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
                break;
            case 3://Transactions

                break;
        }
    }

    public function PullAllTD(array $account_number, $remove_from_ready_list=false)
    {
        StatusUpdate::UpdateMessage("AUTOQUEUE", "Pulling all for specified accounts");
        StatusUpdate::UpdateMessage("CRONUPDATER", "Starting Portfolios (TD)");

        $tdPortfolios = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
            "custodian_portfolios_td", "custodian_balances_td", array());
        $tdPortfolios->SetAccountNumbers($account_number);

        foreach($account_number AS $k => $v) {
            if (!PortfolioInformation_Module_Model::DoesAccountExist($v)) {//The account doesn't exist
                $tdPortfolios->CreateNewPortfoliosFromPortfolioData($v);//Create the account
            }
        }

        $tdPortfolios->UpdatePortfoliosFromPortfolioData($account_number);//Update the existing accounts with the latest data from the custodian

        StatusUpdate::UpdateMessage("CRONUPDATER", "Pulling Custodian Positions (TD) for specified accounts");
        $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td", array(), array(), false);
        $positions->SetAccountNumbers($account_number);

        $missing_positions = $positions->GetMissingCRMPositions();

        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if (!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        StatusUpdate::UpdateMessage("CRONUPDATER", "Starting Securities (TD) for specified accounts");
        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
            "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
            "f.issue_date", "f.share_per_contact", "pr.factor");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array(), $fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have

        if (!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data

        /***STEP 3 - CREATE TRANSACTIONS WORKING***/
        StatusUpdate::UpdateMessage("CRONUPDATER", "Starting Transactions (TD) for specified accounts");

        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity", "m.operation");

        $start = $month = strtotime('2019-01-01');
        $end = strtotime(date("Y-m-d"));
        while ($month < strtotime("+1 month", $end)) {
            $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
                "custodian_portfolios_td", "custodian_transactions_td",
                array(), $fields);
            $transactions->SetAccountNumbers($account_number);

            $s = date("Y-m-d", $month);
            $e = date("Y-m-d", strtotime("+1 month", $month));
            $transactions->GetTransactionsDataBetweenDates($s, $e);
            $missing = $transactions->GetMissingCRMTransactions();
            $transactions->CreateNewTransactionsFromTransactionData($missing);

            //            echo date('Y-m-d', $month), PHP_EOL;
            #            echo "Start: {$s}, End: {$e}<br />";
            #            echo "Start: " . date("Y-m-d", $month) . " End " . date("Y-m-d", strtotime("+1 month", $month)) . '<br />';
            $month = strtotime("+1 month", $month);
        }
        /*********END OF STEP 3********/
        StatusUpdate::UpdateMessage("CRONUPDATER", "Finished");
        StatusUpdate::UpdateMessage("AUTOQUEUE", "Finished Pulling {$account_number}");
        /***STEP 3 - CREATE TRANSACTIONS WORKING***/

        if($remove_from_ready_list)
            $this->RemoveReady($account_number);
    }
}