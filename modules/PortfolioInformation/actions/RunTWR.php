<?php
require_once("libraries/reports/cTWR.php");
//require_once("libraries/reports/cTWR_DEBUG.php");
require_once("libraries/reports/cTransactions.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("include/utils/omniscientCustom.php");

class PortfolioInformation_RunTWR_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        set_time_limit(300);
        $pids = $request->get("pids");

        $inception = array();
        $transaction_handler = new cTWR();
        $portfolio_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);// $portfolio_info->GetAllPortfolioTransactions($pids, null);
        $transaction_handler->FillTransactionTable($portfolio_transactions);
        $all_transactions = $transaction_handler->GetAllTransactions();

        $inception['date'] = $transaction_handler->GetInceptionDate($pids);
        $inception['value'] = 0;

        $date = date('Y-m-d');
        $tmp = $transaction_handler->GetSymbolTotals($date);

        $value = $transaction_handler->AddAllSymbolTotals($tmp);

        $dates = array("sdate"=>$inception['date'],
                       "edate"=>$date);

        if($value != 0)
        {
            $tmp = $value/100;
            $threshold = 0.1*$tmp;
        }
        $ends = $transaction_handler->GetIntervalActivity(null, $dates, $threshold);

        $previous_value = 0;
        $total = 0;

        $inception['start']['date'] = $transaction_handler->GetInceptionDate($pids);
        $t = $transaction_handler->GetSymbolTotals($inception['date']);
        $t = $transaction_handler->AddAllSymbolTotals($t);
        $inception['start']['value'] = $t;
        $inception['end']['date'] = date("Y-m-d");
        $inception['end']['value'] = $tmp;

        $d = date("Y-m-d");

        $qtr['start']['date'] = date('Y-m-d', strtotime('-3 months'));
        $sdate_value = date('Y-m-d', strtotime('-3 months, -1 day'));
        $t = $transaction_handler->GetSymbolTotals($sdate_value);
        $t = $transaction_handler->AddAllSymbolTotals($t);
        $qtr['start']['value'] = $t;
        $qtr['end']['date'] = date("Y-m-d");
        $qtr['end']['value'] = $value;
        $qtr = $transaction_handler->CalculateTWRUsingIntervals($qtr['start']['date'], $qtr['end']['date'], $ends, $transaction_handler, $qtr['start']['value']);

        $trailing['start']['date'] = date('Y-m-d', strtotime('-1 year'));
        $sdate_value = date('Y-m-d', strtotime('-1 year, -1 day'));
        $t = $transaction_handler->GetSymbolTotals($sdate_value);
        $t = $transaction_handler->AddAllSymbolTotals($t);
        $trailing['start']['value'] = $t;
        $trailing['end']['date'] = date("Y-m-d");
        $trailing['end']['value'] = $value;

        $ytd['start']['date'] = date('Y-m-d', strtotime('first day of January, -1 day'));
        $sdate_value = date('Y-m-d', strtotime('last day of december, 1 year ago'));
        $t = $transaction_handler->GetSymbolTotals($sdate_value);
        $t = $transaction_handler->AddAllSymbolTotals($t);
        $ytd['start']['value'] = $t;
        $ytd['end']['date'] = date("Y-m-d");
        $ytd['end']['value'] = $value;

//        if($inception['date'] >= $trailing['start']['date'])
//            $warning = "*";    

        $trailing = $transaction_handler->CalculateTWRUsingIntervals($trailing['start']['date'], $trailing['end']['date'], $ends, $transaction_handler, $trailing['start']['value']);
        $ytd = $transaction_handler->CalculateTWRUsingIntervals($ytd['start']['date'], $ytd['end']['date'], $ends, $transaction_handler, $ytd['start']['value']);

        $inception = $transaction_handler->CalculateTWRUsingIntervals($inception['start']['date'], $inception['end']['date'], $ends, $transaction_handler);
        $tmp = array("INCEPTION"=>"{$inception['value']}",
                     "QTR"=>"{$qtr['value']}",
                     "TRAILING"=>"{$trailing['value']}",
                     "YTD"=>"{$ytd['value']}",
                     "INCEPTION_TYPE"=>$inception['type'],
                     "QTR_TYPE"=>$qtr['type'],
                     "TRAILING_TYPE"=>$ytd['type'],
                     "YTD_TYPE"=>$ytd['type'],
                     "TRAILING_WARNING"=>$warning);

        if(!is_array($pids))
            $pids = explode(",",$pids);
        $numbers = GetPortfolioAccountNumbersFromPids($pids);

        $account = SeparateArrayWithCommas($numbers);
        $pdf_access = new cPDFDBAccess();

        $twr = array("type"=>"twr",
                     "account_number"=>$numbers,
                     "trailing_3"=>$qtr['value'],
                     "trailing_12"=>$trailing['value'],
                     "year_to_date"=>$ytd['value'],
                     "inception"=>$inception['value']);

        $pdf_access->WriteTWR($twr);
        echo json_encode($tmp);
    }
}
?>