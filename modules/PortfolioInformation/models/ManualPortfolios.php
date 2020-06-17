<?php

class PortfolioInformation_ManualPortfolios_Model extends Vtiger_Module{
    static public function GetPortfolioValuesFromParentInfo($parent_module, $parent_id, &$totals){
        global $adb;
        if($parent_module == 'household')
            $query = "SELECT account_number, total_value, market_value, cash_value
                      FROM vtiger_portfolioinformation p
                      JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                      WHERE household_account = ?
                      AND e.deleted = 0 AND p.accountclosed = 0";
        else
            $query = "SELECT account_number, total_value, market_value, cash_value FROM vtiger_portfolioinformation WHERE contact_link = ?";
        
        $result = $adb->pquery($query, array($parent_id));
        $values = array();
        $v['total_value'] = 0;
        $v['market_value'] = 0;
        $v['cash_value'] = 0;
        $total_value = 0;
        $market_value = 0;
        $cash_value = 0;
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $values[] = array("account_number" => $v['account_number'],
                                  "total_value" => $v['total_value'],
                                  "market_value" => $v['market_value'],
                                  "cash_value" => $v['cash_value']);
                $total_value  += $v['total_value'];
                $market_value += $v['market_value'];
                $cash_value   += $v['cash_value'];
            }
            $totals = array('total_value' => $total_value,
                            'market_value' => $market_value,
                            'cash_value' => $cash_value);
            return $values;
        }
        return 0;
    }
}