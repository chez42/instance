<?php

/*
 * This is the updated class to handle Holdings Information
 */

require_once("libraries/reports/cPortfolioInfo.php");

class cPholdingsv2 extends cPortfolioInfo
{
    public function CalculateTotalValues($totals, $edate=null, $noflow=false)
    {
        $order = array();
        foreach($totals AS $a => $b)
            foreach($b AS $k => $v)
            {
                if($v['trade_date'] <= $edate || $edate == null)
                {
                    $calculate = 1;
                    if($noflow == true)
                    {
                        if($v['symbol'] == "CASH")
                        {
                            $calculate = 0;
                        }
                        else
                            $calculate = 1;
                    }
                    else $calculate = 1;
                    if($calculate)
                    {
//                    echo $k . ", Quantity: {$v['quantity']}, Price: {$v['price']}<br />";
                        $value = $v['quantity'] * $v['price'];// * $v['price_adjustment'];   No longer multiply price_adjustment because we pull that in with the price query
                        if($value && $v['cost_basis'] >= 0)//Alteration to require a positive cost basis, otherwise LVS-240 kicks in
                        {
                            $totals[$a][$k]['value'] = $value;
                            $totals[$a][$k]['ugl'] = $value - $v['cost_basis'];
                            if($v['cost_basis'])
                                $totals[$a][$k]['gl'] = $totals[$a][$k]['ugl'] / $v['cost_basis'] * 100;
                            else
                                $totals[$a][$k]['gl'] = 0;

                            if($v['quantity'] >= 1)
                            {
                                $order[$v['code_description']]['value'] += $value;
                                $order[$v['code_description']]['cost_basis'] += $v['cost_basis'];
                                $order[$v['code_description']]['ugl'] += $totals[$a][$k]['ugl'];
                                $order[$v['code_description']]['gl'] += $totals[$a][$k]['gl'];
                                $order[$v['code_description']]['account_number'] = $v['account'];
                                
//                                echo "TRADE DATE: {$v['trade_date']} -- SYMBOL: {$v['symbol']}, VALUE: {$value}<br />";
                            }
                        }
                    }
                }
            }
        $totals['subtotals'] = $order;
        $temp = array();
        foreach($order AS $k => $v)
        {
            $temp['grand_totals']['value'] += $v['value'];
            $temp['grand_totals']['cost_basis'] += $v['cost_basis'];
            $temp['grand_totals']['ugl'] += $v['ugl'];
            $temp['grand_totals']['gl'] += $v['gl'];
            $temp['grand_totals']['weight'] = 100;
            if($k != "Cash" && $v['security_type_id'] != 11)
                $temp['grand_totals']['market_value'] += $v['value'];
            if($k == "Cash" || $v['security_type_id'] == 11)
                $temp['grand_totals']['cash_value'] += $v['value'];
        }
//        echo "<br><strong>GT VALUE FOR {$edate}: {$temp['grand_totals']['value']}</strong><br />";
        if($temp['grand_totals']['cost_basis'])
            $temp['grand_totals']['gl'] = $temp['grand_totals']['ugl'] / $temp['grand_totals']['cost_basis'] * 100;
        $totals['grand_totals'] = $temp;
        
        foreach($totals AS $a => $b)
            foreach($b AS $k => $v)
                if($temp['grand_totals']['cost_basis'])
                    $totals[$a][$k]['weight'] = $totals[$a][$k]['value'] / $temp['grand_totals']['value'] * 100;

        return $totals;
    }
    
    /**Separete the totals into categoreis**/
    public function TotalsToCategories($totals)
    {
        $categories = array();
        foreach($totals['subtotals'] AS $category => $cat_value)
        {
            if($category != "subtotals" && $category != "grand_totals")
                foreach($totals AS $account => $account_array)
                    foreach($account_array AS $k => $v)
                    {
                        if($v['code_description'] == $category)
                            if($v['value'] >= 0 && $v['quantity'] >= 1)
                            {
                                $v['security_type'] = $category;
                                $categories[$category][] = $v;
                            }
                    }
        }

        if($searchtype || $searchcontent)
        {
            $tmp = array();
            foreach($categories AS $k => $v)
                foreach($v AS $a => $b)
                {
                    if($searchtype == "security_symbol")
                        if(stripos($b['symbol'], $searchcontent) !== false)
                           $tmp[$k][$a] = $b;

                    if($searchtype == "security_description")
        //                echo "DESC: {$b['security_description']}, SEARCH: {$searchcontent}<br />";
                        if(stripos($b['security_description'], $searchcontent) !== false)
                           $tmp[$k][$a] = $b;
                }

            $categories = $tmp;
        }
        
        return $categories;
    }
    
    /**Get account info based on account(s) array... $accounts is to be passed in as an array -- [0]=>144555, [1]=>339393, etc**/
    public function GetAccountInfo($accounts, $totals)
    {
        global $adb;
        foreach($accounts AS $k => $v)
        {
            $result = GetPortfolioInfoFromPortfolioAccountNumber($v);
            if($adb->query_result($result, 0, "portfolio_id"))
            {
                $acct_name = $adb->query_result($result, 0, "portfolio_account_name");
                $management_fee = $adb->query_result($result, 0, "advisor_fee");
                $acct_number = $v;
                $acct_type = $adb->query_result($result, 0, "portfolio_account_type");
                if(strlen($acct_type) <= 1)
                    $acct_type = "";
                if(!$acct_type)
                    $acct_type = "Undefined";
                $acct_total = $totals['grand_totals']['grand_totals']['value'];
                $market_value = $totals['grand_totals']['grand_totals']['market_value'];
                $cash_value = $totals['grand_totals']['grand_totals']['cash_value'];
//                $annual_fee = $investments['inception']['management_fees'];
                if($annual_fee <= 0)
                    $annual_fee *= -1;

                $query = "SELECT origination FROM vtiger_portfolio_summary WHERE account_number = ?";
                $r = $adb->pquery($query, array($acct_number));
                if($r)
                    $origination = $adb->query_result($r, 0, "origination");
                $account_info = array("name"=>$acct_name,
                                      "number"=>$acct_number,
                                      "type"=>$acct_type,
                                      "total"=>$acct_total,
                                      "market_value"=>$market_value,
                                      "cash_value"=>$cash_value,
                                      "management_fee"=>$management_fee,
                                      "annual_fee"=>$annual_fee,
                                      "custodian"=>$origination
                                      );
                $other_totals["value"] += $acct_total;
                $other_totals["market_value"] += $market_value;
                $other_totals["cash_value"] += $cash_value;
            }
        }
        return $account_info;
    }
    /**Calculate the account totals for each account*/
    public function CalculateAccountTotals($totals)
    {
        global $adb;
        $order = array();
        foreach($totals AS $account_number => $b)
        {
            foreach($b AS $security => $v)
            {
                $value = $v['quantity'] * $v['price'];// * $v['price_adjustment'];
                if($value != 0)//Setting value to require higher than 0 prevents negative accounts from showing up
                {
                    $totals[$account_number][$security]['value'] = $value;
                    $totals[$account_number][$security]['ugl'] = $value - $v['cost_basis'];
                    if($v['cost_basis'])
                        $totals[$account_number][$security]['gl'] = $totals[$account_number][$security]['ugl'] / $v['cost_basis'] * 100;
                    else
                        $totals[$account_number][$security]['gl'] = 0;
                }
            }
        }
        $accounts = array();
        foreach($totals AS $k => $v)
        {
            foreach($v AS $a => $b)
            {
                $accounts[$k]['value'] += $b['value'];
                if($a != "CASH" && $b['security_type_id'] != 11)
                {
                    $accounts[$k]['market_value'] += $b['value'];
                }
                if($a == "CASH" || $b['security_type_id'] == 11)//May want to test changing this by getting rid of $a == "CASH" and dealing directly with security type
                {
                    $accounts[$k]['cash_value'] += $b['value'];
                }
            }
        }
        
        foreach($accounts AS $k => $v)
        {
            $result = GetPortfolioInfoFromPortfolioAccountNumber($k);
            $acct_name = $adb->query_result($result, 0, "portfolio_account_name");
            $fname = $adb->query_result($result, 0, "portfolio_first_name");
            $lname = $adb->query_result($result, 0, "portfolio_last_name");
            $management_fee = $adb->query_result($result, 0, "advisor_fee");
            $acct_number = $acct;
            $acct_type = $adb->query_result($result, 0, "portfolio_account_type");
            $nickname = $adb->query_result($result, 0, "nickname");
            if(strlen($acct_type) <= 1)
                $acct_type = "";
            if(!$acct_type)
                $acct_type = "Undefined";
            
            $accounts[$k]["name"] = $acct_name;
            $accounts[$k]["firstname"] = $fname;
            $accounts[$k]["lastname"] = $lname;
            $accounts[$k]["type"] = $acct_type;
            $accounts[$k]["nickname"] = $nickname;
        }
        
        return $accounts;
    }    
    /*Sort Symbol Values by code description*/
//    public function 
}

/*
SYMBOL: CASH, VALUE: 4902.71 (8,912.19 ) 8934.58
SYMBOL: DEW, VALUE: 10420.000076294 (9,827.50 ) 9827.50
SYMBOL: DIM, VALUE: 9618.399810791 (8,610.00 ) 8609.99
SYMBOL: ROI, VALUE: 12300 (10,898.10 ) 10898.09
SYMBOL: DEM, VALUE: 10369.800109863 (9,228.60 ) 9228.60
SYMBOL: DES, VALUE: 7854.4000244141 (7,182.40 ) 7182.39
SYMBOL: PSA, VALUE: 69084.999084472  (67,230.00 ) 67230.00
SYMBOL: SDY, VALUE: 11327.99987793 (10,774.00 ) 10773.39
SYMBOL: XLK, VALUE: 6031.9999694824  (5,090.00 ) 5090.00
SYMBOL: HAL, VALUE: 3318.9998626709 (3,451.00 ) 3450.99
SYMBOL: XLP, VALUE: 8860.8004760742 (8,447.40 ) 8447.40
SYMBOL: QQQ, VALUE: 9457.0004272461 (7,816.20 ) 7816.20
SYMBOL: VWO, VALUE: 4347.0001220703 ( ) 
SYMBOL: PFF, VALUE: 9760.0002288818 (8,905.00 ) 8904.99
SYMBOL: JPM, VALUE: 4597.9999542236  (3,325.00 ) 3325.00
SYMBOL: SHRAX, VALUE: 15465.881737816 (13,521.11 ) 13521.10
FINAL TOTAL: 197717.99176223 

 */

?>
