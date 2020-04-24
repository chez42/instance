<?php

class PortfolioInformation_SaveHandsOnTableJson_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        $table = $request->get('table');
        $data = $request->get('data');


        //This should probably be a common function.  Used in LoadHandsOnTableJson.php as well
        $query = "SHOW COLUMNS FROM {$table}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $tmp = array();
            while($x = $adb->fetchByAssoc($result)){
                $tmp[] = $x['field'];
            }
            $headers = $tmp;//implode(', ', $tmp);
        }

        $dupe_update = "";
        foreach($headers AS $k => $v){
            $dupe_update .= "{$v} = VALUES({$v}), ";
        }
        $dupe_update = rtrim($dupe_update, ", ");
        $questions = generateQuestionMarks($data);

        $query = "INSERT INTO {$table} VALUES({$questions}) ON DUPLICATE KEY UPDATE {$dupe_update}";
        $adb->pquery($query, array($data), true);
    }
}