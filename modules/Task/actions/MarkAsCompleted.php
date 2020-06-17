<?php

class Task_MarkAsCompleted_Action extends Task_SaveAjax_Action {
    
    function __construct() {
        $this->exposeMethod('markAsCompleted');
    }
    
    public function process(Vtiger_Request $request) {  
		$mode = $request->getMode();
		if(!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}

	}

    public function markAsCompleted(Vtiger_Request $request) {
    	$moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
        $recordModel->set('mode','edit');
        $response = new Vtiger_Response();
        
		$status = 'Completed';
        $recordModel->set('task_status',$status);
		$result = array("valid"=>TRUE,"markedascompleted"=>TRUE);
        
        if($request->get("returnData")){
        	
        	$result['event_detail'] = array();
        	
        	$item = array();
        	
        	$record = $recordModel->getData();
        		
            $user = Users_Record_Model::getCurrentUserModel();
        	$item['title'] = decode_html($record['subject']) . ' - (' . decode_html(vtranslate($record['task_status'],'Task')) . ')';
            $item['status'] = $record['task_status'];
            $item['id'] = $recordId;
			$dateTimeFieldInstance = new DateTimeField($record['date_start'] . ' ' . $record['time_start']);
			$userDateTimeString = $dateTimeFieldInstance->getFullcalenderDateTimevalue();
			$dateTimeComponents = explode(' ',$userDateTimeString);
			$dateComponent = $dateTimeComponents[0];
			//Conveting the date format in to Y-m-d . since full calendar expects in the same format
			$dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $user->get('date_format'));
			$item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];

			$item['end']   = $record['due_date'];
			$item['url']   = sprintf('index.php?module=Task&view=Detail&record=%s', $recordId);
		    $item['module'] = $moduleName;
        			
			$result['event_detail'] = $item;
			
        }
        $result['activitytype'] = 'Task';
        $recordModel->save();
        $response->setResult($result);
        $response->emit();
    }
}
