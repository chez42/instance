<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-09-05
 * Time: 10:32 AM
 */

require_once("libraries/Stratifi/StratifiAPI.php");

class StratContactObject{
    public $first_name, $last_name, $email;
    function __construct($first_name, $last_name, $email){
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
    }
}

class StratContacts extends StratifiAPI {
    function __construct(){
        parent::__construct();
    }

    /**
     * Create new contact in Stratifi's system.
     * @param $advisor_id
     * @param $name
     * @return mixed
     *
     */
    private function CreateNewStratifiContact($user_object){
        $extension = "/api/v1/investors/";
        $url = $this->getURL() . $extension;
        $body = json_encode($user_object);
        $result = self::execQuery($url, $this->header, $body);
        return $result;
    }

    private function UpdateStratifiContact($strat_id, $user_object){
        $extension = "/api/v1/investors/{$strat_id}/";
        $url = $this->getURL() . $extension;
        $body = json_encode($user_object);
        $result = self::execPatch($url, $this->header, $body);
        return $result;
    }

    private function UpdateStratifiContactAdvisor($strat_id, $advisor_id){
        $extension = "/api/v1/investors/{$strat_id}/";
        $url = $this->getURL() . $extension;

        $body = json_encode(array("advisor" => $advisor_id));
        $result = self::execPatch($url, $this->header, $body);
        return $result;
    }

    public function UpdateStratifiContactFromContactID($omniContactID){
        global $adb;
        $query = "SELECT cd.contactid, cd.firstname, cd.lastname, cf.stratid AS contact_stratid, u.stratid AS user_stratid, u.user_name
                  FROM vtiger_contactdetails cd
                  JOIN vtiger_contactscf cf USING (contactid)
                  JOIN vtiger_crmentity e ON e.crmid = cd.contactid
                  JOIN vtiger_users u ON u.id = e.smownerid
                  WHERE contactid=?";
        $result = $adb->pquery($query, array($omniContactID));
        if($adb->num_rows($result) > 0) {
            $advisor_id = $adb->query_result($result, 0, 'user_stratid');
            $contact_stratid = $adb->query_result($result, 0, 'contact_stratid');
            $last_name = $adb->query_result($result, 0, 'lastname');
            $first_name = $adb->query_result($result, 0, 'firstname');

            $result = $this->UpdateStratifiContactAdvisor($contact_stratid, $advisor_id);
            echo $first_name . " " . $last_name . " NOW BELONGS TO ";
            print_r($result);
            echo '<br /><br />';
        }
    }

    public function GetAllContactsAndUpdateAdvisorOwnership(){
        global $adb;
        $query = "SELECT contactid FROM vtiger_contactscf cf 
                  JOIN vtiger_crmentity e ON e.crmid = cf.contactid 
                  WHERE stratid > 0 AND e.deleted = 0";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetch_array($result)){
#                echo $v['contactid'] . '<br />';
                $this->SaveStratifiContact($v['contactid']);
#                $this->UpdateStratifiContactFromContactID($v['contactid']);
            }
        }
    }

    /**
     * Update the contact in Omniscient filling in the Stratifi ID
     * @param $omniID
     * @param $stratID
     */
    public function UpdateStratifiID($omniID, $stratID){
        global $adb;
        $query = "UPDATE vtiger_contactscf 
                  SET stratid = ? WHERE contactid = ?";
        $adb->pquery($query, array($stratID, $omniID));
    }

    public function CreateContact ($omniID){
        $contact_info = Contacts_Record_Model::getInstanceById($omniID);
        if(strlen($contact_info->get('account_id')) > 0 && $contact_info->get('account_id') != 0){
            $household_info = Accounts_Record_Model::getInstanceById($contact_info->get('account_id'));
            $stratHHID = $household_info->get('stratid');
            $recordOwnerArr=getRecordOwnerId($contact_info->getId());
            foreach($recordOwnerArr as $type=>$id)
            {
                $ownertype=$type;
                $ownerid=$id;
            }
            if($ownertype == "Groups"){
                echo "Contact is assigned to a group... leaving....";
                return 0;
            }
            echo "Owner type: " . $ownertype . ' and owner id: ' . $ownerid . '<br />';
            echo "Assigned User: " . $contact_info->get('assigned_user_id') . "<br />";
            $strat_advisor = GetStratifiAdvisorIDFromOmniAdvisorID($contact_info->get('assigned_user_id'));
            echo "Strat advisor: " . $strat_advisor . '<br />';
        }

        $email = $contact_info->get('email');
        if(strlen($email) <= 0){
            $email = $contact_info->getId() . "_" . 'noemail@noemail.com';
###            echo "No email<br />";
###            return 0;
        }else{
            $email = $contact_info->get('email');
        }

        if(!DoesContactHaveStratifiID($omniID)){
            $obj = new StratContactObject(substr($contact_info->get('firstname'), 0, 29), substr($contact_info->get('lastname'), 0, 29), $email);
            $o = array("household" => $stratHHID, "advisor" => $strat_advisor, "user" => $obj);
            print_r($o);
            $result = json_decode($this->CreateNewStratifiContact($o));
            print_r($result);
            if($result->id){
                $this->UpdateStratifiID($omniID, $result->id);
            }
            return $result;
        }else{
            echo "Contact already has stratifi ID<br /><br />";
        }
        return 0;
    }

    public function SaveStratifiContact($omniID)
    {
        global $adb;
        if($omniID != 0 && $omniID) {
            $contact_info = Contacts_Record_Model::getInstanceById($omniID);
            if (DoesContactHaveStratifiID($omniID)) {
                $query = "SELECT u.stratid AS user_stratid, u.user_name, acf.stratid AS household_stratid
                          FROM vtiger_contactdetails cd
                          JOIN vtiger_contactscf cf USING (contactid)
                          JOIN vtiger_crmentity e ON e.crmid = cd.contactid
                          LEFT JOIN vtiger_accountscf acf ON cd.accountid = acf.accountid
                          JOIN vtiger_users u ON u.id = e.smownerid
                          WHERE contactid=?";
                $result = $adb->pquery($query, array($omniID));
                if($adb->num_rows($result) > 0) {
                    $advisor_id = $adb->query_result($result, 0, 'user_stratid');
                    $household_id = $adb->query_result($result, 0, 'household_stratid');
#                    $result = $this->UpdateStratifiContactAdvisor($contact_stratid, $advisor_id);
                }

                echo 'Saving for: ' . $omniID . '<br />';
                $email = $contact_info->get('email');
                $obj = new StratContactObject(substr($contact_info->get('firstname'), 0, 29), substr($contact_info->get('lastname'), 0, 29), $email);
                $o = array("user" => $obj);
                if(strlen($advisor_id) > 0)
                    $o['advisor'] = $advisor_id;
                if(strlen($household_id) > 0)
                    $o['household'] = $household_id;
                $result = json_decode($this->UpdateStratifiContact(GetStratifiID($omniID), $o));
                return $result;
            }
        }
    }
}

function GetStratifiID($contact_id){
    global $adb;

    $query = "SELECT stratid FROM vtiger_contactscf WHERE contactid = ?";
    $result = $adb->pquery($query, array($contact_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratid');
        return $stratID;
    }
    return 0;
}

function DoesContactHaveStratifiID($contact_id){
    global $adb;

    $query = "SELECT stratid FROM vtiger_contactscf WHERE contactid = ?";
    $result = $adb->pquery($query, array($contact_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratid');
        if($stratID > 0)
            return true;
        else
            return false;
    }
    return false;
}