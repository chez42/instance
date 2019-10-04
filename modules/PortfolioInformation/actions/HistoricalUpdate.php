<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 1816694
 */

include_once('libraries/reports/new/nCommon.php');
include_once('include/utils/omniscientCustom.php');

if (ob_get_level() == 0) ob_start();

class PortfolioInformation_HistoricalUpdate_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        set_time_limit (0);
        
        set_time_limit (120);
    }
    
    public function UpdateAllHistoricalAccounts($from_date){
        global $adb;
        $query = "UPDATE batch_process SET maximum_id = (SELECT MAX(crmid) FROM vtiger_crmentity WHERE setype='PortfolioInformation')";
        $result = $adb->pquery($query, array());
        $query = "SELECT last_update_id, maximum_id FROM batch_process WHERE name='HistoricalPrices'";
        $result = $adb->pquery($query, array());
        $last_updated_id = $adb->query_result($result, 0, 'last_update_id');
        $maximum_id = $adb->query_result($result, 0, 'maximum_id');
        
        $query = "SELECT e.crmid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE e.deleted = 0
                  AND e.crmid >= ?";

        $result = $adb->pquery($query, array($last_updated_id));
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $crmid = $v['crmid'];
//                echo "ABOUT TO UPDATE: {$v['crmid']}<br />";
                $this->HistoricalUpdateIndividualAccount($crmid, $from_date);
                $adb->pquery("UPDATE batch_process SET last_update_id = ? WHERE name='HistoricalPrices'", array($crmid));
            }
        }
        $result = null;
    }
    
    public function HistoricalUpdateIndividualAccount($crmid, $from_date=null){
        global $adb;
        $record = Vtiger_Record_Model::getInstanceById($crmid, 'PortfolioInformation');
        $account_number = $record->get('account_number');
        if(strlen($from_date) == 0)
            $from_date = GetInceptionDate($account_number);        
        $months = GetMonthsBetween($from_date, date('Y-m-d'));
        foreach($months AS $unused => $date){
            $fixed_income = 0;
            $market_value = 0;
            $cash_value = 0;
            $other = 0;

            $assets = CalculateAssetAllocations($record->get('account_number'), $date);
            if($assets != 0){
                $cash_value = $assets['Cash']['total_value'];
                unset($assets['Cash']);
                $fixed_income = $assets['Fixed Income']['total_value'];
                unset($assets['Fixed Income']);
                $other = 0;
                foreach($assets AS $k => $v){
                    if(strcasecmp($v['security_symbol'], "Cash") == 0 || $v['security_type_id'] == 11){
                        $cash_value += $v['total_value'];
                        unset($assets[$k]);
                    }
                    else
                        $other+=$v['total_value'];
                    $description .= $k . ", ";
                }

                $market_value = $fixed_income + $other;
                $message = "Finished";
            }
            else
                $message = "Nothing to calculate";

            if(!$fixed_income)
                $fixed_income = 0;
            if(!$market_value)
                $market_value = 0;
            if(!$cash_value)
                $cash_value = 0;
            if(!$other)
                $other = 0;
            $historical_values[] = array("date"=>$date,
                                       "market_value"=>$market_value,
                                       "cash_value"=>$cash_value,
                                       "fixed_income"=>$fixed_income,
                                       "equities"=>$other,
                                       "total_value"=>$market_value+$cash_value,
                                       "descriptions"=>$description,
                                       "message"=>$message);
        }

        $query = "INSERT INTO vtiger_portfolioinformation_historical 
                  (date, account_number, market_value, cash_value, fixed_income, equities, total_value, last_updated)
                  VALUES ";
        $duplicate = " ON DUPLICATE KEY UPDATE market_value=VALUES(market_value), cash_value=VALUES(cash_value),
                       fixed_income=VALUES(fixed_income), equities=VALUES(equities), total_value=VALUES(total_value), last_updated=NOW()";
        $count = 0;
        foreach($historical_values AS $k => $v){
            $insert .= "('{$v['date']}', '{$account_number}', {$v['market_value']}, {$v['cash_value']},
                         {$v['fixed_income']}, {$v['equities']}, {$v['total_value']}, NOW())";
            $count++;
            if($count < sizeof($historical_values)){//If we need to reset, don't add a comma
                $insert .= ",";
            }            
        }
        $query .= $insert . $duplicate;
        
        $adb->pquery($query, array());
    }
}
