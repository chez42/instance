<?php

require_once("include/utils/omniscientCustom.php");
require_once("libraries/reports/cTransactions.php");
require_once('libraries/reports/cPortfolioDetails.php');
require_once("libraries/reports/cPholdingsInfo.php");
require_once("libraries/reports/pdf/cPDFDBAccess.php");
require_once("libraries/reports/cReports.php");
require_once("libraries/reports/cReturn.php");
require_once("libraries/reports/calculateReturn.php");

class PortfolioInformation_Performance_Model extends Vtiger_Module {
    public $warning, $pids;
    public $inception_irr, $qtr_irr, $ytd_irr, $trailing_irr;
    public $inception = array();
    public $qtr_ref, $trailing_ref, $ytd_ref, $inception_ref;
    public $qtr_bab, $trailing_bab, $ytd_bab, $inception_bab;
    public $goal, $management_fees;
    public $account;
    
    /**
     * Get the calculated goal amount based on the passed in pids
     * @global type $adb
     * @param type $pids
     */
    public function GetGoalPercent($pids){
        global $adb;
        
        // ==== START : 13June,2016 Change query where condition (AND total_value is not null AND total_value != 0  return 0 rows)
        $query = "SELECT portfolioinformationid 
                  FROM vtiger_portfolioinformation p
                  JOIN vtiger_crmentity e ON e.crmid = p.portfolioinformationid
                  WHERE account_number IN (SELECT portfolio_account_number FROM vtiger_portfolios WHERE portfolio_id IN ({$pids}))
                  AND (total_value is not null OR total_value != 0)
                  AND e.deleted = 0";
                  
        $result = $adb->pquery($query, array());
        $goal = 0;
        $calc = $adb->num_rows($result) * 100;//Number of rows * 100 for calculation purposes
        while($v = $adb->fetchByAssoc($result)){
            //$tmp = Vtiger_Record_Model::getInstanceById($v['portfolioinformationid']);
            //$goal += $tmp->get('cf_802');
            $goal += getSingleFieldValue("vtiger_portfolioinformationcf", 'cf_802', 'portfolioinformationid', $v['portfolioinformationid']);
        }

        $goal = $goal / $calc * 100;
        return $goal;
    }

	/* ====  END : Felipe 2016-07-25 MyChanges ===== */
       
    
    /**
     * Returns the account number and management fees amount based on portfolio ids
     * @global type $adb
     * @param type $pids
     * @return type
     */
    public function GetManagementFees($pids){
        global $adb;
        $query = "select p.portfolio_account_number AS account_number, SUM(cost_basis_adjustment) AS management_fee 
                  from vtiger_pc_transactions t
                  join vtiger_portfolios p ON p.portfolio_id = t.portfolio_id
                  WHERE activity_id = 160 
                  AND t.portfolio_id IN ({$pids}) 
                  AND report_as_type_id = 60
                  GROUP BY t.portfolio_id";
        $result = $adb->pquery($query, array());
        $fees = array();
        $total = 0;
        if($adb->num_rows($result) > 0){
	        while($v = $adb->fetchByAssoc($result)){
	            $fees[$v['account_number']] = $v['management_fee'];
	        }
        }
        
        return $fees;        
    }
    
    public function GenerateReport(Vtiger_Request $request){
        $report = new cReports();

        $calling_record = $request->get('calling_record');
        switch($request->get('calling_module')){
            case "Accounts":
                $pids = GetPortfolioIDsFromHHID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                break;
            case "Contacts":
                $pids = GetPortfolioIDsFromContactID($calling_record);
                $numbers = GetPortfolioAccountNumbersFromPids($pids);
                break;
            default:
                $numbers = $request->get('account_number');
                $acct = $request->get('account_number');
                $pids = $report->GetPortfolioIdsFromAccountNumber($acct);
                break;
        }

        //$pids = SeparateArrayWithCommas($pids); changes use php inbuilt function 9June2016
        
        $pids = implode(",", $pids);
        
        $this->goal = $this->GetGoalPercent($pids);
        $this->management_fees = $this->GetManagementFees($pids);
        $transaction_handler = new cReturn();
        $portfolio_transactions = $transaction_handler->GetAllPortfolioTransactions($pids, null);// $portfolio_info->GetAllPortfolioTransactions($pids, null);
        $transaction_handler->FillTransactionTable($portfolio_transactions);
        $all_transactions = $transaction_handler->GetAllTransactions();

        $inception_d = $transaction_handler->GetInceptionDate($pids);
        $inception_start_calculation_date = strtotime("{$inception_d}, -1 day");
        $inception_start_show_date = $inception_d;
        $inception_start_calculation_date = date("Y-m-d", $inception_start_calculation_date);
        $inception_end_calculation_date = strtotime("-1 day");

        $inception_start = str_replace("00:00:00", '', $inception_d);
        $inception_end = date("Y-m-d", $inception_end_calculation_date);

        $inception = GetInvestmentReturnColumn($transaction_handler, $pids, $inception_start_calculation_date, $inception_end);
        $inception['start_date'] = $inception_start;
        $inception['end_date'] = $inception_end;
        $inception['goal'] = $this->goal;
        
        $management_total = 0;
        
        if(!empty($this->management_fees)){
	        foreach($this->management_fees AS $k => $v){
	            $management_total += $v;
	        }
        }
        $inception['management_fees'] = $this->management_fees;
        $inception['management_total'] = $management_total;
        $inception['other_expenses'] = $inception['expenses'] - $management_total;

        $qtr_start_calculation_date = strtotime("-3 month, -2 day");
        $qtr_start_calculation_date = date("Y-m-d", $qtr_start_calculation_date);
        $qtr_start_show_date = strtotime("-3 month, -1 day");
        $qtr_end_calculation_date = strtotime("-1 day");

        $qtr_start = date("Y-m-d", $qtr_start_show_date);//The date we show as the start date.. We have a -1 day calculation date to get the end value of the previous day
        $qtr_end = date("Y-m-d", $qtr_end_calculation_date);//Today's date, when we are finished calculating for the given interval

        $qtr = GetInvestmentReturnColumn($transaction_handler, $pids, $qtr_start_calculation_date, $qtr_end);
        $qtr['start_date'] = $qtr_start;
        $qtr['end_date'] = $qtr_end;

        $trailing_start_calculation_date = strtotime("-1 year, -2 days");
        $trailing_start_show_date = strtotime("-1 year, -1 day");
        $trailing_start_calculation_date = date("Y-m-d", $trailing_start_calculation_date);
        $trailing_end_calculation_date = strtotime("-1 day");

        $trailing_start = date("Y-m-d", $trailing_start_show_date);
        $trailing_end = date("Y-m-d", $trailing_end_calculation_date);

        $trailing = GetInvestmentReturnColumn($transaction_handler, $pids, $trailing_start_calculation_date, $trailing_end);
        $trailing['start_date'] = $trailing_start;
        $trailing['end_date'] = $trailing_end;    

        $ytd_start_calculation_date = strtotime("last day of december, 1 year ago");
        $ytd_start_show_date = strtotime("first day of January, -1 day");
        $ytd_start_calculation_date = date("Y-m-d", $ytd_start_calculation_date);
        $ytd_end_calculation_date = strtotime("-1 day");

        $ytd_start = date("Y-m-d", $ytd_start_show_date);
        $ytd_end = date("Y-m-d", $ytd_end_calculation_date);

        $ytd = GetInvestmentReturnColumn($transaction_handler, $pids, $ytd_start_calculation_date, $ytd_end);
        $ytd['start_date'] = $ytd_start;
        $ytd['end_date'] = $ytd_end;    

        if($qtr['end_value']){
            $qtr_ref = $transaction_handler->getReferenceReturn("S&P",$qtr['start_date'],$qtr['end_date'],$qtr['expenses']/$qtr['end_value']);
            $qtr_bab = $transaction_handler->getReferenceReturn("AGG BOND",$qtr['start_date'],$qtr['end_date'],$qtr['expenses']/$qtr['end_value']);
        }
        $qtr_ref = round(100*$qtr_ref, 2);
        $qtr_bab = round(100*$qtr_bab, 2);

        if($trailing['end_value']){
            $trailing_ref = $transaction_handler->getReferenceReturn("S&P",$trailing['start_date'],$trailing['end_date'],$trailing['expenses']/$trailing['end_value']);
            $trailing_bab = $transaction_handler->getReferenceReturn("AGG BOND",$trailing['start_date'],$trailing['end_date'],$trailing['expenses']/$trailing['end_value']);
        }
        $trailing_ref = round(100*$trailing_ref, 2);
        $trailing_bab = round(100*$trailing_bab, 2);
        
        if($ytd['end_value']){
            $ytd_ref = $transaction_handler->getReferenceReturn("S&P",$ytd['start_date'],$ytd['end_date'],$ytd['expenses']/$ytd['end_value']);
            $ytd_bab = $transaction_handler->getReferenceReturn("AGG BOND",$ytd['start_date'],$ytd['end_date'],$ytd['expenses']/$ytd['end_value']);
        }
        $ytd_ref = round(100*$ytd_ref, 2);
        $ytd_bab = round(100*$ytd_bab, 2);

        if($inception['end_value']){
            $inception_ref = $transaction_handler->getReferenceReturn("S&P",$inception['start_date'],$inception['end_date'],$inception['expenses']/$inception['end_value']);
            $inception_bab = $transaction_handler->getReferenceReturn("AGG BOND",$inception['start_date'],$inception['end_date'],$inception['expenses']/$inception['end_value']);
        }
        $inception_ref = round(100*$inception_ref, 2);
        $inception_bab = round(100*$inception_bab, 2);

        if($inception['start_date'] >= $trailing['start_date'])
            $warning = "*";

        $inception['account_number'] = $numbers;
        
        $pdf_access = new cPDFDBAccess();
        $pdf_access->WritePerformance($inception);
        
        $inception['start_date'] = ConvertDateToMDY($inception['start_date']);
        $inception['end_date'] = ConvertDateToMDY($inception['end_date']);

        $ref = array("type"=>"ref",
                     "account_number"=>$numbers,
                     "trailing_3"=>$qtr_ref,
                     "trailing_12"=>$trailing_ref,
                     "year_to_date"=>$ytd_ref,
                     "inception"=>$inception_ref);
        $bar = array("type"=>"bar",
                     "account_number"=>$numbers,
                     "trailing_3"=>$qtr_bab,
                     "trailing_12"=>$trailing_bab,
                     "year_to_date"=>$ytd_bab,
                     "inception"=>$inception_bab);
        
        $pdf_access->WriteTWR($ref);
        $pdf_access->WriteTWR($bar);
        
        $accounts = $transaction_handler->GetAccountNumbers();
        $this->account = $pdf_access->DetermineAccount($accounts);
        $this->warning = $warning;
        $this->inception = $inception;
        $this->qtr_ref = $qtr_ref;
        $this->trailing_ref = $trailing_ref;
        $this->ytd_ref = $ytd_ref;
        $this->inception_ref = $inception_ref;
        $this->qtr_bab = $qtr_bab;
        $this->trailing_bab = $trailing_bab;
        $this->ytd_bab = $ytd_bab;
        $this->inception_bab = $inception_bab;
//        $this->inception_ref = $inception_ref;
        $this->pids = $pids;
    }
}
?>