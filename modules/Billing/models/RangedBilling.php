<?php

require_once("modules/Billing/models/Billing.php");
require_once("libraries/Reporting/ReportCommonFunctions.php");

class capFlow{
    public $date, $fraction, $period, $flow, $rate, $adjustment_amount;
    public function __construct($date, $fraction, $period, $flow, $rate, $adjustment_amount)
    {
        $this->date = $date;
        $this->fraction = $fraction;
        $this->period = $period;
        $this->flow = $flow;
        $this->rate = $rate;
        $this->adjustment_amount = $adjustment_amount;
    }
}

class Billing_RangedBilling_Model extends Vtiger_DetailView_Model {
    private $billing;
    private $combined;

    public function GenerateIndividualData($accounts = array(), $title, $client_name, $module_type, $start, $end){
        $billing = new Billing($accounts, $title, $client_name, $module_type, $start, $end);
        $portfolios = &$billing->GetPortfoliosByRef();//Get the portfolio record's by reference so when we alter them, it has an effect on the object itself
        $total = 0;
        foreach($portfolios AS $a => $record){
            $data = $record->getData();//Get the record data (we can't access this directly a $v['specid'] for example, as this is an array of Record types (GetRecordByID)
            $data['specid'] = '1';//Make up a new variable name and fill in values.  We can do ANYTHING we want here, so define our report rules here
            $data['ranges'] = $this->GetRangesForSpecID(1);//Get ranges for the specified spec id
#            print_r($billing->GetAccountInfoByNumber($data['account_number']));exit;
            $data['ranges'] = $this->CalculateRangeValues($data['ranges'], $billing->GetAccountInfoByNumber($data['account_number'])->start_amount);
            $data['bill_amount'] = $this->CalculateRangeBillAmount($data['ranges']);
            $data['periodicity_number'] = Vtiger_Billing_Model::PeriodicityToNumber($data['periodicity']);
            $data['flow_date_start'] = GetDateMinusMonthsSpecified($start, $data['periodicity_number']);
            $billing->SetEndPeriod(GetDatePlusMonthsSpecified($start, $data['periodicity_number']));
            $data['capital_flow_transactions'] = new Billing_CapitalFlows_Model($data['account_number'], $data['flow_date_start'], $start);
            $data['capital_flows'] = $this->CalculateCapitalFlows($data['capital_flow_transactions']->GetTransactions(), $data['ranges']);

            foreach($data['capital_flows'] AS $k => $v){
                $total += $v->adjustment_amount;
            }
            $data['capital_flow_total'] = $total;
            $data['total_amount_due'] = $data['bill_amount'] + $data['capital_flow_total'];
            $total = 0;
            /*            $transactions[$data['account_number']] = GetTransactionRecords($data['account_number'], array("transaction_type" => " = 'Flow'",
                                                                                       "trade_date" => " between '{$start}' AND '{$end}'"));
            */
            $record->setData($data);//Set the data
        }
#        $billing->SetTransactions($transactions);
        $this->billing = $billing;
        return $this->billing;
    }

    public function GenerateCombinedData($accounts = array(), $title, $client_name, $module_type, $start, $end){
        $billing = new Billing($accounts, $title, $client_name, $module_type, $start, $end);
        $total = 0;
        $data = array();
        $data['specid'] = '1';//Make up a new variable name and fill in values.  We can do ANYTHING we want here, so define our report rules here
        $data['ranges'] = $this->GetRangesForSpecID(1);//Get ranges for the specified spec id
        $data['ranges'] = $this->CalculateRangeValues($data['ranges'], $billing->GetStartDateValue());
        $data['bill_amount'] = $this->CalculateRangeBillAmount($data['ranges']);
        $data['flow_date_start'] = GetDateMinusMonthsSpecified($start, 3);
        $data['capital_flow_transactions'] = new Billing_CapitalFlows_Model($accounts, $data['flow_date_start'], $start);
        $data['capital_flows'] = $this->CalculateCapitalFlows($data['capital_flow_transactions']->GetTransactions(), $data['ranges']);

        foreach($data['capital_flows'] AS $k => $v){
            $total += $v->adjustment_amount;
        }
        $data['capital_flow_total'] = $total;
        $data['total_amount_due'] = $data['bill_amount'] + $data['capital_flow_total'];

        $this->combined = $data;
        return $this->combined;
    }

    private function CalculateCapitalFlowAmount($fraction, $period, $flow, $rate){
#        echo $fraction . " / " . $period . " * " . $flow . " * " . "(" . $rate . " / " . "100)" . '<br />';
        return $fraction / $period * $flow * ($rate / 100);
    }

    public function CalculateCapitalFlows($transactions, $ranges){
        $caps = array();
        $reversed_ranges = array_reverse($ranges);
        $tier = array();
        $index = 0;//We start at tier 0
        foreach($reversed_ranges AS $k => $v){
            $pot = $v['bill_amount'];
            $max = $v['bill_amount'];
            if($k == 0)
                $max = 9999999999999;
            $tier[] = array("pot" => $pot,
                            "rate" => $v['rate'],
                            "max" => $max);
        }

        foreach($transactions AS $k => $record){
            $data = $record->getData();
            $flow = $data["operation"] . $data["net_amount"];
            while($flow != 0) {
                $pot = $tier[$index]['pot'];
                $remainder = $pot + $flow;
                if ($remainder <= 0) {
                    $calculated_flow = $flow - $remainder;
                    $amount = $this->CalculateCapitalFlowAmount($data['transaction_fraction'], $data['period_amount'],
                                                                $calculated_flow, $tier[$index]['rate']);//We wiped out the pot, so pass in whatever was left of it as the flow amount
                    $caps[] = new capFlow($data['trade_date'],
                        $data['transaction_fraction'],
                        $data['period_amount'],
                        $calculated_flow,
                        $tier[$index]['rate'],
                        $amount);

                    $tier[$index]['pot'] = 0;//Wipe out the pot
                    $index += 1;//Move to the next tier up at a higher percentage
                    $flow = $remainder;
                }else{//remainder is greater than 0
                    if($remainder > $tier[$index]['max']){//We are higher than the tier's max amount, fill in what needs to fill in
                        $calculated_flow = $flow - ($remainder - $tier[$index]['max']);
                        $flow -= $calculated_flow;
                        $index -= 1;
                    }else{
                        $calculated_flow = $flow;
                        $flow = 0;
                    }
                    $amount = $this->CalculateCapitalFlowAmount($data['transaction_fraction'], $data['period_amount'],
                        $calculated_flow, $tier[$index]['rate']);//We wiped out the pot, so pass in whatever was left of it as the flow amount
                    $caps[] = new capFlow($data['trade_date'],
                        $data['transaction_fraction'],
                        $data['period_amount'],
                        $calculated_flow,
                        $tier[$index]['rate'],
                        $amount);
                }
            }
        }
        return $caps;
    }

    /**
     * Calculate the total bill amount from ranges
     * @param $ranges
     * @return int
     */
    public function CalculateRangeBillAmount($ranges){
        $amount = 0;
        foreach($ranges AS $k => $v){
            $amount += $v['amount'];
        }
        return $amount;
    }

    /**
     * Calculate the range values with the passed in seed value (total value of account).  It is called
     * seed value here as it feeds the ranges as far as they can go based on the value passed in.
     * @param $ranges
     * @param $seed_value
     */
    public function CalculateRangeValues(Array $ranges, $seed_value){
        end($ranges);
        $lastElementKey = key($ranges);
        $total_amount = $seed_value;
        $bill_amount = 0;
        $seed_taken = 0;//The
        foreach($ranges AS $k => $v){
            switch($this->GetSeedRangeLocation($total_amount, $v)){
                case -1:
                    $ranges[$k]['amount'] = 0;
                    break;
                case 0:
                    if($bill_amount == 0)
                        $bill_amount = $total_amount;
                    else
                        $bill_amount = $total_amount - $seed_taken;
                    $ranges[$k]['amount'] = $bill_amount * ($v['rate'] / 100);
                    $ranges[$k]['bill_amount'] = $bill_amount;
                    $ranges[$k]['range_end'] = $total_amount;
#                    echo "{$bill_amount} * ({$v['rate']} / 100)<br />";
                    $seed_value -= $seed_value;
                    $seed_taken += $bill_amount;
                    break;
                case 1:
                    $bill_amount = $v['range_end'] - $seed_taken;
                    $ranges[$k]['amount'] = $bill_amount * ($v['rate'] / 100);
                    $ranges[$k]['bill_amount'] = $bill_amount;
#                    echo "{$bill_amount} * ({$v['rate']} / 100)<br />";
                    $seed_value -= $bill_amount;
                    $seed_taken += $bill_amount;
                    break;
            }
        }
        return $ranges;
    }

    /**
     * Takes in the seed value with a single range.
     * If the seed is lower than the start range, it returns -1.
     * If the seed is between the two ranges, it returns 0
     * If the seed is greater than the end range, it returns 1
     * @param $seed_value
     * @param $range
     * @return int
     */
    private function GetSeedRangeLocation($seed_value, $range){
        if($seed_value >=  $range['range_start'] && $range['range_end'] == -1) {//-1 represents infinity
#            echo "<br />0 INFINITE<br />";
            return 0;
        }
        if($seed_value < $range['range_start']) {
#            echo "<br />-1<br />";
            return -1;
        }
        if($seed_value >= $range['range_start'] && $seed_value <= $range['range_end']){
#            echo "<br />0<br />";
            return 0;
        }
        if($seed_value > $range['range_end']) {
#            echo "<br />1<br />";
            return 1;
        }
    }

    public function GetRangesForSpecID($specid){
        global $adb;
        $range = array();
        $query = "SELECT rangeid, range_start, range_end, rate 
                  FROM vtiger_billing_ranges 
                  WHERE rangeid IN (SELECT rangeid FROM vtiger_billing_spec_relations WHERE specid = ?)";
        $result = $adb->pquery($query, array($specid));
        if($adb->num_rows($result) > 0){
            while($x = $adb->fetchByAssoc($result)){
                $range[$x['rangeid']] = $x;
            }
        }
        return $range;
    }

    public function GetSpecIDByName($name){
        global $adb;
        $query = "SELECT specid FROM vtiger_billing_specs WHERE title = ?";
        $result = $adb->pquery($query, array($name));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, "specid");
        }
        return 0;
    }

    public function GetSpecNameByID($specid){
        global $adb;
        $query = "SELECT title FROM vtiger_billing_specs WHERE specid = ?";
        $result = $adb->pquery($query, array($specid));
        if($adb->num_rows($result) > 0){
            return $adb->query_result($result, 0, "title");
        }
        return 0;
    }

    public function GetBillingObject(){
        return $this->billing;
    }

    public function GetCombinedObject(){
        return $this->combined;
    }
}
