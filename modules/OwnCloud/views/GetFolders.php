<?php
include_once 'modules/OwnCloud/vendor/autoload.php';

class OwnCloud_GetFolders_View extends Vtiger_IndexAjax_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getOwnCloudFolders');
        $this->exposeMethod('create_node');
    }
    
    public function process(Vtiger_Request $request) {
        
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
        
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('MODULE', $moduleName);
        
        $request->set('cvid', $request->get('viewname'));
        $selectedIds = $this->getRecordsListFromRequest($request);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        
        echo $viewer->view('OwnCloudFolder.tpl',$moduleName,true);
        
    }
    
    public function getOwnCloudFolders(Vtiger_Request $request){
        
        $userName = OwnCloud_Config_Connector::$username;
        
        $password = html_entity_decode(OwnCloud_Config_Connector::$password);
        
        $url = OwnCloud_Config_Connector::$url;
        
        $api = new Owncloud\Api($url, $userName, $password);
        $management = $api->fileManagement();
        
        $id = ($request->get('id') == '#')?'':$request->get('id');
        $folders = $management->listContents($id);
        
        $folderData = array();
        
        if(!$id){
            $folderData['xxx'] =  array(
            "id"=>'xxx',
            "parent_id"=>'',
            "text"=>'/',
            'is_default'=>1,
            "type"=>"folder") ;
        }
        
        foreach ($folders as $folder){
            if($folder['type'] == 'dir'){
                $folderData[$folder['path']] =  array(
                    "id"=>urldecode($folder['path']),
                    "parent_id"=>urldecode($folder['dirname']),
                    "text"=>urldecode($folder['basename']),
                    "type"=>($folder['type'] == 'dir') ? "folder" : "file",
                    "children"=>true,
                    "icon"=>($folder['type'] == 'dir') ? "jstree-folder" : "jstree-file",
                );
            }
        }
        
        $tree = $this->buildTree($folderData);
        
       // echo"<pre>";print_r(json_encode($tree));echo"</pre>";exit;
        
        echo json_encode($tree);
    }
    
    public function buildTree(array $elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children =  $this->buildTree($elements, $element['id']);
                if($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        
        return $branch;
    }
    
    
    public function create_node(Vtiger_Request $request){
        
        $userName = OwnCloud_Config_Connector::$username;
        
        $password = html_entity_decode(OwnCloud_Config_Connector::$password);
        
        $url = OwnCloud_Config_Connector::$url;
        
        $api = new Owncloud\Api($url, $userName, $password);
        $management = $api->fileManagement();
        
        $folderName = $request->get('text');
        if($request->get('id') != 'xxx')
            $folderName = $request->get('id').'/'.$request->get('text');
        
        $folder = $management->createDir($folderName);
        
        if($folder)
            $result = array("success"=>true,'id'=>$folderName, 'message'=>'Folder update Successfully');
        else 
            $result = array("success"=>false,'message'=>'Something wrong!');
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }

    
}