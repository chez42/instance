<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "include/events/VTEventHandler.inc";
class VTEEmailMarketingHandler extends VTEventHandler
{
    public function handleEvent($eventName, $data)
    {
        global $adb;
        if ($eventName == "vtiger.entity.aftersave" && $data->getModuleName() == "VTEEmailMarketing") {
            $moduleId = $data->getId();
            $moduleName = $data->getModuleName();
            $entityData = $data->getData();
            foreach ($entityData as $key => $value) {
                if (preg_match("/^cf_.+_id\$/", $key, $name)) {
                    $relModule = $adb->pquery("SELECT\r\n                                    vtiger_field.*, vtiger_fieldmodulerel.relmodule, vtiger_tab.name\r\n                                FROM\r\n                                    vtiger_field\r\n                                JOIN vtiger_tab ON (\r\n                                    vtiger_field.tabid = vtiger_tab.tabid\r\n                                )\r\n                                JOIN vtiger_fieldmodulerel ON (\r\n                                    vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid\r\n                                )\r\n                                WHERE\r\n                                    vtiger_field.fieldname = ?\r\n                                 ", array($key));
                    if (0 < $adb->num_rows($relModule)) {
                        $moduleNameRelated = "";
                        while ($row = $adb->fetchByAssoc($relModule)) {
                            $moduleNameRelated = $row["relmodule"];
                        }
                        if (!empty($value)) {
                            $result = $adb->pquery("SELECT vtiger_crmentityrel.*\r\n                                                    FROM\r\n                                                        vtiger_crmentityrel\r\n                                                    WHERE crmid = ? AND relcrmid = ?", array($value, $moduleId));
                            if ($adb->num_rows($result) == 0) {
                                $adb->pquery("INSERT INTO vtiger_crmentityrel (crmid,module,relcrmid,relmodule) VALUE (?,?,?,?)", array($value, $moduleNameRelated, $moduleId, $moduleName));
                            }
                        } else {
                            $result = $adb->pquery("SELECT vtiger_crmentityrel.*\r\n                                                    FROM\r\n                                                        vtiger_crmentityrel\r\n                                                    WHERE module = ? AND relcrmid = ? AND relmodule = ?", array($moduleNameRelated, $moduleId, $moduleName));
                            if ($count = 0 < $adb->num_rows($result)) {
                                $adb->pquery("DELETE FROM vtiger_crmentityrel WHERE module = ? AND relcrmid = ? AND relmodule = ?", array($moduleNameRelated, $moduleId, $moduleName));
                            }
                        }
                    }
                }
            }
        }
    }
}

?>