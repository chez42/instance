<?php

$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

global $adb;

$adb->pquery("ALTER TABLE vtiger_mailscanner ADD userid INT(11) NULL AFTER scannerid;");