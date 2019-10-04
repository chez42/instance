<?php
/** License Text Here **/
class ActivityActions_List_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
//        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
        $source_module = $request->get('source_module');
        $record = $request->get('record');

        $parentRecordModel = Vtiger_Record_Model::getInstanceById($record, $source_module);
        $HelpDesk = Vtiger_RelationListView_Model::getInstance($parentRecordModel, "HelpDesk", $request->get('tab_label'));
        $links = $HelpDesk->getLinks();
        $header = $HelpDesk->getHeaders();
        
        $viewer = $this->getViewer($request);
        $viewer->assign("RECORD", $request->get('record'));
        $viewer->assign('RELATED_LIST_LINKS', $links);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->view('Index.tpl', $request->getModule());
    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "modules.ActivityActions.resources.ActivityActions", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
}

?>