<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class DragDropDocuments_GetMaxLimitAjax_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function process(Vtiger_Request $request)
    {
        $result = array();
        $result["MAX_UPLOAD_LIMIT_MB"] = Vtiger_Util_Helper::getMaxUploadSize();
		$result["license"] = true;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}

?>