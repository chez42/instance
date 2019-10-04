<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_GroupedBySalesStage_Dashboard extends Vtiger_IndexAjax_View {

	/**
	 * Retrieves css styles that need to loaded in the page
	 * @param Vtiger_Request $request - request model
	 * @return <array> - array of Vtiger_CssScript_Model
	 */
	function getHeaderCss(Vtiger_Request $request){
		$cssFileNames = array(
			//Place your widget specific css files here
		);
		$headerCssScriptInstances = $this->checkAndConvertCssStyles($cssFileNames);
		return $headerCssScriptInstances;
	}
    
    function getSearchParams($stage,$assignedto,$dates) {
        $listSearchParams = array();
        $conditions = array();
        array_push($conditions,array("sales_stage","e",decode_html(urlencode(escapeSlashes($stage)))));
        if($assignedto == ''){
            $currenUserModel = Users_Record_Model::getCurrentUserModel();
			$assignedto = $currenUserModel->getId();
        }
        if($assignedto != 'all'){
            $ownerType = vtws_getOwnerType($assignedto);
            if($ownerType == 'Users')
                array_push($conditions,array("assigned_user_id","e",decode_html(urlencode(escapeSlashes(getUserFullName($assignedto))))));
            else{
                $groupName = getGroupName($assignedto);
                $groupName = $groupName[0];
                array_push($conditions,array("assigned_user_id","e",decode_html(urlencode(escapeSlashes($groupName)))));
            }
        } 
        if(!empty($dates)) {
            array_push($conditions,array("closingdate","bw",$dates['start'].','.$dates['end']));
        }
        $listSearchParams[] = $conditions;
        return '&search_params='. json_encode($listSearchParams);
    }
    
	public function process(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$linkId = $request->get('linkid');
		$owner = $request->get('owner');
		$dates = $request->get('expectedclosedate');

		//Date conversion from user to database format
		if(!empty($dates)) {
			$dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($dates['start']);
			$dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($dates['end']);
		}
		
		$tab = $request->get('tab');
		
		global $adb;
		
		$condition = $adb->pquery("SELECT * FROM vtiger_dashboard_widget_conditions
            WHERE user_id = ? AND link_id = ? AND tab_id = ?",array($currentUser->getId(), $linkId, $tab));
		
		if(!$owner){
		    
		    if($adb->num_rows($condition)){
		        
		        $con = $adb->query_result($condition,0,'conditions');
		        
		        if($con){
		            
		            $cond = json_decode(html_entity_decode($con),true);
		            
		            if(!empty($cond)){
		                $owner = $cond['owner'];
		                
		                if(!empty($cond['expectedclosedate']['start']))
		                    $dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($cond['expectedclosedate']['start']);
	                    if(!empty($cond['expectedclosedate']['end']))
	                        $dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($cond['expectedclosedate']['end']);
		            }
		        }
		        
		    }
		    
		}
	
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$data = $moduleModel->getPotentialsCountBySalesStage($owner, $dates);
        $listViewUrl = $moduleModel->getListViewUrlWithAllFilter();
        for($i = 0;$i<count($data);$i++){
            $data[$i][] = $listViewUrl.$this->getSearchParams($data[$i]['link'],$owner,$dates).'&nolistcache=1';
        }
        
		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		$viewer->assign('SELECTED', $owner);
		if($dates['start'])
		  $viewer->assign('START', date('m-d-Y',strtotime($dates['start'])));
		if( $dates['end'])
		  $viewer->assign('END', date('m-d-Y',strtotime($dates['end'])));
		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('DATA', $data);

		//Include special script and css needed for this widget
		$viewer->assign('STYLES',$this->getHeaderCss($request));
		$viewer->assign('CURRENTUSER', $currentUser);

		$content = $request->get('content');
		if(!empty($content)) {
			$viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/GroupBySalesStage.tpl', $moduleName);
		}
	}
}