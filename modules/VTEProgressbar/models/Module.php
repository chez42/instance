<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEProgressbar_Module_Model extends Vtiger_Module_Model
{
    public function getSettingLinks()
    {
        $settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Settings", "linkurl" => "index.php?module=VTEProgressbar&parent=Settings&view=Settings", "linkicon" => "");
        $settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Uninstall", "linkurl" => "index.php?module=VTEProgressbar&parent=Settings&view=Uninstall", "linkicon" => "");
        return $settingsLinks;
    }
    public function getCreateViewUrl($record = "")
    {
        return "index.php?module=VTEProgressbar&parent=Settings&view=Edit" . ($record != "" ? "&record=" . $record : "");
    }
    public function getCreatePreViewLink($record = "")
    {
        return "index.php?module=VTEProgressbar&parent=Settings&view=Preview" . ($record != "" ? "&record=" . $record : "");
    }
    public function getSettingURL()
    {
        return "index.php?module=VTEProgressbar&parent=Settings&view=Settings";
    }
    public function getrandomString()
    {
        return str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
    }
    public function getRelatedFieldName($module, $relModule)
    {
        global $adb;
        $sql = "SELECT fieldname FROM `vtiger_field` WHERE fieldid IN (SELECT fieldid from vtiger_fieldmodulerel WHERE module='" . $module . "' AND relmodule='" . $relModule . "')";
        $results = $adb->pquery($sql, array());
        if (0 < $adb->num_rows($results)) {
            $fieldname = $adb->query_result($results, 0, "fieldname");
        }
        return $fieldname;
    }
    public static function getModuleFields($module)
    {
        $values = array();
        $moduleModel = Vtiger_Module_Model::getInstance($module);
        $blockModelList = $moduleModel->getBlocks();
        foreach ($blockModelList as $blockLabel => $blockModel) {
            $fieldModelList = $blockModel->getFields();
            if (!empty($fieldModelList)) {
                foreach ($fieldModelList as $fieldName => $fieldModel) {
                    $values[$fieldName] = vtranslate($fieldModel->get("label"), $module);
                }
            }
        }
        return $values;
    }
    public function getlistViewEntries($where = "")
    {
        global $adb;
        $Entries = array();
        $sql = "SELECT * FROM `vte_progressbar_settings` ";
        if ($where != "") {
            $sql .= " WHERE " . $where;
        }
        $sql .= " ORDER BY module ASC";
        $results = $adb->pquery($sql, array());
        if (0 < $adb->num_rows($results)) {
            while ($row = $adb->fetchByAssoc($results)) {
                $field_name = $row["field_name"];
                $module_model = Vtiger_Module_Model::getInstance($row["module"]);
                $field_model = Vtiger_Field_Model::getInstance($field_name, $module_model);
                $Entries[] = array("module" => $row["module"], "active" => $row["active"], "field_name" => $field_name, "field_label" => $field_model->get("label"), "id" => $row["id"]);
            }
        }
        return $Entries;
    }
    public static function getAvaiableModuleForProgressbar()
    {
        global $adb;
        $list_modules = array();
        $sql = "SELECT DISTINCT module FROM vte_progressbar_settings";
        $results = $adb->pquery($sql, array());
        if (0 < $adb->num_rows($results)) {
            while ($row = $adb->fetchByAssoc($results)) {
                $list_modules[] = $row["module"];
            }
        }
        return $list_modules;
    }
}

?>