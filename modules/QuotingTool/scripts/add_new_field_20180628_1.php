<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
global $current_user;
$sql = "ALTER TABLE `vtiger_quotingtool` ADD COLUMN `anblock`  tinyint(3) NULL DEFAULT '0';";
$params = array();
$rs = $adb->pquery($sql, $params);

?>