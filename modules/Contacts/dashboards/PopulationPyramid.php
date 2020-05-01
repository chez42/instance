<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_PopulationPyramid_Dashboard extends Vtiger_IndexAjax_View {
    
    public function process(Vtiger_Request $request) {
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        
        $linkId = $request->get('linkid');
        $data = $request->get('data');
        
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $db = PearDatabase::getInstance();
        
        $query = "SELECT  Age,
				SUM(cf_1718 = 'Female') Female,
				SUM(cf_1718 = 'Male') Male,
				COUNT(*) count
				FROM ( SELECT  CASE ";
        
        
        for($i = 0; $i < 110; $i = $i + 5){
            $query .= ' WHEN vtiger_contactscf.cf_3266 BETWEEN ';
            
			if($i == 0){
				$query .= $i;
            } else {
				$query .= ($i+1);
            }
			
            $query .= ' AND ';
            $query .= $i+5;
            $query .= ' THEN ';
            $query .=  "'" . $i;
            $query .= '-';
            $query .= $i + 5 . "'";
        }
        
        $query .= ' END Age, cf_1718 from vtiger_contactscf
		inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactscf.contactid ';
        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Contacts');
        $query .= "where deleted = 0 and (vtiger_contactscf.cf_3266 > 0 and vtiger_contactscf.cf_3266 != '' and vtiger_contactscf.cf_3266 is not NULL and cf_3266 < 110)
			and cf_1718 in ('Male','Female')) age_range group by Age";
        
        $result = $db->pquery($query,array());
        
        $response = array();
        
        $listViewUrl = $moduleModel->getListViewUrlWithAllFilter();
        $listViewUrl = str_ireplace("view=List", "view=GraphFilterList", $listViewUrl);
        
        $data = array();
        
        for($i=0; $i<$db->num_rows($result); $i++) {
            
            $row = $db->query_result_rowdata($result, $i);
            
            $data[$row['age']]['age'] =  $row['age'];
            
            $data[$row['age']]['male'] =  $row['male'];
            
            $data[$row['age']]['female'] =  $row['female'];
            
            $data[$row['age']]['url'] = $listViewUrl.$this->getSearchParams('Male',$row['age']).'&nolistcache=1';
            $data[$row['age']]['url1'] = $listViewUrl.$this->getSearchParams('Female',$row['age']).'&nolistcache=1';
        }
       
        $chart_data = array();
        
        for($i = 0; $i < 110; $i = $i + 5){
            if(isset($data["$i-" . ($i + 5)])){
                $chart_data[] = $data["$i-" . ($i + 5)];
            }
        }
        
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('CHART_DATA', $chart_data);
        $viewer->assign('CURRENTUSER', $currentUser);
        
        
        $content = $request->get('content');
        if(!empty($content)) {
            $viewer->view('dashboards/PopulationPyramidContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/PopulationPyramid.tpl', $moduleName);
        }
    }
    
    function getSearchParams($gender,$range) {
        $listSearchParams = array();
        
        $numVal = explode('-', $range);
        $age = array();
        for($i=$numVal[0]+1;$i<=$numVal[1];$i++){
            $age[] = $i;
        }
        
        $conditions = array(array('cf_1718','e',decode_html(urlencode(escapeSlashes($gender)))),array('cf_3266','c',decode_html(urlencode(escapeSlashes(implode(',',$age))))));
        $listSearchParams[] = $conditions;
        return '&search_params='. json_encode($listSearchParams);
    }
    
    
}
