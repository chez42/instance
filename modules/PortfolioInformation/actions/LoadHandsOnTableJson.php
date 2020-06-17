<?php

class PortfolioInformation_LoadHandsOnTableJson_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        $table = $request->get('table');
        $headers = array();
        $data = array();

        $query = "SHOW COLUMNS FROM {$table}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $tmp = array();
            while($x = $adb->fetchByAssoc($result)){
                $tmp[] = $x['field'];
            }
            $headers = $tmp;//implode(', ', $tmp);
        }

        $query = "SELECT * FROM {$table}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $data[] = array_values($x);
//                $data[] = array(implode(', ', $x));
            }
        }

        $final = array("headers"=>$headers, "data"=>$data);
        echo json_encode($final);
    }
}