<?php

class OmniCal_EditActivity_Action extends Vtiger_BasicAjax_Action{
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
        if($info) {
            $info->preProcess($request);
            echo $info->process($request);
        }
/*        $report_display = $request->get("report");
        $calling_module = $request->get("calling_module");
        $calling_record = $request->get("calling_record");
//        $top = new PortfolioInformation_ReportTop_View();
//        echo $top->process($request);
        switch($report_display){
            case "holdings":
                $info = new PortfolioInformation_Positions_View();                
                break;
            case "monthly_income":
                $info = new PortfolioInformation_MonthlyIncome_View();
                break;
            case "performance":
                $info = new PortfolioInformation_Performance_View();
                break;
            case "overview":
                $info = new PortfolioInformation_Overview_View();
                break;
            default:
                echo "Generation Error";
                break;
        }
        echo $info->process($request);
//        $viewer->assign('TASKS', $task_list);
//        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
//        $output = $viewer->view('TaskView.tpl', "OmniCal", false);//False makes it echo
        
//        return $output;
/*      
        $recordModel = Vtiger_Record_Model::getInstanceById($request->get('activity_id'), 'Calendar');
        $subject = $recordModel->get('subject');
        $data = $recordModel->getData();
        $data['subject'] = "Test Task";
        $recordModel->setData($data);
        $recordModel->set('id', $request->get('activity_id'));
        $recordModel->set('mode', 'edit');        
        $recordModel->save();
        echo "SAVED";*/
    }
}
?>
