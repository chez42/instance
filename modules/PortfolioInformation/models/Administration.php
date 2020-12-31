<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-05-24
 * Time: 3:16 PM
 */

class PortfolioInformation_Administration_Model extends Vtiger_Module{
    static $tenant = "custodian_omniscient";

    static public function GetFileLocations(){
        global $adb;
        $tenant = self::$tenant;
        $query = "SELECT id, custodian, tenant, file_location, rep_code, master_rep_code, omni_code, prefix, suffix FROM {$tenant}.file_locations";
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

    static public function UpdateFileField($id, $field, $value){
        global $adb;
        $tenant = self::$tenant;
        $query = "UPDATE {$tenant}.file_locations SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE vtiger_transaction_type_mapping SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateSchwabMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.schwabmapping SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateFidelityMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.fidelitymapping SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdatePershingMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.pershingmapping SET {$field} = ? WHERE source_code = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateTDMappingField($id, $field, $value){
        global $adb;
        $query = "UPDATE custodian_omniscient.tdmapping SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function UpdateFidelityCashFlowMappingField($id, $field, $value){
        global $adb;
        $tenant = self::$tenant;
        $query = "UPDATE {$tenant}.fidelity_transaction_keys SET {$field} = ? WHERE id = ?";
        $adb->pquery($query, array($value, $id));
    }

    static public function GetTransactionMapping(){
        global $adb;
        $tenant = self::$tenant;
        $query = "SELECT id, transaction_type, transaction_activity, report_as_type, td, fidelity, schwab, pershing, pc FROM vtiger_transaction_type_mapping ORDER BY transaction_type ASC";
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

    static public function GetSchwabTransactionMapping(){
        global $adb;
        $tenant = self::$tenant;
        $query = "SELECT id, source_code, type_code, subtype_code, direction, transaction_activity, schwab_category, omniscient_category, omniscient_activity, operation FROM custodian_omniscient.schwabmapping ORDER BY ISNULL(schwab_category), source_code";
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

    static public function GetTDTransactionMapping(){
        global $adb;
        $query = "SELECT id, transaction_type, transaction_activity, omniscient_category, omniscient_activity, operation FROM custodian_omniscient.tdmapping";
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

    static public function GetFidelityTransactionMapping(){
        global $adb;
        $query = "SELECT id, description, code_description, omniscient_category, omniscient_activity, operation FROM custodian_omniscient.fidelitymapping ORDER BY id";
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

    static public function GetPershingTransactionMapping(){
        global $adb;
        $query = "SELECT source_code, description, omniscient_category, omniscient_activity, operation FROM custodian_omniscient.pershingmapping";
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

    static public function GetFidelityCashFlowMapping(){
        global $adb;
        $tenant = self::$tenant;
        $query = "SELECT id, transaction_key, description, category FROM {$tenant}.fidelity_transaction_keys";
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