<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class QuotingTool_Widget_View
 */
class QuotingTool_Widget_View extends Vtiger_Detail_View
{
    /**
     * @constructor
     */
    public function __construct()
    {
        parent::__construct();
        //$this->vteLicense();
    }
    /**
     *
     */
    public function vteLicense()
    {
        $vTELicense = new QuotingTool_VTELicense_Model("QuotingTool");
        if (!$vTELicense->validate()) {
            header("Location: index.php?module=QuotingTool&view=List&mode=step2");
        }
    }
    /**
     * must be override
     * @param Vtiger_Request $request
     * @return boolean
     */
    public function preProcess(Vtiger_Request $request)
    {
        return true;
    }
    /**
     * must be override
     * @param Vtiger_Request $request
     * @return boolean
     */
    public function postProcess(Vtiger_Request $request)
    {
        return true;
    }
    /**
     * called when the request is received.
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $sourceModule = $request->get("source_module");
        $model = new QuotingTool_Record_Model();
        $templates = $model->findByModule($sourceModule);
        $templateTotal = count($templates);
        $viewer->assign("TEMPLATE_TOTAL", $templateTotal);
        $viewer->assign("TEMPLATES", $templates);
        $viewer->assign("MODULE_NAME", $moduleName);
        $viewer->assign("SOURCE_MODULE", $sourceModule);
        $viewer->view("Widget.tpl", $moduleName);
    }
}

?>