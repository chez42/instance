<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class MailManager_BasicAjax_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
		return;
	}

	public function process(Vtiger_Request $request) {
	    
		$searchValue = $request->get('search_value');
		
		global $adb;
		
		$query = "SELECT DISTINCT 
        	vtiger_crmentity.setype,
        	vtiger_contactdetails.contactid as crmid, 
        	concat(vtiger_contactdetails.firstname,' ',vtiger_contactdetails.lastname) as label, 
        	vtiger_contactdetails.email as email 
        FROM vtiger_contactdetails
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
        AND vtiger_crmentity.deleted = 0
        WHERE (vtiger_contactdetails.firstname LIKE '%".$searchValue."%' 
        OR vtiger_contactdetails.lastname LIKE '%".$searchValue."%' 
        OR vtiger_contactdetails.email LIKE '%".$searchValue."%' ) ";
        $query .= " UNION ";
        $query .= " SELECT DISTINCT 
        	vtiger_crmentity.setype,
        	vtiger_leaddetails.leadid as crmid, 
            concat(vtiger_leaddetails.firstname,' ',vtiger_leaddetails.lastname) as label, 
            vtiger_leaddetails.email as email 
        FROM vtiger_leaddetails
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
        AND vtiger_crmentity.deleted = 0
        WHERE (vtiger_leaddetails.firstname LIKE '%".$searchValue."%' 
        OR vtiger_leaddetails.lastname LIKE '%".$searchValue."%'
        OR vtiger_leaddetails.email LIKE '%".$searchValue."%')";
        
		$result = $adb->pquery($query);
		$noOfRows = $adb->num_rows($result);
		
		$moduleModels = array();
		$matchingRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
		    $row = $adb->query_result_rowdata($result, $i);
		   
		    if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])){
		        $row['id'] = $row['crmid'];
		        $moduleName = $row['setype'];
		        if(!array_key_exists($moduleName, $moduleModels)) {
		            $moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
		        }
		        $moduleModel = $moduleModels[$moduleName];
		        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		        $recordInstance = new $modelClassName();
		        $matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
		    }
		}

		$baseRecordId = $request->get('base_record');
		$result = array();
		foreach($matchingRecords as $moduleName=>$recordModels) {
			foreach($recordModels as $recordModel) {
			    $result[] = array('label'=>decode_html($recordModel->getName().'('.$recordModel->get('email').')'), 'value'=>decode_html($recordModel->get('email')), 'id'=>$recordModel->getId());
			}
		}
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}
