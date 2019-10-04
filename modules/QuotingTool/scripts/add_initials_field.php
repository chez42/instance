<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `initials_primary` VARCHAR(255) NULL;";
$params = array();
$rs = $adb->pquery($sql, $params);
$sql1 = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `initials_secondary` VARCHAR(255)NULL;";
$params = array();
$adb->pquery($sql1, $params);
$sql = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `title_signature_primary` VARCHAR(255) NULL;";
$params = array();
$rs = $adb->pquery($sql, $params);
$sql1 = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `title_signature_secondary` VARCHAR(255)NULL;";
$params = array();
$adb->pquery($sql1, $params);
$sql1 = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `is_draw_signature` VARCHAR(5) NULL DEFAULT 'yes';";
$params = array();
$adb->pquery($sql1, $params);

?>