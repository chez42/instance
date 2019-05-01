<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cPOverview
 *
 * @author Ryan Sandnes
 */

/*
 * IMPORTANT!!
 * Some functions are using a portfolio override.  The original implementation was working purely 
 * with account ID's and then determining what portfolios belonged to those.  This override is being
 * implemented for when we want to specify specific Portfolio ID's instead of getting them based on account.
 * In the future, it may be a much better idea to remove the account as a requirement and have the ID's passed in
 * only.
 */

require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/cPPerformance.php");

class cPOverview extends cPPerformance {
    public function __construct() {
        parent::__construct();
    }
    
    public function GetWithdrawInfo($portfolioIds, $startDate, $endDate, $inception = 0)
    {
        global $adb;///18971
        
        $query = "SELECT * FROM vtiger_pc_transactions 
                  WHERE portfolio_id IN ({$portfolioIds})
                  AND (activity_id IN (10, 120, 50)
                                       OR (activity_id IN (160) AND report_as_type_id IN (80) ) )
                  AND trade_date between ? AND ?";
        $result = $adb->pquery($query, array($startDate, $endDate));
//        echo "NET AMOUNT STUFF<br />";
        $negatives = array();
        $positives = array();
        foreach($result AS $k => $v)
        {
            if($v['quantity'] < 0)//We use quantity because that is universal to flow and transfer of securities
                $negatives[] = array("pid"=>$v['portfolio_id'],
                                     "total_value"=>$v['total_value'],
                                     "amount"=>$v['net_amount'],
                                     "date"=>$v['trade_date']);
            else
            if($v['quantity'] > 0)
                $positives[] = array("pid"=>$v['portfolio_id'],
                                     "total_value"=>$v['total_value'],
                                     "amount"=>$v['net_amount'],
                                     "date"=>$v['trade_date']);
        }
        $counter = 0;
        $pcounter = 0;
        $pback = $positives;
        foreach($negatives AS $k => $v)
        {
//            echo "AMOUNT: " . $v['amount'] . " --- TOTAL VALUE: " . $v['total_value'] . "<br />";
            foreach($positives AS $a => $b)
            {
                if($v['amount'] != 0 && ($v['pid'] != $b['pid']) && ( ($v['amount']*-1 == $b['amount']) && ($v['trade_date'] == $b['trade_date'])))
                {
                    $pback[$a] = null;
                    $negatives[$counter] = null;
                }
                
                if($v['total_value'] != 0)
////                    echo "V TOTAL VALUE: " . $v['total_value'] . ", B TOTAL VALUE: " . $b['total_value'] . "<br />";
                    if(($v['total_value'] == $b['total_value']) && ($v['trade_date'] == $b['trade_date']))
                    {
//                        echo "MATCH!<br />";
                        $negatives[$counter] = null;
                        $pback[$a] = null;
                    }
                $pcounter++;
            }
            $counter++;
            $pcounter = 0;
        }
        $positives = $pback;
        $total = 0;
        $totalp = 0;
        foreach($negatives AS $k => $v)
        {
//            echo "NEGATIVE AMOUNT: " . $v['amount'] . ", TOTAL VALUE: " . $v['total_value'] . "<br />";
            $total += $v['amount'] - $v['total_value'];
        }
        foreach($positives AS $k => $v)
        {
//            echo "POSITIVE AMOUNT: " . $v['amount'] . ", TOTAL VALUE: " . $v['total_value'] . "<br />";
            $totalp += $v['amount'] + $v['total_value'];
        }
        
        return array("withdrawals"=>$total, 
                     "contributions"=>$totalp);
//        return $total;
/*        $query = "SELECT *, SUM(quantity) AS quan
                FROM `v_UserPortfolioTransactions`
                WHERE portfolio_id
                IN ( {$portfolioIds} )
                AND (transactiontype = 'Flow'
                OR transactiontype = 'Transfer of Securities')
                AND (quantity < 0)
                AND trade_date between ? AND ?";
 /*       $query = "SELECT *, SUM(net_amount) AS quan
                  FROM vtiger_pc_transactions
                  WHERE Portfolio_ID IN (309, 444, 17873, 19724, 19725)
                  AND trade_date BETWEEN ? AND ?";*/
/*        if(!$inception)
        {
            $query = "SELECT *, SUM(cost_basis_adjustment - total_value) AS quan 
                    FROM v_UserPortfolioTransactions
                    WHERE portfolio_id 
                    IN ( {$portfolioIds} ) 
                    AND (transactiontype IN ('Flow', 'Transfer of Securities', 'Federal Withholding') 
                         )
                    AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                    AND transactiondescription NOT REGEXP('funds moved')
                    AND trade_date between ? AND ?";
/*
SELECT *, SUM(net_amount) AS NA, SUM(cost_basis_adjustment) AS CBA, SUM(total_value) AS TV
FROM v_UserPortfolioTransactions
WHERE portfolio_id
IN (
2509,
2508,
2510,
2511
)
AND trade_date
BETWEEN '2012-03-31'
AND '2012-06-30'
AND (net_amount < 0 OR quantity < 0 OR cost_basis_adjustment < 0)
AND transactiontype IN ('Flow', 'Transfer of Securities', 'Federal Withholding')
AND transactiondescription NOT REGEXP ('funds moved')
GROUP BY transactiontype
 *//*
            echo "*********** QUERY: {$query}<br />";
            echo "START: {$startDate}<br />";
            echo "END: {$endDate}<br />";
            $result = $adb->pquery($query, array($startDate, $endDate));
            $cb = $adb->query_result($result, 0, "quan");
            echo "*********** COST BASIS: {$cb}<br />";
            return $cb + $tv;
        }
        else
        {
            $m = date('m');
            $d = date('d');
            $Y = date('Y');
            $today = $Y . "-{$m}-{$d}";
            /*
            $q2 = "SELECT SUM(net_amount - total_value) AS bef
                        FROM v_UserPortfolioTransactions
                        WHERE portfolio_id
                        IN ( {$portfolioIds} )
                        AND transactiontype IN ('Flow', 'transfer of securities')
                        AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                        AND TransactionDescription NOT IN ('TFR: TRANSFERRED')
                        AND trade_date between ? AND ?";
            
            $before_result = $adb->pquery($q2, array($startDate, $endDate));
            $before = $adb->query_result($before_result, 0, "bef");
            echo "*****BEFORE: {$before}<br />";
            $query = "SELECT *, SUM(net_amount - total_value) AS quan 
                    FROM v_UserPortfolioTransactions
                    WHERE portfolio_id 
                    IN ( {$portfolioIds} ) 
                    AND transactiontype IN ('Flow', 'transfer of securities', 'Federal Withholding')
                    AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                    AND trade_date between ? AND ?";
*/
                    


                /*    
                    
                    
                    
            $q2 = "SELECT SUM(net_amount - total_value) AS bef
                        FROM v_UserPortfolioTransactions
                        WHERE portfolio_id
                        IN ( {$portfolioIds} )
                        AND transactiontype IN ('Flow', 'transfer of securities', 'Federal Withholding')
                        AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                        AND TransactionDescription NOT IN ('TFR: TRANSFERRED')
                        AND TransactionDescription NOT REGEXP ('funds moved')
                        AND trade_date between ? AND ?";
            
            echo "________________Q2________________<br /><br />";
            echo $q2 . "<br />SD: {$startDate}<br />ED: {$endDate}<br /><br />";
            $before_result = $adb->pquery($q2, array($startDate, $endDate));
            $before = $adb->query_result($before_result, 0, "bef");
            echo "*****BEFORE: {$before}<br />";
            $query = "SELECT *, SUM(cost_basis_adjustment) AS quan 
                    FROM v_UserPortfolioTransactions
                    WHERE portfolio_id 
                    IN ( {$portfolioIds} ) 
                    AND transactiontype IN ('Flow', 'transfer of securities', 'Federal Withholding')
                    AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                    AND TransactionDescription NOT REGEXP ('funds moved')
                    AND trade_date between ? AND ?";                    
                    
            $result = $adb->pquery($query, array($endDate, $today));
            $cb = $adb->query_result($result, 0, "quan");
            
            $query = "SELECT * FROM v_UserPortfolioTransactions WHERE portfolio_id IN ( {$portfolioIds} ) AND transactiontype IN ('Flow', 'transfer of securities', 'Federal Withholding') AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0) AND trade_date between '2002-12-31' AND '2012-07-25'";
            $result = $adb->pquery($query, array());
            $num_rows = $adb->num_rows($result);
            echo "****** TRANSACTION IDS *********<br />";
            for($x = 0; $x < $num_rows; $x++)
                echo $adb->query_result($result, $x, "transaction_id") . ", <br />";
            
            return $cb + $before;
        }
        /*$query = "SELECT *, SUM(net_amount - total_value) AS quan 
                  FROM v_UserPortfolioTransactions
                  WHERE portfolio_id 
                  IN ( {$portfolioIds} ) 
                  AND (transactiontype = 'Flow' OR transactiontype = 'transfer of securities')
                  AND (net_amount < 0 OR cost_basis_adjustment < 0 OR quantity < 0)
                  AND trade_date between ? AND ?";
                  echo "**))*)*)*)*)*)*)*)*)*<br />{$query}*)*)*)*)*)**)*)*)*)))*)*)*)*)*)<br />";//14547.51    18971.31
/*        $query = "SELECT SUM(net_amount) AS quan 
                  FROM v_UserPortfolioTransactions
                  WHERE portfolio_id 
                  IN ( {$portfolioIds} ) 
                  AND (transactiontype = 'Flow')
                  AND (net_amount < 0)
                  AND trade_date between ? AND ?";
        $result = $adb->pquery($query, array($startDate, $endDate));
        $tv = $adb->query_result($result, 0, "quan");
        echo "<br />**********TOTAL VALUE: {$tv}<br />";
        $query = "SELECT *, SUM(total_value) AS quan 
                  FROM v_UserPortfolioTransactions
                  WHERE portfolio_id 
                  IN ( {$portfolioIds} ) 
                  AND (transactiontype = 'transfer of securities')
                  AND (total_value > 0)
                  AND trade_date between ? AND ?";
/*        $query = "SELECT SUM(cost_basis_adjustment) AS quan
                  FROM v_UserPortfolioTransactions
                  WHERE portfolio_id
                  IN ({$portfolioIds})
                  AND (transactiontype 'transfer of securities')"*/
//                  echo $query;
/*       if($inception == "1")
        $query = "SELECT *, SUM(net_amount) AS quan 
                  FROM `v_UserPortfolioTransactions` 
                  WHERE portfolio_id IN ( {$portfolioIds} ) 
                  AND trade_date between ? AND ?";*/
                  
//        $result = $adb->pquery($query, array($startDate, $endDate));
/*        echo "<br /><br />" . $query . "<br />";
        echo $startDate . "--------" . $endDate . "<br /><br />";*/
/*        $cb = $adb->query_result($result, 0, "quan");
        echo "*********** COST BASIS: {$cb}<br />";
        return $cb + $tv;*/
        
//        return $adb->query_result($result, 0, "quan");
    }
    
    public function GetContributionInfo($portfolioIDs, $startDate, $endDate){
/*        $query = "SELECT *, SUM(net_amount) AS quan FROM `vtiger_pc_transactions` 
                  WHERE portfolio_id IN ( 309, 444, 17873, 19724, 19725 ) 
                  AND trade_date between ? AND ? 
                  AND (activity_id = 10 OR activity_id = 50) AND net_amount > 0";*/
        global $adb;
        $query = "SELECT *, SUM(net_amount + total_value) AS quan 
                    FROM v_UserPortfolioTransactions
                    WHERE portfolio_id IN ( {$portfolioIDs} ) 
                    AND trade_date between ? AND ?
                    AND (transactiontype = 'Flow' OR transactiontype = 'receipt of securities') 
                    AND (net_amount > 0 OR total_value > 0)";
//        echo "CONTRIBUTION QUERY:<br />{$query} ---- {$startDate}, {$endDate}<br />";
        $result = $adb->pquery($query, array($startDate, $endDate));
        if($adb->num_rows($result) > 0)
          return $adb->query_result($result, 0, "quan");
    }
    
    public function getTransactionData($portfolioIds,$startDate,$endDate) {
        global $adb;
        $query = "
        select 
            t.activity_id, 
            t.report_as_type_id, 
            ifnull(a.activity_name,'NONE') as ActivityName, 
            ifnull(r.report_as_type_name,'NONE') as ReportAsTypeName, 
            sum(total_value) as TotalValue, 
            sum(net_amount) as NetAmount, 
            sum(cost_basis_adjustment) as CostBasis, 
            sum(principal) as Principal, 
            sum(accrued_interest) as AccruedInterest
        FROM
            vtiger_pc_transactions t
                LEFT JOIN vtiger_pc_activities a on a.activity_id = t.activity_id
                LEFT JOIN vtiger_pc_report_as_types r on r.report_as_type_id = t.report_as_type_id
            WHERE 
                t.trade_date between ? and ?
                AND t.portfolio_id in ({$portfolioIds})
            GROUP BY
                1,2
        ";//'{$endDate}'
//        echo "PORTFOLIO IDS: {$portfolioIds}<br />";
//        $summary = $adb->pquery($query,array($startDate,$endDate));
        $summary = $adb->pquery($query,array($startDate, $endDate));
//        echo "Start Date: {$startDate}<br />";
//        echo "End Date: {$endDate}<br />";
//        echo "QUERY: {$query}<br />";
        $summaryData = array();
        foreach ($summary as $r) {
            foreach(array('TotalValue',    'NetAmount',   'CostBasis',   'Principal',   'AccruedInterest') as $k) {
                $summaryData[$r['ActivityName']][$r['ReportAsTypeName']][$k] = $r[$k];
                $summaryData[$r['ActivityName']]['__TOTAL'][$k] += $r[$k];
                if ($r[$k] > 0)                                                   
                {
                    $summaryData[$r['ActivityName']]['__TOTAL_POS'][$k] += $r[$k];
//                    if($r['ActivityName'] == 'Flow')
//                        echo "FLOW: {$r[$k]}<br />";
                }
                else
                    $summaryData[$r['ActivityName']]['__TOTAL_NEG'][$k] += $r[$k];
                //echo "WITHDRAWL NUMBERS: " . $summaryData[$r['ActivityName']]['__TOTAL_NEG'][$k] . "<br />";
            }
        }
        
        return $summaryData;
    }
    
    public function getReturns($hhid, $portfolioOverride=null){
        global $adb;
        $m = date('m')-3;
        $d = date('d')+7;
        $Y = date('Y');
        $m = date('m');
        $d = date('d');
        $Y = date('Y');

        $lastMonthEnd   = date('Y-m-d',mktime(0,0,0,$m,0,$Y));
//        echo "LAST MONTH END: {$lastMonthEnd}<br />";
        $lastMonthStart = date('Y-m-d',mktime(0,0,0,$m-1,0,$Y));
//        echo "LAST MONTH START: {$lastMonthStart}<br />";
        $today = $Y . "-{$m}-{$d}";
//        echo "TODAY: {$today}<br />";
/*        $lastYearStart = $Y-1 . "-01-01";
        $lastYearEnd = $Y-1 . "-12-31";*/
        
        $lastYearStart = $Y-2 . "-12-31";
//        $lastYearStart = $Y-1 . "-{$m}-{$d}";
        $lastYearEnd   = $Y-1 . "-12-31";
        $ytdStart = $Y-1 . "-{$m}-{$d}";
        $ytdStart = date('Y-m-d',mktime(0,0,0,$m,0,$Y-1));
        $qtrStart = date('Y-m-d',mktime(0,0,0,$m-3,0,$Y));
//        echo "QUARTER START: {$qtrStart}<br />";
        $periods = array();

        if($portfolioOverride)
        {
            $ids = $this->GetPortfolioIDsFromContactID($hhid);
            $portfolioIds = SeparateArrayWithCommasAndSingleQuotes($portfolioOverride); //$portfolioOverride;
//            SeparateArrayWithCommasAndSingleQuotes($portfolioIds);//Separate the returned ID's
        }
        else
        {
            $ids = $this->GetPortfolioIDsFromHHID($hhid);//Get the portfolio id's
            $ids = array_unique($ids);
            $portfolioIds = $this->GetImplodedPortfolioIDsFromHHID($hhid);//Comma separate the ID's
        }
        //$firstIntStart = array();
        //Beginning value, date
////        $ids = array('19804');/////KILL ME
/*        $ids = array('444');//309
        $portfolioIds = "444";//309*/
        foreach ($ids as $p) 
        {
//            echo "PORTFOLIO ID: {$p}<br />";
//            $query = "SELECT interval_begin_value, interval_begin_date, to_days(interval_begin_date) as begin_date_days FROM vtiger_pc_portfolio_intervals where portfolio_id = {$p} ORDER BY interval_begin_date ASC limit 1";
//            echo $query . "<br />";
//            echo $p . ",<br />";
            $intsStart = $adb->pquery("SELECT interval_begin_value, interval_begin_date, to_days(interval_begin_date) as begin_date_days 
                                       FROM vtiger_pc_portfolio_intervals where portfolio_id = ? 
                                       ORDER BY interval_begin_date ASC limit 1",array($p));
            if($adb->num_rows($intsStart) <= 0)//If there is nothing to return, skip it
                continue;

            foreach($intsStart AS $k => $v)
            {
                $intStart = $v;
               // echo "INTSTART: {$v['interval_begin_value']}<br />";
            }

//            $periods['inception']['start_value'] += $intStart['interval_begin_value'];//The inception start value is combined for all portfolios
            if (!$firstIntStart || ($intStart && $firstIntStart['begin_date_days'] > $intStart['begin_date_days']))
            {
                $firstIntStart = $intStart;//Figure out the largest "begin_date_days", as that is the earliest
                $periods['inception']['start_value'] = $intStart['interval_begin_value'];//The inception start value is combined for all portfolios
            }
        }
        $periods['inception']['start_date'] = $firstIntStart['interval_begin_date'];//The inception start date is the earliest of all portfolios

        //Latest month interval, date
//        echo "LM START: {$lastMonthStart}, LM END: {$lastMonthEnd}<br />";
////        echo "PIDS: {$portfolioIds}<br />";
////        $portfolioIds = '19804';///KILL ME
        $intLastMonth = $adb->pquery("SELECT interval_begin_date, interval_end_date, 
                                             sum(interval_begin_value) as interval_begin_value, sum(interval_end_value) as interval_end_value 
                                      FROM vtiger_pc_portfolio_intervals where interval_begin_date = ? and interval_end_date = ? 
                                      AND portfolio_id in ({$portfolioIds})",array($lastMonthStart,$lastMonthEnd));
        $query = "SELECT interval_begin_date, interval_end_date, sum(interval_begin_value) as interval_begin_value, 
                                      sum(interval_end_value) as interval_end_value FROM vtiger_pc_portfolio_intervals 
                                      where interval_begin_date = '{$lastMonthStart}' and interval_end_date = '{$lastMonthEnd}' AND portfolio_id in ({$portfolioIds})";//(309, 444, 17873, 19724, 19725)";
//        echo "QUERY: {$query}<br />";
//        $intLastMonth = $adb->pquery($query, array());
//        echo "NUM ROWS: " . $adb->num_rows($intLastMonth) . "<br />";
        if($adb->num_rows($intLastMonth) > 0)
        foreach($intLastMonth AS $k => $v)
        {
//            echo "KEY: {$v['interval_end_date']}<br />";
//            echo "ILD: " . $adb->query_result($intLastMonth, 0, "interval_end_date") . "<br />";
            $intLastMonth = $v;
//            echo "INTLASTMONTH: {$v['interval_end_date']}<br />";
        }
        //Start of last year
        $intLastYearStart = $adb->pquery("SELECT interval_begin_date, interval_end_date, sum(interval_begin_value) as interval_begin_value, sum(interval_end_value) as interval_end_value FROM vtiger_pc_portfolio_intervals where interval_begin_date = ? and to_days(interval_end_date) - to_days(interval_begin_date) >=28 AND  portfolio_id in ({$portfolioIds})",array($lastYearStart));
        foreach($intLastYearStart AS $k => $v)
        {
            $intLastYearStart = $v;
//            echo "INTLASTYEARSTART: {$v['interval_begin_value']}<br />";
        }

        //End of last year
        $intCurYearStart = $adb->pquery("SELECT interval_begin_date, interval_end_date, sum(interval_begin_value) as interval_begin_value, sum(interval_end_value) as interval_end_value FROM vtiger_pc_portfolio_intervals where interval_begin_date = ? and to_days(interval_end_date) - to_days(interval_begin_date) >=28 AND portfolio_id in ({$portfolioIds})",array($lastYearEnd));
        foreach($intCurYearStart AS $k => $v)
            $intCurYearStart = $v;

        //Start of quarter (90 days ago)
        $intQtrStart = $adb->pquery("SELECT interval_begin_date, interval_end_date, sum(interval_begin_value) as interval_begin_value, sum(interval_end_value) as interval_end_value FROM vtiger_pc_portfolio_intervals where interval_begin_date = ? and to_days(interval_end_date) - to_days(interval_begin_date) >=28 AND portfolio_id in ({$portfolioIds})",array($qtrStart));
//        echo "QUARTER STARTS:<br />";
//        echo "SELECT interval_begin_date, interval_end_date, sum(interval_begin_value) as interval_begin_value, sum(interval_end_value) as interval_end_value FROM vtiger_pc_portfolio_intervals where interval_begin_date = '{$qtrStart}' and to_days(interval_end_date) - to_days(interval_begin_date) >=28 AND portfolio_id in ({$portfolioIds})<br />";
        foreach($intQtrStart AS $k => $v)
            $intQtrStart = $v;

        $periods['qtr']['start_value'] = $intQtrStart['interval_begin_value'];
        $periods['qtr']['start_date'] = $intQtrStart['interval_begin_date'];
        $periods['qtr']['end_value']   = $intLastMonth['interval_end_value'];
        $periods['qtr']['end_date']   = $intLastMonth['interval_end_date'];

        $periods['ytd']['start_value']  = $intLastYearStart['interval_begin_date'];//$intCurYearStart['interval_begin_value'];
        $periods['ytd']['start_date']  = $ytdStart;//$intLastYearStart['interval_begin_date'];//$intCurYearStart['interval_begin_date'];
        $periods['ytd']['end_value']    = $intLastMonth['interval_end_value'];
        $periods['ytd']['end_date']    = $today;//$intLastMonth['interval_end_date'];//$today;//$intLastMonth['interval_end_date'];
        
/*        echo "YEAR TO DATE STARTS:<br />";
        echo "start_value: " . $periods['ytd']['start_value'] . "<br />";
        echo "start_date: " . $periods['ytd']['start_date'] . "<br />";
        echo "end_value: " . $periods['ytd']['end_value'] . "<br />";
        echo "end_date: " . $periods['ytd']['end_date'] . "<br />";
*/        
//        echo "INCEPTION END: {$periods['inception']['end_value']}<br />";
//        echo "INCEPTION END: {$intLastMonth['interval_end_date']}<br />";
        $periods['inception']['end_value']= $intLastMonth['interval_end_value'];
//        echo "<br />INCEPTION END VALUE: {$periods['inception']['end_value']}<br />";
        $periods['inception']['end_date'] = $today;//$intLastMonth['interval_end_date'];//$today;//'2012-04-30'; //$today;//$lastMonthEnd; //$intLastMonth['interval_end_date'];

        $periods['last_year']['start_value'] = $intLastYearStart['interval_begin_value'];
        $periods['last_year']['start_date'] = $lastYearStart; //$lastYearStart; //'2011-07-10';//$intLastYearStart['interval_begin_date'];
        $periods['last_year']['end_value']   = $intCurYearStart['interval_begin_value'];
        $periods['last_year']['end_date']   = $lastYearEnd; //$lastMonthEnd; //$intCurYearStart['interval_begin_date'];

        $periods['last_month']['start_value'] = $intLastMonth['interval_begin_value'];
        $periods['last_month']['start_date'] = $lastMonthStart; //$intLastMonth['interval_begin_date'];
        $periods['last_month']['end_value']   = $intLastMonth['interval_end_value'];
        $periods['last_month']['end_date']   = $lastMonthEnd; //$intLastMonth['interval_end_date'];

        foreach (array_keys($periods) as $p) {
//            echo "<br />" . "{$p} PERIOD DATE: " . $periods[$p]['end_date'] . "<br />";
            $trans = $this->getTransactionData($portfolioIds,$periods[$p]['start_date'], $periods[$p]['end_date']);
            //echo "P: {$p}<br />";
//            echo "<br /><br /><br />";
//            print_r($trans);
//            for($x = 0; $x < sizeof($trans); $x++)
//            foreach($trans AS $k => $v)
            {
//                echo "KEY: {$k}<br />";
            //echo "WITHDRAWAL: " . $trans['Flow']['__TOTAL_NEG']['CostBasis'] . "<br />";
            $periods[$p]['div_interest'] = $trans['Income']['Dividend']['CostBasis'] + $trans['Income']['Interest']['CostBasis'];
            $periods[$p]['net_contrib']  = $trans['Flow']['__TOTAL']['CostBasis'] 
                                          + $trans['Receipt of Securities']['__TOTAL']['TotalValue']
                                          - $trans['Transfer of Securities']['__TOTAL']['TotalValue'];
//560702
//            $periods[$p]['contributions'] = $trans['Flow']['__TOTAL_POS']['CostBasis'] + $trans['Receipt of Securities']['__TOTAL']['TotalValue'];
            
            $totals = $this->GetWithdrawInfo($portfolioIds, $periods[$p]['start_date'], $periods[$p]['end_date']);
            $periods[$p]['contributions'] = $totals['contributions'];
            $periods[$p]['withdrawals'] = $totals['withdrawals'];
/*            $periods[$p]['contributions'] = $this->GetContributionInfo($portfolioIds, $periods[$p]['start_date'], $periods[$p]['end_date']);
            echo "CONTRIBUTIONS: " . $periods[$p]['contributions'] . "<br />";
            //foreach($trans[])
            //echo "PERIODS: {$periods[$p]['start_date']}<br />";
//            echo "WITHDRAW INFO!!!!!!: " . $this->GetWithdrawInfo($portfolioIds, $periods[$p]['start_date'], $periods[$p]['end_date']);
/*            if($p == 'inception')//Bit of a hack job at the moment.  Run a different query if we are calculating inception
            {
                echo "ENTERING INCEPTION<br />";
//                $periods[$p]['withdrawals'] = $this->GetWithdrawInfo($portfolioIds, $periods[$p]['start_date'], $periods[$p]['end_date'], 1);//$this->GetWithdrawInfo($portfolioIds, '2011-07-10', '2012-07-10');
                $periods[$p]['withdrawals'] = $this->GetWithdrawInfo($portfolioIds, $periods[$p]['start_date'], $today, 1);//$periods['ytd']['start_date'], 1);//$this->GetWithdrawInfo($portfolioIds, '2011-07-10', '2012-07-10');
            }
            else
                $periods[$p]['withdrawals'] = $this->GetWithdrawInfo($portfolioIds, $periods[$p]['start_date'], $periods[$p]['end_date']);//$this->GetWithdrawInfo($portfolioIds, '2011-07-10', '2012-07-10');
//            $periods[$p]['withdrawals']   = $trans['Flow']['__TOTAL_NEG']['CostBasis'] - $trans['Transfer of Securities']['__TOTAL']['TotalValue']
                                          ;//+ $trans['Sell']['__TOTAL_NEG']['CostBasis'];*/
//echo "WITHDRAWAL: " . $trans['Flow']['__TOTAL_NEG']['CostBasis'] . "<br />";

            $periods[$p]['mgmt_fee']     = $trans['Expense']['Management Fee']['CostBasis'];

            $periods[$p]['other_fee']    = $trans['Expense']['__TOTAL']['CostBasis'] - $periods[$p]['mgmt_fee'];
            $periods[$p]['return']       = $periods[$p]['end_value'] - $periods[$p]['start_value'] - $periods[$p]['net_contrib'];
            $periods[$p]['value_change'] = $periods[$p]['return'] - $periods[$p]['mgmt_fee'] - $periods[$p]['other_fee'] - $periods[$p]['div_interest'];

/*THROWING IN SOME NONSENSE VALUES FOR DEMO PURPOSES...REMOVE!*/
            $periods['inception']['withdrawals'] += $periods[$p]['withdrawals'];
            $periods['inception']['contributions'] += $periods[$p]['contributions'];
            $periods['inception']['div_interest'] += $periods[$p]['div_interest'];
            $periods['inception']['value_change'] += $periods[$p]['value_change'];
            $periods['inception']['return'] += $periods[$p]['return'];
            $periods['inception']['end_value'] += $periods[$p]['end_value'];
/*DEMO OVER*/
            }
            
//            $periods[$p]['inception'] = 33;
        }

        $irr = array();
        foreach (array('inception','ytd','qtr') as $k) {
///            echo "END DATE: {$periods[$k]['end_date']}<br />";
            $irr[$k] = $this->getPeriodIRR($portfolioIds,$periods[$k]['start_date'],$periods[$k]['end_date'],$periods[$k]['start_value'],$periods[$k]['end_value']);
            $irr['ref_'.$k] = $this->getReferenceReturn("S&P",$periods[$k]['start_date'],$periods[$k]['end_date']);
        }

        foreach ($periods as $p => $vals) {
            foreach ($vals as $k => $v) {
                if (strpos($k,'_date') === FALSE)
                    $periods[$p][$k] = $v;
            }
        }
        
        return array($periods,$irr);
    }
}

//echo "<br /><br /><br /><br />*******NEW STUFF HERE<br /><br /><br /><br />";

global $adb;

/*
global $adb;

$query = "SELECT transaction_id FROM vtiger_pc_transactions WHERE net_amount < 0 AND portfolio_id IN (309, 444)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
{
    
}

/*
$neg_net = array();
//Get all negative FLOW's for each account
$query = "SELECT portfolio_id, net_amount FROM vtiger_pc_transactions WHERE net_amount < 0 AND portfolio_id IN(309, 444) AND activity_id IN(10)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
{
    $neg_net['pid'] = $v['portfolio_id'];
    $neg_net['net_amount'] = $v['net_amount'];
    echo $v['net_amount'] . "<br />";
}

/*
$neg_net = array();
$pos_net = array();
$tv = array();
$haystack_tv = array();

$query = "SELECT net_amount FROM vtiger_pc_transactions WHERE net_amount < 0 AND portfolio_id IN(309, 444) AND activity_id IN(10)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
{
    $neg_net[] = $v['net_amount'];
    echo $v['net_amount'] . "<br />";
}
$query = "SELECT transaction_id, net_amount FROM vtiger_pc_transactions WHERE net_amount < 0 AND portfolio_id IN(309, 444) AND activity_id IN(30)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
{
    $pos_net[] = $v['net_amount'];
    echo "ID: " . $v['transaction_id'] . "AMOUNT: " . $v['net_amount'] . "<br />";
}

$query = "SELECT total_value FROM vtiger_pc_transactions WHERE total_value > 0 AND portfolio_id IN(309, 444) AND activity_id IN(120)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
    $tv[] = $v['total_value'];

$query = "SELECT total_value FROM vtiger_pc_transactions WHERE total_value > 0 AND portfolio_id IN(309, 444) AND activity_id IN(50)";
$result = $adb->pquery($query, array());
foreach($result AS $k => $v)
    $haystack_tv[] = $v['total_value'];

$counter = 0;
$dead_values = array();
foreach($neg_net AS $k => $v)
{
    
    if(in_array($v, $pos_net))
    {
       $neg_net[$counter] = 0;
       for($x = 0; $x < sizeof($pos_net); $x++)
       {
           if($pos_net[$x] == $v)
           {
               $pos_net[$x] = 0;
               break;
           }
       }
           $dead_values[] = $v;
    }
    $counter++;
}

$counter = 0;
foreach($tv AS $k => $v)
{
    if(in_array($v, $haystack_tv))
       $tv[$counter] = 0;
    $counter++;
}

foreach($neg_net AS $k => $v)
    echo $v . "<br />";

foreach($tv AS $k => $v)
    echo $v . "<br />";

echo "DEAD VALUES NET<br />";
foreach($dead_values AS $k => $v)
{
    echo $v. "<br />";
    if(in_array($v, $pos_net))
        echo $v . "<br />";
}
/*
foreach($pos_net AS $k => $v)
    echo $v . "<br />";



/*
$query = "SELECT * FROM vtiger_pc_transactions WHERE quantity < 0 AND portfolio_id IN(309, 444) AND activity_id IN(10, 120, 50)";
$query = "SELECT *
FROM `vtiger_pc_transactions`
WHERE portfolio_id
IN ( 309, 444 )
AND activity_id
IN ( 10, 120, 50 )";

$result = $adb->pquery($query, array());

$neg_net = array();
$pos_net = array();
$neg_tv = array();
$pos_tv = array();
/*
foreach($result as $k => $v)
{
    switch($v['activity_id'])
    {
        case 10:
            if($v['net_amount'] < 0)
                $neg_net[] = $v['net_amount'];
            else
                $pos_net[] = $v['net_amount'];
        case 120:
            $neg_tv[] = $v['total_value'];
            break;
        case 50:
            $pos_tv[] = $v['total_value'];
            break;
    }
}

$counter = 0;
foreach($neg_net AS $k => $v)
{
    $v *= -1;
    if(in_array($v, $pos_net))
       $neg_net[$counter] = 0;
    $counter++;
    //if(in_array)
}

echo "NEGATIVE NET NOW<br />";
foreach($neg_net AS $k => $v)
    echo $v . "<br />";
/*
echo "NEGATIVE NET<br />";
foreach($neg_net AS $k => $v)
    echo $v . "<br />";
echo "POSITIVE NET<br />";
foreach($pos_net AS $k => $v)
    echo $v . "<br />";
echo "NEGATIVE TV<br />";
foreach($neg_tv AS $k => $v)
    echo $v . "<br />";
echo "POSITIVE TV<br />";
foreach($pos_tv AS $k => $v)
    echo $v . "<br />";
 */
/*$query = "SELECT * FROM vtiger_pc_transactions WHERE quantity > 0 AND portfolio_id IN(309, 444) AND activity_id IN(10, 120, 50)";
$pos = $adb->pquery($query, array());
foreach($pos as $k => $v)
    echo $v['quantity'] . "<br />";*/
?>


<?php
/*
 Select 
Transactions.SymbolID  as 'DB_Transactions_SymbolID',
 Transactions.LongPositionFlag  as 'DB_Transactions_LongPositionFlag',
 Transactions.PortfolioID  as 'DB_Transactions_PortfolioID',
 Transactions.TradeDate  as 'DB_Transactions_TradeDate',
 Transactions.ActivityID  as 'DB_Transactions_ActivityID',
 Transactions.TransactionID  as 'DB_Transactions_TransactionID',
 IsNull(PerformanceExcludedAssets.ExcludeFromPerformanceFlag,0)  as 'Performance_ExcludedAssetsExcludedFromPerformanceFlag',
 CASE
	WHEN ( PerformanceSecurity.UseEndOfDayFlowsFlag <> 0 ) AND ( PerformanceSecurityType.AllowEndOfDayFlowsFlag > 0 ) THEN 1 
	WHEN ( CoTransactionSecurity.UseEndOfDayFlowsFlag <> 0 ) AND ( CoTransactionSecurityType.AllowEndOfDayFlowsFlag > 0 ) THEN 1 
	ELSE 0
 END  as 'Performance_SecurityUseEndOfDayFlows',
 CoTransactionSecurityType.SecurityTypeID  as 'Performance_CoTransactionSecurityTypeID',
 CoTransactionSecurity.SecurityID  as 'Performance_CoTransactionSecurityID',
 IsNull(CoTransaction.LongPositionFlag,0)  as 'Performance_CoTransactionLongPositionFlag',
 VirtualSecurityTypes.SecurityTypeID  as 'Performance_SecurityUseEndOfDayFlowVirtualJoin',
 Transactions.ReportAsTypeID  as 'DB_Transactions_ReportAsTypeID',
 Transactions.NetAmount  as 'DB_Transactions_NetAmount',
 Transactions.AddSubStatusTypeID  as 'DB_Transactions_AddSubStatusTypeID',
 Transactions.Principal  as 'DB_Transactions_Principal',
 Transactions.TotalValue  as 'DB_Transactions_TotalValue',
 Transactions.IsReinvestedFlag  as 'DB_Transactions_IsReinvestedFlag',
 Transactions.SettlementDate  as 'DB_Transactions_SettlementDate',
 PerformancePortfolio.PerformanceInceptionDate  as 'Performance_PortfolioInceptionDate',
 Transactions.Quantity  as 'DB_Transactions_Quantity',
 PerformancePortfolio.DisplayAccruedInterestFlag  as 'Performance_PortfolioDisplayAccInt',
 PerformanceInterestRateAsOfDate.InterestRate  as 'PerformanceData_SecurityAnnualIncomeRate',
 Transactions.OriginalTradeDate  as 'DB_Transactions_OriginalTradeDate',
 Transactions.AccruedInterest  as 'DB_Transactions_AccruedInterest',
 Transactions.CompleteTransactionFlag  as 'DB_Transactions_CompleteTransactionFlag',
 PerformancePortfolio.SeparatelyManagedAccountFlag  as 'Performance_PortfolioSeparatelyManagedAccountFlag',
 PerformancePortfolioGroupPortfolios.PerformanceInceptionDate  as 'Performance_GroupPortfolioInceptionDate',
 Transactions.AmountPerShare  as 'DB_Transactions_AmountPerShare',
 Transactions.AdvisorFee  as 'DB_Transactions_AdvisorFee',
 Transactions.OtherFee  as 'DB_Transactions_OtherFee',
 Transactions.StatusTypeID  as 'DB_Transactions_StatusTypeID',
 Transactions.CostBasisAdjustment  as 'DB_Transactions_CostBasisAdjustment',
 Transactions.SellLotID  as 'DB_Transactions_SellLotID',
 Transactions.TradeLotID  as 'DB_Transactions_TradeLotID',
 Transactions.KeepFractionalSharesFlag  as 'DB_Transactions_KeepFractionalSharesFlag',
 Transactions.MatchingMethodID  as 'DB_Transactions_MatchingMethodID',
 Transactions.ResetCostBasisFlag  as 'DB_Transactions_ResetCostBasisFlag',
 PerformancePortfolio.IncludeAccruedGainsFlag  as 'Performance_PortfolioDisplayAccGain',
 PerformancePortfolio.IncludeAccruedDividendsFlag  as 'Performance_PortfolioDisplayAccDiv',
 PerformancePortfolioGroup.OwningPortfolioID  as 'Performance_PortfolioGroupOwnerID'
From Transactions AS Transactions 
JOIN ufn_DataEng_Filter_Portfolio_Group('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, 1)  as SecurePortfolioIDList
  ON ( SecurePortfolioIDList.PortfolioID = Transactions.PortfolioID)
Left Outer Join ExcludedAssets as PerformanceExcludedAssets on PerformanceExcludedAssets.PortfolioID = Transactions.PortfolioID and PerformanceExcludedAssets.SecurityID = Transactions.SymbolID
 Left Outer Join Transactions as CoTransaction on ( ( CoTransaction.TransLinkID = Transactions.TransLinkID ) and  ( ( Transactions.SymbolID = CoTransaction.MoneyID ) or    ( Transactions.MoneyID = CoTransaction.SymbolID ) ) )
 Left Outer Join Securities as CoTransactionSecurity on CoTransaction.SymbolID = CoTransactionSecurity.SecurityID 
 and ( CoTransactionSecurity.DataSetID = 1 )
 Left Outer Join DatasetSecurityTypes as CoTransactionSecurityType on CoTransactionSecurity.SecurityTypeID=CoTransactionSecurityType.SecurityTypeID and CoTransactionSecurity.DataSetID=CoTransactionSecurityType.DataSetID 
 and ( CoTransactionSecurityType.DataSetID = 1 )
 Left Outer Join Securities as PerformanceSecurity on PerformanceSecurity.SecurityID=Transactions.SymbolID 
 and ( PerformanceSecurity.DataSetID = 1 )
 Left Outer Join DatasetSecurityTypes as PerformanceSecurityType on PerformanceSecurity.SecurityTypeID=PerformanceSecurityType.SecurityTypeID and PerformanceSecurity.DataSetID=PerformanceSecurityType.DataSetID 
 and ( PerformanceSecurityType.DataSetID = 1 )
 Left Outer Join SecurityTypes as VirtualSecurityTypes on PerformanceSecurityType.SecurityTypeID = VirtualSecurityTypes.SecurityTypeID and (1=2)
 Left Outer Join Portfolios as PerformancePortfolio on PerformancePortfolio.PortfolioID=Transactions.PortfolioID 
 and ( PerformancePortfolio.DataSetID = 1 )
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2012' ) as PerformanceInterestRateAsOfDate on PerformanceInterestRateAsOfDate.SecurityID = Transactions.SymbolID
 Left Outer Join Portfolios as PerformancePortfolioGroupPortfolios on PerformancePortfolioGroupPortfolios.PortfolioID = 508 and PerformancePortfolioGroupPortfolios.PortfolioTypeID = 32 
 and ( PerformancePortfolioGroupPortfolios.DataSetID = 1 )
 Left Outer Join Groups as PerformancePortfolioGroup on PerformancePortfolioGroup.OwningPortfolioID=508 and PerformancePortfolioGroup.PortfolioID = Transactions.PortfolioID
 Where 
(Transactions.TradeDate <= '07/26/2012' and
 Transactions.SymbolID  is not NULL and
 Transactions.StatusTypeID = 100 and
 (Transactions.ActivityID != 160 or
 Transactions.CompleteTransactionFlag = 0) and
 PerformancePortfolio.PerformanceInceptionDate <= '07/26/2012') and
 ((PerformanceExcludedAssets.ExcludeFromPerformanceFlag  is NULL) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0 )  ) 
Order by 
Transactions.SymbolID  ,
 Transactions.LongPositionFlag  ,
 Transactions.PortfolioID  ,
 Transactions.TradeDate  ,
 Transactions.ActivityID  ,
 Transactions.TransactionID   ; Select 
PerformanceSecurityType.CashAccount  as 'Performance_SecurityIsCashAccount',
 PerformanceSecurity.SecurityTypeID  as 'Performance_SecurityTypeID',
 PerformanceSecurityType.AccruedInterestFlag  as 'Performance_SecurityTypeAccruedInterest',
 PerformanceSecurity.DisplayAccruedFlag  as 'Performance_SecurityDisplayAccrued',
 PerformanceSecurity.AccrualMethodTypeID  as 'Performance_SecurityAccrualMethod',
 PerformanceSecurity.IncomeFrequencyID  as 'Performance_SecurityIncomeFrequency',
 PerformanceSecurity.CompoundingFrequencyID  as 'Performance_SecurityCompoundFrequency',
 PerformanceSecurity.IssueDate  as 'Performance_SecurityIssueDate',
 PerformanceSecurity.MaturityDate  as 'Performance_SecurityMaturityDate',
 PerformanceSecurity.FirstCouponDate  as 'Performance_SecurityFirstCoupon',
 PerformanceSecurityType.CallableFlag  as 'Performance_SecurityTypeCallable',
 PerformanceSecurityType.UsePriceFactorFlag  as 'Performance_SecurityTypeUsePriceFactor',
 PerformanceSecurity.SecurityID  as 'Performance_SecurityID',
 PerformanceSecurityType.MarketValue  as 'Performance_SecurityTypeMarketValue',
 PerformanceSecurity.PaymentDelay  as 'Performance_SecurityPaymentDelay',
 PerformanceSecurity.VariableRateFlag  as 'Performance_SecurityVariableRate',
 PerformanceSecurity.PaymentFrequencyIntervalID  as 'Performance_SecurityPaymentFreqInterval',
 PerformanceSecurity.ResetFrequencyIntervalID  as 'Performance_SecurityResetFreqInterval',
 PerformanceSecurity.PaymentFrequencyIntervalValue  as 'Performance_SecurityPaymentFreqIntervalValue',
 PerformanceSecurity.PaymentFrequencyID  as 'Performance_SecurityPaymentFreqType',
 PerformanceSecurity.ResetFrequencyID  as 'Performance_SecurityResetFreqType',
 PerformanceSecurity.ResetFrequencyIntervalValue  as 'Performance_SecurityResetFreqIntervalValue',
 PerformanceSecurity.PaymentFrequencyMarketHolidayRuleID  as 'Performance_SecurityPaymentFreqMarketHolidayRule',
 PerformanceSecurity.ResetFrequencyMarketHolidayRuleID  as 'Performance_SecurityResetFreqMarketHolidayRule',
 PerformanceSecurity.DaysUntilChange  as 'Performance_SecurityDaysUntilChange',
 PerformanceSecurity.FirstResetDate  as 'Performance_SecurityFirstResetDate',
 PerformanceSecurity.SpreadMarginOperatorID  as 'Performance_SecuritySpreadMarginOperator',
 PerformanceSecurity.SpreadMarginBCDValue  as 'Performance_SecuritySpreadMarginValue',
 PerformanceSecurity.ReferenceIndex  as 'Performance_SecurityReferenceIndex',
 PerformanceSecurity.LastCouponDate  as 'Performance_SecurityLastCoupon',
 PerformanceSecurityType.StraightLineAccrualFlag  as 'Performance_SecurityIsStraightLineAccrual',
 PerformanceSecurity.SharesPerContract  as 'Performance_SecuritySharesPerContract',
 PerformanceSecurityType.PriceAdjustmentValue  as 'Performance_SecurityTypePriceAdjustmentValue',
 PerformanceSecurityType.SplitPrecisionDefault  as 'Performance_SecurityTypeSplitPrecisionDefault',
 PerformanceSecurityType.IsMortgageBackedFlag  as 'Performance_SecurityTypeIsMortBacked',
 PerformanceSecurity.DaysUntilChange  as 'Performance_Security_DaysUntilChange',
 PerformanceSecurity.FirstResetDate  as 'Performance_Security_FirstResetDate',
 PerformanceSecurity.SpreadMarginOperatorID  as 'Performance_Security_SpreadMarginOperator',
 PerformanceSecurity.SpreadMarginBCDValue  as 'Performance_Security_SpreadMarginValue',
 PerformanceSecurity.ReferenceIndex  as 'Performance_Security_ReferenceIndex',
 PerformanceSecurityType.ComputeAccruedGainFlag  as 'Performance_SecurityTypeComputeAccruedGain',
 PerformanceSecurityType.ComputeAccruedIncomeFlag  as 'Performance_SecurityTypeComputeAccruedInco',
 PerformanceSecurity.NextDividendDate  as 'Performance_SecurityNextDivDate',
 PerformanceSecurity.NextEXDate  as 'Performance_SecurityNextExDate',
 PerformanceSecurity.NextDividendAmount  as 'Performance_SecurityNextDivAmt',
 PerformanceSecurityType.AccruedInterestFlag  as 'Performance_SecurityTypeDisplayAccruedInte',
 PerformanceSecurityAssetClass.CodeID  as 'Performance_SecurityAssetClassID',
 PerformanceSecurity.Symbol  as 'Performance_SecuritySymbol'
From Securities AS PerformanceSecurity 
Left Outer Join DatasetSecurityTypes as PerformanceSecurityType on PerformanceSecurity.SecurityTypeID=PerformanceSecurityType.SecurityTypeID and PerformanceSecurity.DataSetID=PerformanceSecurityType.DataSetID 
 and ( PerformanceSecurityType.DataSetID = 1 )
 Left Outer Join SecurityCodes as PerformanceSecurityAssetClass on PerformanceSecurityAssetClass.SecurityID=PerformanceSecurity.SecurityID and PerformanceSecurityAssetClass.CodeTypeID=20
  where 

( PerformanceSecurity.DataSetID = 1 )
 and  ( ( 508 = 0 ) or (PerformanceSecurity.SecurityID in ( Select DISTINCT SymbolID from Transactions where PortfolioID in ( select PortfolioID from dbo.ufn_DataEng_Filter_Portfolio_Group('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, 1) ) ) ) )
 */







/*
Select 
Transactions.SymbolID  as 'DB_Transactions_SymbolID',
 Transactions.LongPositionFlag  as 'DB_Transactions_LongPositionFlag',
 Transactions.PortfolioID  as 'DB_Transactions_PortfolioID',
 Transactions.TradeDate  as 'DB_Transactions_TradeDate',
 Transactions.ActivityID  as 'DB_Transactions_ActivityID',
 Transactions.TransactionID  as 'DB_Transactions_TransactionID',
 IsNull(PerformanceExcludedAssets.ExcludeFromPerformanceFlag,0)  as 'Performance_ExcludedAssetsExcludedFromPerformanceFlag',
 PerformancePortfolio.PerformanceInceptionDate  as 'Performance_PortfolioInceptionDate',
 PerformancePortfolioGroupPortfolios.PerformanceInceptionDate  as 'Performance_GroupPortfolioInceptionDate',
 PerformancePortfolio.DisplayAccruedInterestFlag  as 'Performance_PortfolioDisplayAccInt',
 Transactions.AccruedInterest  as 'DB_Transactions_AccruedInterest',
 Transactions.Quantity  as 'DB_Transactions_Quantity',
 Transactions.SettlementDate  as 'DB_Transactions_SettlementDate',
 PerformanceInterestRateAsOfDate0.InterestRate  as 'PerformanceData_SecurityAnnualIncomeRate0',
 Transactions.OriginalTradeDate  as 'DB_Transactions_OriginalTradeDate',
 Transactions.StatusTypeID  as 'DB_Transactions_StatusTypeID',
 Transactions.CostBasisAdjustment  as 'DB_Transactions_CostBasisAdjustment',
 Transactions.SellLotID  as 'DB_Transactions_SellLotID',
 Transactions.TradeLotID  as 'DB_Transactions_TradeLotID',
 Transactions.AmountPerShare  as 'DB_Transactions_AmountPerShare',
 Transactions.KeepFractionalSharesFlag  as 'DB_Transactions_KeepFractionalSharesFlag',
 Transactions.MatchingMethodID  as 'DB_Transactions_MatchingMethodID',
 Transactions.Principal  as 'DB_Transactions_Principal',
 Transactions.AdvisorFee  as 'DB_Transactions_AdvisorFee',
 Transactions.OtherFee  as 'DB_Transactions_OtherFee',
 Transactions.NetAmount  as 'DB_Transactions_NetAmount',
 Transactions.ResetCostBasisFlag  as 'DB_Transactions_ResetCostBasisFlag',
 PerformanceInterestRateAsOfDate.InterestRate  as 'PerformanceData_SecurityAnnualIncomeRate',
 Transactions.ReportAsTypeID  as 'DB_Transactions_ReportAsTypeID',
 PerformancePortfolio.IncludeAccruedGainsFlag  as 'Performance_PortfolioDisplayAccGain',
 PerformancePortfolio.IncludeAccruedDividendsFlag  as 'Performance_PortfolioDisplayAccDiv',
 PerformanceInterestRateBeginningDate0.InterestRate  as 'Performance_SecurityAnnualIncomeRateOnBeginningDate0',
 Transactions.AddSubStatusTypeID  as 'DB_Transactions_AddSubStatusTypeID',
 Transactions.TotalValue  as 'DB_Transactions_TotalValue',
 PerformancePortfolio.PortfolioID  as 'Performance_PortfolioPortfolioID',
 Transactions.IsReinvestedFlag  as 'DB_Transactions_IsReinvestedFlag',
 Transactions.MoneyID  as 'DB_Transactions_MoneyID',
 Transactions.CompleteTransactionFlag  as 'DB_Transactions_CompleteTransactionFlag',
 IsNull(CoTransaction.LongPositionFlag,0)  as 'Performance_CoTransactionLongPositionFlag',
 IsNull(CoTransactionExcludedAssets.ExcludeFromPerformanceFlag,0)  as 'Performance_CoTransactionExcludedFromPerformance',
 PerformanceInterestRateAsOfDate1.InterestRate  as 'PerformanceData_SecurityAnnualIncomeRate1',
 PerformanceInterestRateBeginningDate1.InterestRate  as 'Performance_SecurityAnnualIncomeRateOnBeginningDate1',
 PerformanceInterestRateAsOfDate2.InterestRate  as 'PerformanceData_SecurityAnnualIncomeRate2',
 PerformanceInterestRateBeginningDate2.InterestRate  as 'Performance_SecurityAnnualIncomeRateOnBeginningDate2'
From Transactions AS Transactions 
JOIN ufn_DataEng_Filter_Portfolio_Group('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, 1)  as SecurePortfolioIDList
  ON ( SecurePortfolioIDList.PortfolioID = Transactions.PortfolioID)
Left Outer Join ExcludedAssets as PerformanceExcludedAssets on PerformanceExcludedAssets.PortfolioID = Transactions.PortfolioID and PerformanceExcludedAssets.SecurityID = Transactions.SymbolID
 Left Outer Join Portfolios as PerformancePortfolio on PerformancePortfolio.PortfolioID=Transactions.PortfolioID 
 and ( PerformancePortfolio.DataSetID = 1 )
 Left Outer Join Portfolios as PerformancePortfolioGroupPortfolios on PerformancePortfolioGroupPortfolios.PortfolioID = 508 and PerformancePortfolioGroupPortfolios.PortfolioTypeID = 32 
 and ( PerformancePortfolioGroupPortfolios.DataSetID = 1 )
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2012' ) as PerformanceInterestRateAsOfDate0 on PerformanceInterestRateAsOfDate0.SecurityID = Transactions.SymbolID
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2012' ) as PerformanceInterestRateAsOfDate on PerformanceInterestRateAsOfDate.SecurityID = Transactions.SymbolID
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '04/26/2012' ) as PerformanceInterestRateBeginningDate0 on PerformanceInterestRateBeginningDate0.SecurityID = Transactions.SymbolID
 Left Outer Join Transactions as CoTransaction on ( ( CoTransaction.TransLinkID = Transactions.TransLinkID ) and  ( ( Transactions.SymbolID = CoTransaction.MoneyID ) or    ( Transactions.MoneyID = CoTransaction.SymbolID ) ) )
 Left Outer Join ExcludedAssets as CoTransactionExcludedAssets on ( CoTransaction.SymbolID = CoTransactionExcludedAssets.SecurityID and CoTransaction.PortfolioID  = CoTransactionExcludedAssets.PortfolioID )
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2012' ) as PerformanceInterestRateAsOfDate1 on PerformanceInterestRateAsOfDate1.SecurityID = Transactions.SymbolID
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2011' ) as PerformanceInterestRateBeginningDate1 on PerformanceInterestRateBeginningDate1.SecurityID = Transactions.SymbolID
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '07/26/2012' ) as PerformanceInterestRateAsOfDate2 on PerformanceInterestRateAsOfDate2.SecurityID = Transactions.SymbolID
 Left Outer Join dbo.ufn_DataEng_FindSecurityInterestRateByDate_Table( 'e4cca382-36cc-4393-afc3-8ce86176c72c', '06/18/2007' ) as PerformanceInterestRateBeginningDate2 on PerformanceInterestRateBeginningDate2.SecurityID = Transactions.SymbolID
 Where 
(Transactions.TradeDate <= '07/26/2012' and
 Transactions.SymbolID  is not NULL and
 Transactions.StatusTypeID = 100 and
 (Transactions.ActivityID != 160 or
 Transactions.CompleteTransactionFlag = 0) and
 PerformancePortfolio.PerformanceInceptionDate <= '07/26/2012') and
 ((PerformanceExcludedAssets.ExcludeFromPerformanceFlag  is NULL) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0) or
 (PerformanceExcludedAssets.ExcludeFromPerformanceFlag = 0)) and
 (Transactions.SymbolID < 2 or
 Transactions.SymbolID > 2 ) 
Order by 
Transactions.SymbolID  ,
 Transactions.LongPositionFlag  ,
 Transactions.PortfolioID  ,
 Transactions.TradeDate  ,
 Transactions.ActivityID  ,
 Transactions.TransactionID   ; 
 * 
 * 
 * Select 
PerformanceSecurity.AccrualMethodTypeID  as 'Performance_SecurityAccrualMethod',
 PerformanceSecurity.CompoundingFrequencyID  as 'Performance_SecurityCompoundFrequency',
 PerformanceSecurity.DisplayAccruedFlag  as 'Performance_SecurityDisplayAccrued',
 PerformanceSecurity.FirstCouponDate  as 'Performance_SecurityFirstCoupon',
 PerformanceSecurity.IncomeFrequencyID  as 'Performance_SecurityIncomeFrequency',
 PerformanceSecurity.IssueDate  as 'Performance_SecurityIssueDate',
 PerformanceSecurity.MaturityDate  as 'Performance_SecurityMaturityDate',
 PerformanceSecurity.PaymentDelay  as 'Performance_SecurityPaymentDelay',
 PerformanceSecurityType.AccruedInterestFlag  as 'Performance_SecurityTypeAccruedInterest',
 PerformanceSecurityType.CallableFlag  as 'Performance_SecurityTypeCallable',
 PerformanceSecurityType.UsePriceFactorFlag  as 'Performance_SecurityTypeUsePriceFactor',
 PerformanceSecurity.SecurityID  as 'Performance_SecurityID',
 PerformanceSecurity.SecurityTypeID  as 'Performance_SecurityTypeID',
 PerformanceSecurity.VariableRateFlag  as 'Performance_SecurityVariableRate',
 PerformanceSecurity.PaymentFrequencyIntervalID  as 'Performance_SecurityPaymentFreqInterval',
 PerformanceSecurity.ResetFrequencyIntervalID  as 'Performance_SecurityResetFreqInterval',
 PerformanceSecurity.PaymentFrequencyIntervalValue  as 'Performance_SecurityPaymentFreqIntervalValue',
 PerformanceSecurity.PaymentFrequencyID  as 'Performance_SecurityPaymentFreqType',
 PerformanceSecurity.ResetFrequencyID  as 'Performance_SecurityResetFreqType',
 PerformanceSecurity.ResetFrequencyIntervalValue  as 'Performance_SecurityResetFreqIntervalValue',
 PerformanceSecurity.PaymentFrequencyMarketHolidayRuleID  as 'Performance_SecurityPaymentFreqMarketHolidayRule',
 PerformanceSecurity.ResetFrequencyMarketHolidayRuleID  as 'Performance_SecurityResetFreqMarketHolidayRule',
 PerformanceSecurity.DaysUntilChange  as 'Performance_Security_DaysUntilChange',
 PerformanceSecurity.FirstResetDate  as 'Performance_Security_FirstResetDate',
 PerformanceSecurity.SpreadMarginOperatorID  as 'Performance_Security_SpreadMarginOperator',
 PerformanceSecurity.SpreadMarginBCDValue  as 'Performance_Security_SpreadMarginValue',
 PerformanceSecurity.ReferenceIndex  as 'Performance_Security_ReferenceIndex',
 PerformanceSecurityType.MarketValue  as 'Performance_SecurityTypeMarketValue',
 PerformanceSecurityType.SplitPrecisionDefault  as 'Performance_SecurityTypeSplitPrecisionDefault',
 PerformanceSecurity.LastCouponDate  as 'Performance_SecurityLastCoupon',
 PerformanceSecurityType.IsMortgageBackedFlag  as 'Performance_SecurityTypeIsMortBacked',
 PerformanceSecurityType.ComputeAccruedGainFlag  as 'Performance_SecurityTypeComputeAccruedGain',
 PerformanceSecurityType.ComputeAccruedIncomeFlag  as 'Performance_SecurityTypeComputeAccruedInco',
 PerformanceSecurity.NextDividendDate  as 'Performance_SecurityNextDivDate',
 PerformanceSecurity.NextEXDate  as 'Performance_SecurityNextExDate',
 PerformanceSecurity.NextDividendAmount  as 'Performance_SecurityNextDivAmt',
 PerformancePriceDateTable0.PriceBeginDate  as 'Performance_PriceDateOnBeginningDate0',
 PerformanceSecurityType.CashAccount  as 'Performance_SecurityIsCashAccount',
 PerformanceSecurityType.DefaultPFSReportAsTypeID  as 'Performance_SecurityTypeProceedsFromSaleRe',
 PerformanceSecurityBeginningPrice0.Price  as 'Performance_SecurityPriceJoinOnBeginningPriceDate0',
 PerformanceSecurity.SharesPerContract  as 'Performance_SecuritySharesPerContract',
 PerformanceSecurityType.PriceAdjustmentValue  as 'Performance_SecurityTypePriceAdjustmentValue',
 PerformanceSecurityType.OptionSecurityFlag  as 'Performance_SecuritiesSecurityTypeOptionSecurityFlag',
 PerformanceSecurityType.Mature  as 'Performance_SecurityTypeMature',
 PerformanceSecurityType.StraightLineAccrualFlag  as 'Performance_SecurityIsStraightLineAccrual',
 PerformancePriceDateTable0.PriceEndDate  as 'Performance_PriceDateOnEndingDate0',
 PerformanceSecurityType.AccruedInterestFlag  as 'Performance_SecurityTypeDisplayAccruedInte',
 PerformancePriceDateTable1.PriceBeginDate  as 'Performance_PriceDateOnBeginningDate1',
 PerformanceSecurityBeginningPrice1.Price  as 'Performance_SecurityPriceJoinOnBeginningPriceDate1',
 PerformancePriceDateTable1.PriceEndDate  as 'Performance_PriceDateOnEndingDate1',
 PerformancePriceDateTable2.PriceBeginDate  as 'Performance_PriceDateOnBeginningDate2',
 PerformanceSecurityBeginningPrice2.Price  as 'Performance_SecurityPriceJoinOnBeginningPriceDate2',
 PerformancePriceDateTable2.PriceEndDate  as 'Performance_PriceDateOnEndingDate2',
 PerformanceSecurity.Symbol  as 'Performance_SecuritySymbol'
From Securities AS PerformanceSecurity 
Left Outer Join DatasetSecurityTypes as PerformanceSecurityType on PerformanceSecurity.SecurityTypeID=PerformanceSecurityType.SecurityTypeID and PerformanceSecurity.DataSetID=PerformanceSecurityType.DataSetID 
 and ( PerformanceSecurityType.DataSetID = 1 )
 Left Outer Join ufn_DataEng_PriceDateJoin_Table('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '04/26/2012', '07/26/2012' ) as PerformancePriceDateTable0 on PerformancePriceDateTable0.PriceBeginDate=PerformancePriceDateTable0.PriceBeginDate AND 0 != 0
 Left Outer Join SecurityPrices as PerformanceSecurityBeginningPrice0 on PerformanceSecurity.SecurityID = PerformanceSecurityBeginningPrice0.SecurityID and ((0 != 0 and PerformanceSecurityBeginningPrice0.PriceDate = dbo.ufn_DataEng_FindBeginningPriceDateOnBeginningDate( 'e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '04/26/2012' )) or (0 = 0 and PerformanceSecurityBeginningPrice0.PriceDate = '04/26/2012')) 
 and ( PerformanceSecurityBeginningPrice0.DataSetID = 1 )
 Left Outer Join ufn_DataEng_PriceDateJoin_Table('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '07/26/2011', '07/26/2012' ) as PerformancePriceDateTable1 on PerformancePriceDateTable1.PriceBeginDate=PerformancePriceDateTable1.PriceBeginDate AND 0 != 0
 Left Outer Join SecurityPrices as PerformanceSecurityBeginningPrice1 on PerformanceSecurity.SecurityID = PerformanceSecurityBeginningPrice1.SecurityID and ((0 != 0 and PerformanceSecurityBeginningPrice1.PriceDate = dbo.ufn_DataEng_FindBeginningPriceDateOnBeginningDate( 'e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '07/26/2011' )) or (0 = 0 and PerformanceSecurityBeginningPrice1.PriceDate = '07/26/2011')) 
 and ( PerformanceSecurityBeginningPrice1.DataSetID = 1 )
 Left Outer Join ufn_DataEng_PriceDateJoin_Table('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '06/18/2007', '07/26/2012' ) as PerformancePriceDateTable2 on PerformancePriceDateTable2.PriceBeginDate=PerformancePriceDateTable2.PriceBeginDate AND 0 != 0
 Left Outer Join SecurityPrices as PerformanceSecurityBeginningPrice2 on PerformanceSecurity.SecurityID = PerformanceSecurityBeginningPrice2.SecurityID and ((0 != 0 and PerformanceSecurityBeginningPrice2.PriceDate = dbo.ufn_DataEng_FindBeginningPriceDateOnBeginningDate( 'e4cca382-36cc-4393-afc3-8ce86176c72c', 508, '06/18/2007' )) or (0 = 0 and PerformanceSecurityBeginningPrice2.PriceDate = '06/18/2007')) 
 and ( PerformanceSecurityBeginningPrice2.DataSetID = 1 )
  where 

( PerformanceSecurity.DataSetID = 1 )
 and  ( ( 508 = 0 ) or (PerformanceSecurity.SecurityID in ( Select DISTINCT SymbolID from Transactions where PortfolioID in ( select PortfolioID from dbo.ufn_DataEng_Filter_Portfolio_Group('e4cca382-36cc-4393-afc3-8ce86176c72c', 508, 1) ) ) ) )
 
 */
?>
