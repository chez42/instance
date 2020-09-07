<?php

chdir('../');
include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'KanbanView';

$db = PearDatabase::getInstance();

$sel = $db->pquery("SELECT * FROM vtiger_tab WHERE name = 'KanbanView'");
if (!$db->num_rows($sel)) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->isentitytype = false;
    $moduleInstance->save();
}