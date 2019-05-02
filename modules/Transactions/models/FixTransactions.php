<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-04-17
 * Time: 1:57 PM
 */

class Transactions_FixTransactions_Model extends Vtiger_Module_Model{
    public static function GetSecurityInfo($security){
        $id = ModSecurities_Module_Model::GetModSecuritiesIdBySymbol($security);
        $record = ModSecurities_Record_Model::getInstanceById($id);
        return $record->getData();
    }
}