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

    static public function RemoveReconciledTransactions(){
        global $adb;
        $query = "DELETE FROM vtiger_crmentity 
                  WHERE crmid IN (SELECT t.transactionsid 
                                  FROM vtiger_transactionscf tcf 
                                  JOIN vtiger_transactions t USING (transactionsid)
                                  WHERE tcf.description LIKE ('%System Generated Transaction%') OR t.security_symbol = 'CRMRECON')";
        $adb->pquery($query, array());
        $query = "SELECT ROW_COUNT() as DelRowCount";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, "delrowcount");
        }
        return 0;
    }
}