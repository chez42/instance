<?php
class Documents_UploadDocuments_View extends Vtiger_IndexAjax_View {
 
    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('MODULE', $moduleName);
        $selectedId = $request->get('record');
        
      
        $viewer->assign('SELECTED_IDS', $selectedId);
        
        $doc_module = Vtiger_Module_Model::getInstance('Documents');
        $folderField = Vtiger_Field_Model::getInstance('doc_folder_id', $doc_module);
        
        $folderValues = $folderField->getDocumentFolderList();
        
        $viewer->assign('FOLDER_VALUES', $folderValues);
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('CSS', $this->getHeaderCss($request));
        
        echo $viewer->view('UploadDocumentRelated.tpl',$moduleName,true);
        
    }
    
    public function getHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            "~libraries/jquery/uppy/dist/uppy.min.js",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
        $cssFileNames = array(
            "~libraries/jquery/uppy/dist/uppy.min.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        return $cssInstances;
    }
    
}