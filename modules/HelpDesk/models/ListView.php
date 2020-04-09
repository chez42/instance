<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$massActionLinks = parent::getListViewMassActions($linkParams);

		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_SEND_EMAIL',
				'linkurl' => 'javascript:Vtiger_List_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().'&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
				'linkicon' => ''
			);
			$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}
		
		$massActionLink = array(
		    'linktype' => 'LISTVIEWMASSACTION',
		    'linklabel' => 'Export TimeSheet',
		    'linkurl' => 'javascript:HelpDesk_List_Js.triggerExportTimeSheetAction("index.php?module='.$this->getModule()->getName().'&view=ExportTimeSheet");',
		    'linkicon' => ''
		);
		$massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		

		return $massActionLinks;
	}
	
	public function getQuery(){
	    
	    $query = parent::getQuery();
		
        $pos = strpos($query, "SELECT vtiger_troubletickets");
        if ($pos !== false) {
            $query = substr_replace($query, "SELECT DISTINCT vtiger_troubletickets", $pos, strlen("SELECT vtiger_troubletickets"));
        }
	    
	    if(!empty($this->get('search_params'))){
	        
	        foreach ($this->get('search_params') as $searchParams){
	            
	            foreach($searchParams['columns'] as $columns){
	                
	                $colpos = strpos($columns['columnname'],'view_permission');
	                
	                if ($colpos !== false) {
	                    
	                    $listQuerycom = explode("WHERE",$query);
	                    
	                    $listQuerycom[0].=" INNER JOIN vtiger_ticket_view_permission ON vtiger_ticket_view_permission.ticketid = vtiger_troubletickets.ticketid ";
	                    
	                    $query = implode(" WHERE ",$listQuerycom);
	                    
	                    $values = explode(',',$columns['value']);
	                    
	                    $column_query = '';
	                    
	                    foreach($values as $key=>$value){
	                        
	                        if($key > 0){
	                            $column_query .= ' OR ';
	                        }
	                        
	                        $column_query .= "vtiger_troubletickets.view_permission LIKE '%$value%'";
	                        
	                    }
	                    
	                    if($column_query){
	                        
	                        $query = str_replace($column_query, "vtiger_ticket_view_permission.view_permission_id IN (".$columns['value'].")", $query);
	                    
	                    }
	                    
	                }
	                
	            }
	            
	        }
	        
	    }
	    
	    return $query;
	    
	}
	
	
}
?>
