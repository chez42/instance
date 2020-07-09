<?php
class TWR{
    public function CalculateTWRCumulative(array $accounts, $start_date, $end_date){
        global $adb;

        $twr = 1;
        $questions = generateQuestionMarks($accounts);
#        print_r($accounts);exit;
        $query = "CALL CALCULATE_INTERVALS_FROM_DAILY_COMBINED(\"{$questions}\", ?, ?)";
        $adb->pquery($query, array($accounts, $start_date, $end_date), true);

        $query = "SELECT * FROM tmpDailyPreTWR";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0){
            while($r = $adb->fetchByAssoc($result)){
                $twr *= $r['netreturnamount'];
            }
            $final_twr = ($twr - 1) * 100;//$adb->query_result($result, 0, 'twr');
        }else{
            $final_twr = 0;
        }

        if($final_twr >= 100 || $final_twr <= -100){
            $final_twr = 0;
        }

        return $final_twr;
    }

    /**
     * Update the portfolio TWR
     * @param $account_number
     * @param $field
     * @param $value
     */
    public function UpdatePortfolioTWR($account_number, $field, $value){
        global $adb;

        $query = "UPDATE vtiger_portfolioinformation p 
                  JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
                  SET {$field} = ? WHERE account_number = ?";
        $adb->pquery($query, array($value, $account_number));
    }

    /**
     * Update the contacts TWR
     * @param $contactid
     * @param $field
     * @param $value
     */
    public function UpdateContactTWR($contactid, $field, $value){
        global $adb;

        $query = "UPDATE vtiger_contactdetails cd
                  SET {$field} = ? WHERE contactid = ?";
        $adb->pquery($query, array($value, $contactid), true);
    }

    /**
     * Update the households TWR
     * @param $accountid
     * @param $field
     * @param $value
     */
    public function UpdateHouseholdTWR($accountid, $field, $value){
        global $adb;

        $query = "UPDATE vtiger_account acc
                  SET {$field} = ? WHERE accountid = ?";
        $adb->pquery($query, array($value, $accountid), true);
    }
}