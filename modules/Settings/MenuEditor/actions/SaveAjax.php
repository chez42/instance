<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

Class Settings_MenuEditor_SaveAjax_Action extends Settings_Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('removeModule');
		$this->exposeMethod('addModule');
		$this->exposeMethod('saveSequence');
		$this->exposeMethod('updateAPPSequenceNumber');
		$this->exposeMethod('saveMenu');
		$this->exposeMethod('removeMenu');
		$this->exposeMethod('editMenu');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	function removeModule(Vtiger_Request $request) {
		$sourceModule = $request->get('sourceModule');
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		$menu = $db->pquery("SELECT menu_id FROM vtiger_menu WHERE menuname = ?",array($appName));
		if($db->num_rows($menu)){
		    $menuId = $db->query_result($menu, 0, 'menu_id');
		    $db->pquery('DELETE FROM vtiger_menu_tab WHERE tabid = ? AND menuid = ?', array(getTabid($sourceModule), $menuId));
		}
		$response = new Vtiger_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function addModule(Vtiger_Request $request) {
		$sourceModules = array($request->get('sourceModule'));
		if ($request->has('sourceModules')) {
			$sourceModules = $request->get('sourceModules');
		}
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		$menu = $db->pquery("SELECT menu_id FROM vtiger_menu WHERE menuname = ?",array($appName));
		if($db->num_rows($menu)){
		    $menuId = $db->query_result($menu, 0, 'menu_id');
		    $seq = $db->pquery(" SELECT MAX(sequence) as sequence FROM vtiger_menu_tab WHERE menuid = ?",array($menuId));
		    if(!$db->num_rows($seq) || !($db->query_result($seq, 0, 'sequence')))
		        $sequence = 1;
	        else
	            $sequence = $db->query_result($seq, 0, 'sequence') + 1;
	        
    		foreach ($sourceModules as $sourceModule) {
    		    $module = $db->pquery("SELECT * FROM vtiger_menu_tab WHERE menuid = ? AND tabid = ?",array($menuId, getTabid($sourceModule)));
    		    if(!$db->num_rows($module)){
		          $db->pquery('INSERT INTO vtiger_menu_tab(menuid, tabid, sequence) VALUES (?, ?, ?)', array($menuId, getTabid($sourceModule), $sequence));
		          $sequence++;
    		    }
    		}
		}
		$response = new Vtiger_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}

	function saveSequence(Vtiger_Request $request) {
		$moduleSequence = $request->get('sequence');
		$appName = $request->get('appname');
		$db = PearDatabase::getInstance();
		$menu = $db->pquery("SELECT menu_id FROM vtiger_menu WHERE menuname = ?",array($appName));
		if($db->num_rows($menu)){
		    $menuId = $db->query_result($menu, 0, 'menu_id');
    		foreach ($moduleSequence as $moduleName => $sequence) {
    			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
    			$db->pquery('UPDATE vtiger_menu_tab SET sequence = ? WHERE tabid = ? AND menuid = ?', array($sequence, $moduleModel->getId(), $menuId));
    		}
		}
		$response = new Vtiger_Response();
		$response->setResult(array('success' => true));
		$response->emit();
	}
	
	function updateAPPSequenceNumber(Vtiger_Request $request){
	    $sequence = $request->get('sequence');
	    $response = new Vtiger_Response();
	    try{
    	    $db = PearDatabase::getInstance();
    	    foreach($sequence as $appSequence => $seq){
    	        $db->pquery('UPDATE vtiger_menu SET sequence=? WHERE menuname=?',array($seq,$appSequence));
    	    }
    	    $response->setResult(array('success'=>true));
	    }catch(Exception $e) {
	        $response->setError($e->getCode(),$e->getMessage());
	    }
	    $response->emit();
	}
	
	function saveMenu(Vtiger_Request $request){
	    $menuName = $request->get('label');
	    $icon = $request->get('icon');
	    $color = $request->get('color_code');
	    
	    $response = new Vtiger_Response();
	    try{
	        $db = PearDatabase::getInstance();
	        $menu = $db->pquery("SELECT * FROM vtiger_menu WHERE menuname = ?",array($menuName));
	        if(!$db->num_rows($menu)){
	            $seq = $db->pquery(" SELECT MAX(sequence) as sequence FROM vtiger_menu ");
	            if(!$db->num_rows($seq) || !($db->query_result($seq, 0, 'sequence')))
	                $sequence = 1;
                else
                    $sequence = $db->query_result($seq, 0, 'sequence') + 1;
                $db->pquery("INSERT INTO vtiger_menu(menuname, sequence, icon, color) VALUES (?, ?, ?, ?)",
                    array($menuName, $sequence, $icon, $color));
                $success = true;
                $message = "Menu Created Successfully";
	        }else{
	            $message = "This Menu is already exists!";
	            $success = false;
	        }
	        $response->setResult(array('success'=>$success, 'message'=>$message, 'appName'=>$menuName,
	            'sequence'=>$sequence, 'icon'=>$icon, 'color'=>$color));
	    }catch(Exception $e) {
	        $response->setError($e->getCode(),$e->getMessage());
	    }
	    $response->emit();
	}
	
	function removeMenu(Vtiger_Request $request){
	    
	    $menuName = $request->get('appname');
	    $response = new Vtiger_Response();
	    try{
	        $success = false;
    	    if($menuName){
    	        $db = PearDatabase::getInstance();
    	        $menu = $db->pquery("SELECT menu_id FROM vtiger_menu WHERE menuname = ?",array($menuName)); 
    	        if($db->num_rows($menu)){
    	           $menuId = $db->query_result($menu, 0, 'menu_id');
    	           $db->pquery('DELETE FROM vtiger_menu WHERE menu_id = ?',array($menuId));
    	           $db->pquery('DELETE FROM vtiger_menu_tab WHERE menuid = ?',array($menuId));
    	           $success = true;
    	        }
    	    }
    	    $response->setResult(array('success'=>$success));
	    }catch(Exception $e) {
	        $response->setError($e->getCode(),$e->getMessage());
	    }
	    $response->emit();
	}
	
	
	function editMenu(Vtiger_Request $request){
	    $menuName = $request->get('label');
	    $icon = $request->get('icon');
	    $color = $request->get('color_code');
	    $response = new Vtiger_Response();
	    try{
	        $db = PearDatabase::getInstance();
	        $message = "This Menu is already exists!";
	        $success = false;
	        $menu = $db->pquery("SELECT * FROM vtiger_menu WHERE menuname = ?",array($request->get('appName')));
	        if($db->num_rows($menu)){
                $db->pquery("UPDATE vtiger_menu SET menuname=?, icon=?, color=? WHERE menuname=?",
                    array($menuName, $icon, $color, $request->get('appName')));
                $success = true;
                $message = "Menu Updated Successfully";
	        }
	        $response->setResult(array('success'=>$success, 'message'=>$message, 'appName'=>$menuName,
	            'icon'=>$icon, 'color'=>$color));
	    }catch(Exception $e) {
	        $response->setError($e->getCode(),$e->getMessage());
	    }
	    $response->emit();
	}
}

?>
