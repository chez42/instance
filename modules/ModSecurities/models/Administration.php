<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:16 PM
 */

class ModSecurities_Administration_Model extends Vtiger_Module{
    static $tenant = "custodian_omniscient";

    static public function UpdateFileField($id, $field, $value){
        global $adb;
        $tenant = self::$tenant;
        $query = "UPDATE {$tenant}.file_locations SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateSchwabMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.securities_mapping_schwab SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateFidelityMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.securities_mapping_fidelity SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateTDMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.securities_mapping_td SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function GetTDSecuritiesMapping(){
        global $adb;
        $query = "SELECT id, code, description, omni_base_asset_class, security_type, security_type2, domestic_international, style, size_capitalization, multiplier 
                  FROM securities_mapping_td";
        $result = $adb->pquery($query, array());
        $rows = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rows[] = $v;
            }
            return $rows;
        }
        return null;
    }

    static public function GetFidelitySecuritiesMapping(){
        global $adb;
        $query = "SELECT id, type, description, asset_class_code, asset_class_type_code, omni_base_asset_class, security_type, security_type2, domestic_international, style, size_capitalization, multiplier 
                  FROM securities_mapping_fidelity";
        $result = $adb->pquery($query, array());
        $rows = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rows[] = $v;
            }
            return $rows;
        }
        return null;
    }

    static public function GetSchwabSecuritiesMapping(){
        global $adb;
        $query = "SELECT id, code, description, omni_base_asset_class, security_type, security_type2, domestic_international, style, size_capitalization, multiplier 
                  FROM securities_mapping_schwab";
        $result = $adb->pquery($query, array());
        $rows = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $rows[] = $v;
            }
            return $rows;
        }
        return null;
    }

    static public function CreateRow($table, $usetenant = false){
        global $adb;
        $tenant = self::$tenant;
        if($usetenant)
            $table = $tenant + "." + $table;
        $query = "INSERT INTO {$table} VALUES ()";
        $adb->pquery($query, array());
    }

    static public function GetMaxID($table, $usetenant = false){
        global $adb;
        $tenant = self::$tenant;
        if($usetenant)
            $table = $tenant + "." + $table;
        $query = "SELECT MAX(id) AS id FROM {$table}";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'id');
    }
}