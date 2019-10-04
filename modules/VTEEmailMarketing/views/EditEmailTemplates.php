<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_EditEmailTemplates_View extends EmailTemplates_Edit_View
{
    public function __construct()
    {
        parent::__construct();
       // $this->vteLicense();
    }
    public function vteLicense()
    {
        $vTELicense = new VTEEmailMarketing_VTELicense_Model("VTEEmailMarketing");
        if (!$vTELicense->validate()) {
            header("Location: index.php?module=VTEEmailMarketing&parent=Settings&view=Settings&mode=step2");
        }
    }
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $request->set("module", "EmailTemplates");
        $request->set("view", "Edit");
        $moduleName = $request->getModule();
        $templateId = $request->get("record");
        global $adb;
        global $site_URL;
        $keytemplate = "";
        $metadata = "";
        $template = "";
        $thumbnailUrl = "";
        if ($templateId) {
            $sql = "SELECT * FROM vtiger_vteemailmarketing_emailtemplate WHERE idtemplate = ? LIMIT 1";
            $rs = $adb->pquery($sql, array($templateId));
            if (0 < $adb->num_rows($rs)) {
                $keytemplate = $adb->query_result($rs, 0, "keytemplate");
                $template = $adb->query_result($rs, 0, "template");
                $metadata = $adb->query_result($rs, 0, "metadata");
                $thumbnailUrl = $adb->query_result($rs, 0, "thumbnail");
            }
        }
        $this->initializeContents($request, $viewer);
        if ($request->get("returnview")) {
            $request->setViewerReturnValues($viewer);
        }
        $imagickCheck = false;
        if (extension_loaded("imagick")) {
            $imagickCheck = true;
        }
        $viewer->assign("IMAGICKCHECK", $imagickCheck);
        $viewer->assign("KEYTEMPLATE", $keytemplate);
        $viewer->assign("EDITTEMPLATE", "1");
        $viewer->assign("METADATA", $metadata);
        $viewer->assign("TEMPLATE", $template);
        $viewer->assign("THUMBNAILURL", $thumbnailUrl);
        $viewer->assign("current_url", $site_URL);
        $viewer->view("EditViewEmailTemplates.tpl", "VTEEmailMarketing");
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array("~layouts/" . Vtiger_Viewer::getDefaultLayoutName() . "/modules/VTEEmailMarketing/resources/Styles.css");
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames = array("~layouts/v7/modules/EmailTemplates/resources/Edit.js", "~layouts/v7/modules/VTEEmailMarketing/resources/EditEmailTemplates.js");
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}

?>