<?php

class OmniCal_CreateActivity_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $activity_type = $request->get('activity_type');
        switch($activity_type){
            case "Task":
                $info = new OmniCal_TaskView_View();
                break;
            case "Event":
                $info = new OmniCal_EventView_View();
                break;
        }
        echo $info->process($request);
    }
}
?>