<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */

function vtws_managemodules($element) {
    
    global $adb;
    
    $data = array();
    
    if($element['mode'] == 'modulesList'){
        $Module_record = array();
        
        $invisible_modules = array('ModTracker', 'Users', 'Mobile', 'Integration', 'WSAPP', 'ModComments', 
        'Dashboard', 'ConfigEditor', 'CronTasks',
        'Import', 'Tooltip', 'CustomerPortal', 'Home', 'VtigerBackup', 'FieldFormulas', 'EmailTemplates', 
        'ExtensionStore');
    
        $Default_AllModule = array();
        
        $allmodule_query = $adb->pquery("select * from `vtiger_tab` where 
        name not in ('" . implode("','", $invisible_modules) . "')");
        
        if($adb->num_rows($allmodule_query)){
            for($am=0;$am<$adb->num_rows($allmodule_query);$am++){
                
                if($adb->query_result($allmodule_query,$am,'ishide') != 1)
                    $activeModules[] = $adb->query_result($allmodule_query,$am,'tabid');
                
                $allModules[] = $adb->query_result($allmodule_query,$am,'tabid');
                
                $Default_AllModule[$adb->query_result($allmodule_query,$am,'tabid')] = array(
                    'name' => $adb->query_result($allmodule_query,$am,'name'),
                    'id' => $adb->query_result($allmodule_query,$am,'tabid'),
                    'ishide' => $adb->query_result($allmodule_query,$am,'ishide'),
                );
            }
        }
        $data['moduleList'] = $Default_AllModule;
        $data['activeModules'] = $activeModules;
        $data['allModules'] = $allModules;
        
    }else if($element['mode'] == 'enableDisableModule'){
        
        $moduleManagerModel = new Settings_ModuleManager_Module_Model();
        
        if(!empty($element['enable'])) {
           
            foreach ($element['enable'] as $enable){
                
                $moduleName = getTabModuleName($enable);
                
                $moduleManagerModel->enableModule($moduleName);
                $adb->pquery("UPDATE vtiger_tab SET ishide = ? WHERE tabid = ?",array(0,$enable));
            }
       
        }
        if(!empty($element['disable'])){
            
            foreach ($element['disable'] as $disable){
                
                $moduleName = getTabModuleName($disable);
                
                $moduleManagerModel->disableModule($moduleName);
                $adb->pquery("UPDATE vtiger_tab SET ishide = ? WHERE tabid = ?",array(1,$disable));
            
            }
            
        }
        
        $data = array('success'=>true);
                
    }
    
    return $data;
}