<?php
$Vtiger_Utils_Log = true;

chdir('../');

include_once 'includes/main/WebUI.php';

global $adb;

$adb->pquery("ALTER TABLE `vtiger_field` ADD `related_tab_field_seq` INT(19) NULL AFTER `isunique`;");

$adb->pquery('CREATE TABLE IF NOT EXISTS vtiger_roundrobin_roles ( roles TEXT NULL );');


$adb->pquery("UPDATE vtiger_field SET presence=1 WHERE tabid = ? AND columnname IN (
    'veo_username', 
    'omniscient_control_number', 
    'advisor_control_number', 
    'exchange_enabled', 
    'upn', 
    'accessible_ids'
)", array(getTabid('Users')));