<?php

class PortfolioInformation_ListView_Model extends Vtiger_ListView_Model{
    
    function getListViewEntries($pagingModel) {
        return parent::getListViewEntries($pagingModel);
        
    }
    
    function getQuery() {
        $query = parent::getQuery();
        $query = str_replace("vtiger_pc_account_custom ON vtiger_portfolioinformation.portfolioinformationid", 
                             "vtiger_pc_account_custom ON vtiger_portfolioinformation.account_number", 
                             $query);
        return $query;
    }
    
}

?>