<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_ClientDistribution_Dashboard extends Vtiger_IndexAjax_View {
    
    public function process(Vtiger_Request $request) {
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        
        $linkId = $request->get('linkid');
        $data = $request->get('data');
        
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $db = PearDatabase::getInstance();
        
        $query = "SELECT SUM(vtiger_transactionscf.net_amount) as totalamount, vtiger_contactscf.cf_3266 FROM vtiger_contactscf
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
        
		INNER JOIN vtiger_portfolioinformation ON vtiger_portfolioinformation.contact_link = vtiger_contactscf.contactid
        INNER JOIN vtiger_crmentity crmpor ON crmpor.crmid = vtiger_portfolioinformation.portfolioinformationid AND crmpor.deleted =0
		
        INNER JOIN vtiger_transactions ON vtiger_transactions.account_number = vtiger_portfolioinformation.account_number
        INNER JOIN vtiger_crmentity crmtrans ON crmtrans.crmid = vtiger_transactions.transactionsid AND crmtrans.deleted = 0
        INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid".
        Users_Privileges_Model::getNonAdminAccessControlQuery('Contacts')
        ."WHERE vtiger_transactionscf.transaction_activity = 'Management fee' and 
		(vtiger_contactscf.cf_3266 > 0 and vtiger_contactscf.cf_3266 != '' and vtiger_contactscf.cf_3266 is not NULL) AND vtiger_crmentity.deleted = 0
		GROUP BY vtiger_contactscf.cf_3266 ORDER BY vtiger_contactscf.cf_3266 ASC ";
        
        $result = $db->pquery($query,array());

		
        $response = array();
        
        $listViewUrl = $moduleModel->getListViewUrlWithAllFilter();
        $listViewUrl = str_ireplace("view=List", "view=GraphFilterList", $listViewUrl);
        
        $data = array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $tmp['title'] = $row['cf_3266'];
            $tmp['value'] = $row['totalamount'];
            $tmp['url'] = $listViewUrl.$this->getSearchParams($row['cf_3266']).'&nolistcache=1';
            $data[] = $tmp;
        }
        
        $data  = array_values($data);
        
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('CHART_DATA', $data);
        $viewer->assign('CURRENTUSER', $currentUser);
        
        $content = $request->get('content');
        if(!empty($content)) {
            $viewer->view('dashboards/ClientDistributionContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/ClientDistribution.tpl', $moduleName);
        }
    }
    
    function getSearchParams($value) {
        $listSearchParams = array();
        $conditions = array(array('cf_3266','e',decode_html(urlencode(escapeSlashes($value)))));
        $listSearchParams[] = $conditions;
        return '&search_params='. json_encode($listSearchParams);
    }
    
    
}
