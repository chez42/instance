<?php
include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;


$modules = array("Positions", "PositionRollup", "HumanResources", "Investments", "Forms", "AdvisorDirect", "OmniCal", "ActivityActions");

foreach($modules as $module_name){
	$module = Vtiger_Module::getInstance($module_name);
	if ($module) $module->delete();
}