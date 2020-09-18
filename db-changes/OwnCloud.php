<?php

chdir('../');
include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'OwnCloud';

$db = PearDatabase::getInstance();

$sel = $db->pquery("SELECT * FROM vtiger_tab WHERE name = 'OwnCloud'");
if (!$db->num_rows($sel)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}