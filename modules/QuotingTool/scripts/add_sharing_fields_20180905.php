<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
global $current_user;
$sql1 = "ALTER TABLE `vtiger_quotingtool` ADD COLUMN `owner`  text NULL ;";
$sql2 = "ALTER TABLE `vtiger_quotingtool` ADD COLUMN `share_status`  text NULL ;";
$sql3 = "ALTER TABLE `vtiger_quotingtool` ADD COLUMN `share_to`  text NULL ;";
$sql4 = "update `vtiger_quotingtool` set `share_status` ='public' where `share_status` is NULL ;";
$params = array();
$rs1 = $adb->pquery($sql1, $params);
$rs2 = $adb->pquery($sql2, $params);
$rs3 = $adb->pquery($sql3, $params);
$rs4 = $adb->pquery($sql4, $params);

?>