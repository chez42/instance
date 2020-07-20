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
        
        $modules = Settings_ModuleManager_Module_Model::getAll();
        
        $allModules = array();
        $activeModules = array();
        $defaultAllModule = array();
        
        foreach($modules as $tabid=>$module){
            
            $allModules[] = $module->id;
            if(!$module->ishide){
                $activeModules[] = $module->id;
            }
            
            $defaultAllModule[$module->id] = array(
                'name' => $module->name,
                'id' => $module->id,
                'ishide' => $module->ishide,
            );
        }
        
        $data['moduleList'] = $defaultAllModule;
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