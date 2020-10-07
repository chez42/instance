<?php

chdir('../');
include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'ModTracker';

$db = PearDatabase::getInstance();

$sel = $db->pquery("SELECT * FROM vtiger_tab WHERE name = 'ModTracker'");
if (!$db->num_rows($sel)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}