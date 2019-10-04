<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class QuotingTool_DeleteAjax_Action
 */
class QuotingTool_DeleteAjax_Action extends Vtiger_DeleteAjax_Action
{
    /**
     *
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
     * @param Vtiger_Request $request
     * @return bool
     */
    public function checkPermission(Vtiger_Request $request)
    {
    }
    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        } else {
            $data = array();
            $moduleName = $request->getModule();
            $recordId = $request->get("record");
            $response = new Vtiger_Response();
            $model = new QuotingTool_Record_Model();
            $success = $model->delete($recordId);
            if ($success) {
                $data["module"] = $moduleName;
                $data["viewname"] = "";
                $response->setResult(array(vtranslate("LBL_DELETED_SUCCESSFULLY", $moduleName)));
            } else {
                $response->setError(200, vtranslate("LBL_DELETED_FAILURE", $moduleName));
            }
            $response->setResult($data);
            $response->emit();
        }
    }
}

?>