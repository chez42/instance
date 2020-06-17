<?php

class Vtiger_Billing_Model extends Vtiger_Base_Model {

    static public function PeriodicityToNumber($periodicity){
        switch (strtolower($periodicity)) {
            case "monthly":
                $periodicity = 1;
                break;
            case "quarterly":
                $periodicity = 3;
                break;
            default:
                $periodicity = 1;
                break;
        }
        return $periodicity;
    }

    static public function CalculateArrearBilling($account_number, $start_date, $end_date, $percentage, $periodicity, &$extra)
    {
        global $adb;
        $extra = array();

        $query = "CALL ARREAR_BILLING(?, ?, ?, ?, ?, @billAmount)";

        switch (strtolower($periodicity)) {
            case "monthly":
                $periodicity = 1;
                break;
            case "quarterly":
                $periodicity = 3;
                break;
            default:
                $periodicity = 1;
                break;
        }
        $adb->pquery($query, array($account_number, $start_date, $end_date, $percentage, $periodicity));

        $extra = self::GetLatestDateRowFromArrearTable();

        $query = "SELECT @billAmount AS billamount";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, "billamount");
        return 0;
    }

    static public function CalculateAdvanceBilling($account_number, $percentage, $periodicity)
    {
        global $adb;

        $query = "CALL ADVANCE_BILLING(?, ?, ?, @billAmount)";
        switch (strtolower($periodicity)) {
            case "monthly":
                $periodicity = 1;
                break;
            case "quarterly":
                $periodicity = 3;
                break;
            default:
                $periodicity = 1;
                break;
        }
        $adb->pquery($query, array($account_number, $percentage, $periodicity));

        $query = "SELECT @billAmount AS billamount";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, "billamount");
        return 0;
    }

    static public function GetLatestDateRowFromArrearTable(){
        global $adb;
        $query = "SELECT * FROM ARREAR_BILLING ORDER BY IntervalEndDate DESC LIMIT 1";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            return $adb->fetchByAssoc($result);
        }
    }
}