<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool` ADD `createnewrecords` INT(11) NULL DEFAULT '0'";
$sql1 = "ALTER TABLE `vtiger_quotingtool` ADD `linkproposal` VARCHAR(250) NULL DEFAULT ''";
$params = array();
$rs = $adb->pquery($sql, $params);
$rs = $adb->pquery($sql1, $params);

?>