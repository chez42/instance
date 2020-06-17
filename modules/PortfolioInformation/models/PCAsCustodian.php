<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-06-15
 * Time: 1:34 PM
 */

class PortfolioInformation_PCAsCustodian_Model extends Vtiger_Module {
    static public function CreateAndUpdatePortfolios($accounts){
        foreach($accounts AS $k => $v){
            $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($k);
            if($crmid == 0){
                $record = PortfolioInformation_Record_Model::getCleanInstance("PortfolioInformation");
                $mode = 'create';
                echo "Creating... " . $v['AccountNumber'];
            }else{
                echo 'Editing: ' . $v['AccountNumber'] . ' -- ' . $crmid . '<br />';
                $record = PortfolioInformation_Record_Model::getInstanceById($crmid);
                $mode = 'edit';
            }
            $data = $record->getData();
            $data['account_number'] = $v['AccountNumber'];
            $data['description'] = $v['AccountDescription'];
            $data['account_title1'] = $v['AccountTitle'];
            $data['account_type'] = $v['AccountType'];
            $data['production_number'] = $v['RepCode'];
            $data['first_name'] = $v['FirstName'];
            $data['last_name'] = $v['LastName'];
            $data['address1'] = $v['AddressLine1'];
            $data['address2'] = $v['AddressLine2'];
            $data['city'] = $v['City'];
            $data['state'] = $v['State'];
            $data['zip'] = $v['Zip'];
            $data['tax_id'] = $v['TaxID'];
            $data['description'] = $v['PortfolioDescription'];
            $data['origination'] = 'Manual';
            $record->setData($data);
            $record->set('mode', $mode);
            $record->save();
        }
    }

    static public function UpdatePortfolioBalances($balances){
        foreach($balances AS $k => $v){
            $crmid = PortfolioInformation_Module_Model::GetCrmidFromAccountNumber($k);
            if($crmid != 0){
                echo 'Editing: ' . $v['AccountNumber'] . ' -- ' . $crmid . '<br />';
                $record = PortfolioInformation_Record_Model::getInstanceById($crmid);
                $mode = 'edit';
                $data = $record->getData();
                $data['total_value'] = $v['IntervalEndValue'];
                $data['stated_value_date'] = $v['IntervalEndDate'];
                $record->setData($data);
                $record->set('mode', $mode);
                $record->save();
            }
        }
    }
}