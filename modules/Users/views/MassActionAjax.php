<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_MassActionAjax_View extends Vtiger_MassActionAjax_View {
	
	protected function getEmailFieldsInfo(Vtiger_Request $request) {
		$sourceModule = $request->getModule();
		$emailFieldsInfo = array();
		$moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
		$recipientPrefModel = Vtiger_RecipientPreference_Model::getInstance($sourceModule);
		
		if($recipientPrefModel)
		$recipientPrefs = $recipientPrefModel->getPreferences();
		$moduleEmailPrefs = $recipientPrefs[$moduleModel->getId()];
		$emailFields = $moduleModel->getFieldsByType('email');
        $accesibleEmailFields = array();
		
        foreach($emailFields as $index=>$emailField) {
            $fieldName = $emailField->getName();
            if($emailField->isViewable()) {
				if($moduleEmailPrefs && in_array($emailField->getId(),$moduleEmailPrefs)){
					$emailField->set('isPreferred',true);
				}
                $accesibleEmailFields[$fieldName] = $emailField;
            }
        }
		
        $emailFields = $accesibleEmailFields;
        if(count($emailFields) > 0) {
            $recordIds = $this->getRecordsListFromRequest($request);
			global $current_user;
            $baseTableId = $moduleModel->get('basetableid');
            $queryGen = new QueryGenerator($moduleModel->getName(), $current_user);
			$selectFields = array_keys($emailFields);
            array_push($selectFields,'id');
			$queryGen->setFields($selectFields);
			$query = $queryGen->getQuery();
                
			$query =  $query.' AND id IN ('.  generateQuestionMarks($recordIds).')';
			
			$emailOptout = $moduleModel->getField('emailoptout');
			if($emailOptout) {
				$query .= ' AND '.$emailOptout->get('column').' = 0';
			}
			
            $db = PearDatabase::getInstance();
            $result = $db->pquery($query,$recordIds);
            $num_rows = $db->num_rows($result);
			
			if($num_rows > 0) {
				for($i=0;$i<$num_rows;$i++){
					$emailFieldsList = array();
					foreach ($emailFields as $emailField) {
						$emailValue = $db->query_result($result, $i, $emailField->get('column')) ;
						if(!empty($emailValue)) {
							$emailFieldsList[$emailValue] = $emailField;
						}
					}
					if(!empty($emailFieldsList)) {
                        $recordId = $db->query_result($result, $i,$baseTableId);
						$emailFieldsInfo[$moduleModel->getName()][$recordId] = $emailFieldsList;
					}
				}
			}
        }
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORDS_COUNT', count($recordIds));
		
		if($recipientPrefModel && !empty($recipientPrefs)) {
			$viewer->assign('RECIPIENT_PREF_ENABLED',true);
		}

		$viewer->assign('EMAIL_FIELDS', $emailFields);

		$viewer->assign('PREF_NEED_TO_UPDATE',  $this->isPreferencesNeedToBeUpdated($request));
		return $emailFieldsInfo;
	}
	
	
}
