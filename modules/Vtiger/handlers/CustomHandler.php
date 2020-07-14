<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class CustomHandler extends VTEventHandler{
    
    
    function handleEvent($eventName, $entityData) {
        $moduleName = $entityData->getModuleName();
        
        if($eventName == 'vtiger.entity.aftersave'){
            $modules = array('Contacts', 'Accounts', 'Leads');
            $data = $entityData->getData();
            
            if(in_array($moduleName, $modules)){
                $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
                $fieldModels = $moduleInstance->getFieldsByType('email');
                
                global $adb;
                $crmid = $entityData->getId();
                $setype = $moduleName;
                $optout = $data['emailoptout'];
                
                if($optout == 'on')
                    $optout=1;
                
                foreach ($fieldModels as $field => $fieldModel) {
                    $fieldId = $fieldModel->get('id');
                    
                    $adb->pquery("UPDATE vtiger_emailslookup SET opt_out=? WHERE crmid=? AND fieldid=?",
                        array($optout, $crmid, $fieldId));
                }
                
            }
        }
       
    }
    
}