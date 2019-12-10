<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-11-29
 * Time: 11:40 AM
 */
include_once("libraries/Stratifi/StratifiAPI.php");

class PortfolioInformation_Stratifi_Model extends Vtiger_Module{
    static public function CreateAccountsInStratifiForControlNumbers(array $control_numbers){
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);
        $count = 1;
        foreach($accounts AS $k => $v){
            $stratid = PortfolioInformation_Module_Model::DoesAccountHaveStratifiID($v);
            echo "{$count} -- StratID for {$v}: " . $stratid . '<br />';
            if($stratid == 0){
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);//We need the crmid to create an account
                PortfolioInformation_Module_Model::CreateStratifiPortfolioAccount($crmid);
##                echo $count . ') ' . $v . '.  Stratifi ID: ' . $stratid . '<br />';
##                $count++;
            }
            $count++;
        }
    }

    static public function CreateAccountsInStratifiForRepCodes(array $rep_codes){
        $accounts = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCode($rep_codes);
        $count = 1;
        foreach($accounts AS $k => $v){
            $stratid = PortfolioInformation_Module_Model::DoesAccountHaveStratifiID($v);
            echo "{$count} -- StratID for {$v}: " . $stratid . '<br />';
            if($stratid == 0){
                $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($v);//We need the crmid to create an account
                PortfolioInformation_Module_Model::CreateStratifiPortfolioAccount($crmid);
                echo $count . ') ' . $v . '.  Stratifi ID: ' . $stratid . '<br />';
                $count++;
            }
            $count++;
        }
    }

    static public function SendAllPositionsToStratifi(){
        $strat = new StratifiAPI();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();
        foreach($account_numbers AS $k => $v){
            $data = PortfolioInformation_Module_Model::GetStratifiData($v);
            $strat->UpdatePositionsToStratifi($data);
        }
    }

    static public function CreateStratifiContactsForAllAccounts(){
        $strat = new StratifiAPI();
        $sContacts = new StratContacts();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();

        foreach($account_numbers AS $k => $v) {
            echo "Trying for: " . $v . "<br />";
            $contact_entity = PortfolioInformation_Module_Model::GetContactEntityFromAccountNumber($v);
            if ($contact_entity) {
                if(DoesContactHaveStratifiID($contact_entity->getId())){
                    echo $contact_entity->getId() . " already has stratifi ID" . "<br /><br />";
                } else {//No contact entity exists, create it
                    echo "NO STRATID FOR CONTACT: " . $contact_entity->getId() . ".... Creating: <br />";
                    echo "CREATING FOR: " . $contact_entity->getId() . '<br />';
                    if ($sContacts->CreateContact($contact_entity->getId()) != 0) {
                        $result = $strat->UpdateStratifiAccountLinking($v);
                        print_r($result);
                        echo '<br /><br />';
                    }else{
                        echo "There was some sort of error<br /><br />";
                    }
                }
            }
        }
    }

    static public function CreateStratifiHouseholdsForAllAccounts(){
        global $adb;
        $stratH = new StratHouseholds();
        $account_numbers = $stratH->GetAccountsThatHaveStratifiID();
        foreach($account_numbers AS $k => $v) {
            $household_id = GetHouseholdIDFromAccountNumber($v);
            if ($household_id != 0) {
                $household_record = Accounts_Record_Model::getInstanceById($household_id);
                $portfolio_record_id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($v);
                $portfolio_record = PortfolioInformation_Record_Model::getInstanceById($portfolio_record_id);
                $owner = getRecordOwnerId($portfolio_record->getId());
                $advisor_id = $owner['Users'];
                $omniID = $household_record->getId();

                $data = $household_record->getData();
                if (strlen($data['stratid']) == 0 || $data['stratid'] == 0) {
                    $query = "SELECT stratid FROM vtiger_users WHERE id = ?";
                    $result = $adb->pquery($query, array($advisor_id));
                    if($adb->num_rows($result) > 0){
                        $strat_advisor_id = $adb->query_result($result, 0, 'stratid');
                        $result = $stratH->CreateHousehold($omniID, $strat_advisor_id, $data['accountname']);
                        print_r($result); echo '<br /><br />';
                    }
                }else{
                    echo "Household: " . $household_record->getId() . " has stratifiID of: " . $data['stratid'] . "<br />";
                }
            }
        }
    }

    static public function UpdateHouseholdFromAccount($account_number){
        global $adb;
        $stratH = new StratHouseholds();
        $household_id = GetHouseholdIDFromAccountNumber($account_number);
        if ($household_id != 0) {
            $household_record = Accounts_Record_Model::getInstanceById($household_id);
            $portfolio_record_id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($account_number);
            $portfolio_record = PortfolioInformation_Record_Model::getInstanceById($portfolio_record_id);
            $owner = getRecordOwnerId($portfolio_record->getId());
            $advisor_id = $owner['Users'];
            $omniID = $household_record->getId();

            $data = $household_record->getData();
            $query = "SELECT stratid FROM vtiger_users WHERE id = ?";
            $result = $adb->pquery($query, array($advisor_id));
            if (strlen($data['stratid']) == 0 || $data['stratid'] == 0) {
                if($adb->num_rows($result) > 0){
                    $strat_advisor_id = $adb->query_result($result, 0, 'stratid');
                    $result = $stratH->CreateHousehold($omniID, $strat_advisor_id, $data['accountname']);
                    print_r($result); echo '<br /><br />';
                }
            }else{
                if($adb->num_rows($result) > 0){
                    $strat_advisor_id = $adb->query_result($result, 0, 'stratid');
                    $result = $stratH->UpdateHousehold($omniID, $strat_advisor_id, $data['accountname']);
                    print_r($result); echo '<br /><br />';
                }
            }
        }
    }

    static public function UpdateStratifiInvestorLinkingForControlNumbers(array $control_numbers){
        $strat = new StratifiAPI();
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);

        foreach($account_numbers AS $k => $v){
            $result = $strat->UpdateStratifiInvestorLinking($v);
            echo $result . '<br /><br />';
        }
    }

    static public function UpdateStratifiAccountLinkingForControlNumbers(array $control_numbers){
        $strat = new StratifiAPI();
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromOmniscientControlNumber($control_numbers);

        foreach($account_numbers AS $k => $v){
            $result = $strat->UpdateStratifiAccountLinking($v);
            echo $result . '<br /><br />';
        }
    }

    static public function UpdateStratifiAccountLinkingForRepCode(array $control_numbers){
        $strat = new StratifiAPI();
        $account_numbers = PortfolioInformation_Module_Model::GetAccountNumbersFromRepCode($control_numbers);

        foreach($account_numbers AS $k => $v){
            $result = $strat->UpdateStratifiAccountLinking($v);
            echo $result . '<br /><br />';
        }
    }

    static public function UpdateStratifiAccountLinkingForAllAccounts(){
        $strat = new StratifiAPI();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();

        foreach($account_numbers AS $k => $v){
            $result = $strat->UpdateStratifiAccountLinking($v);
        }
    }

    static public function UpdateStratifiInvestorLinkingForAllAccounts(){
        $strat = new StratifiAPI();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();

        foreach($account_numbers AS $k => $v){
            $result = $strat->UpdateStratifiInvestorLinking($v);
        }
    }

    static public function GetNumberOfAccountsThatHaveStratifiIDs(){
        $strat = new StratifiAPI();
        $account_numbers = $strat->GetAccountsThatHaveStratifiID();
        return count($account_numbers);
    }

    static public function GetStratifiUserList(){
        $strat = new StratAdvisors();
        $result = json_decode($strat->GetAdvisorsFromStratifi());
        $advisors = $result;
        for($x = 0; $x <= $result->total_pages; $x++){
            $result = json_decode($strat->GetAdvisorsFromStratifi($result->next));
            foreach($result->results AS $k => $v){
                $advisors->results[] = $v;
            }
        }
        return $advisors;
    }

    static public function GetAccountsList(){
        $strat = new StratifiAPI();
        $result = json_decode($strat->getAllAccounts());
        $accounts = $result;
        for($x = 0; $x <= $result->total_pages; $x++){
            $result = json_decode($strat->getAllAccounts($result->next));
            foreach($result->results AS $k => $v){
                $accounts->results[] = $v;
            }
        }
        return $accounts;
    }

    static public function UpdateOmniscientUserWithStratifiIDsUsingUserID($omni_user_id, $stratifi_user_id, $company_id){
        global $adb;
        $query = "UPDATE vtiger_users SET stratid = ?, stratcompanyid = ? WHERE id = ?";
        $adb->pquery($query, array($stratifi_user_id, $company_id, $omni_user_id));
    }

    static public function UpdateOmniscientUserWithStratifiIDsUsingUserEmail($omni_user_email, $stratifi_user_id, $company_id){
        global $adb;
        $query = "UPDATE vtiger_users SET stratid = ?, stratcompanyid = ? WHERE email1 = ?";
        $adb->pquery($query, array($stratifi_user_id, $company_id, $omni_user_email));
    }

}