<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cPPerformance
 *
 * @author Ryan Sandnes
 * 
 */

require_once('include/utils/omniscientCustom.php');
require_once("libraries/reports/Portfolios.php");
require_once("libraries/reports/cPholdings.php");

class cPPerformance extends cPholdings {
    public $myguess, $myidays;
    public $data, $otherData, $title;
    public function __construct() {
        $this->data = array();
        $this->otherData = array();
        
        parent::__construct();
    }
    
    public function getSummaryData($portfolioId,$startDate,$endDate) {
        global $adb;
        $summary = $adb->pquery("
        select 
            t.activity_id, 
            t.report_as_type_id, 
            a.activity_name as ActivityName, 
            r.report_as_type_name as ReportAsTypeName, 
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
                AND t.portfolio_id = ?
                AND t.status_type_id = 100
                AND t.symbol_id is not NULL
            GROUP BY
                1,2
        ",array($startDate,$endDate,$portfolioId));

        $summaryData = array();
        if($adb->num_rows($summary) > 0)
        foreach ($summary as $r) {
            foreach(array('TotalValue',    'NetAmount',   'CostBasis',   'Principal',   'AccruedInterest') as $k) {
                $summaryData[$r['ActivityName']][$r['ReportAsTypeName']][$k] = $r[$k];
                $summaryData[$r['ActivityName']]['__TOTAL'][$k] += $r[$k];
            }
        }
        return $summaryData;
    }
    
    function getPeriodIRR($portfolios,$startDate,$endDate,$startVal,$endVal) {
        global $adb;
//        echo "END DATE: {$endDate}<br />Start Date: {$startDate}<br />";
        $result = $adb->pquery("SELECT to_days(?) - to_days(?)", array($endDate, $startDate));

        foreach($result AS $k => $v)
            $intervalDays = $v[0];
//        echo "INTERVAL DAYS: {$intervalDays}<br />";
/*        
        list($row) = $this->db->executeParam("SELECT to_days(?) - to_days(?)",$endDate,$startDate);
        $intervalDays = $row[0];*/
        //IRR
        $ivals = array();
        $query = "
            select
                if(trade_date > '{$startDate}',(to_days(trade_date) - to_days('{$startDate}') -1)/{$intervalDays},0) as days, 
                sum(if (t.symbol_id = 1 OR t.symbol_id is NULL,net_amount,if(t.activity_id in (70,90,110,120,130,140,160),-1,1)*if(total_value = 0,net_amount,total_value))) as VAL
            FROM vtiger_pc_transactions t 
                LEFT JOIN vtiger_pc_activities a on a.activity_id = t.activity_id 
                LEFT JOIN vtiger_pc_report_as_types r on r.report_as_type_id = t.report_as_type_id 
                LEFT JOIN vtiger_securities s on s.security_id = t.symbol_id
            WHERE 
                t.trade_date between '{$startDate}' and '{$endDate}'
                AND t.portfolio_id in ({$portfolios})
                AND t.status_type_id = 100
                AND (
                    (t.activity_id in (10,50,120) AND t.report_as_type_id is NULL)
                    OR (t.activity_id = 160 AND t.report_as_type_id = 80)
                )
            group by 1
            having VAL <> 0
        ";
        $ivals = $adb->pquery($query,array());

        $counter = 0;
        if($adb->num_rows($ivals) > 0)
        foreach($ivals AS $k => $v)
            $sivals[] = $v;//array(0 => $v[0], 1 => $v[1]);

        $sivals[] = array(0, $startVal);
        $sivals[] = array(1, $endVal * -1);
//        echo "IVALS:<br /><br />";
//        print_r($sivals);
//        echo "<br /><br />";
//        echo "INTERVAL DAYS: {$endVal}<br />";
        $guess = $this->getIRR($sivals);
//        echo "GUESS: {$guess}<br /><br />";
        
        if ($intervalDays >= 365)
            $irr = pow((1+$guess),(365/$intervalDays)) - 1;
        else
            $irr = $guess;
        return $irr;
    }
    
    function getReferenceReturn($symbol,$startDate,$endDate,$feePct = 0) {
        global $adb;

        $start = $adb->pquery("SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date <= ? AND security_symbol = ? order by price_date DESC limit 1",array($startDate,$symbol));
        
        if($adb->num_rows($start) <= 0)
            return 0;
        
        foreach($start AS $k => $v)
            $start = $v;
        
//        echo "SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date = {$endDate} AND security_symbol = {$symbol} order by price_date desc limit 1<br />";
        $end = $adb->pquery("SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date <= ? AND security_symbol = ? order by price_date desc limit 1",array($endDate,$symbol));
        if($adb->num_rows($end) <= 0)
            return 0;
        
        foreach($end AS $k => $v)
            $end = $v;
/*        list($start) = $adb->pquery("SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date = ? AND security_symbol = ? order by price_date asc limit 1",array($startDate,$symbol));
        list($end) = $adb->pquery("SELECT to_days(price_date), price_date, price from vtiger_securities join vtiger_pc_security_prices using (security_id) where price_date = ? AND security_symbol = ? order by price_date desc limit 1",array($endDate,$symbol));
*/
        $intervalDays = $end[0] - $start[0];

        $guess = $end[2] / $start[2] - 1;

        if ($intervalDays >= 365)
            $irr = pow((1+$guess),(365/$intervalDays)) - 1;
        else
            $irr = $guess;

        return $irr;

    }

    function getIRR($ivals) {
        //$guess = -1.0 * (1.0 + ($ivals[1][1] / $ivals[0][1]));
        $guess = 0.5;
        if ($guess <= -1)
            $guess = -0.999999999;

        $cnt = 0;
        do {
            $sum = 0;
            $sumDeriv = 0;
            foreach ($ivals as $i) {
                $pow = pow(1 + $guess,$i[0]);
                //debug("pow(1+$guess,$i[0]) = $pow<br/>");
                $sum += $i[1] / $pow;
                $sumDeriv += ($i[1]*$i[0]) / $pow;
            }
            if($sumDeriv != 0)
                $guess = $guess - $sum / (-1*$sumDeriv);    
            $cnt++;
            //debug("SUM: $sum SUMDERIV: $sumDeriv GUESS: $guess CNT: $cnt<BR/>\n");

        } while (abs($sum) > 0.00001 && $cnt < 200 && $guess > -1);

        return $guess;
    }
    
    public function LoadHouseholdPerformance($id, $portfolioOverride=null)
    {
        global $adb;
        $portfolios = array();
        
        if($portfolioOverride)
        {
            if($portfolioOverride == 'account')
            {
                $ids = $id;
                $portfolios = SeparateArrayWithCommas($id);
            }
            else
            {
                $ids = $this->GetPortfolioIDsFromContactID($id);
                $portfolios = SeparateArrayWithCommasAndSingleQuotes($ids);
            }
        }
        else
        {
            $ids = $this->GetPortfolioIDsFromHHID($id);
            $portfolios = $this->GetImplodedPortfolioIDsFromHHID($id);
        }

        $begin_val     = 0;
        $end_val       = 0;
        $net_contrib   = 0;
        $Ytd_net_contrib = 0;
        $income        = 0;
        $mgmt_fee      = 0;
        $other_fee     = 0;
        $invest_return = 0;
        $capital_appr  = 0;
        $expense_total = 0;
        
        $firstIvalStart = NULL;
        $ivalStart = NULL;
        $ivalEnd = NULL;
        $qtr_begin_val = NULL;
        $qtr_fee = 0;
        
        foreach ($ids as $portfolio_id) 
        {
            $tmp = $adb->pquery("SELECT interval_begin_value, interval_begin_date, to_days(interval_begin_date) as begin_date_days FROM vtiger_pc_portfolio_intervals where portfolio_id = '{$portfolio_id}' ORDER BY interval_begin_date ASC limit 1",null);
            
            if($adb->num_rows($tmp) <= 0)
                continue;
            
            $ivalStart = $tmp;
            foreach($ivalStart AS $k => $v)
                $ivalStart = $v;
            
            if (!$ivalStart) { //no intervals! bail
                continue;
            }

            if (!$firstIvalStart || $firstIvalStart['begin_date_days'] > $ivalStart['begin_date_days'])
                $firstIvalStart = $ivalStart;
            
            $ivalEnd = $adb->pquery("SELECT interval_end_value, interval_end_date FROM vtiger_pc_portfolio_intervals where portfolio_id = ? AND interval_end_date < concat(year(curdate()),'-',month(curdate()),'-01') ORDER BY interval_end_date DESC limit 1",array($portfolio_id));
            foreach($ivalEnd AS $k => $v)
                $ivalEnd = $v;
            
            $ivalYTDStart = $adb->pquery("SELECT interval_begin_value, interval_begin_date FROM vtiger_pc_portfolio_intervals where portfolio_id = ? AND interval_begin_date >= concat(year(?)-1,'-12-31') ORDER BY interval_begin_date ASC limit 1",array($portfolio_id,$ivalEnd['interval_end_date']));
            foreach($ivalYTDStart AS $k => $v)
                $ivalYTDStart = $v;
            
            $ivalQtrStart = $adb->pquery("SELECT interval_begin_value, interval_begin_date FROM vtiger_pc_portfolio_intervals where portfolio_id = ? AND interval_begin_date >= ? - interval 92 day ORDER BY interval_begin_date ASC limit 1",array($portfolio_id,$ivalEnd['interval_end_date']));

            foreach($ivalQtrStart AS $k => $v)
                $ivalQtrStart = $v;
            
            $summaryData = $this->getSummaryData($portfolio_id,$ivalStart['interval_begin_date'],$ivalEnd['interval_end_date']);
            $YtdSummaryData = $this->getSummaryData($portfolio_id,$ivalYTDStart['interval_begin_date'],$ivalEnd['interval_end_date']);
            $QtrSummaryData = $this->getSummaryData($portfolio_id,$ivalQtrStart['interval_begin_date'],$ivalEnd['interval_end_date']);

            $begin_val     += $ivalStart['interval_begin_value'];

            $end_val       += $ivalEnd['interval_end_value'];
            
            $net_contrib   += $summaryData['Flow']['__TOTAL']['CostBasis']
                           + $summaryData['Receipt of Securities']['__TOTAL']['TotalValue']
                           - $summaryData['Transfer of Securities']['__TOTAL']['TotalValue'] + $summaryData['Expense']['Federal Withholding']['CostBasis'];

            $Ytd_net_contrib   = $YtdSummaryData['Flow']['__TOTAL']['CostBasis']
                             + $YtdSummaryData['Receipt of Securities']['__TOTAL']['TotalValue']
                             - $YtdSummaryData['Transfer of Securities']['__TOTAL']['TotalValue'];
            
            $income        += $summaryData['Income']['__TOTAL']['CostBasis']
                             - $summaryData['Income']['LongTerm Gain']['CostBasis']
                             - $summaryData['Income']['Short-Term Gain']['CostBasis']
                             - $summaryData['Income']['Unclassified Gain']['CostBasis'];
            
            $mgmt_fee      += $summaryData['Expense']['Management Fee']['CostBasis'];
            
            $other_fee     += $expense_total - $mgmt_fee - $summaryData['Expense']['Federal Withholding']['CostBasis'];
            
            $ytd_fee = $YtdSummaryData['Expense']['__TOTAL']['CostBasis'];
            
            $qtr_fee = $QtrSummaryData['Expense']['__TOTAL']['CostBasis'];

            $ytd_begin_val += $ivalYTDStart['interval_begin_value'];
            
            $qtr_begin_val += $ivalQtrStart['interval_begin_value'];

            //$invest_return += $end_val - $net_contrib;
            $invest_return = $end_val - $begin_val - $net_contrib;

            $expense_total += $summaryData['Expense']['__TOTAL']['CostBasis'];
        }
            $other_fee = $expense_total - $mgmt_fee - $summaryData['Expense']['Federal Withholding']['CostBasis'];
            $capital_appr = $invest_return - $mgmt_fee - $other_fee - $income;
        
        //only calc for single portfolios for now
        $irr    = $this->getPeriodIRR($portfolios,$firstIvalStart['interval_begin_date'],$ivalEnd['interval_end_date'],$begin_val,$end_val);
        $irrYtd = $this->getPeriodIRR($portfolios,$ivalYTDStart['interval_begin_date'],$ivalEnd['interval_end_date'],$ytd_begin_val,$end_val);
        $irrQtr = $this->getPeriodIRR($portfolios,$ivalQtrStart['interval_begin_date'],$ivalEnd['interval_end_date'],$qtr_begin_val,$end_val);

        $refIrr    = $this->getReferenceReturn("S&P",$firstIvalStart['interval_begin_date'],$ivalEnd['interval_end_date'],($mgmt_fee + $other_fee)/$end_val);
        $refIrrYtd = $this->getReferenceReturn("S&P",$ivalYTDStart['interval_begin_date'],$ivalEnd['interval_end_date'],$ytd_fee/$end_val);
        $refIrrQtr = $this->getReferenceReturn("S&P",$ivalQtrStart['interval_begin_date'],$ivalEnd['interval_end_date'],$qtr_fee/$end_val);

        $this->data = array(
            'begin_val' => array('title'=>'Beginning Value','value'=>$begin_val),
            'net_contrib' => array('title'=>'Net Contributions','value'=>$net_contrib),
            'capital_appr' => array('title'=>'Capital Appreciation','value'=>$capital_appr),
            'income' => array('title'=>'Income','value'=>$income),
            'mgmt_fee' => array('title'=>'Management Fees','value'=>$mgmt_fee),
            'other_fee' => array('title'=>'Other Expenses','value'=>$other_fee),
            'end_val'   => array('title'=>'Ending Value','value'=>$end_val),
            'invest_return' => array('title'=>'Investment Return','value'=>$invest_return)
        );

        $this->otherData['ytd_return'] = round(100*$irrYtd,2);
        $this->otherData['qtr_return'] = round(100*$irrQtr,2);
        $this->otherData['total_return'] = round(100*$irr,2);

        $this->otherData['gspc_ytd_return'] = round(100*$refIrrYtd,2);
        $this->otherData['gspc_qtr_return'] = round(100*$refIrrQtr,2);
        $this->otherData['gspc_total_return'] = round(100*$refIrr,2);
        
        $this->title = date('m/d/Y',strtotime($firstIvalStart['interval_begin_date'])) . ' - ' . date('m/d/Y',strtotime($ivalEnd['interval_end_date']));
    }    
}

?>
