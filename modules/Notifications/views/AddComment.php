<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Notifications_AddComment_View extends Vtiger_IndexAjax_View {
	

	function process(Vtiger_Request $request) {
		
		$sourceModule = $request->getModule();
		$moduleName = 'ModComments';
		
		$viewer = $this->getViewer($request);
		$viewer->assign('SOURCE_MODULE', $sourceModule);
		$viewer->assign('MODULE', $moduleName);
		$related_id = $request->get('related_id');
		$parentComment = $request->get('comment_id');
		
		$modCommentsModel = Vtiger_Module_Model::getInstance($moduleName);
		$fileNameFieldModel = Vtiger_Field::getInstance("filename", $modCommentsModel);
		$fileFieldModel = Vtiger_Field_Model::getInstanceFromFieldObject($fileNameFieldModel);
		$viewer->assign('FIELD_MODEL', $fileFieldModel);
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
		
		$viewer->assign('RELATED_TO', $related_id);
		$viewer->assign('PARENT_COMMENT', $parentComment);
      
		echo $viewer->view('AddComment.tpl',$sourceModule,true);
		
		
	}

	
}
