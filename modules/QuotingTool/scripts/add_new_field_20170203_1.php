<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
global $current_user;
$sql = "ALTER TABLE vtiger_quotingtool_transactions ADD `hash` VARCHAR(255) NULL DEFAULT '';";
$params = array();
$rs = $adb->pquery($sql, $params);

?>