<?php
include_once("libraries/Reporting/ReportCommonFunctions.php");

class Vtiger_Billing_Action extends Vtiger_Action_Controller {
    private $periodicity_val = array("quarterly" => 3, "monthly" => 1);

    public function process(Vtiger_Request $request)
    {
        $records = $request->get('recordmodels');
        $records = unserialize(base64_decode(($records)));
        $as_of = date("Y-m-d", strtotime($request->get('as_of')));
        $extra = array();
        foreach ($records AS $k => $v) {
            $tmp = array();
            $data = $v->getData();
            $period = $this->periodicity_val[strtolower($data['periodicity'])];
            $start = GetDateMinusMonthsSpecified($as_of, $period);
            $records[$k] = $data;

            switch(strtolower($data['periodicity'])){
                case "quarterly":
                    $bill_amount = Vtiger_Billing_Model::CalculateArrearBilling($data['account_number'],
                        $start,
                        $as_of,
                        $data['annual_fee_percentage'],
                        $period,$extra);
                    $data['bill_amount'] = $bill_amount;
                    $data['total_value'] = $extra['intervalendvalue'];
//                    $data['production_number'] = 'hello';
                    break;
            }
            $records[$k] = $data;
            $row = Vtiger_Billing_Model::GetLatestDateRowFromArrearTable();
            $records[$k]['row'] = $row;
            $records[$k]['as_of_tmp'] = $as_of;
        }

##        $record_serialized = base64_encode(serialize($records));
##        $records['serialized'] = $record_serialized;
        echo json_encode($records);
    }
}