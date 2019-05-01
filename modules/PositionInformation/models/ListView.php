<?php

class PositionInformation_ListView_Model extends Vtiger_ListView_Model{
    
    function getListViewEntries($pagingModel) {
        return parent::getListViewEntries($pagingModel);
        
    }
    function getQuery() {
        $query = parent::getQuery();
/*        $query = str_replace("FROM vtiger_positioninformation ", 
                             "FROM vtiger_positioninformation JOIN vtiger_crmentity e ON e.crmid = vtiger_positioninformation.contact_link ", $query);
        $query .= " AND e.deleted = 0 AND (vtiger_positioninformation.quantity > 0 OR vtiger_positioninformation.quantity < 0)";*/
        return $query;
//        echo $query;exit;
//        return parent::getQuery();
/*        
        $queryGenerator = $this->get('query_generator');
//        $queryGenerator->addCondition('contact_link','contact_link','n','AND');
//        $queryGenerator->addRelatedModuleCondition("Contacts", 'contact_link');
//        $queryGenerator->addReferenceModuleFieldCondition("Contacts", "contactid", 'contact_link', '1', 'n', null);
        
        $listQuery = $queryGenerator->getQuery();
        echo $listQuery;exit;
//        $listQuery = str_replace("")
//        $listQuery = str_replace("vtiger_positioninformation.current_value", "SUM(vtiger_positioninformation.current_value) AS current_value", $listQuery);
//        $listQuery .= " GROUP BY security_symbol ";
//        return $listQuery;*/
    }
    
}

?>
