<?php

class Transactions_ListView_Model extends Vtiger_ListView_Model{
    
    function getListViewEntries($pagingModel) {
        return parent::getListViewEntries($pagingModel);
        
    }
    
    function getQuery() {
        
        $listquery = parent::getQuery();
        $listquery = str_replace("AND vtiger_transactions.transactionsid > 0 ", " ", $listquery);
        
        if($this->get('contactage')){
            
            $db = PearDatabase::getInstance();
            
            $query = "SELECT DISTINCT vtiger_transactions.transactionsid FROM vtiger_contactscf
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
                
			INNER JOIN vtiger_portfolioinformation ON vtiger_portfolioinformation.contact_link = vtiger_contactscf.contactid
			INNER JOIN vtiger_crmentity crmpor ON crmpor.crmid = vtiger_portfolioinformation.portfolioinformationid AND crmpor.deleted =0
                
			INNER JOIN vtiger_transactions ON vtiger_transactions.account_number = vtiger_portfolioinformation.account_number
			INNER JOIN vtiger_crmentity crmtrans ON crmtrans.crmid = vtiger_transactions.transactionsid AND crmtrans.deleted = 0
			INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid".
			Users_Privileges_Model::getNonAdminAccessControlQuery('Contacts')
			."WHERE vtiger_transactionscf.transaction_activity = 'Management fee' and
			(vtiger_contactscf.cf_3266 =".$this->get('contactage').") AND vtiger_crmentity.deleted = 0 ";
			
			$startDate = $this->get('start')?$this->get('start'):"";
			
			if($startDate)
			    $query .= " AND vtiger_transactions.trade_date >= '" . getValidDBInsertDateValue($startDate) . "'";
			    
            $endDate = $this->get('end')? $this->get('end'):"";
		    
    	    if($endDate)
    	        $query .= " AND vtiger_transactions.trade_date <= '".getValidDBInsertDateValue($endDate)."' ";
	        
	        $result = $db->pquery($query,array());
	        
	        $transactionIds = array();
	        
	        for($t=0;$t<$db->num_rows($result);$t++){
	            
	            $transactionIds[] = $db->query_result($result, $t, 'transactionsid');
	            
	        }
	        $listquery .= " AND vtiger_transactions.transactionsid IN (".(implode(',', $transactionIds)).") ";
        }
        
        return $listquery;
        
    }
    
}

?>