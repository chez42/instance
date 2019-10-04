<?php

class Historical_Model extends Vtiger_Module {

    public function Historical_Model(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        //Create the historical table
        $query = "CALL HISTORICAL(\"{$questions}\")";
        $adb->pquery($query, array($account_numbers));
    }

    public function GetEndValues($start_date, $end_date){
        global $adb;
        $query = "SELECT AccountNumber, IntervalID, IntervalBeginDate, DATE_FORMAT(IntervalEndDate, '%m-%d-%Y') AS IntervalEndDateFormatted, IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, journalamount, tradeamount 
                  FROM HISTORICAL 
                  WHERE IntervalEndDate 
                  BETWEEN ? AND ?
                  ORDER BY IntervalEndDate ASC";
        $result = $adb->pquery($query, array($start_date, $end_date));
        if($adb->num_rows($result) > 0){
            $tmp = array();
            while($v = $adb->fetchByAssoc($result)){
                $tmp[] = $v;
            }
            return $tmp;
        }
        return 0;
    }

    public function GetEndValuesWithoutDay($start_date, $end_date){
        global $adb;
        $query = "SELECT AccountNumber, IntervalID, IntervalBeginDate, DATE_FORMAT(IntervalEndDate, '%b %Y') AS IntervalEndDateFormatted, IntervalBeginValue, IntervalEndValue, NetFlowAmount, NetReturnAmount, GrossReturnAmount, EntryDate, PriceBeginDate, PriceEndDate, FirstDayFlows, FirstDayGrossFlows, LastModifiedDate, expenseamount, incomeamount, journalamount, tradeamount 
                  FROM HISTORICAL 
                  WHERE IntervalEndDate 
                  BETWEEN ? AND ?
                  ORDER BY IntervalEndDate ASC";
        $result = $adb->pquery($query, array($start_date, $end_date));
        if($adb->num_rows($result) > 0){
            $tmp = array();
            while($v = $adb->fetchByAssoc($result)){
                $tmp[] = $v;
            }
            return $tmp;
        }
        return 0;
    }
}