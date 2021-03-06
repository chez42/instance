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
        
        $moduleModel = Vtiger_Module_Model::getInstance('Transactions');
        
        $trade_date= array();
        
        if($request->get('trade_date')){
        
            $trade_date = $request->get('trade_date');
            $start = new DateTimeField($trade_date['start']);
            $trade_date['start'] = $start->getDBInsertDateValue();
            
            $end = new DateTimeField($trade_date['end']);
            $trade_date['end'] = $end->getDBInsertDateValue();
            $_SESSION['distributionWidgetId'] =$trade_date;
            
        }else if($_SESSION['distributionWidgetId']){
            
            $trade_date = $_SESSION['distributionWidgetId'];
        
        }else{
            
            $date = new DateTime();
            $dateMinus12 = $date->modify("-12 months");
            $lastDay = $date->format("Y-m-d");
            
            $trade_date['start'] = $lastDay;
            $trade_date['end'] = date('Y-m-d');
            
        }
        
        
        
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
		(vtiger_contactscf.cf_3266 > 0 and vtiger_contactscf.cf_3266 != '' and vtiger_contactscf.cf_3266 is not NULL) AND vtiger_crmentity.deleted = 0 ";
        
        $startDate = (isset($trade_date['start']))?$trade_date['start']:"";
        
        if($startDate)
            $query .= " AND vtiger_transactions.trade_date >= '" . getValidDBInsertDateValue($startDate) . "'";
            
            
        $endDate = (isset($trade_date['end']))?$trade_date['end']:"";
        
        if($endDate)
            $query .= " AND vtiger_transactions.trade_date <= '".getValidDBInsertDateValue($endDate)."' ";
            
        $query .= "GROUP BY vtiger_contactscf.cf_3266
		ORDER BY vtiger_contactscf.cf_3266 ASC";
        
        $result = $db->pquery($query,array());

		
        $response = array();
        
        $listViewUrl = $moduleModel->getListViewUrlWithAllFilter();
        $listViewUrl = str_ireplace("view=List", "view=GraphFilterList", $listViewUrl);
        
        $data = array();
        for($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $tmp['title'] = $row['cf_3266'];
            $tmp['value'] = $row['totalamount'];
            //$tmp['url'] = $listViewUrl.$this->getSearchParams($row['cf_3266']).'&nolistcache=1';
            $tmp['url'] = $listViewUrl.'&start='.$startDate.'&end='.$endDate.'&contactage='.$row['cf_3266'].'&nolistcache=1';
            $data[] = $tmp;
        }
        
        $data  = array_values($data);
        
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('CHART_DATA', $data);
        $viewer->assign('CURRENTUSER', $currentUser);
        
       
        
        $tradeDates = array();
        
        if(!$trade_date){
            
            if(date('l') == 'Monday'){
                $tradeDates['start_date'] = date("Y-m-d", strtotime("previous friday"));
            }else{
                $tradeDates['start_date'] = date("Y-m-d", strtotime("-1 day"));
            }
            
            $tradeDates['end_date'] = date("Y-m-d");
            
            $seachParams = implode(",",array_map('getValidDisplayDate', $tradeDates));
            
        } else {
            
            $trade_date = $trade_date;
            
            $tradeDates['start_date'] = getValidDBInsertDateValue($trade_date['start']);
            
            $tradeDates['end_date'] = getValidDBInsertDateValue($trade_date['end']);
            
            $seachParams = implode(",",array_map('getValidDisplayDate', $tradeDates));
        }
        
        $viewer->assign('TRADE_DATE', $tradeDates);
        
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
