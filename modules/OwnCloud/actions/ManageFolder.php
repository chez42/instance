<?php
include_once 'modules/OwnCloud/vendor/autoload.php';

class OwnCloud_ManageFolder_Action extends Vtiger_Action_Controller{
    
    public function checkPermission(Vtiger_Request $request){return true;}
    
    function __construct() {
        
        parent::__construct();
        
        $this->exposeMethod('CreateFolder');
        
        $this->exposeMethod('GetFolder');
    
    }
    
    public function process(Vtiger_Request $request){
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    public function CreateFolder(Vtiger_Request $request){
        
        $userName = OwnCloud_Config_Connector::$username;
        
        $password = html_entity_decode(OwnCloud_Config_Connector::$password);
        
        $url = OwnCloud_Config_Connector::$url;
        
        $selectedIds = $request->get('selectedIds');
        
        $api = new Owncloud\Api($url, $userName, $password);
        
        $management = $api->fileManagement();
        
        $folderName = $request->get('text');
        
        if($request->get('id') != 'xxx')
            $folderName = str_replace(' ','%20',$request->get('id')).'/'.str_replace(' ','%20',$request->get('text'));
            
        $folder = $management->createDir($folderName);
            
        if($folder)
            $result = array("success" => true, 'id' => $folderName);
        else
            $result = array("success" => false);
                
        $response = new Vtiger_Response();
        
        $response->setResult($result);
        
        $response->emit();
    }
}
?>
