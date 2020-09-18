<?php
include_once 'modules/OwnCloud/vendor/autoload.php';

class OwnCloud_GetFolders_View extends Vtiger_IndexAjax_View {
    
    function __construct() {
        
        parent::__construct();
        
        $this->exposeMethod('getOwnCloudFolders');
        
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
        
        echo $viewer->view('OwnCloudFolder.tpl',$moduleName,true);
        
    }
    
    public function getOwnCloudFolders(Vtiger_Request $request){
        
        $userName = OwnCloud_Config_Connector::$username;
        
        $password = html_entity_decode(OwnCloud_Config_Connector::$password);
        
        $url = OwnCloud_Config_Connector::$url;
        
        $api = new Owncloud\Api($url, $userName, $password);
        
        $management = $api->fileManagement();
        
        $id = ($request->get('id') == '#') ? '' : urldecode($request->get('id'));
        
        $folders = $management->listContents(str_replace(" ", "%20", $id));
        
        $folderData = array();
        
        if(!$id){
            $folderData[] =  array(
                "id" => 'xxx',
                "text" => '/',
                "type" => "root",
            ) ;
        }
        
        foreach ($folders as $folder){
            if($folder['type'] == 'dir'){
                $folderData[] =  array(
                    "id" => urldecode($folder['path']),
                    "parent_id" => urldecode($folder['dirname']),
                    "text" => urldecode($folder['basename']),
                    "type" => "folder",
                    "children" => true,
                    "icon" => "jstree-folder"
                );
            }
        }
        
        $tree = $this->buildTree($folderData);
        
        echo json_encode($folderData);
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
}