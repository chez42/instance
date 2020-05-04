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

        $td = new cTDPortfolios();
        $td->SetRepCodes(array("A7KK", "AMSZ", "AKXQ"));
        $td->CalculatePortfolioPersonalData(array(), "custodian_portfolios_td");
        $td->CalculatePortfolioBalanceData(array());

        $data = $td->GetPortfolioData();

        echo 'here';exit;
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