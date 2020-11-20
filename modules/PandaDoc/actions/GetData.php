<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PandaDoc_GetData_Action extends Vtiger_GetData_Action {
    
    public function process(Vtiger_Request $request) {
        
        $record = $request->get('record');
        $sourceModule = $request->get('source_module');
        $response = new Vtiger_Response();
        
        $permitted = Users_Privileges_Model::isPermitted($sourceModule, 'DetailView', $record);
        if($permitted) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $sourceModule);
            $data = $recordModel->getData();
            
            $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
            $fields = $moduleModel->getFields();
            
            $token= array();
            $companyId = '';
            $recipients = array();
            
            foreach ($fields as $fieldName => $fieldModel){
                $value = $data[$fieldName];
                if($fieldModel->getFieldDataType() == 'reference'){
                    $entityNames = getEntityName(getSalesEntityType($value), array($value));
                    $value = $entityNames[$value];
                }
                if($fieldModel->getFieldDataType() == 'owner'){
                    $entityNames = getEntityName('Users', array($value));
                    $value = $entityNames[$value];
                }
                if($sourceModule == 'Contacts'){
                    $token['Client'.'.'.preg_replace('/[^A-Za-z0-9\-]/', '',vtranslate($fieldModel->get('label'), $sourceModule))] = $value;
                    if($fieldModel->getFieldDataType() == 'reference' && getSalesEntityType($data[$fieldName]) == 'Accounts'){
                        $companyId = $data[$fieldName];
                    }
                }else if($sourceModule == 'Accounts'){
                    $token['Company'.'.'.preg_replace('/[^A-Za-z0-9\-]/', '',vtranslate($fieldModel->get('label'), $sourceModule))] = $value;
                }
            }
            
            if($sourceModule == 'Contacts' ){
                
                $entityNames = getEntityName(getSalesEntityType($data['account_id']), array($data['account_id']));
                $companyName = $entityNames[$data['account_id']];
                
                $recipients[] = array(
                    'first_name'=> $data['firstname'],
                    'last_name'=> $data['lastname'],
                    'email' => $data['email'],
                    'phone' => $data['mobile'],
                    'company' => $companyName,
                    'roleName' => "Client",
                    //default: true
                );
                if($companyId){
                    $comPermitted = Users_Privileges_Model::isPermitted('Accounts', 'DetailView', $companyId);
                    if($comPermitted) {
                        $comRecordModel = Vtiger_Record_Model::getInstanceById($companyId, 'Accounts');
                        $comData = $comRecordModel->getData();
                        
                        $comModuleModel = Vtiger_Module_Model::getInstance('Accounts');
                        $comFields = $comModuleModel->getFields();
                        
                        foreach ($comFields as $comFieldName => $comFieldModel){
                            $comValue = $comData[$comFieldName];
                            if($comFieldModel->getFieldDataType() == 'reference'){
                                $comEntityNames = getEntityName(getSalesEntityType($comValue), array($comValue));
                                $comValue = $comEntityNames[$comValue];
                            }
                            if($comFieldModel->getFieldDataType() == 'owner'){
                                $comEntityNames = getEntityName('Users', array($comValue));
                                $comValue = $comEntityNames[$comValue];
                            }
                            
                            $token['Company'.'.'.preg_replace('/[^A-Za-z0-9\-]/', '',vtranslate($comFieldModel->get('label'), 'Accounts'))] = $comValue;
                        }
                        
                        $recipients[] = array(
                            'first_name'=> $comData['accountname'],
                            //'last_name'=> $data['lastname'],
                            'email' => $comData['email1'],
                            'phone' => $comData['phone'],
                            'company' => $comData['label'],
                            'roleName' => "Company",
                            //default: true
                        );
                    }
                }
            }else if ($sourceModule == 'Accounts'){
                $recipients[] = array(
                    'first_name'=> $data['accountname'],
                    //'last_name'=> $data['lastname'],
                    'email' => $data['email1'],
                    'phone' => $data['phone'],
                    'company' => $data['label'],
                    'roleName' => "Company",
                    //default: true
                );
            }
            
            $response->setResult(array('success'=>true, 'token'=>array_map('decode_html',$token), 'recipients'=>$recipients));
        } else {
            $response->setResult(array('success'=>false, 'message'=>vtranslate('LBL_PERMISSION_DENIED')));
        }
        $response->emit();
        
    }
    
}
