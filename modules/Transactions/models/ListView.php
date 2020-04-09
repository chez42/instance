<?php

class Transactions_ListView_Model extends Vtiger_ListView_Model{
    
    function getListViewEntries($pagingModel) {
        return parent::getListViewEntries($pagingModel);
        
    }
    
    function getQuery() {
        $query = parent::getQuery();
        $query = str_replace("AND vtiger_transactions.transactionsid > 0 ", " ", $query);
        return $query;
    }
    
}

?>