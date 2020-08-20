<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Vtiger_QuickCreateMenu_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $db = PearDatabase::getInstance();
            $moduleIdsList = $request->get('moduleIdsList');
            if (!$moduleIdsList) {
                $moduleIdsList = array(0);
            }
            
            //Fields Info
            if (count($moduleIdsList)) {
                
                $query = 'UPDATE vtiger_tab SET quick_create_seq =?';
                $params = array(null);
                $db->pquery($query, $params);
                
                for ($i=0; $i<=count($moduleIdsList); $i++){
                    $query = 'UPDATE vtiger_tab SET quick_create_seq =? WHERE tabid=?';
                    $params = array($i+1,$moduleIdsList[$i]);
                    $db->pquery($query, $params);
                }
            }
            
            $response->setResult(true);
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
}
