<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-03-30
 * Time: 3:40 PM
 */

require_once("libraries/javaBridge/JavaConnector.php");

class JavaCloudToCRM extends JavaConnector{
    public function WriteFiles($custodian, $operation, $skipDays, $dontIgnoreFileIfExists, $extension=null){
        $data = array();
        $data['custodian'] = $custodian;
        $data['operation'] = $operation;
        $data['skipDays'] = $skipDays;
        $data['dontIgnoreFileIfExists'] = $dontIgnoreFileIfExists;
        if(!is_null($extension))
            $data['extension'] = $extension;
/*        $data = array("custodian" => $custodian,
            "operation" => $operation,
            "skipDays" => $skipDays,
            "dontIgnoreFileIfExists" => $dontIgnoreFileIfExists);*/
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdatePortfolios($custodian, $vtigerDBName){
        $data = array("custodian" => $custodian,
            "operation" => "updateportfolios",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdateSecurities($custodian, $vtigerDBName){
        $data = array("custodian" => $custodian,
            "operation" => "updatesecurities",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdatePositions($custodian, $vtigerDBName){
        $data = array("custodian" => $custodian,
            "operation" => "updatepositions",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function UpdateTransactions($custodian, $vtigerDBName){
        $data = array("custodian" => $custodian,
            "operation" => "updatetransactions",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function VerifyDirectories($custodian, $vtigerDBName, $directory){
        $data = array("custodian" => $custodian,
                      "operation" => "verifydirectories",
                      "vtigerDBName" => $vtigerDBName,
                      "directory" => $directory);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function CalculateWeight($vtigerDBName){
        $data = array("operation" => "updatepositionweight",
            "custodian" => "td",
            "vtigerDBName" => $vtigerDBName);
        $result = $this->MakeCall("http://lanserver24/OmniServ/AutoParse", $data);
        return $result;
    }

    public function GetCustodianStatus(){
        global $adb;
        $query = "SELECT active FROM vtiger_custodian_status";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, 'active');
        }
        return -1;
    }

    public function SetCustodianStatus($active, $current_event=null, $previous_event=null){
        global $adb;
        $update = "";
        $params = array();
        $params[] = $active;

        if(strlen($current_event) > 0) {
            $update .= ", current_event = ? ";
            $params[] = $current_event;
        }
        if(strlen($previous_event) > 0) {
            $update .= ", previous_event = ? ";
            $params[] = $previous_event;
        }

        $query = "UPDATE vtiger_custodian_status SET active = ?, last_write = NOW() {$update} ";
        $adb->pquery($query, $params);
    }

    public function SetCustodianFailedStatus($message){
        global $adb;
        $query = "INSERT INTO vtiger_custodian_failed_status SET message = ? ";
        $adb->pquery($query, array($message));
    }

    public function AutoSetActiveStatus($override = null){
        global $adb;
        $query = "UPDATE vtiger_custodian_status SET active = CASE WHEN HOUR(TIMEDIFF(last_write, NOW())) >= 1 THEN 0 ELSE active END";
        $adb->pquery($query, array());

        if($override != null){
            $query = "UPDATE vtiger_custodian_status SET active = ?";
            $adb->pquery($query, array($override));
        }
    }
}