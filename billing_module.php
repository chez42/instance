<?php

include_once 'vtlib/Vtiger/Module.php';
$Vtiger_Utils_Log = true;

$MODULENAME = 'Billing';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if ($moduleInstance || file_exists('modules/'.$MODULENAME)) {
    echo "Module already present - choose a different name.";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent= 'Tools';
    $moduleInstance->save();

    mkdir('modules/'.$MODULENAME);
    echo "OK\n";
}
