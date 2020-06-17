<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class EmailTemplates_Save_Action extends Vtiger_Save_Action {

	public function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$record = $request->get('record');

		$actionName = ($record) ? 'EditView' : 'CreateView';
		if(!Users_Privileges_Model::isPermitted($moduleName, $actionName, $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}

		if (!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
	}

	public function process(Vtiger_Request $request) {
		$site_URL = vglobal('site_URL');
		$adb = PearDatabase::getInstance();
		$moduleName = $request->getModule();
		$record = $request->get('record');
		$emitResponse = $request->get('emitResponse');
		$recordModel = new EmailTemplates_Record_Model();
		$recordModel->setModule($moduleName);

		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		if (!empty($record)) {
			$recordModel->setId($record);
		}

		$recordModel->set('templatename', $request->get('templatename'));
		$recordModel->set('description', $request->get('description'));
		$recordModel->set('subject', $request->get('subject'));
		$recordModel->set('module', $request->get('modulename'));
		$recordModel->set('systemtemplate', $request->get('systemtemplate'));
		$content = $request->getRaw('templatecontent');
		$processedContent = Emails_Mailer_Model::getProcessedContent($content); // To remove script tags
		$recordModel->set('body', $processedContent);
		$recordModel->set('creator', $currentUser->id);

		$recordId = $recordModel->save();
		$recordModel->updateImageName($recordId);
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		$view_permission_ids = $request->get("template_view_permission");
		
		if(!$view_permission_ids)
		    $view_permission_ids = array($currentUser->id);
	    else if(is_array($view_permission_ids) && !in_array($currentUser->id, $view_permission_ids))
	        $view_permission_ids = array_merge($view_permission_ids, array($currentUser->id));
	        
        $exitingIds = array();
        
        $result = $adb->pquery("select * from vtiger_emailtemplates_view_permission where template_id = ?",array($recordModel->getId()));
        
        if($adb->num_rows($result)){
            
            while($row = $adb->fetchByAssoc($result)){
                
                $exitingIds[] = $row['view_permission_id'];
            }
        }
        
        if(!empty($view_permission_ids)){
            
            $permission_sql = "insert into vtiger_emailtemplates_view_permission(template_id, view_permission_id) values";
            
            $values = "";
            
            foreach($view_permission_ids as $view_permission_id){
                $values .= " (". $recordModel->getId().",".$view_permission_id."),";
            }
            
            if($values){
                
                $values = rtrim($values, ",");
                
                $values .= ' ON DUPLICATE KEY UPDATE ';
                
                $values .= "template_id = VALUES(template_id), view_permission_id = VALUES(view_permission_id)";
                
                $permission_sql = $permission_sql.$values;
                
                $adb->pquery($permission_sql,array());
            }
        }
        
        if(!empty($exitingIds)){
            
            $deletedIds = array_diff($exitingIds,$view_permission_ids);
            
            if(!empty($deletedIds)){
                $adb->pquery("delete from vtiger_emailtemplates_view_permission where template_id = ? and view_permission_id IN (". generateQuestionMarks($deletedIds) .")",
                    array($recordModel->getId(), $deletedIds));
            }
        }
	
		if ($request->get('returnmodule') && $request->get('returnview')){
			$loadUrl = 'index.php?'.$request->getReturnURL();
		} else {
			if ($request->get('returnmodule') && $request->get('returnview')) {
				$loadUrl = 'index.php?' . $request->getReturnURL();
			} else {
				$loadUrl = $recordModel->getDetailViewUrl();
			}
		}
		header("Location: $loadUrl");
	}

}
