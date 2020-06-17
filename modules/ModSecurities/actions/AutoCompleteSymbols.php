<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-29
 * Time: 2:14 PM
 */

class ModSecurities_AutoCompleteSymbols_Action extends Vtiger_BasicAjax_Action{

    static public function GetSymbolsStartingWith($chars){
        global $adb;
        $like = $chars . "%";
        $query = "SELECT TRIM(security_symbol) AS security_symbol FROM vtiger_modsecurities WHERE security_symbol LIKE (?)";
        $result = $adb->pquery($query, array($like));
        $returns = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)) {
                $returns[] = $v['security_symbol'];
            }
            return $returns;
        }
        return array();
    }

    static public function GetSymbolContaining($chars){
        global $adb;
        $like = "%" . $chars . "%";
        $query = "SELECT TRIM(security_symbol) AS security_symbol FROM vtiger_modsecurities WHERE security_symbol LIKE (?)";
        $result = $adb->pquery($query, array($like));
        $returns = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)) {
                $returns[] = $v['security_symbol'];
            }
            return $returns;
        }
        return array();
    }

    public function process(Vtiger_Request $request) {
        $formatted = self::GetSymbolsStartingWith($request->get('term'));
        echo json_encode($formatted);
    }
}