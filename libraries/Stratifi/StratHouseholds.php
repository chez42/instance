<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-09-05
 * Time: 10:53 AM
 */
require_once("libraries/Stratifi/StratifiAPI.php");

class StratHouseholds extends StratifiAPI {
    function __construct(){
        parent::__construct();
    }

    /**
     * Create new household in Stratifi's system. advisor_id is the omniscient advisor ID, name is the name of the household
     * @param $advisor_id
     * @param $name
     * @return mixed
     *
     */
    private function CreateNewStratifiHousehold($advisor_id, $name){
            $extension = "/api/v1/households/";
            $url = $this->getURL() . $extension;

            $body = json_encode(array("advisor" => $advisor_id, "name" => $name));
            $result = self::execQuery($url, $this->header, $body);
            return $result;
    }

    private function UpdateStratifiHousehold($advisor_id, $name, $stratifiHHID){
        $extension = "/api/v1/households/{$stratifiHHID}/";
        $url = $this->getURL() . $extension;

        $body = json_encode(array("advisor" => $advisor_id, "name" => $name));
        $result = self::execPatch($url, $this->header, $body);
        return $result;
    }

    public function UpdateStratifiHouseholdFromHHID($omniHHID){
        global $adb;
        $query = "SELECT a.accountid, a.accountname, u.stratid AS user_stratid, u.user_name, cf.stratid AS account_stratid
                  FROM vtiger_account a
                  JOIN vtiger_accountscf cf USING (accountid)
                  JOIN vtiger_crmentity e ON e.crmid = a.accountid
                  JOIN vtiger_users u ON u.id = e.smownerid
                  WHERE accountid=?";
        $result = $adb->pquery($query, array($omniHHID));
        if($adb->num_rows($result) > 0) {
            $advisor_id = $adb->query_result($result, 0, 'user_stratid');
            $account_stratid = $adb->query_result($result, 0, 'account_stratid');
            $account_name = $adb->query_result($result, 0, 'accountname');

            $result = $this->UpdateStratifiHousehold($advisor_id, $account_name, $account_stratid);
            echo $account_name . " NOW BELONGS TO ";
            print_r($result);
            echo '<br /><br />';
        }
    }

    public function GetAllHouseholdsAndUpdateAdvisorOwnership(){
        global $adb;
        $query = "SELECT accountid FROM vtiger_accountscf WHERE stratid > 0";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetch_array($result)){
                $this->UpdateStratifiHouseholdFromHHID($v['accountid']);
            }
        }
    }

    /**
     * Update the household in Omniscient filling in the Stratifi ID
     * @param $omniID
     * @param $stratID
     */
    public function UpdateStratifiID($omniID, $stratID){
        global $adb;
        $query = "UPDATE vtiger_accountscf 
                  SET stratid = ? WHERE accountid = ?";
        $adb->pquery($query, array($stratID, $omniID));
    }

    public function CreateHousehold($entityID, $advisor_id, $name){
        if(strlen($advisor_id) > 0 && strlen($name) > 0) {
            if (!DoesHouseholdHaveStratifiID($entityID)) {
                $result = json_decode(self::CreateNewStratifiHousehold($advisor_id, $name));
                if ($result->id) {
                    self::UpdateStratifiID($entityID, $result->id);
                }
                return $result;
            }
            return 0;
        }
    }

    public function UpdateHousehold($entityID, $advisor_id, $name){
        if(strlen($advisor_id) > 0 && strlen($name) > 0) {
            if (!DoesHouseholdHaveStratifiID($entityID)) {
                $result = json_decode(self::UpdateStratifiHousehold($advisor_id, $name));
                return $result;
            }
            return 0;
        }
    }

    public function AutoCreateHouseholdsFromRepCodes(){

    }
}

function DoesHouseholdHaveStratifiID($household_id){
    global $adb;

    $query = "SELECT stratid FROM vtiger_accountscf WHERE accountid = ?";
    $result = $adb->pquery($query, array($household_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratid');
        if($stratID > 0)
            return true;
        else
            return false;
    }
        return false;
}