<?php
trait tSecurities{
    protected $custodian_securities, $existing_securities, $missing_securities;

    /**
     * Sets up the variables for determining which securities exist with the custodian, which exist in the CRM, and which are missing
     */
    protected function SetupSecuritiesComparisons(){
        global $adb;
        $params = array();
        $custodian_securities = array();//List of custodian accounts
        $existing_securities = array();//Accounts that exist in the crm
        foreach($this->securities_data AS $k => $v){
            $custodian_securities[$k] = $k;
        }

        if(!empty($custodian_securities)){
            $questions = generateQuestionMarks($custodian_securities);
            $params[] = $custodian_securities;
            $query = "SELECT security_symbol FROM vtiger_modsecurities WHERE security_symbol IN ({$questions})";
            $result = $adb->pquery($query, $params);
            if($adb->num_rows($result) > 0){
                while ($r = $adb->fetchByAssoc($result)) {
                    if(array_key_exists($r['security_symbol'], $this->symbol_replacements))//If we have TDCASH for example, match it to 'Cash' on the custodian
                        $existing_securities[strtoupper(TRIM($r['security_symbol']))] = $this->symbol_replacements[$r['security_symbol']];
                    else
                        $existing_securities[strtoupper(TRIM($r['security_symbol']))] = $r['security_symbol'];
                }
            }
        }

        $this->custodian_securities = $custodian_securities;
        $this->existing_securities = $existing_securities;
        $this->missing_securities = array_diff_key($this->custodian_securities, $this->existing_securities);
    }

    /**
     * Determine if the security exists in the CRM already
     * @param $symbol
     * @return bool
     */
    public function DoesSecurityExistInCRM($symbol){
        global $adb;
        $query = "SELECT security_symbol 
                  FROM vtiger_modsecurities 
                  WHERE security_symbol = ?";
        $result = $adb->pquery($query, array($symbol));
        if($adb->num_rows($result) > 0)
            return true;
        return false;
    }

    /**
     * Returns the list of custodian accounts
     * @return mixed
     */
    public function GetCustodianSecurities(){
        return $this->custodian_securities;
    }

    /**
     * Returns a list of accounts in the CRM that currently exist compared to the custodian accounts
     * @return mixed
     */
    public function GetExistingCRMSecurities(){
        return $this->existing_securities;
    }

    /**
     * Returns a list of accounts that exist in the custodian table, but not in the CRM
     * @return mixed
     */
    public function GetMissingCRMSecurities(){
        return $this->missing_securities;
    }

    /**
     * Returns a list of all securities in the CRM
     * @return array
     */
    public function GetAllCRMSecurities(){
        global $adb;
        $crm_securities = array();
        $query = "SELECT security_symbol FROM vtiger_modsecurities";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while ($r = $adb->fetchByAssoc($result)) {
                $crm_securities[strtoupper(TRIM($r['security_symbol']))] = $r['security_symbol'];
            }
        }
        return $crm_securities;
    }

    public function GetTableIntersection($db1, $table1, $db2, $table2, $prefix){
        global $adb;
        $fields1 = $fields2 = array();

        $query = "SELECT COLUMN_NAME AS field
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";

        $result = $adb->pquery($query, array($db1, $table1));
        while($r = $adb->fetchByAssoc($result)){
            $fields1[$prefix . $r["field"]] = $prefix . $r["field"];
        }

        $result = $adb->pquery($query, array($db2, $table2));
        while($r = $adb->fetchByAssoc($result)){
            $fields2[$prefix . $r["field"]] = $r[$prefix . "field"];
        }

        $valid = array_intersect_key($fields1, $fields2);
        return $valid;
    }

}