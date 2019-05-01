<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-09-13
 * Time: 3:33 PM
 */


class Omniscient_CustodianInteractions_Model extends Vtiger_Module_Model {
    public function __construct() {
        parent::__construct();
    }

    static public function GetPositionCustodianFieldName($custodian, $omni_field_name){
        global $adb;
        $query = "SELECT custodian_field FROM omni_position_table_mapping WHERE omni_field = ? AND custodian = ?";
        $result = $adb->pquery($query, array($omni_field_name, $custodian));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result,0, "custodian_field");
        }
        return 0;
    }
}