<?php

require_once("libraries/custodians/cCustodian.php");
require_once('modules/ModSecurities/actions/ConvertCustodian.php');
include_once("include/utils/omniscientCustom.php");
include_once("libraries/statusupdates/StatusUpdate.php");

class cTDUpdater{
    public function __construct(array $readyData){
        switch($readyData['ready_type']){
            case 1:
                $td = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
                    "custodian_portfolios_td", "custodian_balances_td", array());
                if(!PortfolioInformation_Module_Model::DoesAccountExist($readyData['account_number'])) {//The account doesn't exist
                    $td->CreateNewPortfoliosFromPortfolioData(array($readyData['account_number']));//Create the account
                }
                $td->UpdatePortfoliosFromPortfolioData(array($readyData['account_number']));//Update the account
            break;
            case 2:
                break;
            case 3:
                break;
        }
    }

    public function UpdateAll(){
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();

        StatusUpdate::UpdateMessage("TDUPDATER", "Starting Portfolios (TD)");
        /***STEP 1 - CREATE AND UPDATE PORTFOLIOS WORKING -- REQUIRES advisor_control_number or fails because smownerid can't be null***/
        //Pull portfolio and balance information for the specified rep codes
        $td = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
            "custodian_portfolios_td", "custodian_balances_td", $rep_codes);
        $data = $td->GetExistingCRMAccounts();//Get accounts already in the CRM
        $missing = $td->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
        $td->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
        $existing = $td->GetExistingCRMAccounts();//Get existing CRM accounts
        $td->UpdatePortfoliosFromPortfolioData($existing);//Update the existing accounts with the latest data from the custodian
        /*********END OF STEP 1********/

        /***STEP 2 - CREATE AND UPDATE POSITIONS/SECURITIES WORKING***/
        //Pull all specified position data.  Auto setup will pull all info and set it up for us.  If there are memory issues due to too much data
        //then account numbers will need to be set manually and auto setup turned off.  We can then use the GetPositionsData function (follow the
        //constructor for an example on how to load.  This could be done in a loop setting <x> number of account numbers at a time
        StatusUpdate::UpdateMessage("TDUPDATER", "Pulling Custodian Positions (TD)");
        $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td", $rep_codes, array());
        $missing_positions = $positions->GetMissingCRMPositions();
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if (!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        StatusUpdate::UpdateMessage("TDUPDATER", "Starting Securities (TD)");
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
        /*********END OF STEP 2********/

        /***STEP 3 - CREATE TRANSACTIONS WORKING***/
        StatusUpdate::UpdateMessage("TDUPDATER", "Starting Transactions (TD)");
        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity", "m.operation");

        $start = $month = strtotime('2019-09-01');
        $end = strtotime(date("Y-m-d"));
        while ($month < strtotime("+1 month", $end)) {
            $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
                "custodian_portfolios_td", "custodian_transactions_td",
                $rep_codes, $fields);

            $s = date("Y-m-d", $month);
            $e = date("Y-m-d", strtotime("+1 month", $month));
            $transactions->GetTransactionsDataBetweenDates($s, $e);
            $missing = $transactions->GetMissingCRMTransactions();
            $transactions->CreateNewTransactionsFromTransactionData($missing);

            $month = strtotime("+1 month", $month);
        }
        /*********END OF STEP 3********/
        StatusUpdate::UpdateMessage("TDUPDATER", "Finished");
    }
}