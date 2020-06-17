<?php

class PortfolioInformation_HistoricalInformation_Model extends Vtiger_Module{
    private static function GetQuestions($account_numbers){
        if(is_array($account_numbers)){
            $questions = generateQuestionMarks($account_numbers);
        }
        else{
            $account_numbers = array($account_numbers);//Make it into an array
            $questions = generateQuestionMarks($account_numbers);            
        }        
        return $questions;
    }
    
    public static function GetAssetPie($account_numbers){
        global $adb;
        $questions = self::GetQuestions($account_numbers);
        $query = "SELECT ROUND(SUM(market_value),2) AS market_value, ROUND(SUM(equities),2) AS equities, ROUND(SUM(fixed_income),2) AS fixed_income, ROUND(SUM(cash_value),2) AS cash,
						 ROUND(SUM(securities),2) AS securities
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                  WHERE p.account_number IN ({$questions})";
        $result = $adb->pquery($query, array($account_numbers));

        if($adb->num_rows($result) > 0){
            if($adb->query_result($result, 0, 'equities') == 0 && $adb->query_result($result, 0, 'fixed_income') == 0)
                $values['Equities'] = $adb->query_result($result, 0, 'market_value');
            else
                $values['Equities'] = $adb->query_result($result, 0, 'securities');
            $values['Cash'] = $adb->query_result($result, 0, 'cash');
            $values['Fixed Income'] = $adb->query_result($result, 0, 'fixed_income');
        }
        else{
            $values['Equities'] = 0;
            $values['Cash'] = 0;
            $values['Fixed Income'] = 0;
        }
        return $values;
    }
    
    public static function GetTrailing12Revenue($account_numbers){
        global $adb;
        $questions = self::GetQuestions($account_numbers);

        $query = "SELECT *, DATE_FORMAT(combined_date,'%b') AS month_name FROM (SELECT month, year, SUM(expense_amount) AS expense_amount,
                  DATE(CONCAT(year, '-', month, '-', '01')) AS combined_date 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_portfolioinformationcf cf ON p.portfolioinformationid = cf.portfolioinformationid
                  JOIN vtiger_portfolioinformation_fees vpif ON vpif.account_number = p.account_number
                  WHERE p.account_number IN ({$questions})
                  GROUP BY month, year) AS t1
                  WHERE t1.combined_date between CAST(DATE_FORMAT(NOW()-interval 1 year,'%Y-%m-01') as DATE) AND NOW()-interval 1 month ORDER BY year, month ASC";
        $result = $adb->pquery($query, array($account_numbers));
        
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $values[] = array('date'=>$v['month_name'] . ' (' . $v['year'] . ')',
                                  'value'=>abs($v['expense_amount']));
            }
        }
        else{
            $values[] = array('date'=>0,
                              'value'=>0);
        }
        return $values;
    }
    
    public static function GetTrailing12AUM($account_numbers){
        global $adb;
        $questions = self::GetQuestions($account_numbers);

        $query = "SELECT DATE_FORMAT(date, '%b') AS date, SUM(vpih.market_value) AS market_value, 
                         SUM(vpih.cash_value) AS cash_value, SUM(vpih.fixed_income) AS fixed_income, SUM(vpih.equities) AS equities,
                         SUM(vpih.total_value) AS total_value
                         FROM vtiger_portfolioinformation_historical vpih 
                         WHERE account_number IN ({$questions})
                         AND vpih.date between NOW()-interval 1 year AND LAST_DAY(NOW()-interval 1 month)
                         GROUP BY date";

        $result = $adb->pquery($query, array($account_numbers));
        if($adb->num_rows($result) == 1){
            $values[] = array('date'=>0,
                              'market_value' => 0,
                              'cash_value' => 0,
                              'fixed_income' => 0,
                              'equities' => 0,
                              'value'=>0);
        }
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $values[] = array('date'=>$v['date'],
                                  'market_value' => $v['market_value'],
                                  'cash_value' => $v['cash_value'],
                                  'fixed_income' => $v['fixed_income'],
                                  'equities' => $v['equities'],
                                  'value'=>$v['total_value']);
            }
        }
        else{
            $values[] = array('date'=>0,
                              'market_value' => $v['market_value'],
                              'cash_value' => 0,
                              'fixed_income' => 0,
                              'equities' => 0,
                              'value'=>0);
        }
        
        return $values;
    }

    static public function GetConsolidatedBalances($accounts, $start_date, $end_date){
        global $adb;

        $questions = generateQuestionMarks($accounts);
        $params = array();
        $params[] = $start_date;
        $params[] = $end_date;
        $params[] = $accounts;

        $query = "SELECT SUM(account_value) AS total_value, CONCAT(as_of_date, 'T10:00:01') AS as_of_date 
                  FROM consolidated_balances 
                  WHERE as_of_date BETWEEN ? AND ? AND account_number IN ({$questions})
                  GROUP BY as_of_date";
        $result = $adb->pquery($query, $params);
        if($adb->num_rows($result) > 0){
            $totals = array();
            while($v = $adb->fetchByAssoc($result)) {
                $totals[] = array("value" => $v['total_value'],
                                  "date" => $v['as_of_date']);
            }
            return $totals;
        }
        return 0;
    }

    static public function TransferConsolidatedBalancesFromCloud($start_date, $end_date){
        global $adb;
        $query = "CALL CONSOLIDATE_BALANCES(?, ?)";
        $adb->pquery($query, array($start_date, $end_date));
    }
}