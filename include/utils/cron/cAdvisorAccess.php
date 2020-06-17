<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cAdvisorAccess
 *
 * @author theshado
 */
class cAdvisorAccess {
    private $pc;
    private $datasets;
    private $reset;
    
    public function __construct() {
        $this->pc = new cPortfolioCenter();
        $this->datasets = $this->pc->GetDatasets();//"1, 28";
        $this->reset = 500;
    }
    
    public function __destruct() {
        ;
    }
    
    /**
     * Gets the pc_id, pc_name, description from PC and inserts it into vtiger_pc_advisors
     */
    public function CreateAdvisors(){
        global $adb;
        if(!$this->pc->connect())//Try connecting
                return "Error Connecting to PC";
        
        $query = "SELECT AdvisorID, AdvisorName, Description FROM Advisors WHERE DataSetID IN ({$this->datasets})";//Get all advisors
        $results = mssql_query($query);
        $query = "INSERT INTO vtiger_pc_advisors (pc_id, pc_name, Description) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE pc_id=?, pc_name=?, description=?";
        if($results)
        while($row = mssql_fetch_array($results))
        {
            $advisor_id = $row['AdvisorID'];
            $advisor_name = $row['AdvisorName'];
            $description = $row['Description'];
            $adb->pquery($query, array($advisor_id, $advisor_name, $description, $advisor_id, $advisor_name, $description));
        }
    }
    
    /**
     * 
     * @global type $adb
     */
    public function CreateAdvisorLinking(){
        global $adb;
        $query = "SELECT u.id, u.advisor_control_number FROM vtiger_users u WHERE advisor_control_number != ''";
        $result = $adb->pquery($query, array());
        $user_info = array();
        foreach($result AS $k => $v){
            $tmp = explode(',', $v['advisor_control_number']);
            foreach($tmp AS $a => $b){
                $query = "SELECT pc_id FROM vtiger_pc_advisors WHERE pc_name = ?";
                $b = str_replace(" ", "", $b);
                $res = $adb->pquery($query, array($b));
                $pc_id = $adb->query_result($res, 0, 'pc_id');
                $user_info[] = array("id" => $v['id'],
                                     "pc_id" => $pc_id,
                                     "advisor_control_number" => $b);
            }
        }
        foreach($user_info AS $k => $v){
            $query = "INSERT INTO vtiger_pc_advisor_linking (pc_id, user_id) VALUES ({$v['pc_id']}, {$v['id']})
                      ON DUPLICATE KEY UPDATE pc_id = VALUES(pc_id), user_id = VALUES(user_id)";
            $adb->pquery($query, array());
        }

        /*
        $query = "SELECT u.id, u.advisor_control_number, vpa.pc_id
                  FROM vtiger_users u
                  LEFT JOIN vtiger_pc_advisors vpa ON u.advisor_control_number = vpa.pc_name
                  WHERE pc_id IS NOT null";
        $result = $adb->pquery($query, array());
                
        foreach($result AS $k => $v){
            $query = "INSERT INTO vtiger_pc_advisor_linking (pc_id, user_id) VALUES ({$v['pc_id']}, {$v['id']})
                      ON DUPLICATE KEY UPDATE pc_id = VALUES(pc_id), user_id = VALUES(user_id)";
            $adb->pquery($query, array());
        }*/
    }
}

?>
