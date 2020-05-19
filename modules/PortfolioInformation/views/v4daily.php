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
        echo "Script start: " . date("Y-m-d H:i:s") . '<br />';

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
        $missing_symbols = $td->GetMissingCRMSecurities();

#        $crm_symbols = $td->GetAllCRMSecurities();//Get all securities that are in the CRM
#        $missing_symbols = array_diff_key($symbols, $crm_symbols);
#        $td->SetSecurities($missing_symbols);
        $td->CreateNewSecuritiesFromSecurityData($missing_symbols);
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