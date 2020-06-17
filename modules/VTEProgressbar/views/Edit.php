<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEProgressbar_Edit_View extends Settings_Vtiger_Index_View
{
    public function __construct()
    {
        parent::__construct();
    }
    public function preProcess(Vtiger_Request $request)
    {
        parent::preProcess($request);
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign("QUALIFIED_MODULE", $module);
       /* $rs = $adb->pquery("SELECT * FROM `vte_modules` WHERE module=? AND valid='1';", array($module));
        if ($adb->num_rows($rs) == 0) {
            $viewer->view("InstallerHeader.tpl", $module);
        }*/
    }
    public function process(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
        $mode = $request->getMode();
        /*$vTELicense = new VTEProgressbar_VTELicense_Model($module);
        if (!$vTELicense->validate()) {
            $this->step2($request, $vTELicense);
        } else {
            $rs = $adb->pquery("SELECT * FROM `vte_modules` WHERE module=? AND valid='1';", array($module));
            if ($adb->num_rows($rs) == 0) {
                $this->step3($request);
            } else {*/
                $mode = $request->getMode();
                if ($mode) {
                    $this->{$mode}($request);
                } else {
                    $this->renderSettingsUI($request);
                }
          /*  }
        }*/
    }
    public function step2(Vtiger_Request $request)
    {
        global $site_URL;
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign("SITE_URL", $site_URL);
        $viewer->view("Step2.tpl", $module);
    }
    public function step3(Vtiger_Request $request)
    {
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->view("Step3.tpl", $module);
    }
    public function renderSettingsUI(Vtiger_Request $request)
    {
        global $vtiger_current_version;
        $record = $request->get("record");
        $module = $request->getModule();
        $moduleModels = Vtiger_Module_Model::getEntityModules();
        $avaiableModuleForProgressbar = VTEProgressbar_Module_Model::getAvaiableModuleForProgressbar();
        $allModules = array();
        foreach ($moduleModels as $tabId => $moduleModel) {
            $module_name = $moduleModel->get("name");
            if (!in_array($module_name, $avaiableModuleForProgressbar)) {
                $allModules[$tabId] = $moduleModel;
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign("MODULE", $module);
        $product_module_model = Vtiger_Module_Model::getInstance("Products");
        $service_module_model = Vtiger_Module_Model::getInstance("Services");
        $viewer->assign("PRODUCT_MODULE", $product_module_model);
        $viewer->assign("SERVICE_MODULE", $service_module_model);
        if (0 < $record) {
            $module_model = Vtiger_Module_Model::getInstance($module);
            $Entries = $module_model->getlistViewEntries("id=" . $record);
            $recordentries = $Entries[0];
            $tabId = getTabid($recordentries["module"]);
            $selectedModuleModel = Vtiger_Module_Model::getInstance($recordentries["module"]);
            $allModules[$tabId] = $selectedModuleModel;
            $viewer->assign("RECORD", $record);
            $viewer->assign("RECORDENTRIES", $recordentries);
            $viewer->assign("SELECTED_MODULE_FIELDS", $selectedModuleModel->getFields());
        }
        $viewer->assign("ALL_MODULES", $allModules);
        echo $viewer->view("Edit.tpl", $module, true);
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array("~layouts/v7/modules/VTEProgressbar/resources/style.css", "~/libraries/jquery/bootstrapswitch/css/bootstrap3/bootstrap-switch.min.css");
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
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
        $jsFileNames = array("modules.VTEProgressbar.resources.Edit");
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}

?>