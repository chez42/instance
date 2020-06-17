<?php
vimport ('~~/include/Webservices/Query.php');

class Task_Feed_Action extends Vtiger_BasicAjax_Action {

	public function process(Vtiger_Request $request) {
		try {
			
			$result = array();

			$start = $request->get('start');
			$end   = $request->get('end');
			$type = $request->get('type');
			$color = '#3A87AD';
			$textColor = 'white';

			$user = Users_Record_Model::getCurrentUserModel();
			$db = PearDatabase::getInstance();
	
			$moduleModel = Vtiger_Module_Model::getInstance('Task');
	        
			$userAndGroupIds = array_merge(array($user->getId()),$this->getGroupsIdsForUsers($user->getId()));
			
			$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
	
			$queryGenerator->setFields(array('taskid','subject', 'task_status', 'date_start','time_start','due_date','time_end','id'));
			
			$query = $queryGenerator->getQuery();
	
			$hideCompleted = $user->get('hidecompletedevents');
	        
	        if($hideCompleted)
	            $query.= "vtiger_task.task_status != 'Completed' AND ";
			
	        $query.= " AND ((date_start >= ? AND due_date < ?) OR ( due_date >= ?))";
	        
	        $params = array($start,$end,$start);
	        
	        $params = array_merge($params, $userAndGroupIds);
			
	        $query.= " AND vtiger_crmentity.smownerid IN (".generateQuestionMarks($userAndGroupIds).")";
			
			$queryResult = $db->pquery($query,$params);
			
			while($record = $db->fetchByAssoc($queryResult)){
				$item = array();
				$crmid = $record['taskid'];
				$item['title'] = decode_html($record['subject']) . ' - (' . decode_html(vtranslate($record['task_status'],'Task')) . ')';
	            $item['status'] = $record['task_status'];
	            $item['id'] = $crmid;
				$dateTimeFieldInstance = new DateTimeField($record['date_start'] . ' ' . $record['time_start']);
				$userDateTimeString = $dateTimeFieldInstance->getFullcalenderDateTimevalue();
				$dateTimeComponents = explode(' ',$userDateTimeString);
				$dateComponent = $dateTimeComponents[0];
				//Conveting the date format in to Y-m-d . since full calendar expects in the same format
				$dataBaseDateFormatedString = DateTimeField::__convertToDBFormat($dateComponent, $user->get('date_format'));
				$item['start'] = $dataBaseDateFormatedString.' '. $dateTimeComponents[1];
	
				$item['end']   = $record['due_date'];
				$item['url']   = sprintf('index.php?module=Task&view=Detail&record=%s', $crmid);
				$item['color'] = $color;
				$item['textColor'] = $textColor;
	            $item['module'] = $moduleModel->getName();
				$result[] = $item;
			}
			
			echo json_encode($result);
		} catch (Exception $ex) {
			echo $ex->getMessage();
		}
	}
    
    protected function getGroupsIdsForUsers($userId) {
        vimport('~~/include/utils/GetUserGroups.php');
        
        $userGroupInstance = new GetUserGroups();
        $userGroupInstance->getAllUserGroups($userId);
        return $userGroupInstance->user_groups;
    }
}