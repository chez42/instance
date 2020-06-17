<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-06-13
 * Time: 3:23 PM
 */

class PortfolioInformation_MonthOverMonth_Model extends Vtiger_Module {
    /**
     * Generates the Month over Month table from the passed in account numbers
     * @param $accounts
     */
    static public function GenerateMonthOverMonthTable($accounts, $inType){
        global $adb;
        $questions = generateQuestionMarks($accounts);
        $query = "CALL MonthOverMonth(\"{$questions}\", \"?\")";
        $adb->pquery($query, array($accounts, $inType));

        $query = "SELECT * FROM MonthOverMonthTotals GROUP BY trade_date ORDER BY trade_date ASC";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $tmp = array();
            while($v = $adb->fetchByAssoc($result)){
                $tmp[] = $v;
            }
            return $tmp;
        }
        return 0;
    }

    static public function GetMonthOverMonthYears(){
        global $adb;
        $query = "SELECT year FROM MonthOverMonthTotals GROUP BY year ORDER BY year ASC;";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $years = array();
            while($v = $adb->fetchByAssoc($result)){
                $years[] = $v['year'];
            }
            return $years;
        }
        return 0;
    }

    static public function GetMonthEndPrices($symbol){
        global $adb;
        $query = "SELECT date, DATE_FORMAT(date, '%Y') AS year, DATE_FORMAT(date, '%c') AS month, close FROM vtiger_prices_index
                  WHERE date IN (select MAX(date)
                                 FROM vtiger_prices_index
                                 WHERE symbol = ?
                                 GROUP BY YEAR(date), MONTH(date))
                  AND symbol = ?";
        $result = $adb->pquery($query, array($symbol, $symbol));
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $prices[$v['year']][$v['month']] = $v;
            }
            return $prices;
        }
        return 0;
    }
}
