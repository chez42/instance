<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEProgressbar_Activate_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
    }
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("activate");
        $this->exposeMethod("valid");
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    public function valid(Vtiger_Request $request)
    {
        global $adb;
        $response = new Vtiger_Response();
        $module = $request->getModule();
        $adb->pquery("UPDATE `vte_modules` SET `valid`='1' WHERE (`module`=?);", array($module));
        $response->setResult("success");
        $response->emit();
    }
    public function activate(Vtiger_Request $request)
    {
        global $site_URL;
        $response = new Vtiger_Response();
        $module = $request->getModule();
        try {
            $vTELicense = new VTEProgressbar_VTELicense_Model($module);
            $data = array("site_url" => $site_URL, "license" => $request->get("license"));
            $vTELicense->activateLicense($data);
            $response->setResult(array("message" => $vTELicense->message));
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
}

?>