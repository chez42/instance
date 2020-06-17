<?php

include_once "includes/main/WebUI.php";
include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$module = Vtiger_Module::getInstance('VTEHistoryLog');
if ($module) $module->delete();
