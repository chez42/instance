<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool_settings` ADD `success_content` text NULL";
$params = array();
$rs = $adb->pquery($sql, $params);

?>