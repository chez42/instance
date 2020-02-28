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
include_once("modules/Trading/models/Ameritrade.php");


/**
 * CREATE TABLE `parsing_directory_structure` (
 *     `id` int(11) NOT NULL AUTO_INCREMENT,
 *     `path` varchar(255) NOT NULL,
 *     `size` int(11) NOT NULL,
 *     `fileatime` int(11) NOT NULL,
 *     `filemtime` int(11) NOT NULL,
 *     `filectime` int(11) NOT NULL,
 *     `realtime` int(11) NOT NULL,
 *     `md5` varchar(255) NOT NULL,
 *     `type` varchar(64) NOT NULL,
 *     PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
class Scan
{
    public function scanner($db, $dir, $dbname)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (!in_array($file, ['.', '..'])) {
                $name = $dir . '' . $file;
                $type = filetype($name);
                $size = (is_dir($name)) ? -1 : filesize($name);
                $md5 = (is_dir($name)) ? -1 : md5(file_get_contents($name));
                $atime = date ("Y-m-d H:i:s", filemtime($name));
                $mtime = date ("Y-m-d H:i:s", filemtime($name));
                $ctime = date ("Y-m-d H:i:s", filemtime($name));
                $parts = pathinfo($name);
                $directory = $parts['dirname'];
                $base = $parts['basename'];
                $extension = $parts['extension'];
                $filename = $parts['filename'];

                $query = "INSERT INTO {$dbname} (`path`, `size`, `fileatime`, `filemtime`, `filectime`, `realtime`, `md5`, `type`, `directory`, `basename`, `extension`, `filename`)
                          VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE filename = values(filename)";
                $params = array($name, $size, $atime, $mtime, $ctime, $md5, $type, $directory, $base, $extension, $filename);
                $db->pquery($query, $params, true);
                if ($type == 'dir') {
                    $this->scanner($db, $name . "/", $dbname);
                }
            }
        }
    }
}

/**
 * EXAMPLE
 * $scan = new Scan();
 * $scan -> scanner($db, PATH);
 */
class PortfolioInformation_v4daily_View extends Vtiger_BasicAjax_View{

    function process(Vtiger_Request $request)
    {
        global $adb;
        $scan = new Scan();
        $scan -> scanner($adb, "/mnt/lanserver2n/", "custodian_omniscient.parsing_directory_structure");

echo 'all done';exit;
        $sdate = "2019-12-30";
        $edate = "2019-12-31";
#        PortfolioInformation_Module_Model::TDBalanceCalculations("2019-12-31", "2020-02-14");
//        PortfolioInformation_Module_Model::TDBalanceCalculationsIndividual("942826345", $sdate, $edate);
echo 'check balances now';exit;
        $sdate = '2017-01-01';
        $edate = '2020-02-10';
        $token_date = $sdate;
#        $indexes = ModSecurities_Module_Model::GetAllIndexes();
        while(strtotime($token_date) <= strtotime($edate)){
            $tmp_end = date("Y-m-t", strtotime($token_date));
            if($token_date > $edate)
                return;
            if($tmp_end > $edate)
                $tmp_end = $edate;

 #           foreach($indexes AS $k => $v){
                ModSecurities_ConvertCustodian_Model::UpdateIndexEOD('MSCIEAFE', $token_date, $tmp_end);
#            }
            $token_date = date("Y-m-01", (strtotime('+1 month', strtotime($token_date) ) ));
        }
echo 'should be done EAFE';
global $adb;
        $ids = GetAllActiveUserIDs();
        $date = '2019-02-05';
#        if($date == null)
#            $date = date('Y-m-d');
#        set_error_handler("exception_error_handler");
 #       foreach($ids AS $k => $v){
            try {
                $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForSpecificUser(1, false);
                $questions = generateQuestionMarks($account_numbers);
                $query = "INSERT INTO vtiger_asset_class_history_daily_users
                          SELECT 1, SUM(value) AS value, ach.base_asset_class, as_of_date
                          FROM vtiger_asset_class_history ach
                          WHERE base_asset_class IS NOT NULL AND base_asset_class != ''
                          AND account_number IN ({$questions}) AND as_of_date = ? AND value != 0
                          GROUP BY base_asset_class
                          ON DUPLICATE KEY UPDATE value=VALUES(value)";
                $adb->pquery($query, array($account_numbers, $date));
            }catch(Exception $e){
                $note = "Trying to run WriteAndUpdateAssetAllocationUserDaily for user {$v}  Likely caused by the users privilege file not existing";
                $query = "INSERT INTO vtiger_exceptions(message, date_time, code_notes) VALUES(?, NOW(), ?)";
                $adb->pquery($query, array($e->getMessage(), $note));
            }
#        }
        echo 'done';
        exit;

        $sdate = "2019-01-01";
        $edate = "2019-02-01";
        PortfolioInformation_Module_Model::TDBalanceCalculations($sdate, $edate);
        echo 'done';exit;

        ModSecurities_ConvertCustodian_Model::UpdateAllIndexesEOD('2018-11-01', '2019-01-30');
        echo 'check now';exit;
        $advisors = PortfolioInformation_Stratifi_Model::GetStratifiUserList();//Get the list of users and their info from stratifi
        foreach($advisors->results AS $k => $v) {//Update omniscient with the proper company/user ID's
            print_r($v);
            echo '<br />';
        }
#            PortfolioInformation_Stratifi_Model::UpdateOmniscientUserWithStratifiIDsUsingUserEmail($v->user->email, $v->id, $v->company);
        echo 'all done';exit;

/*        global $adb;
        Transactions_Module_Model::MarkDupes();
        echo 'done again';exit;
*/
        PortfolioInformation_ManualInteractions_Model::UpdateAllContactTotalValue();
        PortfolioInformation_ManualInteractions_Model::UpdateAllHouseholdTotalValue();
echo 'check it now';exit;

$account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForSpecificUser(15368, false);
        foreach($account_numbers AS $k => $v){
            echo "'" . $v . "',<br />";
        }
        exit;
/*#        $query = "CALL CALCULATE_DAILY_INTERVALS_LOOP(?, ?, ?, ?, ?)";
#            CALL CALCULATE_MONTHLY_INTERVALS_LOOP("34300882", "1900-01-01", "2017-10-12", "schwab", "live_omniscient");
#        $adb->pquery($query, array('942684773', '2019-11-29', '2019-11-25', 'TD', 'live_omniscient'));
#echo 'check now';exit;
        $sdate = "2019-11-20";
        $edate = "2019-11-25";
#        $sdate = "2019-10-30";
#        $edate = "2019-11-01";
        PortfolioInformation_Module_Model::TDBalanceCalculations($sdate, $edate);
echo 'all finished';
        echo 'doing nothing';exit;
        require_once('modules/ModSecurities/actions/ConvertCustodian.php');
global $adb;
        $start = date("Y-m-d", strtotime("today -8 Month"));//Go back a month
        $end = date("Y-m-d");//Today's date for the index

        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        $note = "Indexing start";
        $adb->pquery($query, array($note));

        ModSecurities_ConvertCustodian_Model::UpdateAllIndexesEOD($start, $end);
        ModSecurities_Module_Model::UpdateIndexPricesWithLatest();

        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        $note = "Indexing finished";

        echo 'finished';exit;
        $adb->pquery($query, array($note));
        $start = date('Y-m-d', strtotime('-5 days'));
        $end = date('Y-m-d', strtotime('-1 day'));
        $account_numbers = PortfolioInformation_Module_Model::GetAccountsThatDontHaveIntervalForDate($end);
        if(is_array($account_numbers))
            PortfolioInformation_Module_Model::CalculateDailyIntervalsForAccounts($account_numbers, $start, $end);
echo 'check now again';exit;
        include_once("libraries/javaBridge/JavaCloudToCRM.php");

        global $adb;
        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";

        $note = "Updating Weight";
        $adb->pquery($query, array($note));

        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->CalculateWeight("live_omniscient");
echo 'check now';exit;*/
        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1',
                                 'NSGV', 'NSGV1');//SD2 is patrick berry, no longer active
#        $control_numbers = array('AMS0', 'amsz', 'amsy');
        $strat_hh = new StratHouseholds();
        $strat_contact = new StratContacts();
        $sAdvisors = new StratAdvisors();

        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForRepCode($control_numbers);
        echo 'and werd';exit;
        $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();

        echo 'done-o';exit;

/*
        $advisors = PortfolioInformation_Stratifi_Model::GetStratifiUserList();//Get the list of users and their info from stratifi
        foreach($advisors->results AS $k => $v)//Update omniscient with the proper company/user ID's
            PortfolioInformation_Stratifi_Model::UpdateOmniscientUserWithStratifiIDsUsingUserEmail($v->user->email, $v->id, $v->company);

#        $accounts = PortfolioInformation_Stratifi_Model::GetAccountsList();
#        print_r($accounts);exit;
        */
#    $strat_hh->GetStratifiAccountID();
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCode($control_numbers);
        $sAdvisors->AutoCreateCompanies();
        $sAdvisors->AutoCreateAdvisors();

        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForRepCodes(($control_numbers));
        PortfolioInformation_Stratifi_Model::CreateStratifiContactsForAllAccounts();
        PortfolioInformation_Stratifi_Model::CreateStratifiHouseholdsForAllAccounts();
        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
#PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);###THIS IS NOW DONE IN THE FUNCTION GetAllContactsAndUpdateAdvisorOwnership
        $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();
        $strat_contact->GetAllContactsAndUpdateAdvisorOwnership();
        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();

        echo 'Stratifi Done';exit;
        $date = date("Y-m-d");
        PortfolioInformation_GlobalSummary_Model::CalculateAllAccountAssetAllocationValues();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateAssetAllocationUserDaily();
echo 'bla';exit;
        PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios();
        PortfolioInformation_ConvertCustodian_Model::LinkHouseholdsToPortfolios();
        PortfolioInformation_ManualInteractions_Model::UpdateAllHouseholdTotalValue();
        echo 'check now';exit;
        $sdate = "2019-01-01";
        $edate = "2019-02-01";
        PortfolioInformation_Module_Model::TDBalanceCalculations($sdate, $edate);
        echo 'done';exit;
#        $data = json_decode(ModSecurities_ConvertCustodian_Model::GetIndexHistory("DJT", '2019-09-01', '2019-09-25'));
#        print_r($data);exit;
        $start = date("Y-m-d", strtotime("today -1 Month"));//Go back a month
        $end = date("Y-m-d");//Today's date for the index
        ModSecurities_ConvertCustodian_Model::UpdateAllIndexesEOD($start, $end);
        echo 'bla';
        exit;
        ModSecurities_Module_Model::UpdateIndexPricesWithLatest();
echo 'Price Check';exit;
        $indexes = ModSecurities_Module_Model::GetAllIndexData();
        foreach($indexes AS $k => $v){
            print_r($v);
            echo "<br />";
            $recordModel = ModSecurities_Record_Model::getCleanInstance("ModSecurities");
            $data = $recordModel->getData();
            $data['security_symbol'] = $v['security_symbol'];
            $data['security_name'] = $v['description'];
            $data['cusip'] = $v['security_symbol'];
            $data['aclass'] = "Index";
            $data['securitytype'] = "Index";
            $data['security_price_adjustment'] = 1;

            $recordModel->setData($data);
            $recordModel->set('mode', 'create');
            $recordModel->save();

//            $tmp = ModSecurities_Record_Model::getCleanInstance();
//            echo $v['security_symbol'];
        }
        echo 'done';exit;

#        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
#        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'done';exit;
        exit;
        global $adb;
#        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersForSpecificUser(15368, false);
#        foreach($account_numbers AS $k => $v){
            $query = "CALL custodian_omniscient.CREATE_POSITIONS_MANUAL('903933850')";
            $adb->pquery($query, array());
            $query = "CALL custodian_omniscient.UPDATE_POSITIONS_MANUAL('903933850')";
            $adb->pquery($query, array());
            $query = "CALL custodian_omniscient.UPDATE_BALANCES_MANUAL('903933850')";
            $adb->pquery($query, array());
#        }
        echo 'all finished';
        exit;

        ModSecurities_ConvertCustodian_Model::UpdateAllIndexesEOD("2015-01-01", "2019-09-18");
        echo 'done';exit;
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'done';
        exit;
#        print_r($account_numbers);exit;
        $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
        $custodian = "td";//Set the custodian
        $newonly = 1;

        global $adb;
        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";

        $note = "Balances Section Start {$custodian}";
        $adb->pquery($query, array($note));
        $note = "Writing {$custodian} Balances To Cloud";
        $adb->pquery($query, array($note));
        $x = 1;
        $increment = 2000;
        $trade = new Trading_Ameritrade_Model();
        $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, null, 1, 1);
        $max = $tmp['model']['getBalancesJson']['responseInfo']['totalSize'];
        while($x <= $max){
            PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud($custodian, null, null, $x, $x+$increment);
            $x+=$increment;
        }
        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
        $note = "Balances Section Finished {$custodian}";
        $adb->pquery($query, array($note));

        PortfolioInformation_Module_Model::UpdatePortfolioTDInfo();
//        PortfolioInformation_Module_Model::UpdatePortfolioTypeTDOnly();
        echo 'ALL DONE';
        exit;

        PortfolioInformation_Module_Model::UpdatePortfolioTypeTDOnly();
        echo 'yay';exit;

        PortfolioInformation_Module_Model::CreateDailyIntervalsForAccounts(array("941471625"), "2019-01-02");
        echo 'done';exit;

        /*        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
                PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'done';exit;*/
        require_once('modules/ModSecurities/actions/ConvertCustodian.php');
        require_once('modules/Transactions/actions/ConvertCustodian.php');
        require_once('modules/PositionInformation/actions/ConvertCustodian.php');
        require_once('modules/PortfolioInformation/actions/ConvertCustodian.php');

        $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
        $custodian = "td";//Set the custodian
        $newonly = 1;

        global $adb;

        $x = 1;
        $increment = 2000;
        $trade = new Trading_Ameritrade_Model();
        $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, null, 1, 1);
        $max = $tmp['model']['getBalancesJson']['responseInfo']['totalSize'];

        echo "MAX IS: " . $max . '<br /><br />';
print_r($tmp);exit;
        do{
            echo "X is " . $x;
            PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud($custodian, null, null, $x, $x+$increment);
            $x+=$increment;
            echo " and now is " . $x . '<br />';
        }while($x <= $max);
#        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
####echo 'ALL DONE';exit;
####        PortfolioInformation_Module_Model::UpdatePortfolioTDInfo();
####        PortfolioInformation_Module_Model::UpdatePortfolioTypeTDOnly();*/
echo 'fini';exit;

        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'need to do something here';exit;
        $strat = new StratifiAPI();
        $sContacts = new StratContacts();
        $sAdvisors = new StratAdvisors();
        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1');//SD2 is patrick berry, no longer active
        $strat_hh = new StratHouseholds();
        $strat_contact = new StratContacts();

/*        $sAdvisors->AutoCreateCompanies();
        $sAdvisors->AutoCreateAdvisors();
#        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);
        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
        PortfolioInformation_Stratifi_Model::CreateStratifiContactsForAllAccounts();
        PortfolioInformation_Stratifi_Model::CreateStratifiHouseholdsForAllAccounts();
        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
#        PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);
        $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();*/
        $strat_contact->GetAllContactsAndUpdateAdvisorOwnership();
#        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();


        echo 'entire phase complete';exit;
        global $adb;
        $strat_hh = new StratHouseholds();
        $strat_contact = new StratContacts();
        $strat_hh->GetAllHouseholdsAndUpdateAdvisorOwnership();
        $strat_contact->GetAllContactsAndUpdateAdvisorOwnership();

        echo 'ALL FINISHED';exit;

        $query = "SELECT filename, skeleton_table
	              FROM custodian_omniscient.files_to_parse
	              WHERE finished = 0 AND skeleton_table IS NOT NULL";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetch_array($result)){
                $query = "DROP TABLE IF EXISTS tmp";

                echo $v['filename'] . '<br />';
                $query = "CREATE TEMPORARY TABLE tmp LIKE {$v['skeleton_table']}";
                $adb->pquery($query, array());

                $query = "LOAD DATA LOCAL INFILE ? 
			              INTO TABLE tmp
			              FIELDS TERMINATED BY '|'
			              LINES TERMINATED BY '\\r\\n'";
                $adb->pquery($query, array($v['filename']));
                $query = "SELECT * FROM tmp";
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) > 0){
                    while($a = $adb->fetch_array($result)){
                        print_r($a); echo "<br />";
                    }
                }
                exit;
            }
        }
exit;
/*        PortfolioInformation_TotalBalances_Model::ConsolidateBalances();
        PortfolioInformation_TotalBalances_Model::WriteAndUpdateLast7DaysForAllUsers();
        echo 'done again';exit;

        ModSecurities_ConvertCustodian_Model::UpdateIndexEOD("990100", '2001-01-01', '2019-05-06');
        echo 'hey';exit;
*/

        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1');//SD2 is patrick berry, no longer active
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);

        foreach($account_numbers AS $k => $v) {
            PortfolioInformation_Stratifi_Model::UpdateHouseholdFromAccount($v);
            echo "THE PAIN FOR {$v}";
        }
exit;
        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
        PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);
        PortfolioInformation_Stratifi_Model::SendAllPositionsToStratifi();

        echo 'ALL FINISHED';exit;
        /**NOTE TO SELF... First auto create companies... Then auto create advisors**/
        PortfolioInformation_Module_Model::UpdatePortfolioDataInCloudForTDByRepCode(array("2XL", "H0F", "AWDJ"));
        /*
        global $adb;
        $trade = new Trading_Ameritrade_Model();
        $result = $trade->GetAllAccountsForRepCode("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", array("2XL", "H0F", "AWDJ"));
        #$result = $trade->GetAllAccountsForRepCode("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", array("KXW"));
        $query = "INSERT INTO custodian_omniscient.custodian_portfolios_td (account_number, first_name, last_name, street, address2, city, 
                              state, zip, account_type, phone_number, advisor_id, rep_code)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), street = VALUES(street), address2 = VALUES(address2),
                                          city = VALUES(city), state = VALUES(state), zip = VALUES(zip), account_type = VALUES(account_type), 
                                          phone_number = VALUES(phone_number), advisor_id = VALUES(advisor_id), rep_code = VALUES(rep_code)";
        foreach($result['model']['getAccountsJson']['account'] AS $k => $v){
#            echo $query;
#            print_r($v);
            $adb->pquery($query, array($v['accountNumber'], $v['firstName'], $v['lastName'], $v['address1'], $v['address2'], $v['city'], $v['state'],
                                       $v['zip'], $v['accountType'], $v['secondaryPhone'], $v['repCode'], $v['repCode']));
            echo "<br /><br />";

        }
#        $result = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", array("726010395"));
#        print_r($result);*/
        echo 'done';
        exit;
    }

}

?>