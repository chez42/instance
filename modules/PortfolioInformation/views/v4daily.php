<?php
if (ob_get_level() == 0) ob_start();
ob_implicit_flush(true);
ob_end_flush();

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
require_once("libraries/EODHistoricalData/EODGuzzle.php");
include_once("modules/PortfolioInformation/models/PrintingContactInfo.php");

require_once("libraries/custodians/cCustodian.php");
require_once('modules/ModSecurities/actions/ConvertCustodian.php');
include_once("include/utils/omniscientCustom.php");

spl_autoload_register(function ($className) {
    if (file_exists("libraries/EODHistoricalData/$className.php")) {
        include_once "libraries/EODHistoricalData/$className.php";
    }
});

class PortfolioInformation_v4daily_View extends Vtiger_BasicAjax_View{

    function process(Vtiger_Request $request)
    {
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLastXDaysForAllUsers(5000);
        echo 'Check widget!';exit;

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        foreach($accounts AS $k => $v){
            echo "'{$v}'," . '<br />';
        }
        exit;

        $locations = new cFileHandling();
        $data = $locations->GetLocationDataFromRepCode($rep_codes);
        foreach($data AS $k => $v){
            StatusUpdate::UpdateMessage("MANUALPARSING", "Auto Parsing {$v->rep_code}");
            $parse = new FileParsing($v->custodian, 'parse_all', 3, 0, $v->rep_code);
            $parse->parseFiles();
        }
        StatusUpdate::UpdateMessage("MANUALPARSING", "finished");
        echo 'fini';exit;
        /*        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
                $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        print_r($rep_codes);
        echo '<br /><br />';
        print_r($accounts);
        exit;
                echo 'hi';exit;
                include("cron/modules/Custodian/LatestData.service");
                echo 'dun';exit;
                $ready = new cReady();
        #        $ready->PullAllTD('942266318');
        #        echo 'done 942266318';exit;*/
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        PortfolioInformation_Module_Model::TDBalanceCalculationsRepCodes($rep_codes, '2020-09-25', '2020-10-01', false);
        echo 'werd 2';exit;
        exit;
        $data = $ready->GetReadyModuleDataViaRepCode($rep_codes, 3);//We now have a list of elements that are ready
        if(isset($data->account_list))
            $ready->PullAllTD($data->account_list, true);
        echo 'DUN';exit;
        foreach($data AS $k => $v){
            switch(strtolower($v['custodian'])){
                case "td":
                    $ready->PullAllTD($v['account_number']);
                    break;
            }
        }

        echo 'dun';exit;

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        //2012 set for no particular reason, 'true' finds the earliest date as is
        PortfolioInformation_Module_Model::TDBalanceCalculationsRepCodes($rep_codes, '2020-05-10', '2020-05-31', false);
        echo 'dun';exit;

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        print_r($rep_codes);exit;
        /*Fill in the consolidated balances table*/
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        foreach($accounts AS $k => $v){
            echo "'{$v}'," . '<br />';
        }
        exit;

        require_once('modules/PortfolioInformation/models/DailyBalances.php');
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        /**
         * One simple call to write the daily balances for all active users using the past 7 days (allows for mistakes and missing data to catch up)
         */

        global $dbconfig, $adb;

        $query = "INSERT IGNORE INTO vtiger_prices
          SELECT * FROM live_omniscient.vtiger_prices WHERE date > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)
          AND symbol IN (SELECT security_symbol FROM vtiger_modsecurities)";
        $adb->pquery($query, array());

        $query = "INSERT IGNORE INTO vtiger_prices_index
          SELECT * FROM live_omniscient.vtiger_prices_index WHERE date > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
        $adb->pquery($query, array());

        /*Positions asset allocation widget calculations*/
        PortfolioInformation_TotalBalances_Model::ClosePositionsBasedOnTheirPortfolio();
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        /*Fill in the consolidated balances table*/
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        $start = date('Y-m-d', strtotime('-7 days'));
        $finish = date('Y-m-d');
        $questions = generateQuestionMarks($accounts);
        $query = "CALL custodian_omniscient.CONSOLIDATE_BALANCES_DEFINED(\"{$questions}\", ?, ?, ?)";//Write to consolidated balances
//Write to the users table from consolidated balances for grand total of all accounts
        $adb->pquery($query, array($accounts, $dbconfig['db_name'], $start, $finish), true);

        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();

        echo 'FINI!';exit;


        require_once('modules/PortfolioInformation/models/DailyBalances.php');
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        /**
         * One simple call to write the daily balances for all active users using the past 7 days (allows for mistakes and missing data to catch up)
         */

        global $dbconfig, $adb;

        $query = "INSERT IGNORE INTO vtiger_prices
          SELECT * FROM live_omniscient.vtiger_prices WHERE date > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)
          AND symbol IN (SELECT security_symbol FROM vtiger_modsecurities)";
        $adb->pquery($query, array());

        $query = "INSERT IGNORE INTO vtiger_prices_index
          SELECT * FROM live_omniscient.vtiger_prices_index WHERE date > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
        $adb->pquery($query, array());

        /*Positions asset allocation widget calculations*/
        PortfolioInformation_TotalBalances_Model::ClosePositionsBasedOnTheirPortfolio();
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        /*Fill in the consolidated balances table*/
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        $start = date('Y-m-d', strtotime('-7 days'));
        $finish = date('Y-m-d');
        $questions = generateQuestionMarks($accounts);
        $query = "CALL custodian_omniscient.CONSOLIDATE_BALANCES_DEFINED(\"{$questions}\", ?, ?, ?)";//Write to consolidated balances
//Write to the users table from consolidated balances for grand total of all accounts
        $adb->pquery($query, array($accounts, $dbconfig['db_name'], $start, $finish), true);

        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'fini';exit;
        global $adb, $dbconfig;
        /*        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
                $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
                $start = date('Y-m-d', strtotime('-5000 days'));
                $finish = date('Y-m-d');
                $questions = generateQuestionMarks($accounts);
                $query = "CALL custodian_omniscient.CONSOLIDATE_BALANCES_DEFINED(\"{$questions}\", ?, ?, ?)";//Write to consolidated balances
        //Write to the users table from consolidated balances for grand total of all accounts
                $adb->pquery($query, array($accounts, $dbconfig['db_name'], $start, $finish), true);
        */
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLastXDaysForAllUsers(5000);
        echo 'Check widget!';exit;

        echo date("m-d-Y H:i:s") . '<br />';
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
#        PortfolioInformation_Module_Model::TDBalanceCalculationsMissingOnly("2010-01-01", "2020-09-11");
#        PortfolioInformation_Module_Model::TDBalanceCalculationsRepCodes($rep_codes, '2019-12-10', '2019-12-13', false);
        echo date("m-d-Y H:i:s") . '<br />';
        echo 'balances done';exit;
        /***STEP 3 - CREATE TRANSACTIONS WORKING***/
        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity", "m.operation");

        $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
            "custodian_portfolios_td", "custodian_transactions_td",
            $rep_codes, $fields);
        $transactions->GetTransactionsDataBetweenDates('2020-07-01', date("Y-m-d"));
        $missing = $transactions->GetMissingCRMTransactions();
        $transactions->CreateNewTransactionsFromTransactionData($missing);
        /*********END OF STEP 3********/
        echo 'done';exit;

        set_time_limit(0);
        $control_numbers = array('SV2');/*, 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1',
            'NSGV', 'NSGV1');//SD2 is patrick berry, no longer active*/
        $strat_hh = new StratHouseholds();
        $strat_contact = new StratContacts();
        $sAdvisors = new StratAdvisors();
        /*
                $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);
                $sAdvisors->AutoCreateCompanies();
                $sAdvisors->AutoCreateAdvisors();

                PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
                PortfolioInformation_Stratifi_Model::CreateStratifiContactsForAllAccounts();
                PortfolioInformation_Stratifi_Model::CreateStratifiHouseholdsForAllAccounts();
                PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
        #PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);###THIS IS NOW DONE IN THE FUNCTION GetAllContactsAndUpdateAdvisorOwnership
                $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();
                $strat_contact->GetAllContactsAndUpdateAdvisorOwnership();*/
        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();
        echo 'done';exit;


        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        /***STEP 3 - CREATE TRANSACTIONS WORKING***/
        $fields = array("t.transaction_id", "t.advisor_rep_code", "t.file_date", "t.account_number", "t.transaction_code", "t.cancel_status_flag",
            "t.symbol", "t.security_code", "t.trade_date", "t.quantity", "t.net_amount", "t.principal", "t.broker_fee", "t.other_fee",
            "t.settle_date", "t.from_to_account", "t.account_type", "t.accrued_interest", "t.comment", "t.closing_method",
            "t.filename", "t.insert_date", "t.dupe_saver_id", "mscf.security_price_adjustment", "m.omniscient_category", "m.omniscient_activity", "m.operation");

        $transactions = new cTDTransactions("TD", "custodian_omniscient", "transactions",
            "custodian_portfolios_td", "custodian_transactions_td",
            $rep_codes, $fields);
        $transactions->UpdateAllTransactionsOperations(array());
#        echo 'done';exit;
        $transactions->SetAccountNumbers(array("939137688"));
        $transactions->GetTransactionsDataBetweenDates('2020-08-01', date("Y-m-d"));
        $missing = $transactions->GetMissingCRMTransactions();
        $transactions->CreateNewTransactionsFromTransactionData($missing);
        /*********END OF STEP 3********/
        echo 'done';exit;
#        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();
        $recordid = '74049453';
        $coverpage = new FormattedContactInfo($recordid);
        $coverpage->SetTitle("Portfolio Review");
        $coverpage->SetLogo("layouts/hardcoded_images/lhimage.jpg");
#        $output = $coverpage->GetFormattedLogo();
        $viewer = new Vtiger_Viewer();
        $viewer->assign("COVERPAGE", $coverpage);
#        $output = $viewer->view('Reports/LighthouseCover.tpl', 'PortfolioInformation', true);
        $output = $viewer->view('Reports/CoverPage.tpl', 'PortfolioInformation', true);
        echo $output;
        exit;
        /*
                PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();
                echo 'fini';exit;*/
        /*
                $strat = new StratifiAPI();
                $data = PortfolioInformation_Module_Model::GetStratifiData('941107663');
                $result = $strat->UpdatePositionsToStratifi($data);
                echo '<br /><br />';
                print_r($result);
                echo '<br /><br />';
                echo 'done';exit;
        print_r($data);exit;
        */

        set_time_limit(0);
        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1',
            'NSGV', 'NSGV1');//SD2 is patrick berry, no longer active
        $strat_hh = new StratHouseholds();
        $strat_contact = new StratContacts();
        $sAdvisors = new StratAdvisors();

        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);
        $sAdvisors->AutoCreateCompanies();
        $sAdvisors->AutoCreateAdvisors();

        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
        PortfolioInformation_Stratifi_Model::CreateStratifiContactsForAllAccounts();
        PortfolioInformation_Stratifi_Model::CreateStratifiHouseholdsForAllAccounts();
        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
#PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);###THIS IS NOW DONE IN THE FUNCTION GetAllContactsAndUpdateAdvisorOwnership
        $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();
        $strat_contact->GetAllContactsAndUpdateAdvisorOwnership();
        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();
        echo 'done';exit;
        /*        $guz = new cEodGuzzle();
                $data = json_decode($guz->getFundamentals("HYG"));
                $div = json_decode($guz->getDividends("HYG", "US", '2019-01-01', '2019-12-31'));

                $fund = new TypeETF($data, $div);
                print_r($fund);
                $fund->UpdateIntoOmni();
        echo 'done';
                exit;

        echo 'test';exit;*/

        $symbols = ModSecurities_Module_Model::GetAllSecuritySymbols();
        ModSecurities_OmniToOmniTransfer_Model::CopyFromInstance("live_omniscient", $symbols);
        echo 'now check';exit;
        $limit = 50000;
        $count = 0;
        $etfs = OmnisolReader::MatchSymbolsOfSecurityType($symbols, "etf");
        $count = 0;//Reset the counter
        /*        foreach($etfs AS $k => $v){
                    if($count >= $limit)
                        continue;
                    $writer->WriteEodToOmni($v);
                    echo "Wrote " . $v . '<br />';
                    $count++;
                }*/

        echo "Start Funds: " . Date("Y-m-d H:i:s") . '<br />';
        $funds = OmnisolReader::MatchSymbolsOfSecurityType($symbols, "fund");
        $count = 0;//Reset the counter
        foreach($funds AS $k => $v){
            if($count >= $limit)
                continue;
            $writer->WriteEodToOmni($v);
            echo "Wrote " . $v . '<br />';
            $count++;
        }
        echo "Finished Funds: " . Date("Y-m-d H:i:s") . '<br />';
        echo "Start Update Attributes: " . Date("Y-m-d H:i:s") . '<br />';
        PositionInformation_Module_Model::UpdatePositionSecurityAttributes();
        echo "Finished Update Attributes: " . Date("Y-m-d H:i:s") . '<br />';
        echo 'All Done';exit;

        $guz = new cEodGuzzle();
        $data = json_decode($guz->getFundamentals("VTSAX"));
        $div = json_decode($guz->getDividends("VTSAX", "US", '2019-01-01', '2019-12-31'));
        $fund = new TypeFund($data, $div);
        $fund->UpdateIntoOmni();
        print_r($fund);exit;


#        PortfolioInformation_Module_Model::TDBalanceCalculationsRepCodes(array("GOX"), '2016-01-01', '2020-07-27');
        echo "Start: " . Date("Y-m-d H:i:s") . '<br />';
        ob_flush();
        flush();

        $writer = new OmniscientWriter();
        $symbols = ModSecurities_Module_Model::GetAllSecuritySymbols();
        $etfs = OmnisolReader::MatchSymbolsOfSecurityType($symbols, "etf");

        $limit = 500000;
        $count = 0;
        foreach($etfs AS $k => $v){
            if($count >= $limit)
                continue;
            $writer->WriteEodToOmni($v);
            echo "Wrote " . $v . '<br />';
            ob_flush();
            flush();
            $count++;
        }

        PositionInformation_Module_Model::UpdatePositionSecurityAttributes();

        echo "End: " . Date("Y-m-d H:i:s") . '<br />';
        ob_flush();
        flush();
        echo 'done';exit;


        $guz = new cEodGuzzle();
        $writer = new OmnisolWriter();
        $type = OmnisolReader::DetermineSecurityTypeGivenByEOD("AGG");

#        $exchanges = $writer->GetAndWriteExchangeData();
#        foreach($exchanges AS $k => $v) {
#            print_r($v);
#            echo '<br /><br />';
#            $writer->GetAndWriteTickers('US');
#        }
        echo 'done';
        exit;
        $type = OmnisolReader::DetermineSecurityTypeGivenByEOD("AGG");
        echo $type . '<br />';
#        echo $type;exit;
        $data = $guz->getFundamentals("AGG");
        print_r($data);exit;
        /*        foreach($data->Highlights AS $k => $v){
        #            echo "$" . $k . ",";
                    echo '$this->' . $k . ' = $data->' . $k . ';';
                    echo "<br />";
                }*/
        $stock = new TypeStock($data);
        $writer = new OmnisolWriter();
#        print_r($data);
        $writer->WriteNewStockData($stock);
        print_r($stock);
        echo 'check now';exit;


        /**
         * This section gets all of the different exchanges and a list of symbols within them.   With that list, it inserts them into
         * the database.  We may be able to speed this up if we can import the CSV rather than loop through the individual lines
         */
        $writer = new OmnisolWriter();
        $exchanges = $writer->GetAndWriteExchangeData();
        foreach($exchanges AS $k => $v) {
            $writer->GetAndWriteTickers($v->Code);
        }
        echo 'done';
        exit;
        /******************************************/

        echo 'done';exit;


        include_once("libraries/custodians/OptionsMapping.php");
        $guz = new cEodGuzzle();
#        $data = json_decode($guz->getFundamentals("AAPL"));
#        print_r($data);exit;
        exit;
        $exchanges = json_decode($guz->GetExchanges());
        foreach($exchanges AS $k => $v){
            $res = $guz->getTickers($v->Code);
            $guz->writeTickers($res);
            echo $v->Code . ' Finished...<br />';
        }
        exit;

        $guz->writeTicker($res);
        echo 'check now';exit;
##        print_r($res);exit;
##        echo 'done';exit;

        $data = json_decode($guz->getFundamentals("AAPL"));
        foreach($data->Highlights AS $k => $v){
#            echo "$" . $k . ",";
            echo '$this->' . $k . ' = $data->' . $k . ';';
            echo "<br />";
        }

        $stock = new TypeStock($data);

        print_r($stock);exit;

        $data = json_decode($guz->getFundamentals("AAPL"));
        $fund = new TypeFund($data);
        print_r($fund);exit;
        foreach($data->MutualFund_Data AS $k => $v){
            echo '$this->' . $k . ' = $fund_data->MutualFund_Data->' . $k . ';';
            echo "<br />";
        }
#        $stock = new TypeStock($data);
        exit;
        foreach($data->MutualFund_Data->Asset_Allocation AS $k => $v){
            foreach($v AS $a => $b){

#                echo "$" . $a . ", ";
                /*                echo "$" . $k . ", ";
                                print_r($v);
                                echo $v->'Net_%' . '<br />';*/
                echo "<br />";
            }
        }
        echo ";";
        exit;

        $eod = json_decode($guz->getFundamentals("AGG"));
        print_r($eod);exit;
        /*
        $price = json_decode($guz->getSymbolRealTimePricing("XOM"));
print_r($price);exit;*/
        #$option = OptionsMapping::MapToStandards("AAPL", 13, 11, 01, "C", 470*1000);
        $option = OptionsMapping::MapTDToStandard("XOM AUG 18 2017 82.5 CALL");
        $symbol = OptionsMapping::GetSymbolFromStandardizedOption($option);//Now XOM
//        $eod = json_decode($guz->getOptions($symbol));

        $op_data = json_decode($guz->getOptionContract($option));//This is the individual contract, but takes much longer

        print_r($op_data);exit;
        require_once("modules/PortfolioInformation/models/TWR.php");
        require_once("libraries/Reporting/ReportCommonFunctions.php");
        $twr = new TWR();
        $end_date = date('Y-m-d');

        $accounts = PortfolioInformation_Module_Model::GetAllActiveAccountNumbers(false);//We don't want manual only
        $contacts = PortfolioInformation_Module_Model::GetUniqueContactIDsFromPortfolioModule();
        $households = PortfolioInformation_Module_Model::GetUniqueHouseholdIDsFromPortfolioModule();

        $t30 = GetDateMinusMonths(TRAILING_1, $end_date);
        $t90 = GetDateMinusMonths(TRAILING_3, $end_date);
        $t365 = GetDateMinusMonths(TRAILING_12, $end_date);

#        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();

        /******CALCULATE INDIVIDUAL TWR**********/


        $date = date("Y-m-d");
#$counter = 0;
        foreach($accounts AS $k => $v){
#            if($counter >= 25){
#                echo "COUNT DONE";return;
#            }
            $count = PortfolioInformation_Module_Model::GetNumberOfAccountIntervals($v);
#            echo $v . ' has ' . $count . '<br />';
            if($count == 0){
                echo "Calculating for " . $v . '<br />';
                PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts(array($v), null, null, true);
            }

            $t30_result = $twr->CalculateTWRCumulative(array($v), $t30, $end_date);
            $t90_result = $twr->CalculateTWRCumulative(array($v), $t90, $end_date);
            $t365_result = $twr->CalculateTWRCumulative(array($v), $t365, $end_date);

            $twr->UpdatePortfolioTWR($v, "previous_month_percentage", $t30_result);
            $twr->UpdatePortfolioTWR($v, "trailing_3_month_percentage", $t90_result);
            $twr->UpdatePortfolioTWR($v, "trailing_6_month_percentage", $t365_result);
#            $t12_result = $twr->CalculateTWRCumulative(array($v), $t12, $end_date);
            echo $v . ' T30: ' . $t30_result . ', T90: ' . $t90_result . ', T365: ' . $t365_result . '<br /><br />';
#            $counter++;
        }

        foreach($contacts AS $k => $v){
            $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromContactID($v);
            $t30_result = $twr->CalculateTWRCumulative($accounts, $t30, $end_date);
            $t90_result = $twr->CalculateTWRCumulative($accounts, $t90, $end_date);
            $t365_result = $twr->CalculateTWRCumulative($accounts, $t365, $end_date);

            $twr->UpdateContactTWR($v, "twr_30", $t30_result);
            $twr->UpdateContactTWR($v, "twr_90", $t90_result);
            $twr->UpdateContactTWR($v, "twr_365", $t365_result);

            echo "Contact ID: " . $v . ' T30: ' . $t30_result . ', T90: ' . $t90_result . ', T365: ' . $t365_result . '<br />';
            echo "<br />";
        }

        foreach($households AS $k => $v){
            $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromHouseholdID($v);
            $t30_result = $twr->CalculateTWRCumulative($accounts, $t30, $end_date);
            $t90_result = $twr->CalculateTWRCumulative($accounts, $t90, $end_date);
            $t365_result = $twr->CalculateTWRCumulative($accounts, $t365, $end_date);

            $twr->UpdateHouseholdTWR($v, "twr_30", $t30_result);
            $twr->UpdateHouseholdTWR($v, "twr_90", $t90_result);
            $twr->UpdateHouseholdTWR($v, "twr_365", $t365_result);

            echo "Household ID: " . $v . ' T30: ' . $t30_result . ', T90: ' . $t90_result . ', T365: ' . $t365_result . '<br />';
            echo "<br />";
        }
        echo 'done';
        exit;


        /*        foreach($accounts AS $k => $v) {
                    $tmp = $twr->CalculateIndividualTWRCumulative(array($v), '2020-01-01', '2020-07-07');
                    echo $v . ' -- ' . $tmp . '<br />';
                }*/


        echo 'done';exit;

        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily($date);
        echo 'test it now';exit;
        require_once("libraries/custodians/cCustodian.php");
        require_once('modules/ModSecurities/actions/ConvertCustodian.php');
        include_once("include/utils/omniscientCustom.php");

        global $adb, $dbconfig;

        global $dbconfig;
        $date = date("Y-m-d");

        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily($date);

        /*Positions asset allocation widget calculations*/
        PortfolioInformation_TotalBalances_Model::ClosePositionsBasedOnTheirPortfolio();
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();

        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        /*Fill in the consolidated balances table*/
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        $start = date('Y-m-d', strtotime('-7 days'));
        $finish = date('Y-m-d');
        $questions = generateQuestionMarks($accounts);
        $query = "CALL custodian_omniscient.CONSOLIDATE_BALANCES_DEFINED(\"{$questions}\", ?, ?, ?)";//Write to consolidated balances
//Write to the users table from consolidated balances for grand total of all accounts
        $adb->pquery($query, array($accounts, $dbconfig['db_name'], $start, $finish), true);

        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();

        echo 'done';exit;
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        $start = date('Y-m-d', strtotime('-7 days'));
        $start = "2019-01-01";
        $finish = date('Y-m-d');
        $questions = generateQuestionMarks($accounts);
        $query = "CALL custodian_omniscient.CONSOLIDATE_BALANCES_DEFINED(\"{$questions}\", ?, ?, ?)";//Write to consolidated balances

        //Write to the users table from consolidated balances for grand total of all accounts
        $adb->pquery($query, array($accounts, $dbconfig['db_name'], $start, $finish), true);
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();

        echo 'done';exit;
        echo "Script start: " . date("Y-m-d H:i:s") . '<br />';
        PortfolioInformation_TotalBalances_Model::ClosePositionsBasedOnTheirPortfolio();
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();
        echo 'now look';exit;


        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
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
        echo 'done';exit;








        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAllForUser(1);//Update user balances history
        exit;
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        $valid_accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCodeOpenAndClosed($rep_codes);
        $invalid_accounts = PortfolioInformation_Module_Model::GetAccountNumbersNotBelongingToRepcodes($rep_codes);

        PortfolioInformation_Module_Model::RemoveConsolidatedBalancesBelongingToAccounts($invalid_accounts);
        PortfolioInformation_Module_Model::RemoveIntervalsBelongingToAccounts($invalid_accounts);
        PortfolioInformation_Module_Model::RemovePortfoliosBelongingToAccounts($invalid_accounts);


        $all_position_accounts = PositionInformation_Module_Model::GetDistinctAccountNumbers();
        $merged = array_merge($valid_accounts, $all_position_accounts);
        $delete_position_accounts = array_keys(array_intersect(array_count_values($merged),[1]));
        PositionInformation_Module_Model::RemovePositionsBelongingToAccounts($delete_position_accounts);
        echo 'check positions now';exit;

        $all_transaction_accounts = Transactions_Module_Model::GetDistinctAccountNumbers();
        $merged = array_merge($valid_accounts, $all_transaction_accounts);
        $delete_transaction_accounts = array_keys(array_intersect(array_count_values($merged),[1]));
        Transactions_Module_Model::RemoveTransactionsBelongingToAccounts($delete_transaction_accounts);
        echo 'now check';exit;

        ini_set('memory_limit', -1);
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAllForUser(1);//Update user balances history
        echo "Script finished: " . date("Y-m-d H:i:s") . '<br />';
        exit;
        $omniSecurities = new cOmniscientSecurities("Omniscient", "live_omniscient", "securities", "vtiger_modsecurities", array(), array(), array());
        $intersect = $omniSecurities->GetTableIntersection("live_omniscient", "vtiger_modsecurities",
            "360vew_synctest", "vtiger_modsecurities", "m.");

        $intersect = array_merge($intersect, $omniSecurities->GetTableIntersection("live_omniscient", "vtiger_modsecuritiescf",
            "360vew_synctest", "vtiger_modsecuritiescf", "mcf."));
        $omniSecurities->UpdateSecuritiesDirectJoin($intersect);

#        $omniSecurities->GetAllSecuritiesByAssetClass(array("Stock", "Stocks", "Cash"));
#        $missing = $omniSecurities->GetMissingCRMSecurities();
#        $omniSecurities->CreateNewSecuritiesFromSecurityData();
        echo 'dun';
        exit;








        /****FIDELITY**********************************/
        /***STEP 1 - CREATE AND UPDATE PORTFOLIOS WORKING -- REQUIRES advisor_control_number or fails because smownerid can't be null***/
        $rep_codes = PortfolioInformation_Module_Model::GetRepCodeListFromUsersTable();
        //Pull portfolio and balance information for the specified rep codes
        $fidelity = new cFidelityPortfolios("FIDELITY", "custodian_omniscient", "portfolios",
            "custodian_portfolios_fidelity", "custodian_balances_fidelity", $rep_codes);
        $data = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
        $missing = $fidelity->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
        $fidelity->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
        $existing = $fidelity->GetExistingCRMAccounts();//Get accounts already in the CRM
        $fidelity->UpdatePortfoliosFromPortfolioData($existing);
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
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        if(!empty($missing_positions))
            $positions->CreateNewPositionsFromPositionData($missing_positions);

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
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        if(!empty($missing_securities))
            $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);//Update the defined symbols in the CRM (only has access to the ones passed in the constructor)
        $positions->ManualSetupPositionComparisons();//Needed because there may be new positions
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        /*********END OF STEP 2********/

        $fields = array("f.transaction_id", "f.account_number", "f.symbol", "f.cusip", "m.operation", "amount", "production_number", "omniscient_negative_category", "omniscient_category", "buy_sell_indicator",
            "omniscient_negative_activity", "omniscient_activity", "m.description AS description", "commission", "key_code_description", "service_charge_misc_fee",
            "option_symbol", "account_type_description", "f.comment", "comment2", "div_payable_date", "div_record_date", "fund_load_override",
            "fund_load_percent", "interest_amount", "postage_fee", "reg_rep1", "reg_rep2", "service_fee", "short_term_redemption_fee",
            "state_tax_amount", "transaction_code_description", "transaction_key_mnemonic", "f.price AS price", "security_price_adjustment", "quantity");

        $fidelity = new cFidelityTransactions("Fidelity", "custodian_omniscient", "transactions", "custodian_portfolios_fidelity",
            "custodian_transactions_fidelity", $rep_codes, $fields);
        //        $fidelity->SetColumns(array("transaction_id"));
        #        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
        $fidelity->GetTransactionsDataBetweenDates(date("Y-m-d", strtotime("-1 months")), date("Y-m-d"));
        $missing = $fidelity->GetMissingCRMTransactions();
        $fidelity->CreateNewTransactionsFromTransactionData($missing);

        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");

        /****FIDELITY**********************************/


        exit;























        $rep_codes = array("08901624");



        $transactions = new cSchwabTransactions("Schwab", "custodian_omniscient", "transactions",
            "custodian_portfolios_schwab", "custodian_transactions_schwab", $rep_codes);
//        $fidelity->SetColumns(array("transaction_id"));
#        $data = $td->GetTransactionsDataForDate('2020-04-01');
        echo 'Memory Before: ' . memory_get_usage() . '<br />';
        $data = $transactions->GetTransactionsDataBetweenDates('2020-01-01', '2020-05-01');
        $missing = $transactions->GetMissingCRMTransactions();
        $transactions->CreateNewTransactionsFromTransactionData($missing);
#        echo count($data) . '<br />';
#        print_r($data);
        echo 'Memory After: ' . memory_get_usage() . '<br />';
        echo date("Y-m-d H:i:s");
        exit;






        /******SCHWAB POSITIONS******
        $positions = new cSchwabPositions("SCHWAB", "custodian_omniscient", "positions",
        "custodian_portfolios_schwab", "custodian_positions_schwab",
        $rep_codes, array());

        #        $positions->SetAccountNumbers(array("678105996"));
        $symbols = $positions->GetAllOldAndNewPositionSymbols($positions->GetAccountNumbers());//Get only symbols that belong to the account numbers we care about
        /*        $fields = array( "f.header", "f.custodian_id", "f.master_account_number", "f.master_account_name", "f.business_date", "f.prod_code", "f.prod_catg_code", "f.tax_code", "f.ly", "TRIM(f.symbol) AS symbol", "f.industry_ticker_symbol", "f.cusip", "f.sec_nbr", "f.reorg_sec_nbr", "f.item_issue_id", "f.rulst_sufid", "f.isin", "f.sedol", "f.options_display_symbol", "f.description1", "f.description2", "f.description3", "f.scrty_des", "f.underlying_ticker_symbol", "f.underlying_industry_ticker_symbol", "f.underlying_cusip", "f.underly_schwab", "f.underlying_itm_iss_id", "f.unrul_sufid", "f.underlying_isin", "f.underly_sedol", "f.mnymk_code", "f.last_update", "f.s_f", "f.closing_price", "f.secprice_lstupd", "f.security_valuation_unit", "f.optnrt_symbol", "f.opt_expr_date", "f.c_p", "f.strike_price", "f.interest_rate", "f.maturity_date", "f.tips_factor", "f.asset_backed_factor", "f.face_value_amt", "f.st_cd", "f.vers_mrkr_1", "f.p_i", "f.o_i", "f.vers_mrkr_2", "f.closing_price_unfactored", "f.factor", "f.factor_date", "f.product_code", "f.product_code_category", "f.legacy_security_type", "f.ticker_symbol", "f.schwab_security_number", "f.re_org_schwab_internal_security_number", "f.rule_set_suffix", "f.security_description_line1", "f.security_description_line2", "f.security_description_line3", "f.security_description_line4", "f.underlying_schwab_security_number", "f.underlying_item_issue_id", "f.underlying_rule_set_suffix_id", "f.underlying_sedol", "f.money_market_code", "f.last_update_date", "f.sweep_fund_indicator", "f.security_price_update_date", "f.option_root_symbol", "f.option_expiration_date", "f.option_call_or_put_code", "f.strike_price_amount", "f.face_value_amount", "f.issuer_state", "f.version_marker_number", "f.schwab_proprietary_indicator", "f.schwab_one_source_indicator", "f.version_marker2", "f.file_date", "f.filename", "f.insert_date",
        "map.multiplier", "map.omni_base_asset_class", "pr.price", "m.us_stock", "m.intl_stock", "m.us_bond", "m.intl_bond",
        "m.preferred_net", "m.convertible_net", "m.cash_net", "m.other_net", "m.unclassified_net",
        "m.security_price_adjustment", "map.security_type");

        $securities = new cSchwabSecurities("SCHWAB", "custodian_omniscient", "securities",
        "custodian_securities_schwab", $symbols, array(), $fields);
        echo "3";
        $missing_securities = $securities->GetMissingCRMSecurities();//Get a list of securities the CRM doesn't currently have
        echo "4";
        if(!empty($missing_securities))
        $securities->CreateNewSecuritiesFromSecurityData($missing_securities);//Create new securities from the missing list
        echo "5";
        $securities->UpdateSecuritiesFromSecuritiesData($symbols);
         */
        /*        $missing_positions = $positions->GetMissingCRMPositions();

                if(!empty($missing_positions))
                    $positions->CreateNewPositionsFromPositionData($missing_positions);

                $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        ***********END SCHWAB POSITIONS*/

        echo "6";
#        $positions->ManualSetupPositionComparisons();
        echo "7";
#        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
        echo 'done';exit;
        #        $symbols = array("DBD");

#        $positions->GetPositionsData();

        echo "Step finished: " . date("Y-m-d H:i:s") . '<br />';
        exit;

        $rep_codes = array("08901624");
        /***STEP 1 - CREATE AND UPDATE PORTFOLIOS WORKING -- REQUIRES advisor_control_number or fails because smownerid can't be null***/
        //Pull portfolio and balance information for the specified rep codes
        $td = new cSchwabPortfolios("SCHWAB", "custodian_omniscient", "portfolios",
            "custodian_portfolios_schwab", "custodian_balances_schwab ", $rep_codes);
#        $data = $td->GetExistingCRMAccounts();//Get accounts already in the CRM
        $missing = $td->GetMissingCRMAccounts();//Compare CRM accounts to Custodian accounts and return what the CRM doesn't have
        $td->CreateNewPortfoliosFromPortfolioData($missing);//Create the accounts that are missing into the CRM
        $existing = $td->GetExistingCRMAccounts();//Get existing CRM accounts
        $td->UpdatePortfoliosFromPortfolioData($existing);//Update the existing accounts with the latest data from the custodian
        echo 'updated';
        exit;
#        $td->UpdatePortfoliosFromPortfolioData($existing);//Update the existing accounts with the latest data from the custodian
        /*********END OF STEP 1********/







        echo "Step 1 finished: " . date("Y-m-d H:i:s") . '<br />';















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
        $positions->ManualSetupPositionComparisons();//Needed because there may be new positions
        echo "7";
        $positions->UpdatePositionsFromPositionsData($positions->GetCustodianPositions());//Update the positions with the latest data
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