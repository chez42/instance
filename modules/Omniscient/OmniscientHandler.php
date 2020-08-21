<?php

require_once 'include/utils/omniscientCustom.php';

class OmniscientHandler extends VTEventHandler{
    function handleEvent($eventName, $entityData) {
        
		$recordId = $entityData->getId();
		
		$moduleName = $entityData->getModuleName();
		
		if ($moduleName == 'HelpDesk') {

			if($eventName == 'vtiger.entity.aftersave'){
				
				$adb = PearDatabase::getInstance();
				
				$ticket_id = $entityData->getId();
				
				$parent_id = $entityData->get("parent_id");
				
				if($parent_id){
    				$advisor = getRecordOwnerId($parent_id);
    				
    				if(isset($advisor['Users']))
    					$advisor = $advisor['Users'];
    				else if(isset($advisor['Groups']))
    					$advisor = $advisor['Groups'];
    				
    				//$advisor = GetAdvisorNameFromTicketId($ticket_id);
    				
    				$query = "UPDATE vtiger_troubletickets SET financial_advisor = ? WHERE ticketid=?";
    				$adb->pquery($query,array($advisor, $ticket_id));
				}
			}
		}
    }
}

?>