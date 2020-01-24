<?php

class OmniCal_EditActivity_View extends Vtiger_Detail_View {
    
    public function process(Vtiger_Request $request) {
        $activity_type = getActivityType($request->get('activity_id'));
        switch($activity_type){
            case "Task":
                $info = new OmniCal_TaskView_View();
                break;
            case "Call":
            case "Meeting":
                $info = new OmniCal_EventView_View();
                break;
        }
/*        $info->preProcess($request);
        echo $info->process($request);*/

    }

    public function getHeaderScripts(Vtiger_Request $request) {
            $moduleName = $request->getModule();
            $jsFileNames = array(
                "~/libraries/jquery/osx/osx.js",
                "~/libraries/jquery/accordion/multiaccordion.jquery.min.js",
                "~/libraries/jquery/cookie/jquery.cookie.js",
                "modules.OmniCal.resources.Accordion",
                "modules.OmniCal.resources.NewInteraction",// . = delimiter
                "modules.OmniCal.resources.ActivityInteraction",// . = delimiter
                "modules.OmniCal.resources.NewRecurrence",
            );
            $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
            return $jsScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
            $headerCssInstances = parent::getHeaderCss($request);
            $cssFileNames = array(
                '~/layouts/vlayout/modules/OmniCal/css/NewEventHeader.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewEventView.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewRecurrence.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewUserSelect.css',
                '~/layouts/vlayout/modules/OmniCal/css/NewAppointment.css',
                //'~/libraries/jquery/accordion/multiaccordion.jquery.min.css',
            );
            $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
            return $cssInstances;
    }
}

?> 