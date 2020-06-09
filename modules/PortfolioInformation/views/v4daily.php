<?php
if (ob_get_level() == 0) ob_start();

/**
 * Created by PhpStorm.
 * User: rsandnes
 * Date: 2016-07-06
 * Time: 3:55 PM
 */
include_once("libraries/reports/pdf/cNewPDFGenerator.php");
include_once("libraries/javaBridge/JavaCloudToCRM.php");
include_once("include/utils/cron/cTransactionsAccess.php");
require_once("include/utils/cron/cPortfolioAccess.php");
require_once("include/utils/cron/cPricingAccess.php");
include_once("libraries/Stratifi/StratifiAPI.php");

class PortfolioInformation_v4daily_View extends Vtiger_BasicAjax_View{

    function process(Vtiger_Request $request)
    {
        require_once("libraries/custodians/cCustodian.php");
        require_once('modules/ModSecurities/actions/ConvertCustodian.php');
        include_once("include/utils/omniscientCustom.php");

        echo "Script start: " . date("Y-m-d H:i:s") . '<br />';

####        PortfolioInformation_Module_Model::TDBalanceCalculationsRepCodes(array("GOX"), '2020-06-02', '2020-06-09');

####        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
#foreach($rep_codes AS $k => $v){
        $rep_codes = array("GOX");
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
echo "Step 1 finished: " . date("Y-m-d H:i:s") . '<br />';

        /***STEP 2 - CREATE AND UPDATE POSITIONS/SECURITIES WORKING***/
        //Pull all specified position data.  Auto setup will pull all info and set it up for us.  If there are memory issues due to too much data
        //then account numbers will need to be set manually and auto setup turned off.  We can then use the GetPositionsData function (follow the
        //constructor for an example on how to load.  This could be done in a loop setting <x> number of account numbers at a time
        $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td", $rep_codes, array());
        $missing_positions = $positions->GetMissingCRMPositions();
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about

        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
            "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
            "f.issue_date", "f.share_per_contact", "pr.factor");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array(), $fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        /*********END OF STEP 2********/
        echo "Step 2 finished: " . date("Y-m-d H:i:s") . '<br />';

        /***STEP 3 - CREATE TRANSACTIONS WORKING***/
        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity");

        $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
            "custodian_portfolios_td", "custodian_transactions_td",
            $rep_codes, $fields);
        $transactions->GetTransactionsDataBetweenDates('2019-01-01', date("Y-m-d"));
        $missing = $transactions->GetMissingCRMTransactions();
        $transactions->CreateNewTransactionsFromTransactionData($missing);
        /*********END OF STEP 3********/
        echo "Step 3 finished: " . date("Y-m-d H:i:s") . '<br />';


        echo "Script end: " . date("Y-m-d H:i:s") . '<br />';
exit;










        $fields = array("f.transaction_id", "f.account_number", "f.symbol", "f.cusip", "m.operation", "amount", "production_number", "omniscient_negative_category", "omniscient_category", "buy_sell_indicator",
                        "omniscient_negative_activity", "omniscient_activity", "m.description AS description", "commission", "key_code_description", "service_charge_misc_fee",
                        "option_symbol", "account_type_description", "f.comment", "comment2", "div_payable_date", "div_record_date", "fund_load_override",
                        "fund_load_percent", "interest_amount", "postage_fee", "reg_rep1", "reg_rep2", "service_fee", "short_term_redemption_fee",
                        "state_tax_amount", "transaction_code_description", "transaction_key_mnemonic", "f.price AS price", "security_price_adjustment", "quantity");

        $fidelity = new cFidelityTransactions("Fidelity", "custodian_omniscient", "transactions",
                                              "custodian_portfolios_fidelity", "custodian_transactions_fidelity",
                                               array('GH1'), $fields);
//        $fidelity->SetColumns(array("transaction_id"));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
        $fidelity->GetTransactionsDataBetweenDates('2020-01-01', '2020-05-01');
        $missing = $fidelity->GetMissingCRMTransactions();
        $fidelity->CreateNewTransactionsFromTransactionData($missing);

        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");
        exit;

/*
        $symbols = array("DBD");
        $securities = new cOmniscientSecurities("Omniscient", "live_omniscient", "securities",
                                                "vtiger_modsecurities", $symbols, array(), array());
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);
*/
        /*
                $rep_codes = array("GH1");
                $positions = new cFidelityPositions("FIDELITY", "custodian_omniscient", "positions",
                    "custodian_portfolios_fidelity", "custodian_positions_fidelity", $rep_codes, array());

                $symbols = array("DBD");
                $positions = new cFidelityPositions("FIDELITY", "custodian_omniscient", "positions",
                    "custodian_portfolios_fidelity", "custodian_positions_fidelity", $rep_codes, array());

        #        $positions->SetAccountNumbers(array("678105996"));
                $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        #        $symbols = array("DBD");

                $positions->GetPositionsData();

                echo "1";
        #        $positions->SetupPositionComparisons();*/
        echo "2";
        $symbols = array("DBD");
        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        $security_fields = array("f.symbol", "f.type", "f.description", "f.cusip", "f.dividend_yield", "f.option_expiration_date", "f.strike_price",
                        "f.option_symbol", "f.interest_rate", "f.maturity_date", "f.issue_date", "f.first_coupon_date",
                        "f.zero_coupon_indicator", "f.abbreviated_fund_name", "f.accrual_method", "f.as_of_date", "f.asset_class_code",
                        "f.asset_class_type_code", "f.bond_class", "f.close_price", "f.close_price_unfactored",
                        "f.current_factor_inflation_factor", "f.current_factor_date", "f.dividend_rate", "f.exchange", "f.expiration_date",
                        "f.fixed_income_call_put_date", "f.fixed_income_call_put_price", "f.floor_symbol", "f.foreign_security", "f.fund_family",
                        "f.fund_family_id", "f.fund_number", "f.host_type_code", "f.interest_frequency", "f.issue_state", "f.margin",
                        "f.mmkt_fund_designation", "f.operation_code", "f.options_symbol_underlying_security", "f.pricing_factor",
                        "f.security_group", "f.security_id", "f.security_type_description", "f.sic_code", "f.tradable", "f.yield_to_maturity",
                        "f.file_date", "f.filename", "f.insert_date", "pr.price AS latest_price", "map.multiplier", "map.omni_base_asset_class",
                        "map.security_type");

#$symbols = array("FDRXX");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cFidelitySecurities("FIDELITY", "custodian_omniscient", "securities",
            "custodian_securities_fidelity", $symbols, array(), $security_fields);
        echo "3";
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        echo "4";
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        echo "5";
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);
        echo "6";
#        $positions->ManualSetupPositionComparisons();
        echo "7";
#        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        echo 'done';exit;
exit;
#        $sec_data = $securities->GetSecuritiesData();
#        print_r($missing_securities);exit;
#        print_r($missing_securities);exit;
#        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
#        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data


#        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();

#        foreach($rep_codes AS $k => $v){
/****FIDELITY**********************************/
        /***STEP 1 - CREATE AND UPDATE PORTFOLIOS WORKING -- REQUIRES advisor_control_number or fails because smownerid can't be null***/
/*        $rep_codes = array("GH1");
        //Pull portfolio and balance information for the specified rep codes
        $fidelity = new cFidelityPortfolios("FIDELITY", "custodian_omniscient", "portfolios",
            "custodian_portfolios_fidelity", "custodian_balances_fidelity", $rep_codes);
        $data = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
        $missing = $fidelity->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
        $fidelity->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
        $existing = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
        $fidelity->UpdatePortfoliosFromPortfolioData($existing);
        exit;
        /*********END OF STEP 1********/

        /***STEP 2 - CREATE AND UPDATE POSITIONS/SECURITIES WORKING***/
        //Pull all specified position data.  Auto setup will pull all info and set it up for us.  If there are memory issues due to too much data
        //then account numbers will need to be set manually and auto setup turned off.  We can then use the GetPositionsData function (follow the
        //constructor for an example on how to load.  This could be done in a loop setting <x> number of account numbers at a time
        $position_fields = array( "account_number", "account_type", "cusip", "symbol", "SUM(trade_date_quantity) AS trade_date_quantity", "SUM(settle_date_quantity) AS settle_date_quantity",
            "close_price", "description", "as_of_date", "current_factor", "original_face_amount", "factored_clean_price",
            "factored_indicator", "security_type_code", "option_symbol", "registered_rep_1", "registered_rep_2", "filename",
            "zero_percent_shares", "one_percent_shares", "two_percent_shares", "three_percent_shares", "account_source",
            "account_type_description", "accrual_amount", "asset_class_type_code", "capital_gain_instruction_long_term",
            "capital_gain_instruction_short_term", "clean_price", "SUM(closing_market_value) AS closing_market_value", "core_fund_indicator", "cost",
            "cost_basis_indicator", "st_basis_per_share", "cost_method", "current_face", "custom_short_name",
            "dividend_instruction", "exchange", "fbsi_short_name", "floor_symbol", "fund_number", "host_type_code",
            "lt_shares", "maturity_date", "money_source_id", "money_source", "operation_code", "plan_name", "plan_number",
            "pool_id", "position_type", "pricing_factor", "primary_account_owner", "product_name", "product_type",
            "registration", "security_asset_class", "security_group", "security_id", "security_type_description", "st_shares",
            "SUM(unrealized_gain_loss_amount) AS unrealized_gain_loss_amount", "unsettled_cash", "file_date", "insert_date");

        $positions = new cFidelityPositions("FIDELITY", "custodian_omniscient", "positions",
            "custodian_portfolios_fidelity", "custodian_positions_fidelity", $rep_codes, array());
        $missing_positions = $positions->GetMissingCRMPositions();
#        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);
        echo 'check positions';exit;
#        print_r($missing_positions);exit;
/*        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
            "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
            "f.issue_date", "f.share_per_contact", "pr.factor");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array(), $fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        /*********END OF STEP 2********/

/****FIDELITY**********************************/

/****TD**********************************/
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
            $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
                "custodian_portfolios_td", "custodian_positions_td", $rep_codes, array());
            $missing_positions = $positions->GetMissingCRMPositions();
            $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
            if(!empty($missing_positions))
                $positions->CreateNewPositionsFromPositionData($missing_positions);

            //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
            $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
                "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
                "f.issue_date", "f.share_per_contact", "pr.factor");
            //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
            $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
                "custodian_securities_td", $symbols, array(), $fields);
            $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
            if(!empty($missing_securities))
                $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
            $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
            $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
            /*********END OF STEP 2********/

            /***STEP 3 - CREATE TRANSACTIONS WORKING***/
            $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
                "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
                "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
                "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity");

            $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
                "custodian_portfolios_td", "custodian_transactions_td",
                $rep_codes, $fields);
            $transactions->GetTransactionsDataBetweenDates('2019-01-01', date("Y-m-d"));
            $missing = $transactions->GetMissingCRMTransactions();
            echo sizeof($missing);exit;
#            $transactions->CreateNewTransactionsFromTransactionData($missing);
            /*********END OF STEP 3********/
/****TD**********************************/
#        }
        echo "Script End: " . date("Y-m-d H:i:s") . '<br />';
        exit;
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        foreach($rep_codes AS $k => $v){
            echo $v . '<br />';
        }
        exit;
        require_once("libraries/custodians/cCustodian.php");
        echo "Script start: " . date("Y-m-d H:i:s") . '<br />';


        require_once('modules/ModSecurities/actions/ConvertCustodian.php');
        global $adb;

#        $start = date("Y-m-d", strtotime("today -1 Month"));//Go back a month
        $start = "2018-01-01";
        $end = date("Y-m-d");//Today's date for the index

#        ModSecurities_ConvertCustodian_Model::UpdateAllIndexesEOD($start, $end);
        ModSecurities_ConvertCustodian_Model::UpdateIndexSymbolsEOD(array("MSCIEAFE", "DVG", "GSPC", "SP500BDT", "IDCOTCTR"), $start, $end);
        ModSecurities_Module_Model::UpdateIndexPricesWithLatest();

echo 'check now!';exit;
        /***STEP 1 - CREATE AND UPDATE PORTFOLIOS WORKING -- REQUIRES advisor_control_number or fails because smownerid can't be null***/

         //Pull portfolio and balance information for the specified rep codes
       $td = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
                             "custodian_portfolios_td", "custodian_balances_td",
                                array('A7KK'));//, 'AMSZ', 'AKXQ'));
       $data = $td->GetExistingCRMAccounts();//Get accounts already in the CRM
       $missing = $td->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
       $td->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
       $existing = $td->GetExistingCRMAccounts();//Get existing CRM accounts
       $td->UpdatePortfoliosFromPortfolioData($existing);//Update the existing accounts with the latest data from the custodian

        echo 'step 1 done';exit;
/*********END OF STEP 1********/

/***STEP 2 - CREATE AND UPDATE POSITIONS/SECURITIES WORKING***/
/*
        //Pull all specified position data.  Auto setup will pull all info and set it up for us.  If there are memory issues due to too much data
        //then account numbers will need to be set manually and auto setup turned off.  We can then use the GetPositionsData function (follow the
        //constructor for an example on how to load.  This could be done in a loop setting <x> number of account numbers at a time
        $positions = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td", array('A7KK'), array());
        $missing_positions = $positions->GetMissingCRMPositions();
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

        //Fields specifically identified here because there are joins to other tables (prices for example), and we don't want * to conserve memory
        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
            "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
            "f.issue_date", "f.share_per_contact", "pr.factor");
        //Securities REQUIRES a list of symbols.  It does not auto compare to positions because we may not necessarily want just those symbols
        $securities = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array(), $fields);
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        exit;
*/
/***STEP 3 - CREATE TRANSACTIONS WORKING***/
        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity");

        $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
            "custodian_portfolios_td", "custodian_transactions_td",
            array('A7KK'), $fields);
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $transactions->GetTransactionsDataBetweenDates('2019-01-01', '2020-05-29');
        $missing = $transactions->GetMissingCRMTransactions();
        $transactions->CreateNewTransactionsFromTransactionData($missing);
        echo 'all done';
        exit;
#        print_r($data);
        exit;


        echo "Script End: " . date("Y-m-d H:i:s") . '<br />';
        echo 'done';exit;

/*
        $pershing = new cPershingSecurities("Pershing", "custodian_omniscient", "securities",
            "custodian_securities_pershing");
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $pershing->GetSecuritiesData(array("AAPL", "MSFT"));
        print_r($data);
        exit;
*/
/*
        $schwab = new cTDSecurities("Schwab", "custodian_omniscient", "securities",
                                "custodian_securities_schwab");
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $schwab->GetSecuritiesData(array("AAPL", "MSFT"));
        print_r($data);
        exit;
*/
/*        global $adb;
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCode(array('A81E', 'A7OY', 'A7P4'));
        $questions = generateQuestionMarks($accounts);
        $query = "SELECT symbol FROM custodian_omniscient.custodian_positions_td WHERE account_number IN ({$questions}) GROUP BY symbol";
        $result = $adb->pquery($query, array($accounts));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                echo $v['symbol'] . '<br />';
            }
        }
        exit;
*/
        $td = new cTDPositions("TD", "custodian_omniscient", "positions",
            "custodian_portfolios_td", "custodian_positions_td",
            array('A7KK', 'AMSZ', 'AKXQ'), array(), false);//Get positions, but don't auto load or anything (accounts still get set)
        $symbols = $td->GetAllOldAndNewPositionSymbols($td->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about

        $fields = array("f.symbol", "f.description", "f.security_type", "pr.price", "f.maturity", "f.annual_income_amount", "f.interest_rate", "acm.multiplier",
                        "acm.omni_base_asset_class", "acm.security_type AS mapped_security_type", "f.call_date", "f.first_coupon", "f.call_price",
                        "f.issue_date", "f.share_per_contact", "pr.factor");
        $td = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols, array("TDCASH" => "Cash"), $fields);
#        $crm_symbols = $td->GetExistingCRMSecurities();
#        $missing_symbols = $td->GetMissingCRMSecurities();

#        $crm_symbols = $td->GetAllCRMSecurities();//Get all securities that are in the CRM
#        $missing_symbols = array_diff_key($symbols, $crm_symbols);
#        $td->SetSecurities($missing_symbols);
#        $td->CreateNewSecuritiesFromSecurityData($missing_symbols);
        $custodian_symbols = $td->GetCustodianSecurities();
        $td->UpdateSecuritiesFromSecuritiesData($custodian_symbols);
        echo "Now check";
        exit;


        print_r($symbols);exit;
        $latest_date = $td->GetLatestPositionsDate();
        $symbols = $td->GetPositionSymbolsFromDate($accounts, $latest_date);

        $td = new cTDSecurities("TD", "custodian_omniscient", "securities",
            "custodian_securities_td", $symbols);
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $td->GetMissingCRMSecurities();
        $td->CreateNewSecuritiesFromSecurityData($data);
        exit;


        $missing = $td->GetMissingCRMPositions(array());

        $td = new cTDSecurities("TD", "custodian_omniscient", "securities",
                                "custodian_securities_td");
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $td->GetSecuritiesData(array("AAPL", "MSFT"));
        print_r($data);
        exit;

/*
        $td = new cFidelitySecurities("Fidelity", "custodian_omniscient", "securities",
            "custodian_securities_fidelity");
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $td->GetSecuritiesData(array("AAPL", "MSFT"));
        print_r($data);
        exit;
*/

/*
        $pershing = new cPershingTransactions("Pershing", "custodian_omniscient", "transactions",
            "custodian_portfolios_pershing", "custodian_transactions_pershing",
            array('60E'));
//        $fidelity->SetColumns(array("transaction_id"));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
        $data = $pershing->GetTransactionsDataBetweenDates('2020-01-01', '2020-05-01');
        echo count($data) . '<br />';
        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");
        exit;
*/
/*
        $schwab = new cSchwabTransactions("Schwab", "custodian_omniscient", "transactions",
            "custodian_portfolios_schwab", "custodian_transactions_schwab",
            array('08134583'));
//        $fidelity->SetColumns(array("transaction_id"));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
        $data = $schwab->GetTransactionsDataBetweenDates('2020-01-01', '2020-05-01');
        echo count($data) . '<br />';
        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");
        exit;
*/
/*
        $td = new cTDTransactions("TD", "custodian_omniscient", "transactions",
                                  "custodian_portfolios_td", "custodian_transactions_td",
                                   array('A7KK', 'AMSZ'));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        $data = $td->GetTransactionsDataBetweenDates('2019-01-01', '2020-05-01', array(), array());
        print_r($data);
        exit;
*/
/*
        $fidelity = new cFidelityTransactions("Fidelity", "custodian_omniscient", "transactions",
            "custodian_portfolios_fidelity", "custodian_transactions_fidelity",
            array('GH1', 'GH2'));
//        $fidelity->SetColumns(array("transaction_id"));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
            $data = $fidelity->GetTransactionsDataBetweenDates('2020-01-01', '2020-05-01', array(), array());
//        echo count($data) . '<br />';
        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");
        exit;
*/
/*
        $td = new cPershingPrices("Pershing", "custodian_omniscient", "prices",
                             "custodian_portfolios_pershing", "custodian_prices_pershing",
                                           array());
        #$data = $td->GetPricesDataForDate("2020-01-16", array("AAPL", "MSFT"));
        $data = $td->GetPricesDataBetweenDates(array("AAPL", "MSFT"), "2020-01-16", "2020-01-24");
        print_r($data);
        exit;
*/
/*
        $td = new cSchwabPrices("Schwab", "custodian_omniscient", "prices",
                             "custodian_portfolios_schwab", "custodian_prices_schwab",
                                           array());
        #$data = $td->GetPricesDataForDate("2020-01-16", array("AAPL", "MSFT"));
        $data = $td->GetPricesDataBetweenDates(array("AAPL", "MSFT"), "2020-01-16", "2020-01-24");
        print_r($data);
        exit;
*/
/*
        $td = new cTDPrices("TD", "custodian_omniscient", "prices",
                             "custodian_portfolios_td", "custodian_prices_td",
                                           array());
        #$data = $td->GetPricesDataForDate("2020-01-16", array("AAPL", "MSFT"));
        $data = $td->GetPricesDataBetweenDates(array("AAPL", "MSFT"), "2020-01-16", "2020-01-24");
        print_r($data);
        exit;
*/
/*
        $fidelity = new cFidelityPrices("Fidelity", "custodian_omniscient", "prices",
                             "custodian_prices_fidelity", "custodian_prices_fidelity",
                                           array());
        $data = $fidelity->GetPricesDataForDate("2020-01-16", array("AAPL", "MSFT"));
        $data = $fidelity->GetPricesDataBetweenDates(array("AAPL", "MSFT"), "2020-01-16", "2020-01-24");
        print_r($data);
        exit;
*/

/*
        $pershing = new cSchwabPositions("Pershing", "custodian_omniscient", "positions",
                                "custodian_portfolios_pershing", "custodian_positions_pershing",
                                              array('60E'));
        $pershing->SetColumns(array("account_number", "symbol"));
        $data = $pershing->GetPositionsData();
        echo count($data);
        print_r($data);
*/
/*
        $td = new cTDPositions("TD", "custodian_omniscient", "positions",
                                "custodian_portfolios_td", "custodian_positions_td",
                                              array('A7KK', 'AMSZ', 'AKXQ'), array("TDCASH" => "Cash"));
        $missing = $td->GetMissingCRMPositions(array());

        if(!empty($missing))
            $td->CreateNewPositionsFromPositionData($missing);
        if(!empty($td->GetCustodianPositions()))
            $td->UpdatePositionsFromPositionsData($td->GetCustodianPositions());
#        $data = $td->GetPositionsData();
#        echo count($data);exit;
        echo "Script End: " . date("Y-m-d H:i:s") . '<br />';
        echo 'done';
        exit;
*/
#Array ( [943401100] => Array ( [3] => DGRO )
#        [941110632] => Array ( [8] => IBB )
#        [941107909] => Array ( [4] => IBB )
#        [941795346] => Array ( [0] => LUV ) )
/*
        $fidelity = new cFidelityPositions("Fidelity", "custodian_omniscient", "positions",
                                "custodian_portfolios_fidelity", "custodian_positions_fidelity",
                                              array('GH1', 'GH2'));
        echo date("Y-m-d H:i:s");
        $data = $fidelity->GetPositionsData();
        echo count($data);
        echo date("Y-m-d H:i:s");
#        print_r($data);
        exit;
*/
/*
        $fidelity = new cSchwabPositions("Schwab", "custodian_omniscient", "positions",
                                "custodian_portfolios_schwab", "custodian_positions_schwab",
                                              array('08901624', '08781415'));
        $fidelity->SetColumns(array("account_number", "symbol"));
        $data = $fidelity->GetPositionsData();
        echo count($data);
        print_r($data);
*/

/*
        $pershing = new cPershingPortfolios("Pershing", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_pershing", "custodian_balances_pershing",
                                    array('60E'));
        $data = $pershing->GetPortfolioData();
        print_r($data);
        echo 'done';exit;
*/
/*
        $fidelity = new cFidelityPortfolios("Fidelity", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_fidelity", "custodian_balances_fidelity",
                                    array('GH1'));
        $data = $fidelity->GetPortfolioData();
        print_r($data);
        echo 'done';exit;
*/


/*        $td = new cTDPortfolios("TD", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_td", "custodian_balances_td",
                                    array('A7KK', 'AMSZ', 'AKXQ'));
        $data = $td->GetExistingCRMAccounts();

        $existing = $td->GetExistingCRMAccounts();
        $td->UpdatePortfoliosFromPortfolioData($existing);
*/
#        print_r($data);exit;
#        $data = $td->GetCustodianAccounts();
#        print_r($data);exit;
#        $missing = $td->GetMissingCRMAccounts();
#        $td->CreateNewPortfoliosFromPortfolioData($missing);
#        print_r($missing);exit;
#        echo $td->GetMissingPortfolios();exit;
#        $data = $td->GetPortfolioData();
#        $data = $td->GetPortfolioData();
#        print_r($data);
        echo 'done';exit;

        $fidelity = new cFidelityPortfolios("Fidelity", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_fidelity", "custodian_balances_fidelity",
                                    array('GH1'));
        $data = $fidelity->GetPortfolioData();
        print_r($data);
        echo 'done';exit;

/*
        $schwab = new cSchwabPortfolios("Schwab", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_schwab", "custodian_balances_schwab",
                                    array('08901624', '08781415'));
        $data = $schwab->GetPortfolioData();
        echo count($data) . '<br />';
*/
/*
        $fidelity = new cFidelityPortfolios("Fidelity", "custodian_omniscient", "portfolios",
                                 "custodian_portfolios_fidelity", "custodian_balances_fidelity",
                                    array('GH1'));
        $data = $fidelity->GetPortfolioData();
        print_r($data);
        echo 'done';exit;
*/
        echo "Script End: " . date("Y-m-d H:i:s") . '<br />';
        echo 'done';exit;
        /**NOTE TO SELF... First auto create companies... Then auto create advisors**/
        global $adb;
        $query = "SELECT id, user_name, first_name, last_name, advisor_control_number
                  FROM vtiger_users
                  GROUP BY id";
        $result = $adb->pquery($query, array());
        $list = array();
        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $tmp = array();
                $tmp['id'] = $r['id'];
                $tmp['user_name'] = $r['user_name'];
                $tmp['first_name'] = $r['first_name'];
                $tmp['last_name'] = $r['last_name'];
                $ids = explode(",", $r['advisor_control_number']);
                foreach($ids AS $k => $v){
                    $tmp['advisor_control_number'] = $v;
                    $list[] = $tmp;
                }
            }
        }

if(!copy("/var/www/sites/360vew/user_privileges/user_privileges_1.php","/var/www/sites/jimd/user_privileges/user_privileges_1.php")){
    echo "failed to copy file...";
}else{
echo "It claims to have worked";
}
        foreach($list AS $k => $v){
            $query = "INSERT IGNORE INTO users_to_repcode (username, first_name, last_name, rep_code) VALUES (?, ?, ?, ?)";
            $adb->pquery($query, array($v['user_name'], $v['first_name'], $v['last_name'], $v['advisor_control_number']), true);
        }

        exit;

        require_once('modules/PortfolioInformation/models/DailyBalances.php');
        /**
         * One simple call to write the daily balances for all active users using the past 7 days (allows for mistakes and missing data to catch up)
         */
        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
    }
}
#        CALL ARREAR_BILLING("939489313", "2019-01-08", "2019-04-08", 1, 3, @billAmount);
/*        global $adb;
        $current_date = date("Y-m-d");
        $query = "SELECT portfolioinformationid, account_number, periodicity, annual_fee_percentage
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0 AND account_number != 0 AND account_number IS NOT NULL AND account_number != ''";
        $result = $adb->pquery($query, array());

        $query = "CALL ARREAR_BILLING(?, ?, ?, ?, ?, @billAmount)";
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)) {
                switch(strtolower($v['periodicity'])){
                    case "monthly":
                        $periodicity = 1;#184
                        break;
                    case "quarterly":
                        $periodicity = 3;
                        break;
                    default:
                        $periodicity = 1;
                        break;
                }

                $start_date = date("Y-m-d", strtotime("-" . $periodicity . " Months"));
                $adb->pquery($query, array($v['account_number'], $start_date, $current_date, $v['annual_fee_percentage'], $periodicity));
                $q = "UPDATE vtiger_portfolioinformationcf SET bill_amount = (SELECT @billAmount) WHERE portfolioinformationid = ?";
                $adb->pquery($q, array($v['portfolioinformationid']));
            }
        }
        echo 'all finished';exit;
        echo 'no loop';exit;
        $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";

        $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();

        if($users) {
            foreach ($users AS $a => $b) {
                $td = new Trading_Ameritrade_Model($b['userid'], $b['password']);
                $data = $td->GetPositions($url, null, 'B');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateTDSecurityTableBonds($v);
                }
                $data = $td->GetPositions($url, null, 'O');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateTDSecurityTableOptions($v);
                }

                $data = $td->GetPositions($url, null, 'F');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateTDSecurityTableMutualFund($v);
                }

                $data = $td->GetPositions($url, null, 'M');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateTDSecurityTableMoneyMarketFund($v);
                }

                $data = $td->GetPositions($url, null, 'E');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateTDSecurityTableCommonStock($v);
                }
            }

            ModSecurities_Module_Model::CopyTmpTDTableToCRM();
            ModSecurities_Module_Model::SetPriceAdjustmentFromTDPrices();
        }
    echo 'songs over';exit;

        global $adb;
        $account_numbers = Transactions_Module_Model::GetTDAccountsMissingNetAmountsReceiptOfSecurities();
        print_r($account_numbers);exit;
        foreach($account_numbers AS $k => $v){
            $query = "CALL TD_REC_TRANSACTIONS(?);";
            $adb->pquery($query, array($v));
        }
        echo "ALL FINISHED";exit;
#        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
#        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily( );
#        echo 'done';exit;
        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1');//SD2 is patrick berry, no longer active
##        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
        $strat = new StratifiAPI();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();
        foreach($account_numbers AS $k => $v){
            $data = PortfolioInformation_Module_Model::GetStratifiData($v);
            print_r($data);/*
            $result = $strat->UpdatePositionsToStratifi($data);
            echo "Result: ";
            print_r($result);*/
/*            echo '<br /><br />';
        }
        echo "Finished Everything";
exit;
}
*/
/*        #PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily("2019-02-06");
        echo 'allocations done';exit;
        //https://lanserver24.concertglobal.com:8085/OmniServ/AutoParse?custodian=schwab&tenant=Omniscient&user=syncuser&password=Concert222&connection=192.168.100.224&skipDays=1&dontIgnoreFileIfExists=1&operation=writefiles&extension=RPS
#        echo 'done';exit;
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $date = date("Y-m-d H:m:s");
#        $tmp->SetCustodianStatus(1, "Writing Files TD for last 7 days " . $date);
        $result = $tmp->WriteFiles("schwab", "writefiles", "1", "0", "RPS");
        $date = date("Y-m-d H:m:s");
#        $tmp->SetCustodianStatus(0, null, "Finished Writing Files " . $date);

#        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
#        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();
        echo 'fini';exit;
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();

        $strat = new StratifiAPI();

        $account_numbers = $strat->GetAccountsThatHaveStratifiID();
        foreach($account_numbers AS $k => $v){
            $data = PortfolioInformation_Module_Model::GetStratifiData($v);
            $result = $strat->UpdatePositionsToStratifi($data);
            print_r($result);
            echo '<br /><br />';
        }
        echo "Finished Everything";
    }

    public function getCustomScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
#			"~/libraries/jquery/qtip/jquery.qtip.js",
#			"~/libraries/amcharts/amcharts_3.20.9/amcharts/amcharts.js",
#			"~/libraries/amcharts/amcharts_3.20.9/amcharts/pie.js",
            "~/libraries/jquery/d3/d3.min.js",
#			"~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.js",

#			"~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
#			"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
#			"modules.$moduleName.resources.NewHoldingsReport", // . = delimiter
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
#			'~/layouts/vlayout/modules/PortfolioInformation/css/HoldingsReport.css',
#			'~/libraries/jquery/qtip/jquery.qtip.css',
#			"~/libraries/amcharts/amcharts_3.20.9/amcharts/plugins/export/export.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }
}

?>