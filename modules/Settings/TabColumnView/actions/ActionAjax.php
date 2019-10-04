<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_TabColumnView_ActionAjax_Action extends Settings_Vtiger_Index_Action {
    
    public function __construct() {
        $this->exposeMethod('switchToTab');
        $this->exposeMethod('save');
        $this->exposeMethod('saveTabView');
        $this->exposeMethod('deleteTab');
        $this->exposeMethod('updateSequenceNumber');
    }
    
    public function switchToTab(Vtiger_Request $request) {
        $moduleName = $request->get('module_name');
        $isTab = $request->get('is_tab');
        $response = new Vtiger_Response();
        global $adb;
        $block = $adb->pquery("SELECT * FROM vtiger_module_tab_view WHERE module_name = ?",
            array($moduleName));
        if($adb->num_rows($block)){
            $tab = $adb->query_result($block, 0, 'is_tab');
            if($tab == 1){
                $isTab = 0;
                $message = "Turn off Tabs successfully";
            }elseif($tab == 0){
                $isTab = 1;
                $message = "Turn on Tabs successfully";
            }
            $adb->pquery("UPDATE vtiger_module_tab_view SET is_tab = ? WHERE module_name = ?",
                array($isTab, $moduleName));
            $success = true;
        }else{
            $adb->pquery("INSERT INTO vtiger_module_tab_view (module_name, is_tab) VALUES (?, ?)",
                array($moduleName, $isTab));
            $blocks = $adb->pquery("SELECT * FROM vtiger_blocks WHERE tabid = ?",array(getTabid($moduleName)));
            if($adb->num_rows($blocks)){
                $seq = $adb->pquery("SELECT MAX(sequence) as sequence FROM vtiger_module_tab WHERE module_name = ?",
                    array($moduleName));
                if(!$adb->num_rows($seq) || !($adb->query_result($seq, 0, 'sequence')))
                    $sequence = 1;
                else 
                    $sequence = $adb->query_result($seq, 0, 'sequence') + 1;
                
                for($i=0;$i<$adb->num_rows($blocks);$i++){
                    $blockData = $adb->query_result_rowdata($blocks, $i);
                    $checkBlock = $adb->pquery("SELECT * FROM vtiger_module_tab
                    INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
                    WHERE vtiger_module_tab_blocks.blockid=? AND vtiger_module_tab.module_name=?",
                        array($blockData['blockid'], $moduleName));
                    if(!$adb->num_rows($checkBlock)){
                        $adb->pquery("INSERT INTO vtiger_module_tab(tab_name, module_name, module_id, sequence) 
                        VALUES (?,?,?,?)",array($blockData['blocklabel'], $moduleName, getTabid($moduleName), $sequence));
                        if($adb->getLastInsertID()){
                            $adb->pquery("INSERT INTO vtiger_module_tab_blocks(tabid, blockid, columns, blocksequence) 
                            VALUES (?,?,?,?)",array($adb->getLastInsertID(),$blockData['blockid'],2,1));
                        }
                        $sequence += 1;
                    }
                }
            }
            $success = true;
            $message = "Turn on Tabs successfully";
        }
        if($success){
            $response->setResult(array('success'=>true, 'message'=>$message,'isTab'=>$isTab));
        }else{
            $response->setError(array('success'=>false, 'message'=>'Falied To convert Tabs'));
        }
        $response->emit();
    }
    
    public function save(Vtiger_Request $request) {
        $moduleName = $request->get('sourceModule');
        $isTab = $request->get('is_tab');
        $name = $request->get('label');
        $tabId ='';
        $response = new Vtiger_Response();
        global $adb;
        $block = $adb->pquery("SELECT * FROM vtiger_module_tab WHERE module_name = ? AND tab_name = ?",
            array($moduleName,$name));
        if($adb->num_rows($block)){
            $message = "This tab is already exists!";
            $success = false;
        }else{
            $seq = $adb->pquery("SELECT MAX(sequence) as sequence FROM vtiger_module_tab WHERE module_name = ?",
                array($moduleName));
            if(!$adb->num_rows($seq) || !($adb->query_result($seq, 0, 'sequence')))
                $sequence = 1;
            else
                $sequence = $adb->query_result($seq, 0, 'sequence') + 1;
            
            $adb->pquery("INSERT INTO vtiger_module_tab(tab_name, module_name, module_id, sequence) 
                        VALUES (?,?,?,?)",
                array($name, $moduleName, getTabid($moduleName), $sequence));
            $tabId = $adb->getLastInsertID();
            $success = true;
            $message = "Tab Created Successfully";
        }
        if($success){
            $response->setResult(array('success'=>true, 'message'=>$message,'tabName'=>$name, 'tabId'=>$tabId, 'sequence'=>$sequence));
        }else{
            $response->setResult(array('success'=>false, 'message'=>$message));
        }
        $response->emit();
    }
    
    public function saveTabView(Vtiger_Request $request) {
        
        $sourceModule = $request->get('sourceModule');
        $data = $request->get('tabData');
        $response = new Vtiger_Response();
        global $adb;
        
        foreach($data as $key=>$tabData){
            
            foreach($tabData as $tabkey=>$tab){
                
                $seq = $adb->pquery("SELECT * FROM vtiger_module_tab
                INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
                WHERE tabid = ?",
                    array($tab['tabId']));
                if(!$adb->num_rows($seq) || !($adb->query_result($seq, 0, 'blocksequence')))
                    $sequence = 1;
                else
                    $sequence = $adb->query_result($seq, 0, 'blocksequence') + 1;
                
                if($tabkey == 'columnsData'){
                    $block = $adb->pquery("SELECT * FROM vtiger_module_tab
                    INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
                    WHERE vtiger_module_tab_blocks.blockid = ? AND vtiger_module_tab.module_name=?",
                        array($tab['block_id'], $sourceModule));
                    if($adb->num_rows($block)){
                        $tabid = $adb->query_result($block, 0,'id');
                        $adb->pquery("UPDATE vtiger_module_tab_blocks SET columns = ? WHERE blockid = ? AND tabid=?",
                            array($tab['columns'], $tab['block_id'], $tabid));
                    }
                }elseif($tabkey == 'tabData'){
                    $block = $adb->pquery("SELECT * FROM vtiger_module_tab
                    INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
                    WHERE vtiger_module_tab_blocks.blockid = ? AND vtiger_module_tab.module_name=?",
                        array($tab['blockId'], $sourceModule));
                    if($adb->num_rows($block)){
                        $tabid = $adb->query_result($block, 0,'id');
                        $adb->pquery("UPDATE vtiger_module_tab_blocks SET tabid = ? WHERE blockid = ? AND tabid = ?",
                            array($tab['tabId'], $tab['blockId'], $tabid));
                    }else{
                        $adb->pquery("INSERT INTO vtiger_module_tab_blocks(tabid, blockid, columns, blocksequence) 
                            VALUES (?,?,?,?)",
                            array($tab['tabId'], $tab['blockId'], 2, $sequence));
                    }
                }elseif($tabkey == 'fieldData'){
                    $adb->pquery("UPDATE vtiger_module_tab_blocks SET blocksequence = ? WHERE blockid = ? AND tabid = ?",
                        array($tab['blockSequence'], $tab['blockid'], $tab['tabid']));
                }
                $sequence+=1;
            }
        }
        $response->setResult(array('success'=>true, 'message'=>'SuccessFully Updated Tabs.'));
        $response->emit();
    }
    
    public function deleteTab(Vtiger_Request $request) {
        $moduleName = $request->get('sourceModule');
        $tabId = $request->get('tabid');
        $response = new Vtiger_Response();
        global $adb;
        
        try{
            $checkTab = $adb->pquery("SELECT * FROM vtiger_module_tab
            INNER JOIN vtiger_module_tab_blocks ON vtiger_module_tab_blocks.tabid = vtiger_module_tab.id
            WHERE vtiger_module_tab.module_name=? AND vtiger_module_tab.id=? ",array($moduleName, $tabId));
            if($adb->num_rows($checkTab)){
                    $response->setResult(array('success'=>false, 'message'=>"Some blocks in this tab first move the blocks into another tab."));
            }else{
                $adb->pquery("DELETE FROM vtiger_module_tab
                    WHERE vtiger_module_tab.module_name=? AND vtiger_module_tab.id=? ",array($moduleName, $tabId));
                $response->setResult(array('success'=>true, 'message'=>"Tab Deleted Successfully."));
            }
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
    
    public function updateSequenceNumber(Vtiger_Request $request) {
        
        $sequence = $request->get('sequence');
        $moduleName = $request->get('selectedModule');
        $response = new Vtiger_Response();
        global $adb;
        try{
            foreach($sequence as $key=>$seq){
                $adb->pquery("UPDATE vtiger_module_tab SET sequence=? WHERE id = ? AND module_name = ?",
                    array($seq, $key, $moduleName));
            }
            $response->setResult(array('success'=>true));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        
        $response->emit();
    }
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
    
}