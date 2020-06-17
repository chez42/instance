<?php
class Omniscient_OMUpdate_Model extends Vtiger_Module_Model{
    
    public function CopyTransactions($account_numbers){
        global $adb;
        $pids = array();
        
        $account_questions = generateQuestionMarks($account_numbers);
        $pid_query = "SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number IN ({$account_questions})";
        $result = $adb->pquery($pid_query, array($account_numbers));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $pids[] = $v['portfolio_id'];
            }
            $pid_questions = generateQuestionMarks($pids);
            $query = "INSERT INTO om_omniscientDB.vtiger_pc_transactions
                        SELECT * FROM live_omniscientDB.vtiger_pc_transactions t WHERE t.portfolio_id IN ({$pid_questions})
                      ON DUPLICATE KEY UPDATE transaction_id = VALUES(transaction_id)";
            $result = $adb->pquery($query, array($pids));
        }
    }
}

?>