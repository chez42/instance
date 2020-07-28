<?php

class OmnisolReader{
    static public function DoesFieldDataExist($field_name, $table_name, $value){
        global $adb;
        $query = "SELECT {$field_name} FROM {$table_name} WHERE {$field_name} = ?";
        $result = $adb->pquery($query, array($value));
        if($adb->num_rows($result) > 0){
            return true;
        }
        return false;
    }

}