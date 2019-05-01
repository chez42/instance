<?php


	ini_set('display_errors','on');
	
	chdir(dirname(__FILE__) . '/..');
	include_once("includes/main/WebUI.php");

	global $adb;
	
	
	$appModules = array();
	
	$appModules['SUPPORT'] = array('ModComments');
	
	$appModules['MARKETING'] = array('Potentials');
	
	$appModules['TOOLS'] = array('PortfolioInformation','PositionRollup','ModSecurities');
	
	foreach($appModules as $app => $module){
		foreach($module as $modules){
			Settings_MenuEditor_Module_Model::addModuleToApp($modules, $app);
		}
		
	}
	
	$seqresult = $adb->pquery('SELECT MAX(sequence) AS maxsequence FROM vtiger_app2tab WHERE appname=?', array('SUPPORT'));
	$sequence = 0;
	if ($adb->num_rows($seqresult) > 0) {
		$sequence = $adb->query_result($seqresult, 0, 'maxsequence');
	}
	
	$result = $adb->pquery('SELECT * FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array(getTabid('Reports'), 'SUPPORT'));

	$sequence = $sequence + 1;
	if ($adb->num_rows($result) == 0) {
		$adb->pquery('INSERT INTO vtiger_app2tab(tabid,appname,sequence) VALUES(?,?,?)', array(getTabid('Reports'), 'SUPPORT', $sequence));
	}
