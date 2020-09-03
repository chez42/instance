<?php
class cCustodianUpdater{
    private $custodian_databsase;

    public function __construct($custodian_database){
        $this->custodian_databsase = $custodian_database;
    }

    public function UpdateTable($table, array $fields, array $values, $where){
        global $adb;
        $set = implode(" = ?, ", $fields);
        $set .= " = ? ";

        $query = "UPDATE {$this->custodian_databsase}.{$table} 
                  SET {$set} 
                  WHERE {$where}";
        $adb->pquery($query, $values, true);
    }
}