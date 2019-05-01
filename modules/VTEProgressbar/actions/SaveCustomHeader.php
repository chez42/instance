<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEProgressbar_SaveProgressbar_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function process(Vtiger_Request $request)
    {
        global $adb;
        $module = $request->get("module");
        $record = $request->get("record");
        $custom_module = $request->get("custom_module");
        $header = $request->get("header");
        $color = $request->get("color");
        $icon = $request->get("icon");
        $sequence = $request->get("sequence");
        $active_val = 0;
        $active = $request->get("active");
        if ($active == "Active") {
            $active_val = 1;
        }
        $field_name = $request->get("field_name");
        $VTEModuleModel = Vtiger_Module_Model::getInstance($module);
        $redirectUrl = $VTEModuleModel->getSettingURL();
        if (!empty($custom_module)) {
            if (0 < $record) {
                $sql = "UPDATE `vte_custom_header_settings` SET module=?,header=?,icon=?,color=?,active=?,sequence=?,field_name=? WHERE id=" . $record;
            } else {
                $sql = "INSERT INTO vte_custom_header_settings (\r\n                    module,\r\n                    header,\r\n                    icon,\r\n                    color,\r\n                    active,\r\n                    sequence,\r\n                    field_name\r\n                )\r\n                VALUES\r\n                    (?, ?, ?, ?, ?, ?,?)";
            }
        }
        $adb->pquery($sql, array($custom_module, $header, $icon, $color, $active_val, $sequence, $field_name));
        header("Location: " . $redirectUrl);
    }
}

?>