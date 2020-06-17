<?php
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_GetAccountingData_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        return;
    }
    
    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $records = $request->get('records');
        
        $sql = 'SELECT
                    vtiger_timecontrol.*,
                    vtiger_crmentity.label as productlabel,
                    time_to_sec(vtiger_timecontrol.totaltime) as totaltimesec
                FROM vtiger_timecontrol
                    LEFT JOIN vtiger_crmentity ON(vtiger_crmentity.crmid = vtiger_timecontrol.product_id)
                WHERE vtiger_timecontrol.timecontrolid IN ('.generateQuestionMarks($records).') AND invoiced_on = ""';
        $result = $adb->pquery($sql, $records);
        
        $data = array();
        $product_template = \TimeControl\Config::get('product_template');
        
        \TimeControl\VTTemplate::setTranslationModule('Timecontrol');
        
        $accountid = 0;
        $timeentryDate = array();
        while($row = $adb->fetchByAssoc($result)) {
            $TimeControlRecord = \TimeControl\VTEntity::getForId($row['timecontrolid']);
            
            if(empty($accountid)) $accountid = array('id' => $row['related_account_id'], 'name' => \Vtiger_Functions::getCRMRecordLabel($row['related_account_id']));
            
            if(empty($row['product_id'])) {
                continue;
            }
            /*$sql = 'SELECT vtiger_producttaxrel.*, vtiger_taxclass.taxclass
             FROM vtiger_producttaxrel
             INNER JOIN vtiger_taxclass ON (taxclassid = taxid)
             WHERE productid = ?';
             $resultTax = $adb->pquery($sql, array($row['product_id']));
             $taxes = array();
             while($tax = $adb->fetchByAssoc($resultTax)) {
             $taxes[$tax['taxid']] = array('taxlabel' => $tax['taxclass'], 'percentage' => $tax['taxpercentage']);
             }*/
            
            $product = \TimeControl\VTEntity::getForId($row['product_id']);
            $unit_price = $product->get('unit_price');
            $description = $product->get('description');
            
            $tax = getTaxDetailsForProduct($row['product_id'],'all');
            $taxes = array();
            foreach($tax as $tax_data){
                $regionList = array('default'=> $tax_data['percentage']);
                $compound = array();
                //$amount = (((ceil(($row['totaltimesec'] / 3600) * 10) / 10 )*$unit_price)*$tax_data['percentage'])/100;
                
                $taxes[$tax_data['taxname']] =array('productid'=>$tax_data['productid'],'taxid'=>$tax_data['taxid'],'taxlabel' => $tax_data['taxlabel'], 'taxpercentage' => $tax_data['percentage'],
                    'compoundOn'=>htmlentities(json_encode($compound)), 'regionsList' => htmlentities(json_encode($regionList)));
                
            }
            
            $text ='';
            
            if($row['festpreis'] || $row['anmerkung_zur']){
                $text =' - FIXED PRICE';
            }
            
            $data[] = array(
                'duration' => $row['totaltime'],
                'quantity' => ceil(($row['totaltimesec'] / 3600) * 10) / 10,
                'productid' => $row['product_id'],
                'productlabel' => $row['productlabel'],
                'unit_price' => $unit_price,
                'taxes' => $taxes,
                'module' => $product->getModuleName(),
                'description' => html_entity_decode($row['relatedname']).' - '.str_replace('\r\n',' ',trim(\TimeControl\VTTemplate::parse($product_template, $TimeControlRecord))).' - Dauer : '. $row['totaltime'] . $text,
            );
            
            $timeentryDate[] = $row['date_start'];
        }
        
        $gerMonth = array("January"=>"Januar", "February"=>"Februar", "March"=>"Marz", "April"=>"April", "May"=>"Mai", "June"=>"Juni",
            "July"=>"Juli", "August"=>"August","September"=>"September", "October"=>"Oktober", "November"=>"November", "December"=>"Dezember");
        
        if(count($timeentryDate) > 1){
            for ($i = 0; $i < count($timeentryDate); $i++)
            {
                if ($i == 0)
                {
                    $max_date = date('Y-m-d', strtotime($timeentryDate[$i]));
                    $min_date = date('Y-m-d', strtotime($timeentryDate[$i]));
                }
                else if ($i != 0)
                {
                    $new_date = date('Y-m-d', strtotime($timeentryDate[$i]));
                    if ($new_date > $max_date)
                    {
                        $max_date = $new_date;
                    }
                    else if ($new_date < $min_date)
                    {
                        $min_date = $new_date;
                    }
                }
            }
            
            $maxMonth = date('F',strtotime($max_date));
            $minMonth = date('F',strtotime($min_date));
            $maxYear = date('Y',strtotime($max_date));
            $minYear = date('Y',strtotime($min_date));
            
            if($minYear == $maxYear){
                if($minMonth == $maxMonth){
                    $preriodOfPerformance = "Leistrungszeitraum ". $gerMonth[$maxMonth] .' '.$maxYear;
                    $invoiceTitle = "Support ". $gerMonth[$maxMonth] .' '.$maxYear;
                }else{
                    $preriodOfPerformance = "Leistrungszeitraum ". $gerMonth[$minMonth] .' - '.$gerMonth[$maxMonth].' '.$maxYear;
                    $invoiceTitle = "Support ". $gerMonth[$minMonth] .' - '.$gerMonth[$maxMonth].' '.$maxYear;
                }
            }else{
                $preriodOfPerformance = "Leistrungszeitraum ". $gerMonth[$minMonth] .' '.$minYear.' - '.$gerMonth[$maxMonth].' '.$maxYear;
                $invoiceTitle = "Support ". $gerMonth[$minMonth] .' '.$minYear.' - '.$gerMonth[$maxMonth].' '.$maxYear;
            }
        }else if(!empty($timeentryDate)){
            
            $month = date('F',strtotime($timeentryDate[0]));
            $year = date('Y',strtotime($timeentryDate[0]));
            
            $preriodOfPerformance = "Leistrungszeitraum ".$gerMonth[$month].' '.$year;
            $invoiceTitle = "Support ". $gerMonth[$month].' '.$year;
            
        }
        
        if($request->get('invoiceid')){
            
            $invoice = $adb->pquery("SELECT count(*) as rowcount FROM vtiger_invoice
            INNER JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_invoice.invoiceid
            WHERE vtiger_invoice.invoiceid = ?",array($request->get('invoiceid')));
            
            if($adb->num_rows($invoice)){
                $data['rowcount'] = $adb->query_result($invoice,0,'rowcount');
            }
        }
        
        die(json_encode(array('accountid' => $accountid, 'data' => $data, 'preriodOfPerformance' => $preriodOfPerformance, 'invoicetitle'=>$invoiceTitle)));
    }
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }
}
