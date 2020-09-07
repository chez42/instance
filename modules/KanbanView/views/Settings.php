<?php
/*
 * @ https://EasyToYou.eu - IonCube v10 Decoder Online
 * @ PHP 5.6
 * @ Decoder version: 1.0.4
 * @ Release: 02/06/2020
 *
 * @ ZendGuard Decoder PHP 5.6
 */

ini_set("display_errors", "0");
class KanbanView_Settings_View extends Settings_Vtiger_Index_View
{
    public function preProcess(Vtiger_Request $request)
    {
        parent::preProcess($request);
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign("QUALIFIED_MODULE", $module);
        $rs = $adb->pquery("SELECT * FROM `vte_modules` WHERE module=? AND valid='1';", array($module));
        if ($adb->num_rows($rs) == 0) {
            $viewer->view("InstallerHeader.tpl", $module);
        }
    }
    public function process(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
      
        $this->renderSettingsUI($request);
           
    }
  
    public function renderSettingsUI(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $rs = $adb->pquery("SELECT `enable` FROM `kanban_view_settings`;", array());
        $enable = $adb->query_result($rs, 0, "enable");
        $viewer->assign("ENABLE", $enable);
        echo $viewer->view("Settings.tpl", $module, true);
    }
    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array("modules." . $moduleName . ".resources.Settings");
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}

?>