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
include_once("libraries/Reporting/ReportCommonFunctions.php");

class PortfolioInformation_d3_View extends Vtiger_BasicAjax_View{

    function process(Vtiger_Request $request)
    {
 #       $control_numbers = array('DW1', 'HT1', 'LR1', 'SD2', 'SV1', 'SV2', 'SV3', 'SV4', 'TV1', 'AT1');
        $control_numbers = array('SV2', 'LR1', 'AW1', 'SV3', 'HT1', 'SV1', 'AT1', 'TV1');//SD2 is patrick berry, no longer active
        $control_numbers = array('HT1');
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);

        PortfolioInformation_Stratifi_Model::CreateAccountsInStratifiForControlNumbers(($control_numbers));
###        PortfolioInformation_Stratifi_Model::UpdateStratifiAccountLinkingForControlNumbers($control_numbers);
###        PortfolioInformation_Stratifi_Model::UpdateStratifiInvestorLinkingForControlNumbers($control_numbers);
        echo "ALL DONE";
        exit;
//        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);



        /**NOTE TO SELF... First auto create companies... Then auto create advisors**/
    global $adb;

    $current_date = date("Y-m-d");
    $t1 = GetDateMinusMonths(TRAILING_1);
    $account_numbers = PortfolioInformation_Module_Model::GetAllOpenAccountNumbers();
    $q = "UPDATE vtiger_portfolioinformation p 
             JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
             SET previous_month_percentage = ? WHERE account_number = ?";
$count = 0;

    foreach ($account_numbers AS $k => $v) {
//        if($count < 100) {
//            PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts(array($v));
            echo 'Trying: ' . $v . '<br />';
            PortfolioInformation_Module_Model::UpdateAccountPreviousTWR($v);

/*            $query = "CALL MONTHLY_INTERVALS_CALCULATED(\"'{$v}'\")";
            $adb->pquery($query, array());

            $query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $adb->pquery($query, array($t1, $current_date));

            $query = "SELECT @twr AS twr";
            $result = $adb->pquery($query, array());
            if ($adb->num_rows($result) > 0) {
                $twr = $adb->query_result($result, 0, 'twr');
                echo $v . ' is now.... ' . $twr . '<br />';
                $adb->pquery($q, array($twr, $v));
            }*/
            $count++;
//        }
    }
echo 'all done';exit;

    $t3 = GetDateMinusMonths(TRAILING_3);
    $t6 = GetDateMinusMonths(TRAILING_6);

    echo $t1;exit;

        ModSecurities_ConvertCustodian_Model::UpdateSecurityFromEOD('SCHZ', "US");
#        echo 'done';exit;
        global $adb;
        $accounts = PortfolioInformation_Module_Model::GetAllOpenAccountNumbers();
        PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($accounts);

        echo "FINISHED!!!";exit;
        global $adb;
        $accounts = PortfolioInformation_Module_Model::GetAllOpenAccountNumbers();
        $start_date = GetDateMinusMonths(TRAILING_6);
        $end_date = date("Y-m-d");
        foreach($accounts AS $k => $v){
#            echo $v . '<br />';
            PortfolioInformation_Module_Model::CalculateMonthlyIntervalsForAccounts($v);
/*            $intervals = PortfolioInformation_Module_Model::GetIntervalsForAccounts($accounts);//Create combined accounts intervals

            $query = "CALL TWR_CALCULATED(?, ?, @twr)";
            $adb->pquery($query, array($start_date, $end_date));

            $query = "SELECT @twr AS twr";
            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                $twr =  $adb->query_result($result, 0, 'twr');
            }else{
                $twr = 0;
            }*/
        }
        exit;
        echo "TWR: " . $twr;
echo 'done';exit;
//        $t3_performance = new Performance_Model($accounts, GetDateMinusMonths(TRAILING_3), date("Y-m-d"));




        $strat = new StratifiAPI();
        $sContacts = new StratContacts();
	    $sAdvisors = new StratAdvisors();
        $sAdvisors->AutoCreateAdvisors();
echo 'done';exit;
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();
#        $account_numbers = array('83775712');
        /*        foreach($account_numbers AS $k => $v){
                    $data = PortfolioInformation_Module_Model::GetStratifiData($v);
                    $result = $strat->UpdatePositionsToStratifi($data);
                    print_r($result);
                    echo '<br /><br />';
                }
                echo "<strong>ALL DONE</strong>";exit;*/
        echo sizeof($account_numbers) . '<br /><br />';
        foreach($account_numbers AS $k => $v){
            $contact_entity = PortfolioInformation_Module_Model::GetContactEntityFromAccountNumber($v);
            if($contact_entity) {
                if ($contact_entity->get('stratid')) {
                    echo "STRATID: " . $contact_entity->get('stratid') . '<br />';
                } else {
                    echo "NO STRATID FOR CONTACT: " . $contact_entity->getId() . ".... Creating: <br />";
                    if($sContacts->CreateContact($contact_entity->getId()) != 0){
                        $result = $strat->UpdateStratifiAccountLinking($v);
                        echo $result . '<br />';
                    }
                }
            }
            PortfolioInformation_Module_Model::CreateStratifiPortfolioAccount("18720470");
##            $data = $strat->GetStratifiLinkingInformation($v);
##            print_r($data);
##            echo "<br />";
            /*            $contact_instance = PortfolioInformation_Module_Model::GetContactEntityFromAccountNumber($v);
                        if($contact_instance)
                            echo $contact_instance->get('id') . '<br />';
                        else{
                            $sContacts->C
                        }*/
        }
        echo 'contacts done';exit;
        /*

                foreach($account_numbers AS $k => $v){
                    $data = PortfolioInformation_Module_Model::GetStratifiData($v);
                    $result = $strat->UpdatePositionsToStratifi($data);
                    print_r($result);
                    echo '<br /><br />';
                }

                echo 'done';exit;

        #	    $result = $strat->getAllAccounts();
                echo sizeof($account_numbers) . '<br /><br />';
                foreach($account_numbers AS $k => $v){
                    $contact_entity = PortfolioInformation_Module_Model::GetContactEntityFromAccountNumber($v);
                    if($contact_entity) {
                        if ($contact_entity->get('stratid')) {
                            echo "STRATID: " . $contact_entity->get('stratid') . '<br />';
                        } else {
                            echo "NO STRATID FOR CONTACT: " . $contact_entity->getId() . ".... Creating: <br />";
                            if($sContacts->CreateContact($contact_entity->getId()) != 0){
                                $result = $strat->UpdateStratifiAccountLinking($v);
                                echo $result . '<br />';
                            }
                        }
                    }
        ##            $data = $strat->GetStratifiLinkingInformation($v);
        ##            print_r($data);
        ##            echo "<br />";
        /*            $contact_instance = PortfolioInformation_Module_Model::GetContactEntityFromAccountNumber($v);
                    if($contact_instance)
                        echo $contact_instance->get('id') . '<br />';
                    else{
                        $sContacts->C
                    }*/
#        }
#	    $result = $strat->UpdateStratifiAccountLinking('57068945');
#	    print_r($result);
#	    exit;



        /*
        $stratH = new StratHouseholds();

        $control_numbers = array('DW1', 'HT1', 'LR1', 'SD2', 'SV1', 'SV2', 'SV3', 'SV4', 'TV1');

        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers, "1");
        $household_id = GetHouseholdIDFromAccountNumber($account_numbers[0]);

        if($household_id != 0){
            $household_record = Accounts_Record_Model::getInstanceById($household_id);
            $portfolio_record_id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($account_numbers[0]);
            $portfolio_record = PortfolioInformation_Record_Model::getInstanceById($portfolio_record_id);
            $owner = getRecordOwnerId($portfolio_record->getId());
            $advisor_id = $owner['Users'];
            $omniID = $household_record->getId();

            $data = $household_record->getData();
            if(strlen($data['stratid']) == 0){
                $stratH->CreateHousehold($omniID, $advisor_id, $data['accountname']);
            }
        }

        echo "finished";exit;

#        PortfolioInformation_Module_Model::CreateStratifiPortfolioAccount("18720470");
#        $stratifi = new StratifiAPI();

###        PortfolioInformation_Module_Model::CreateStratifiPortfolioAccount('1855671');
        $data = PortfolioInformation_Module_Model::GetStratifiData('678323748');
        $result = $stratifi->SendPositionsToStratifi($data);
        echo $stratifi->getAllAccounts();
#        $stratifi->CreateNewStratifiAccount("TESTING 1234");
//        echo $stratifi->getAccountByID("14676");
        exit;
/*
        $data = PortfolioInformation_Module_Model::GetStratifiData('90027505');
        print_r($data);
        exit;
        global $adb;

$query = "CREATE TABLE `vtiger_base_asset_class` (
`base_asset_classid` int(19) NOT NULL auto_increment,
`base_asset_class` varchar(200) collate latin1_german2_ci NOT NULL,
`presence` int(1) NOT NULL default '1',
`picklist_valueid` int(19) NOT NULL default '0',
PRIMARY KEY (`base_asset_classid`),
UNIQUE KEY `base_asset_class_base_asset_class_idx` (`base_asset_class`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=18 ;";
$adb->pquery($query, array());

$query = "CREATE TABLE `vtiger_base_asset_class_seq` (
`id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;";
$adb->pquery($query, array());

        echo 'done';exit;


        $account_number = $request->get('account_number') . '%';
        $currentUser = Users_Record_Model::getCurrentUserModel();
#        $adb = PearDatabase::getInstance();

        $query = "SELECT account_number FROM vtiger_portfolioinformation
                  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portfolioinformation.portfolioinformationid ";

        if(!$currentUser->isAdminUser()){
            $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('PortfolioInformation');
        }

        $query .= " WHERE vtiger_crmentity.deleted=0 AND account_number LIKE (?) ";
        $result = $adb->pquery($query, array($account_number));

        if($adb->num_rows($result) > 0){
            $account_numbers = array();
            while($x = $adb->fetchByAssoc($result)){
                $account_numbers[] = $x['account_number'];
            }
            echo json_encode($account_numbers);
            return;
        }
        echo 0;exit;


        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $tmp->AutoSetActiveStatus();

        $date = date("Y-m-d H:m:s");
        $tmp->SetCustodianStatus(1, "Writing Files Fidelity for last 7 days " . $date);

        $date = date("Y-m-d H:m:s");
        $tmp->SetCustodianStatus(0, null, "Finished Writing Files " . $date);
echo "CHECK NOW";exit;

        PortfolioInformation_Module_Model::AssignPortfolioBasedOnRepCodes();
        echo "CHECK NOW";
        exit;
        $query = "DROP TABLE IF EXISTS SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE SecuritiesToCreate
                  SELECT account_number, security_symbol, base_asset_class, security_type, multiplier, last_price, description, 0 AS crmid
                  FROM vtiger_positioninformation p
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  WHERE p.security_symbol NOT IN (SELECT security_symbol FROM vtiger_modsecurities)
                  AND security_type IS NOT NULL
                  AND base_asset_class IS NOT NULL
                  AND multiplier IS NOT NULL
                  AND last_price IS NOT NULL
                  GROUP BY p.security_symbol";
        $adb->pquery($query, array());

        $query = "UPDATE SecuritiesToCreate SET crmid = IncreaseAndReturnCrmEntitySequence()";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime, label)
                  SELECT crmid, 1, 1, 1, 'ModSecurities', NOW(), NOW(), description FROM SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_modsecurities (modsecuritiesid, security_symbol, security_name, securitytype, security_price, last_update)
                  SELECT crmid, security_symbol, description, security_type, last_price, NOW() FROM SecuritiesToCreate";
        $adb->pquery($query, array());

        $query = "INSERT INTO vtiger_modsecuritiescf (modsecuritiesid, aclass, security_price_adjustment)
                  SELECT crmid, base_asset_class, multiplier FROM SecuritiesToCreate";
        $adb->pquery($query, array());

        echo "CHECK FOR SECURITIES";exit;

        Transactions_ConvertCustodian_Model::AssignPositionsBasedOnPortfolio();

        Transactions_ConvertCustodian_Model::UpdateRepCodes();

        echo "ALL DONE";exit;
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->WriteFiles("folio", "writefiles", "1", "0");

        echo $result;
exit;
/*
        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        $note = "Reading Folio Files Using Java";
        $adb->pquery($query, array($note));
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->WriteFiles("folio", "writefiles", "14", "0");
        $note = "Finished Folio Files Reading Using Java";
        $adb->pquery($query, array($note));

        $note = "Updating Folio Portfolios/Balances Using Java";
        $adb->pquery($query, array($note));
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->UpdatePortfolios("folio", "live_omniscient");
        $note = "Finished Folio Portfolios/Balances Using Java";
        $adb->pquery($query, array($note));

        $note = "Updating Folio Securities Using Java";
        $adb->pquery($query, array($note));
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->UpdateSecurities("folio", "live_omniscient");
        $note = "Finished Folio Securities Using Java";
        $adb->pquery($query, array($note));

        $note = "Updating Folio Positions Using Java";
        $adb->pquery($query, array($note));
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->UpdatePositions("folio", "live_omniscient");
        $note = "Finished Folio Positions Using Java";
        $adb->pquery($query, array($note));

        $note = "Updating Folio Transactions Using Java";
        $adb->pquery($query, array($note));
        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
        $result = $tmp->UpdateTransactions("folio", "live_omniscient");
        $note = "Finished Folio Positions Using Java";
        $adb->pquery($query, array($note));

        echo 'ALL DONE...';exit;


        ModSecurities_ConvertCustodian_Model::UpdateIndexOmniscient("EEM", '1900-01-01', '2018-02-15');exit;

        ModSecurities_Module_Model::UpdateAllMutualFundAssetClass();
        echo 'songs over';
        exit;

        $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";

        $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
        if($users) {
            foreach ($users AS $a => $b) {
                $td = new Trading_Ameritrade_Model($b['userid'], $b['password']);
#                $data = $td->GetPositions($url,  null, 'O');//'925027733', 'F');916030690   936009290
                $counter = 0;
                $data = $td->GetPositions($url,  '912963599', null);//'925027733', 'F');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    $counter++;
                    print_r($v); echo "<br /><br />";
#                    ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
                }
            }
        }
        echo "ALL DONE!";
        exit;


        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber(array("GH1","GH2","GH3"));
        foreach($account_numbers AS $k => $v){
            if(PortfolioInformation_Module_Model::HavePCTransactionsBeenTransferred($v) != 1){
                $acc[] = $v;
                $custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($v);
                PortfolioInformation_Module_Model::CreateTransactionsFromPCCloud($custodian, $v);
            }
            echo "RESET: <strong>" . $v . "</strong><br />";
        }

        echo "<strong>ALL DONE CHIEF</strong>";
        exit;
        /*
                $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
                $result = $tmp->WriteFiles("fidelity", "writefiles", "7", "0");
        echo "DONE...";exit;

        #PFD  -- Bonds, 100% allocation, preferred = 1
                $trade = new Trading_Ameritrade_Model();
                $accounts = PortfolioInformation_ConvertCustodian_Model::GetMissingTDAccountsFromBalances();
                $max = 9000;
                $interval = 3000;
                $x = 1;
                echo "MAX IS: " + $max  + "<br /><br />";
        #        while($x <= $max) {
                echo "SIZE: ";
                echo sizeof($accounts);
                echo "<br /><br />";
                $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", $accounts, 1, 300);
                foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
                    if (PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v['accountNumber']) == 0) {
                        $recordModel = PortfolioInformation_Record_Model::getCleanInstance("PortfolioInformation");
                        $data = $recordModel->getData();
                        $data['account_number'] = $v['accountNumber'];
                        $data['description'] = $v['accountDescription'];
                        $data['account_title1'] = $v['accountTitle'];
                        $data['account_type'] = $v['accountType'];
                        $data['production_number'] = $v['repCode'];
                        $data['first_name'] = $v['firstName'];
                        $data['last_name'] = $v['lastName'];
                        $data['address1'] = $v['address1'];
                        $data['address2'] = $v['address2'];
                        $data['city'] = $v['city'];
                        $data['state'] = $v['state'];
                        $data['zip'] = $v['zip'];
                        $data['origination'] = 'td';

                        $recordModel->setData($data);
                        $recordModel->set('mode', 'create');
                        $recordModel->save();
                        echo "<p>CREATED: {$data['account_number']}</p>";
                        #                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD('2017-03-13', $v['accountNumber']);
                    } else {
                        echo "<p>EXISTS!</p>";
                    }
                }

                echo "<p>ALL FINISHED...</p>";
                exit;

                $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
                $result = $tmp->WriteFiles("td", "writefiles", "365", "0");
        echo 'done writing TD files';exit;

        /*        $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
                $result = $tmp->WriteFiles("td", "writefiles", "365", "0");
                $note = "Finished TD Files Reading Using Java";*/
#        Transactions_ConvertCustodian_Model::ReassignTransactions();//Assign transactions that currently belong to admin to the owner of the portfolio they are associated with
        Transactions_ConvertCustodian_Model::UpdateRepCodes();
        echo 'assigning done';exit;

#        PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios();
#        ModSecurities_ConvertCustodian_Model::UpdateIndexEOD("GDAXI", '2017-01-01', '2017-11-30');
        global $adb;

        $query = "CALL EOD_SYMBOL_PULL();";
        $adb->pquery($query, array());

        $query = "SELECT security_symbol FROM EOD_SYMBOLS LIMIT 50";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)) {
                ModSecurities_ConvertCustodian_Model::UpdateSecurityFromEOD($v['security_symbol'], "US");
            }
        }

        echo 'And finished';exit;

        echo "INDEX PULLING";exit;
        $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";
        $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
        if($users)
            foreach($users AS $k => $v) {
                $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
#                $data = $td->GetPositions($url, '920035265', 'B');
                $data = $td->GetSecurity($url, 'VUSTX');
                print_r($data);
                exit;
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
                }
            }
#        PortfolioInformation_Module_Model::UpdatePortfolioTDInfo();
        exit;
#        PortfolioInformation_Module_Model::UpdatePortfolioTDInfo();
        exit;
        $recordModel = new ModSecurities_Record_Model();
        $symbols = ModSecurities_Module_Model::GetYahooFinanceNullSymbols();
        foreach($symbols AS $k => $v){
            $symbol_data = $recordModel->getYahooFinanceSymbolDetail($v);
            if(!empty($symbol_data)){
                $symbol_data['Symbol'] = $v;
                ModSecurities_Module_Model::FillYahooFinanceTableWithSymbolData($symbol_data);
                echo $v . " SUCCESS<br />";
                ob_flush();
                flush();
            }else{
                echo $v . " RETURNED AN EMPTY RESULT<br />";
                ob_flush();
                flush();
            }
            ob_flush();
            flush();
        }

        echo "<br /><br />DONE PULLING YAHOO<br /><br />";
        exit;
        $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";
        $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
        if($users)
            foreach($users AS $k => $v){
                $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
                $data = $td->GetPositions($url, null, 'B');
                foreach ($data->model->getPositionsJson->position AS $k => $v) {
                    ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
                }
            }

        foreach($users AS $a => $b){
            $td = new Trading_Ameritrade_Model($b['userid'], $b['password']);
            $data = $td->GetPositions($url, null, 'O');
            foreach ($data->model->getPositionsJson->position AS $k => $v) {
                ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
            }
        }

        foreach($users AS $k => $v){
            $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
            $data = $td->GetPositions($url, null, 'F');
            foreach ($data->model->getPositionsJson->position AS $k => $v) {
                ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
            }
        }

        foreach($users AS $k => $v){
            $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
            $data = $td->GetPositions($url, null, 'E');
            foreach ($data->model->getPositionsJson->position AS $k => $v) {
                ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
            }
        }

        foreach($users AS $k => $v){
            $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
            $data = $td->GetPositions($url, null, 'M');
            foreach ($data->model->getPositionsJson->position AS $k => $v) {
                ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
            }
        }

        echo "ALL DONE";exit;
        global $adb;
        require_once('modules/ModSecurities/actions/ConvertCustodian.php');

        $start = date("Y-m-d", strtotime("today -1 Month"));//Go back a month
        $end = date("Y-m-d");//Today's date for the index

        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        $note = "Indexing start";
        $adb->pquery($query, array($note));

        ModSecurities_ConvertCustodian_Model::UpdateIndexYahoo("S&P 500", $start, $end);
        ModSecurities_ConvertCustodian_Model::UpdateIndexYahoo("AGG", $start, $end);

        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        $note = "Indexing finished";
        $adb->pquery($query, array($note));
        exit;

        global $adb;

        $manual = new PortfolioInformation_ManualInteractions_Model();
        $manual->GetPCIntervals();
        echo "INTERVALS DONE...";exit;

        /*
        PortfolioInformation_ConvertCustodian_Model::LinkContactsToPortfolios();
        PortfolioInformation_ConvertCustodian_Model::LinkHouseholdsToPortfolios();
        PortfolioInformation_ConvertCustodian_Model::CreateContactFromPortfolio();
        echo "ALL DONE";exit;

        /*
        $transactions = new cTransactionsAccess(true);
        $port = new cPortfolioAccess();
        $pricing = new cPricingAccess();

        echo "About to start copying transactions " . date('m-d-Y H:i:s') . "<br />\r\n";
        ob_flush();
        flush();
        $rows = $transactions->CopyTransactionsFromPCToCRM(null, '2017-02-01');
        echo "Transactions complete, there were {$rows} rows " . date('m-d-Y H:i:s') . "<br /><br />\r\n";

        $port->CopyPortfoliosFromPCToCRM();

        echo "Portfolios done copying<br /><br />";
        $pricing_result = $pricing->UpdatePrices(null, '2017-02-01');
        echo $pricing_result;

        exit;

        */
        /*
                $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";
                $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
                if($users)
                    foreach($users AS $k => $v){
                        $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
                        $data = $td->GetPositions($url, null, 'O');
                        foreach ($data->model->getPositionsJson->position AS $k => $v) {
                            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
                        }
                    }
        */

        /*
                $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
                $x = 1;
                $increment = 2000;
                $trade = new Trading_Ameritrade_Model();
                $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, null, 1, 1);
                $max = $tmp['model']['getBalancesJson']['responseInfo']['totalSize'];
                echo "MAX IS: " + $max  + "<br /><br />";
                while($x <= $max){
                    PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud("td", null, null, $x, $x+$increment);
                    $x+=$increment;
                }
        //        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
                echo "CHECK FOR BALANCES NOW";

        /*
                $url = "https://veoapi.advisorservices.com/InstitutionalAPIv2/api";
                $users = Trading_Ameritrade_Model::GetAmeritradeUsersInformation();
                if($users)
                    foreach($users AS $k => $v){
                        $td = new Trading_Ameritrade_Model($v['userid'], $v['password']);
                        $data = $td->GetPositions($url, null, 'B');
                        foreach ($data->model->getPositionsJson->position AS $k => $v) {
                            ModSecurities_Module_Model::UpdateSecurityInformationTD($v);
                        }
                    }

        /*$date = '2017-03-29';
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesFidelity($date);
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesPershing($date);
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesSchwab($date);
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
                echo 'numbers now?';exit;*/
        /*
                $trade = new Trading_Ameritrade_Model();
                $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", "915941125", null, 1, 1);
                print_r($tmp);
                exit;
        #        $max = $tmp['model']['getBalancesJson']['responseInfo']['totalSize'];
        #        echo "MAX IS: " + $max  + "<br /><br />";
        #        while($x <= $max){
        //            PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud("td", null, null);
        */
        /*
                $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
                $x = 1;
                $increment = 2000;
                $trade = new Trading_Ameritrade_Model();
                $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, null, 1, 1);
                $max = $tmp['model']['getBalancesJson']['responseInfo']['totalSize'];
                echo "MAX IS: " + $max  + "<br /><br />";
                while($x <= $max){
                    PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud("td", null, null, $x, $x+$increment);
                    $x+=$increment;
                }
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
                echo "CHECK FOR BALANCES NOW";
        exit;
        /*
                foreach($tmp['model']['getBalancesJson']['balance'] AS $k => $v){
                    $balances[] = $v;
                }
        */

        /*
                PortfolioInformation_Module_Model::AssignPortfolioBasedOnRepCodes();
                echo "ASSIGNED!";exit;
                $tmp = new JavaCloudToCRM("omniscient", "syncuser", "Concert222", "192.168.100.224", "custodian_omniscient");
                $result = $tmp->WriteFiles("fidelity", "writefiles", "2", "0");
                echo $result;
        exit;
        /*
         *         $start = date("Y-m-d", strtotime("today -2 Month"));//Go back a month
                $end = date("Y-m-d");//Today's date for the index

                $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
                $note = "Indexing start";
                $adb->pquery($query, array($note));

                ModSecurities_ConvertCustodian_Model::UpdateIndexYahoo("S&P 500", $start, $end);
                ModSecurities_ConvertCustodian_Model::UpdateIndexYahoo("AGG", $start, $end);

                $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
                $note = "Indexing finished";
                $adb->pquery($query, array($note));
                echo 'ALL DONE';exit;
        */

        /*
                $trade = new Trading_Ameritrade_Model();
        //        $tmp = $trade->GetBalances("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", null, null);
                $accounts = PortfolioInformation_ConvertCustodian_Model::GetMissingFidelityAccountsFromBalances();
                $r = PortfolioInformation_ConvertCustodian_Model::CreateAndUpdatePortfoliosFromFidelity($accounts);
        */
#        PositionInformation_ConvertCustodian_Model::UpdatePositionInformationFidelity();
#echo "SONGS OVER";exit;

        $trade = new Trading_Ameritrade_Model();
        $accounts = PortfolioInformation_ConvertCustodian_Model::GetMissingTDAccountsFromBalances();
        $max = 9000;
        $interval = 3000;
        $x = 1;
        echo "MAX IS: " + $max  + "<br /><br />";
#        while($x <= $max) {
        echo "SIZE: ";
        echo sizeof($accounts);
        echo "<br /><br />";
        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api", $accounts, 1, 300);
        foreach ($tmp['model']['getAccountsJson']['account'] AS $k => $v) {
            if (PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v['accountNumber']) == 0) {
                $recordModel = PortfolioInformation_Record_Model::getCleanInstance("PortfolioInformation");
                $data = $recordModel->getData();
                $data['account_number'] = $v['accountNumber'];
                $data['description'] = $v['accountDescription'];
                $data['account_title1'] = $v['accountTitle'];
                $data['account_type'] = $v['accountType'];
                $data['production_number'] = $v['repCode'];
                $data['first_name'] = $v['firstName'];
                $data['last_name'] = $v['lastName'];
                $data['address1'] = $v['address1'];
                $data['address2'] = $v['address2'];
                $data['city'] = $v['city'];
                $data['state'] = $v['state'];
                $data['zip'] = $v['zip'];
                $data['origination'] = 'td';

                $recordModel->setData($data);
                $recordModel->set('mode', 'create');
                $recordModel->save();
                echo "<p>CREATED: {$data['account_number']}</p>";
                #                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD('2017-03-13', $v['accountNumber']);
            } else {
                echo "<p>EXISTS!</p>";
            }
        }

        echo "<p>ALL FINISHED...</p>";
        exit;
        /*
                $balances = array();
                foreach($tmp['model']['getBalancesJson']['balance'] AS $k => $v){
                    $balances[] = $v;
                }
                print_r($balances);

                /*
                        $trade = new Trading_Ameritrade_Model();
                        $tmp = $trade->GetAllAccounts("https://veoapi.advisorservices.com/InstitutionalAPIv2/api");
                        print_r($tmp);
                        $accounts = array();
                        foreach($tmp['model']['getAccountsJson']['account'] AS $k => $v){
                            $accounts[] = $v['accountNumber'];
                        }
                /*
                        $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
                        $custodian = "td";//Set the custodian
                        $newonly = 1;

                        global $adb;
                /*        $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";

                        $note = "Balances Section Start {$custodian}";
                        $adb->pquery($query, array($note));
                        $note = "Writing {$custodian} Balances To Cloud";
                        $adb->pquery($query, array($note));
                        PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud($custodian, null, $date);
                        PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesTD($date);
                        $note = "Balances Section Finished {$custodian}";
                        $adb->pquery($query, array($note));

                        $note = "Entering New Securities {$custodian}\r\n";
                        $adb->pquery($query, array($note));
                        ModSecurities_ConvertCustodian_Model::ConvertCustodian($custodian, $date, "=");
                        $note = "Entering Update Securities {$custodian}\r\n";
                        $adb->pquery($query, array($note));

                        $note = "Updating {$custodian} Securities\r\n";
                        $adb->pquery($query, array($note));
                        ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsTD(null, true);
                        $note = "Finished Updating Securities {$custodian}\r\n";
                        $adb->pquery($query, array($note));
                */
#        $note = "Positions Section Start {$custodian}";
#        $adb->pquery($query, array($note));
#Get New Positions Then Update Positions
        /*        $pos = new PositionInformation_ConvertCustodian_Action();
                $posR = new Vtiger_Request(array());
                $posR->set('custodian', $custodian);
                $posR->set('date', $date);
                $posR->set('convert_table', "new_positions");
                echo "Getting New Positions {$custodian}<br /><br />";
                $pos->process($posR);
                echo "New Positions Pulled {$custodian}<br /><br />";

                echo "Updating Positions {$custodian}<br /><br />";
                PositionInformation_ConvertCustodian_Model::UpdatePositionInformationTD($date);
                echo "Finished Updating Positions {$custodian}<br /><br />";

        /*
                $symbols = PortfolioInformation_xignite_Model::GetXigniteSyncSymbols(450);
                ModSecurities_Module_Model::FillWithXigniteData($symbols);
                echo "450 should be filled...";

        /*
                $date = date("Y-m-d", strtotime("today -1 Weekday"));//Determine the date we will be using automatically
                $custodian = "schwab";//Set the custodian
                $newonly = 1;

                global $adb;
                $query = "INSERT INTO vtiger_cloud_updates (note, time) VALUES (?, NOW())";
        echo "BALANCES SECTION START!....<br />";
                $note = "Balances Section Start {$custodian}";
                $adb->pquery($query, array($note));
                $note = "Writing {$custodian} Balances To Cloud";
                $adb->pquery($query, array($note));
                PortfolioInformation_ConvertCustodian_Model::WriteBalancesToCloud($custodian, null, $date);
                PortfolioInformation_ConvertCustodian_Model::UpdatePortfolioValuesSchwab($date);
                $note = "Balances Section Finished {$custodian}";
                $adb->pquery($query, array($note));

                $note = "Entering New Securities {$custodian}\r\n";
                $adb->pquery($query, array($note));
                ModSecurities_ConvertCustodian_Model::ConvertCustodian($custodian, $date, "=");
                $note = "Entering Update Securities {$custodian}\r\n";
                $adb->pquery($query, array($note));

                $note = "Updating {$custodian} Securities\r\n";
                $adb->pquery($query, array($note));
                ModSecurities_ConvertCustodian_Model::UpdateSecurityFieldsSchwab(null, true);
                $note = "Finished Updating Securities {$custodian}\r\n";
                $adb->pquery($query, array($note));
        /*
                $note = "Positions Section Start {$custodian}";
                $adb->pquery($query, array($note));
        #Get New Positions Then Update Positions
                $pos = new PositionInformation_ConvertCustodian_Action();
                $posR = new Vtiger_Request(array());
                $posR->set('custodian', $custodian);
                $posR->set('date', $date);
                $posR->set('convert_table', "new_positions");
                $note = "Getting New Positions {$custodian}\r\n";
                $adb->pquery($query, array($note));
                $pos->process($posR);
                $note = "New Positions Pulled {$custodian}\r\n";
                $adb->pquery($query, array($note));

                $note = "Updating Positions {$custodian}\r\n";
                $adb->pquery($query, array($note));
                PositionInformation_ConvertCustodian_Model::UpdatePositionInformationSchwab($date);
                $note = "Finished Updating Positions {$custodian}\r\n";
                $adb->pquery($query, array($note));
                echo "ALL OVER";*/
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