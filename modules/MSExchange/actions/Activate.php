<?php

class MSExchange_Activate_Action extends Vtiger_Action_Controller{
	
    public function checkPermission(Vtiger_Request $request){
	}

	public function __construct(){
		parent::__construct();
		$this->exposeMethod('activate');
		$this->exposeMethod("upgrade");
	}

	public function process(Vtiger_Request $request){
		$mode = $request->get('mode');

		if (!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return NULL;
		}
	}

	public function activate(Vtiger_Request $request){
		global $site_URL;
		$response = new Vtiger_Response();
		$module = $request->getModule();
        try {
		    $exchangeLicense = new MSExchange_License_Model();
			$data = array('site_url' => $site_URL, 'license' => $request->get('license'));
			$exchangeLicense->activateLicense($data);
			$response->setResult(array('message' => $exchangeLicense->message));
		}
		catch (Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
        $response->emit();
	}
	
	public function upgrade(Vtiger_Request $request){
	    
	    global $site_URL;
	    
	    $response = new Vtiger_Response();
	    
	    $module = $request->getModule();
	    
	    try {
	        $exchangeLicense = new MSExchange_License_Model($module);
	        $data = array('site_url' => $site_URL, 'license' => $request->get('license'));
	        $exchangeLicense->deleteLicense();
	        $exchangeLicense->activateLicense($data);
	        $response->setResult(array('message' => $exchangeLicense->message));
	    }
	    catch (Exception $e) {
	        $response->setError($e->getCode(), $e->getMessage());
	    }
	    $response->emit();
	}
	
}


?>
