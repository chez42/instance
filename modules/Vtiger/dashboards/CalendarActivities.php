<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_CalendarActivities_Dashboard extends Vtiger_IndexAjax_View {

	public function process(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$page = $request->get('page');
		$linkId = $request->get('linkid');

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $page);
		$pagingModel->set('limit', 10);

		$user = $request->get('type');
		
		$tab = $request->get('tab');
		
		global $adb;
		
		$condition = $adb->pquery("SELECT * FROM vtiger_dashboard_widget_conditions
            WHERE user_id = ? AND link_id = ? AND tab_id = ?",array($currentUser->getId(), $linkId, $tab));
		
		if(!$user){
		    
		    if($adb->num_rows($condition)){
		        
		        $con = $adb->query_result($condition,0,'conditions');
		        
		        if($con){
		            
		            $cond = json_decode(html_entity_decode($con),true);
		            if(!empty($cond)){
		                $user = $cond['type'];
		            }
		        }
		        
		    }
		    
		}
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$calendarActivities = $moduleModel->getCalendarActivities('upcoming', $pagingModel, $user);

		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('SELECTED', $user);
		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('ACTIVITIES', $calendarActivities);
		$viewer->assign('PAGING', $pagingModel);
		$viewer->assign('CURRENTUSER', $currentUser);

		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/CalendarActivitiesContents.tpl', $moduleName);
		} else {
			$sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
			$sharedGroups = Calendar_Module_Model::getSharedCalendarGroupsList($currentUser->id);
			$viewer->assign('SHARED_USERS', $sharedUsers);
			$viewer->assign('SHARED_GROUPS', $sharedGroups);
			
			$viewer->view('dashboards/CalendarActivities.tpl', $moduleName);
		}
	}
}
