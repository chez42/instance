<?php

class PortfolioInformation_CreateCSV_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        $records = $request->get('recordmodels');
        $records = unserialize(base64_decode(($records)));
        $filename = $request->get('filename');
        $extension = $request->get('extension');
        $new_csv = fopen('/tmp/'.$filename.$extension, 'w');
        $custodian = $request->get('custodian_select');
        $report = $request->get('bill_report');

        switch([strtolower($report), strtolower($custodian)]){
            case ["fees", "td"]:
                $rows = $this->GenerateRowsFromFields(array("account_number", "Q", "bill_amount"), $records);
                break;
            case ["fees", "fidelity"]:
                $rows = $this->GenerateRowsFromFields(array("account_number", "last_name", "total_value", "cash", "annual_fee_percentage", "bill_amount"), $records);
                $headers = array("Account Number", "Description", "Total Value", "Total Cash", "Fee (%)", "Fee ($)", "Cash Needed");
                fputcsv($new_csv, $headers);
                foreach($rows AS $k => $v){
                    $bill = str_replace(',', '', $v['bill_amount']);
                    $cash = str_replace(',', '', $v['cash']);
                    if($bill > $cash)
                        $rows[$k][] = 'YES';
                    else
                        $rows[$k][] = '';
                }
                break;
        }

        foreach($rows AS $k => $v){
            fputcsv($new_csv, $v);
        }

        fclose($new_csv);
        header("Content-type: text/csv");
        header("Content-disposition: attachment; filename = " . $filename . $extension);
        readfile("/tmp/".$filename.$extension);
    }

    public function GenerateRowsFromFields(array $fields, $records){
        $row = array();

        foreach($records AS $k => $v){
            $tmp = array();
            $data = $v->getData();
#            print_r($data);echo "<br /><br />";
            foreach($fields AS $a => $b){
                if(array_key_exists($b, $data))
                    $tmp[$b] = $v->getDisplayValue($b);
                else
                    $tmp[$b] = $b;
            }
            $row[] = $tmp;
        }
        return $row;
    }
}