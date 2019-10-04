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
        
        
        $query = "select concat(5*floor(cf_3266/5), '-', 5*floor(cf_3266/5) + 5) as `range`,cf_1718 as gender,
         count(*) as count from `vtiger_contactdetails` inner join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
		 inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid ".
		 Users_Privileges_Model::getNonAdminAccessControlQuery('Contacts')
		 ." where deleted=0 and cf_3266>0 and cf_1718 in ('Male','Female') group by `range`, gender order by cf_3266 asc" ;
		 
		 $result = $db->pquery($query,array());
		 
		 $response = array();
		 
		 $data = array();
		 for($i=0; $i<$db->num_rows($result); $i++) {
		     $row = $db->query_result_rowdata($result, $i);
		     
		     $range = $row['range'];
		     
		     $gender =  strtolower($row['gender']);
		     
		     $count = $row['count'];
		     
		     $data[$range]['age'] =  $range;
		     
		     $data[$range][$gender] =  $count;
		     
		     
		     
		 }
		 
		 $data  = array_values($data);
		 
		 
		 $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
		 
		 
		 
		 $viewer->assign('WIDGET', $widget);
		 $viewer->assign('MODULE_NAME', $moduleName);
		 $viewer->assign('CHART_DATA', $data);
		 $viewer->assign('CURRENTUSER', $currentUser);
		 
		 
		 $content = $request->get('content');
		 if(!empty($content)) {
		     $viewer->view('dashboards/PopulationPyramidContents.tpl', $moduleName);
		 } else {
		     $viewer->view('dashboards/PopulationPyramid.tpl', $moduleName);
		 }
    }
    
    
}
