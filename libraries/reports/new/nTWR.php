<?php

class nTWR{
    static public function GetStoppingDates($account_numbers){
        global $adb;
        $dates = array();
        $questions = generateQuestionMarks($account_numbers);
        $query = "SELECT stop_date FROM twr_interval_totals 
                  WHERE account_number IN ({$questions})
                  GROUP BY stop_date";
        $result = $adb->pquery($query, array($account_numbers));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $dates[] = $v['stop_date'];
            }
            return $dates;
        }
        return 0;
    }
    
    static private function CreateIntervalTables($account_number, $stop_date=null){
        global $adb;

        $query = "DROP TABLE IF EXISTS before_prices;";
        $adb->pquery($query, array());

        if(strlen($trade_date) > 0){
            $and = " AND trade_date >= '{$stop_date}' ";
        }
        $query = "CREATE TEMPORARY TABLE before_prices
        SELECT portfolio_id, symbol_id, SUM(quantity) AS quantity, s.security_price_adjustment, case when (c.code_description is null) then 'unknown' else c.code_description end as code_description, 
        s.security_type_id, security_symbol, activity_id, report_as_type_id, date(t.trade_date) AS trade_date, status_type_id
        FROM vtiger_pc_transactions t
        LEFT JOIN vtiger_securities s ON s.security_id = symbol_id
        LEFT JOIN vtiger_pc_codes c ON c.code_id = 
                (SELECT code_id FROM vtiger_pc_security_codes WHERE security_id = s.security_id AND code_type_id = 20)
        WHERE portfolio_id 
        IN (SELECT portfolio_id FROM vtiger_portfolios WHERE portfolio_account_number = ?)
        AND (activity_id IN (10, 50, 60, 70, 80, 90, 120, 130, 140, 150, 160)
                          OR report_as_type_id IN (60, 70, 130)
                          OR (activity_id = 160 AND report_as_type_id = 80))
        AND t.status_type_id = 100
        {$and}
        GROUP BY trade_date, symbol_id, activity_id, report_as_type_id;";
        $adb->pquery($query, array($account_number));

        $query = "DROP TABLE IF EXISTS after_prices;";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE after_prices
                      SELECT bp.*, pr.price, date(pr.price_date) AS price_date, pr.factor 
                      FROM before_prices bp
                      LEFT JOIN vtiger_pc_security_prices pr ON pr.security_price_id = 
                      (SELECT security_price_id 
                        FROM vtiger_pc_security_prices sp
                        WHERE security_id = bp.symbol_id 
                        AND price_date <= bp.trade_date
                        ORDER BY price_date DESC LIMIT 1);";
        $adb->pquery($query, array());        
    }
    /**
     * Takes a single account number, not multiple.  Trade date is where it will start from (No sense calculating stuff from 3 years ago if not needed)
     * @global type $adb
     * @param type $account_number
     */
    static public function CreateIntervals($account_number, $stop_date=null){
        global $adb;

        self::CreateIntervalTables($account_number, $stop_date);
        $query = "INSERT INTO twr_interval_breakdown
        SELECT *, SUM(value) AS total_value FROM
                        (SELECT *,
                                   CASE WHEN (factor > 0) 
                                                  THEN security_price_adjustment * price * factor * quantity 
                                                WHEN (security_type_id = 11)
                                                  THEN 1*quantity
                                                ELSE security_price_adjustment * price * quantity 
                                                END AS value
                                          FROM after_prices) AS positions
                                          WHERE quantity != 0
                                          GROUP BY trade_date, symbol_id, activity_id, report_as_type_id
        ON DUPLICATE KEY UPDATE price=VALUES(price), total_value=VALUES(total_value), security_type_id=VALUES(security_type_id);";
        $adb->pquery($query, array());

        $query = "INSERT INTO twr_interval_totals
        SELECT p.portfolio_id, p.portfolio_account_number AS account_number, trade_date AS stop_date, SUM(value) AS transactions_value, 0 FROM
                        (SELECT *,
                                   CASE WHEN (factor > 0) 
                                                  THEN security_price_adjustment * price * factor * quantity 
                                                WHEN (security_type_id = 11)
                                                  THEN 1*quantity
                                                ELSE security_price_adjustment * price * quantity 
                                                END AS value
                                          FROM after_prices) AS positions
        JOIN vtiger_portfolios p ON p.portfolio_id = positions.portfolio_id
        WHERE quantity != 0
        GROUP BY trade_date
        ON DUPLICATE KEY UPDATE transactions_value = VALUES(transactions_value);";
        $adb->pquery($query, array());
    }
    
    /**
     * Get the account value as of the specified date
     * @param type $account_number
     * @param type $trade_date
     * @return type
     */
    static public function GetIntervalAccountTotal($account_number, $stop_date){
        $totals = CalculateAssetAllocations($account_number, $stop_date);
        $value = 0;
        foreach($totals AS $k => $v){
            $value += $v['total_value'];
        }
        return $value;
    }
    
    static public function WriteIntervalTotal($account_number, $stop_date, $value){
        global $adb;
        $query = "UPDATE twr_interval_totals SET account_value = ? WHERE account_number = ? AND stop_date = ?";
        $adb->pquery($query, array($value, $account_number, $stop_date));
    }
    
    static public function GetTWRInceptionDateValues($account_number){
        global $adb;
        $query = "SELECT * FROM twr_interval_totals WHERE account_number = ? ORDER BY stop_date ASC LIMIT 1";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result))
            foreach($result AS $k => $v){
                return $v;
            }
        return 0;
    }
    
    static public function GetLatestTWRValuesBeforeDate($account_number, $date){
        global $adb;
        $query = "SELECT * FROM twr_interval_totals WHERE account_number = ? AND stop_date < ? DESC LIMIT 1";
        $result = $adb->pquery($query, array($account_number, $date));
        if($adb->num_rows($result))
            foreach($result AS $k => $v){
                return $v;
            }
        return 0;
    }
    
    static public function GetTWRTotal($account_number, $sdate, $edate){
        global $adb;
        $start_value = self::GetIntervalAccountTotal($account_number, $start_date);
        echo $start_value;exit;
    }
}