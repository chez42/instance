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