<?php
class OmniThemeManager_CustomStyle_View extends Vtiger_View_Controller {
    
	function __construct() {

		$this->exposeMethod('getStyleForCurrentUser');
		
		$this->exposeMethod('getCSSForCurrentUser');
		$this->exposeMethod('getStylePresets');
		$this->exposeMethod('getMYCStylePresets');
		$this->exposeMethod('getCSSStyle');
	}
	
	public function validateRequest(Vtiger_Request $request) {
		$mode = $request->get('mode');
		$this->invokeExposedMethod($mode, $request);
		die();
	}

		
    public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        $this->invokeExposedMethod($mode, $request);       
    }
    
    public function getCSSForCurrentUser(Vtiger_Request $request){
		global $config;
		require("config.inc.php");
	    $db = PearDatabase::getInstance();       
        $currentuserid=$_SESSION['authenticated_user_id'];
        
        $style = $db->pquery("SELECT * FROM vtiger_mycthemeswitcher_userstyles WHERE userid=?;",array($currentuserid));
        $rowCount =  $db->num_rows($style);
		if(isset($rowCount) && $rowCount!="" && $rowCount>0){
        	$styleData = $db->query_result_rowdata($style, 0);
        }
        else $styleData = array("styleid"=>"style-myc-1");
        
		$_REQUEST["cs"]=$styleData["styleid"];
		header("Content-type: text/css; charset: UTF-8");
		include("modules/OmniThemeManager/utils/customStyle.php");
		die();
    }
    
    public function getCSSStyle(Vtiger_Request $request){
		global $config;
		require("config.inc.php");
	
        $style = $request->get('tp');

		$_REQUEST["tp"]=$style;
		header("Content-type: text/css; charset: UTF-8");
		include("modules/OmniThemeManager/utils/customStyle.php");
		die();
    }
    
    public function getStyleForCurrentUser(Vtiger_Request $request){
	    $db = PearDatabase::getInstance();       
        $currentuserid=$_SESSION['authenticated_user_id'];
        
        $style = $db->pquery("SELECT * FROM vtiger_mycthemeswitcher_userstyles WHERE userid=?;",array($currentuserid));
        $rowCount =  $db->num_rows($style);
		if(isset($rowCount) && $rowCount!="" && $rowCount>0){
        	$styleData = $db->query_result_rowdata($style, 0);
        }
        else $styleData = array("styleid"=>"style-myc-1");

		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$response = new Vtiger_Response();
		$responsemessage=array();
		$responsemessage['success']=true;
		$responsemessage['style']=$styleData["styleid"];
		$responsemessage['user']=$currentuserid;
		$responsemessage['isAdmin']=$currentUserModel->isAdminUser();
        $response->setResult($responsemessage);        
        $response->emit();
    }
    
    public function getStylePresets(){
	    //$actualStyles = json_decode(file_get_contents(__DIR__."/../utils/stylePresets.json"),true);
		echo file_get_contents(__DIR__."/../utils/stylePresets.json");
		die();
    }
    
    public function getMYCStylePresets(){
	    //$actualStyles = json_decode(file_get_contents(__DIR__."/../utils/stylePresets.json"),true);
		echo file_get_contents(__DIR__."/../utils/stylePresetsMYC.json");
		die();
    }    
   
    
}