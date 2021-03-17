<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');

class NotificationTask extends VTTask {
	public $executeImmediately = true; 
	
	public function getFieldNames(){
		return array('content','subject','notification_type');
	}
	
	public function doTask($entity){
		
	    global $current_user, $adb;
	    
	    $userIdCom = vtws_getIdComponents($entity->get('assigned_user_id'));
			
		global $adb, $current_user,$log;
		
		$util = new VTWorkflowUtils();
		
		$admin = $util->adminUser();
		
		$ws_id = $entity->getId();
		
		$entityCache = new VTEntityCache($admin);
		
		$ct = new VTSimpleTemplate($this->content);
		$content = $ct->render($entityCache, $ws_id);
		$relatedCRMid = substr($ws_id, stripos($ws_id, 'x')+1);
		
		$relatedModule = $entity->getModuleName();
		
		$focus = CRMEntity::getInstance('Notifications');
		$focus->column_fields['assigned_user_id'] = $userIdCom[1];
		$focus->column_fields['related_to'] = $relatedCRMid;
		$focus->column_fields['description'] = decode_html($content);
		
		$parentModule = getSalesEntityType($relatedCRMid);
		
		$focus->column_fields['notification_type'] = $this->notification_type;
		
		$ct = new VTSimpleTemplate($this->subject);
		$subject = $ct->render($entityCache, $ws_id);
		
		$focus->column_fields['title'] = $subject;
		
		if(strpos($content, "changes") !== FALSE || strpos($content, "rejected") !== FALSE){
		    $ct = new VTSimpleTemplate('$lastComment');
		    $content = $ct->render($entityCache, $ws_id);
		    $focus->column_fields['comments'] = $content;
		}
		
		$focus->column_fields['related_record'] = $relatedCRMid;
		$focus->column_fields['source'] = 'WORKFLOW';
		$focus->save('Notifications');
		
		$util->revertUser();
		
	}

	public function matchHandler($match) {
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $match[1], $matches);
		// If parent is empty then we can't do any thing here
		if(!empty($this->parent)){
			if(count($matches) != 0){
				list($full, $referenceField, $referenceModule, $fieldname) = $matches;
				$referenceId = $this->parent->get($referenceField);
				if($referenceModule==="Users" || $referenceId==null){
					$result ="";
				} else {
					$result = $referenceId;
				}
			}
		}
		return $result;
	}
	
}
?>
