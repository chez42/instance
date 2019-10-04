<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CustomStyler_AjaxActions_Action extends Vtiger_Action_Controller {
	
    function __construct() {
		parent::__construct();
		$this->exposeMethod('getStyleForCurrentUser');
		$this->exposeMethod('getMYCStylePresets');
		$this->exposeMethod('setStyleForCurrentUser');
		$this->exposeMethod('saveStylePreset');
		$this->exposeMethod('deleteStylePreset');
		
	}

	function checkPermission(Vtiger_Request $request) {
		return true;
	}
	
	function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
	function setStyleForCurrentUser(Vtiger_Request $request){
		$moduleName = $request->getModule();
		global $current_user, $adb;
		
		$result = $adb->pquery("select * from vtiger_customstyler_current_user_style where userid = ?",
		array($current_user->id));
		
		if($adb->num_rows($result)){
			$adb->pquery("update vtiger_customstyler_current_user_style set style = ? where userid = ?",
			array($request->get("styleid"), $current_user->id));
		} else {
			$adb->pquery("insert into vtiger_customstyler_current_user_style(userid, style) values(?,?)",
			array($current_user->id, $request->get("styleid")));
		}
		
		$result = array('success'=>true, 'result' => array("success" => true));
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	public function getStyleForCurrentUser(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		global $current_user, $adb;
		
		$result = $adb->pquery("select * from vtiger_customstyler_current_user_style where userid = ?",
		array($current_user->id));
		
		$data = array();
		
		if($current_user->is_admin == 'on'){
			$data['isAdmin'] = true;	
		} else {
			$data['isAdmin'] = false;	
		}
		$data['user'] = $current_user->id;
		
		if($adb->num_rows($result)){
			$data['style'] = $adb->query_result($result, 0, "style");
		}
		
		$result = array('success'=>true, 'result' => $data);
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
	
	
	public function getMYCStylePresets(Vtiger_Request $request) {
	    
	    global $adb,$current_user;
	    
	    $defaultTheme = $adb->pquery("SELECT * FROM vtiger_customstyler 
        LEFT JOIN vtiger_customstyler_current_user_style ON vtiger_customstyler_current_user_style.style = vtiger_customstyler.stylerid        
        WHERE type = 'default'");
	    
	    $data = array();
	    if($adb->num_rows($defaultTheme)){
	        for($i=0;$i<$adb->num_rows($defaultTheme);$i++){
	            if($adb->query_result($defaultTheme, $i, 'userid') == $current_user->id){
	                $currentTheme = $adb->query_result($defaultTheme, $i, 'style');
	            }
	            $data[$adb->query_result($defaultTheme, $i, 'stylerid')] = array(
	                    "theme-name" => $adb->query_result($defaultTheme, $i, 'theme_name'),
	                    "font-name" => $adb->query_result($defaultTheme, $i, 'font_name'),
	                    "font-zoom" => $adb->query_result($defaultTheme, $i, 'font_zoom'),
	                    "topbar-color" => $adb->query_result($defaultTheme, $i, 'topbar_color'),
	                    "topbar-font-color" => $adb->query_result($defaultTheme, $i, 'topbar_font_color'),
	                    "menu-style" => $adb->query_result($defaultTheme, $i, 'menu_style'),
	                    "menu-color" => $adb->query_result($defaultTheme, $i, 'menu_color'),
	                    "menu-font-color" => $adb->query_result($defaultTheme, $i, 'menu_font_color'),
                        "menu-active-font-color" => $adb->query_result($defaultTheme, $i, 'menu_active_font_color'),
	                    "container-color" => $adb->query_result($defaultTheme, $i, 'container_color'),
	                    "border-radius" => $adb->query_result($defaultTheme, $i, 'border_radius'),
	                    "isApplied" => $adb->query_result($defaultTheme, $i, 'isapplied'),
	                    "currentStyle" => $currentTheme
	                );
	            
	        }
	    }
	    
		echo json_encode($data);
		exit;
	}
	
	public function saveStylePreset(Vtiger_Request $request){
	    
	    global $adb,$current_user;
	    
	    $colName='';
	    $val = '';
	    $upval ='';
	    $preset = $request->get('presetparams');
	    
	    $count = count($preset);
	    $n = 0;
	    foreach($preset as $key => $value){
	        if($n>0 && $n<$count && $key != 'current_user' && $key != 'isadmin' && $key != 'owner' && $key != 'currentStyle'){
	            $colName.=', ';
	            $val .= ', ';
	            $upval .=', ';
	        }
	        if($key == 'current_user' || $key == 'isadmin' || $key == 'owner' || $key == 'currentStyle')
	            continue;
	        if($key == 'isApplied' && $value == true)
	            $value=1;
            elseif($key == 'isApplied' && $value != true)
                $value=0;
            
	        $colName .= strtolower(str_replace('-','_',$key));
	        $val .= '"'.$value.'"';
	        $upval .= strtolower(str_replace('-','_',$key)) .' = '. '"'.$value.'"';
	        $n++;
	    }
	    
	    if($request->get('presetKey')){
	        $adb->pquery("UPDATE vtiger_customstyler SET $upval WHERE stylerid = ?",array($request->get('presetKey')));
	    }else{
	        $adb->pquery('INSERT INTO vtiger_customstyler('.$colName.', owner, type) VALUES ('.$val.','.$current_user->id.', "custom")');
	    }
	    
	}
	
	public function deleteStylePreset(Vtiger_Request $request){
	    
	    global $adb;
	    
	    if($request->get('presetKey'))
	        $adb->pquery("DELETE FROM vtiger_customstyler WHERE stylerid = ?",array($request->get('presetKey')));
	    
	}
	
}
