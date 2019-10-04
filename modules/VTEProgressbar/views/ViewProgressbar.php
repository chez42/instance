<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/21/2018
 * Time: 11:57 AM
 */
class VTEProgressbar_ViewProgressbar_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        //$this->vteLicense();
    }
    public function vteLicense()
    {
        $vTELicense = new VTEProgressbar_VTELicense_Model("VTEProgressbar");
        if (!$vTELicense->validate()) {
            exit("");
        }
    }
    public function process(Vtiger_Request $request)
    {
        $moduleSelected = $request->get("moduleSelected");
        $module = "VTEProgressbar";
        $record_id = $request->get("record");
        $viewer = $this->getViewer($request);
        global $adb;
        $sql = "SELECT * FROM `vte_progressbar_settings` WHERE module='" . $moduleSelected . "' AND active = 1";
        $results = $adb->pquery($sql, array());
        $status_array = array();
        $field_value = "";
        $field_name = "";
        if (0 < $adb->getRowCount($results)) {
            $current_record_model = Vtiger_Record_Model::getInstanceById($record_id);
            $field_name = $adb->query_result($results, 0, "field_name");
            $module_name = $adb->query_result($results, 0, "module");
            $module_model = Vtiger_Module_Model::getInstance($module_name);
            $field_model = Vtiger_Field_Model::getInstance($field_name, $module_model);
            $field_label = $field_model->get("label");
            $field_value = $current_record_model->getDisplayValue($field_name, $record_id);
            $sql = "SELECT * FROM `vtiger_" . $field_name . "`  ORDER BY sortorderid";
            $results = $adb->pquery($sql, array());
            $status_array = array();
            while ($row = $adb->fetchByAssoc($results)) {
                $status_array[] = $row[$field_name];
            }
        }
        if (0 < count($status_array)) {
            $viewer->assign("CURRENT_STATUS", $field_value);
            $viewer->assign("FIELD_NAME", $field_name);
            $viewer->assign("FIELD_LABEL", $field_label);
            $viewer->assign("PROGRESSBARS", $status_array);
            $viewer->assign("MODULE_NAME", $module_name);
            echo $viewer->view("ViewProgressbar.tpl", $module, true);
        } else {
            echo "";
        }
    }
}

?>