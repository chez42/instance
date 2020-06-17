<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $adb;
$checkExsist = $adb->pquery("SELECT `fieldname` FROM `vtiger_field` WHERE `columnname`=? AND tablename = ?", array("signedrecord_status", "vtiger_signedrecord"));
if (0 < $adb->num_rows($checkExsist)) {
    $status = $adb->pquery("SELECT `fieldid`, `fieldname` FROM `vtiger_field` WHERE `columnname`=? AND tablename = ?", array("status", "vtiger_signedrecord"));
    if (0 < $adb->num_rows($status)) {
        $idStatusField = $adb->query_result($status, 0, "fieldid");
        $adb->pquery("UPDATE `vtiger_field` SET `presence` =? WHERE `fieldid` = ?", array(1, $idStatusField));
        $adb->pquery("ALTER TABLE `vtiger_signedrecord` CHANGE  `status` `signedrecord_status` VARCHAR(50)");
    }
}

?>