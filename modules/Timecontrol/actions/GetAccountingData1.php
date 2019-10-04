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
                WHERE vtiger_timecontrol.timecontrolid IN ('.generateQuestionMarks($records).')';
        $result = $adb->pquery($sql, $records);

        $data = array();
        $product_template = \TimeControl\Config::get('product_template');

        \TimeControl\VTTemplate::setTranslationModule('Timecontrol');

        $accountid = 0;

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
                $taxes[] = array('taxid'=>$tax['taxid'],'taxlabel' => $tax['taxclass'], 'percentage' => $tax['taxpercentage']);
            }*/
            

            //echo"<pre>";print_r($taxes);echo"</pre>";
            $product = \TimeControl\VTEntity::getForId($row['product_id']);
            $unit_price = $product->get('unit_price');
            $description = $product->get('description');

            $tax = getTaxDetailsForProduct($row['product_id'],'all');
			$taxes = array();
            foreach($tax as $tax_data){
            	$regionList = array('default'=> $tax_data['percentage']);
            	$compound = array();
            	$amount = (((ceil(($row['totaltimesec'] / 3600) * 10) / 10 )*$unit_price)*$tax_data['percentage'])/100;
            	
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
            	'entityType' =>$product->getModuleName(),
            	'entityIdentifier'=>$product->getModuleName(),
                'description' => $row['relatedname'].' - '.trim(\TimeControl\VTTemplate::parse($product_template, $TimeControlRecord)).' - Dauer : '. $row['totaltime'] .$text,
            );
        }
        
        //echo"<pre>";print_r(trim(\TimeControl\VTTemplate::parse($product_template, $TimeControlRecord)));echo"</pre>";
//"TitelOfRealtedItem - Period 26.12.2017 18:03:00  - 18:04:00 - Dauer: 00:01"
        die(json_encode(array('accountid' => $accountid, 'data' => $data)));
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }
}