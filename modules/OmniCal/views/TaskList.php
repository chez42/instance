<?php

class OmniCal_TaskList_View extends Vtiger_BasicAjax_View {
    public function process(Vtiger_Request $request) {
        global $current_user;
        $handler = new OmniCal_TaskList_Model();
        $tasks = $handler->get_tasks($current_user->user_name, 0, 100);
        $task_list = array();
        foreach($tasks['list'] AS $k => $v)
            $task_list[$v['id']] = array("title"=>$v['name'], "id"=>$v['id']);

        $viewer = $this->getViewer($request);
        $viewer->assign('TASKS', $task_list);
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $output = $viewer->view('TaskList.tpl', "OmniCal", false);//False makes it echo
    }
    
    public function preProcess(Vtiger_Request $request, $display = true) {

    }

    // Injecting custom javascript resources
    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                    "modules.$moduleName.resources.Activities", // . = delimiter
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }    
}

?>
