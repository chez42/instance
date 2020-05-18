<?php

$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

global $adb;

$adb->pquery("ALTER TABLE `vtiger_mail_accounts` ADD `is_default` TINYINT(3) 
NULL DEFAULT NULL AFTER `user_id`");
