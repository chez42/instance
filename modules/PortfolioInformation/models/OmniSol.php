<?php

class PortfolioInformation_OmniSol_Model extends Vtiger_Module{
    private function AssociatedResult($result){
        global $adb;
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rows[] = $v;
            }
            return $rows;
        }
    }

    public function AccountCompareCount($balance_table, $field1, $field2, $start, $end){
        global $adb;
#        $query = "CALL ACCOUNT_COMPARE(?, ?, ?, ?, ?)";
        $params[] = $balance_table;
        $params[] = $field1;
        $params[] = $field2;
        $params[] = $start;
        $params[] = $end;
        $questions = generateQuestionMarks($params);
        $query = "CALL ACCOUNT_COMPARE({$questions})";
        $adb->pquery($query, $params);//$balance_table, $field1, $field2, $start, $end));

        $accounts_closed = "SELECT * FROM ACCOUNT_COMPARE_NO_LONGER_EXISTS";
        $closed_result = $adb->pquery($accounts_closed, array());

        $accounts_new = "SELECT * FROM ACCOUNT_COMPARE_NEW_ACCOUNTS";
        $new_result = $adb->pquery($accounts_new, array());

        $accounts = array("old" => $this->AssociatedResult($closed_result),
                          "old_count" => $adb->num_rows($closed_result),
                          "new" => $this->AssociatedResult($new_result),
                          "new_count" => $adb->num_rows($new_result));
        return $accounts;
    }
}