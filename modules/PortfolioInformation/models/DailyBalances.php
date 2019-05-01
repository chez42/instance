<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2016-12-28
 * Time: 11:12 AM
 */

include_once("include/utils/omniscientCustom.php");

class PortfolioInformation_DailyBalances_Model extends Vtiger_Module_Model
{
    static $tenant = "custodian_omniscient";

    /**
     *
     * @param $custodian
     * @param $date
     * @param $comparitor
     * @return array|int
     */
    static public function GetBalances($custodian, $date)
    {
        global $adb;
        $tenant = self::$tenant;
        $date_field = '';
        $value_field = '';

        switch($custodian){
            case "fidelity":
                $date_field = 'as_of_date';
                $round = 'ROUND(net_worth, 2) AS net_worth';
                $value_field = 'net_worth';
                break;
            case "schwab":
                $date_field = 'as_of_date';
                $round = 'ROUND(account_value, 2) AS account_value';
                $value_field = 'account_value';
                break;
            case "td":
                $date_field = 'as_of_date';
                $round = 'ROUND(account_value, 2) AS account_value';
                $value_field = 'account_value';
                break;
            case "pershing":
                $date_field = 'date';
                $round = 'CONCAT(net_worth_sign, ROUND(net_worth,2)) AS net_worth';
                $value_field = 'net_worth';
                break;
        }

        $query = "SET SESSION group_concat_max_len = 1000000";
        $adb->pquery($query, array());

        $query = "DROP TABLE IF EXISTS TmpResults";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE TmpResults
                  SELECT account_number, {$round}, {$date_field}
                  FROM custodian_omniscient.custodian_balances_{$custodian} f
                  WHERE f.as_of_date >= ?
                  GROUP BY account_number, {$date_field}
                  ORDER BY account_number, {$date_field}";
        $adb->pquery($query, array($date));

        $query = "SELECT CONCAT('
                  SELECT account_number, ',value_by_dates,'
                  FROM TmpResults
                  GROUP BY account_number
                  ORDER BY account_number'
                    )
                INTO @query  
                FROM
                (
                    SELECT GROUP_CONCAT(CONCAT('IFNULL(MAX(CASE WHEN {$date_field}=''',actual_date,''' THEN {$value_field} END), ''-'') AS \"',col_name,'\"')) value_by_dates
                  FROM (
                      SELECT actual_date, DATE_FORMAT(actual_date,'%a %m/%d/%Y') AS col_name
                    FROM (SELECT DISTINCT {$date_field} AS actual_date FROM TmpResults) AS dates
                  ) dates_with_col_names
                ) result";
        $adb->pquery($query, array());

        $query = "PREPARE statement FROM @query";
        $adb->pquery($query);

        $query = "EXECUTE statement";
        $result = $adb->pquery($query, array());

        $query = "DEALLOCATE PREPARE statement";
        $adb->query($query, array());

        if ($adb->num_rows($result) > 0) {
            $rows = array();
            while($v = $adb->fetchByAssoc($result)) {
                $rows[] = $v;
            }
            return $rows;
        }
        return 0;
    }
}