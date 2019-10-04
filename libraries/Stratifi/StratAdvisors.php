<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-09-21
 * Time: 12:43 PM
 */

require_once("libraries/Stratifi/StratifiAPI.php");

class StratAdvisors extends StratifiAPI {
    function __construct(){
        parent::__construct();
    }

    /**
     * Create new Advisor in Stratifi's system. advisor_id is the omniscient advisor ID, name is the name of the Advisor
     * @param $advisor_id
     * @param $name
     * @return mixed
     *
     */
    private function CreateNewStratifiAdvisor($company_id, $user_object){
        $extension = "/api/v1/advisors/";
        $url = $this->getURL() . $extension;
        $body = json_encode(array("company" => $company_id, "user" => $user_object));
        $result = self::execQuery($url, $this->header, $body);
        return $result;
    }

    public function CreateNewStratifiCompany($company_name){
        $extension = "/api/v1/companies/";
        $url = $this->getURL() . $extension;
        $body = json_encode(array("name" => $company_name));
        $result = self::execQuery($url, $this->header, $body);
        return $result;
    }

    public function GetAdvisorsFromStratifi(){
        $extension = "/api/v1/advisors";
        $url = $this->getURL() . $extension;
        $body = "";
        $result = self::execQuery($url, $this->header, $body, false);
        return $result;
    }

    public function GetCompaniesFromStratifi(){
        $extension = "/api/v1/companies";
        $url = $this->getURL() . $extension;
        $body = "";
        $result = self::execQuery($url, $this->header, $body, false);
        return $result;
    }

    public function AutoCreateCompanies(){
        global $adb;

        $this->FillStratifiCompanyTableWithNewDepartments();

        $query = "SELECT department FROM vtiger_stratifi_companies WHERE stratid IS NULL";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $departments[] = $v['department'];
            }
        }

        foreach($departments AS $k => $v){
            $result = json_decode($this->CreateNewStratifiCompany($v));
            if($result->id){
                $this->UpdateStratifiCompanyID($result->id, $v);
            }
        }

        $query = "UPDATE vtiger_users u 
                  JOIN vtiger_stratifi_companies s ON u.department = s.department
                  SET u.stratcompanyid = s.stratid";
        $adb->pquery($query, array());
    }

    public function AutoCreateAdvisors(){
        global $adb;

        $query = "SELECT id, stratcompanyid, first_name, last_name, email1 AS email FROM vtiger_users WHERE stratid IS NULL AND stratcompanyid IS NOT NULL AND email1 NOT IN ('none@none.com', 'nobody@nobody.com', '')";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                echo "Inputting: ";
                print_r($v);
                echo "<br />";
                $user_object = array("first_name" => $v['first_name'], "last_name" => $v['last_name'], "email" => $v['email']);
                $tmp_result = $this->CreateAdvisor($v['id'], $v['stratcompanyid'], $user_object);
                print_r($tmp_result); echo '<br /><br />';
            }
        }
    }

    /**
     * Update the advisor in Omniscient filling in the Stratifi ID
     * @param $omniID
     * @param $stratID
     */
    public function UpdateStratifiID($omniID, $stratID){
        global $adb;
        $query = "UPDATE vtiger_users 
                  SET stratid = ? WHERE id = ?";
        $adb->pquery($query, array($stratID, $omniID));
    }

    public function UpdateStratifiCompanyID($stratID, $company_name){
        global $adb;
        $query = "UPDATE vtiger_stratifi_companies
                  SET stratid = ? WHERE department = ?";
        $adb->pquery($query, array($stratID, $company_name));
    }

    /**
     * Auto creates the stratifi relationship table between the companies and departments
     */
    public function FillStratifiCompanyTableWithNewDepartments(){
        global $adb;

        $query = "INSERT IGNORE INTO vtiger_stratifi_companies (department)
                  SELECT department FROM vtiger_users WHERE department != '' GROUP BY department";
        $adb->pquery($query, array());
    }

    /**
     * Create new stratifi advisor
     * @param $advisor_id
     * @param $name
     * @return int|mixed|string
     */
    public function CreateAdvisor($advisor_id, $company_id, $user_object){
//        $result = self::GetAdvisorsFromStratifi();
        if(strlen($advisor_id) > 0) {
            if (!DoesAdvisorHaveStratifiID($advisor_id)) {//Does the advisor have an ID assigned
                if(!DoesAdvisorHaveStratifiCompanyID($advisor_id)){
                    return "NO COMPANY FILLED IN";
                }
                $result = json_decode(self::CreateNewStratifiAdvisor($company_id, $user_object));
                if ($result->id) {
                    self::UpdateStratifiID($advisor_id, $result->id);
                    echo "updated<br />";
                }
                echo "returning<br />";
                return $result;
            }
            return 0;
        }
    }
}

function DoesCompanyHaveStratifiID($company_name){
    global $adb;

#    $query = "SELECT "
}

function DoesAdvisorHaveStratifiCompanyID($advisor_id){
    global $adb;

    $query = "SELECT stratcompanyid FROM vtiger_users WHERE id = ?";
    $result = $adb->pquery($query, array($advisor_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratcompanyid');
        if($stratID > 0)
            return true;
        else
            return false;
    }
    return false;
}

function GetStratifiAdvisorIDFromOmniAdvisorID($advisor_id){
    global $adb;

    $query = "SELECT stratid FROM vtiger_users WHERE id = ?";
    $result = $adb->pquery($query, array($advisor_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratid');
        if($stratID > 0)
            return $stratID;
        else
            return 0;
    }
    return 0;
}

function DoesAdvisorHaveStratifiID($advisor_id){
    global $adb;

    $query = "SELECT stratid FROM vtiger_users WHERE id = ?";
    $result = $adb->pquery($query, array($advisor_id));
    if($adb->num_rows($result) > 0){
        $stratID = $adb->query_result($result, 0, 'stratid');
        if($stratID > 0)
            return true;
        else
            return false;
    }
    return false;
}