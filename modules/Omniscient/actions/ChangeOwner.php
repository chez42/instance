<?php

class Omniscient_ChangeOwner_Action extends Vtiger_BasicAjax_Action{
	public function process(Vtiger_Request $request) {
		global $adb;
		$selected_ids = $request->get('selected_ids');
		$new_assigned_id = $request->get('new_assigned_id');
		$questions = generateQuestionMarks($selected_ids);
		$query = "UPDATE vtiger_crmentity SET smownerid = ? WHERE crmid IN ({$questions})";
		$adb->pquery($query, array($new_assigned_id, $selected_ids));
//		echo "This is not saving at the moment, but the query is simple.. it would simply apply the new assigned to ID to the entities...\r\n";
//		echo "The following ids will be assigned to: {$new_assigned_id}:";
//		print_r($selected_ids);
	}
}

?>