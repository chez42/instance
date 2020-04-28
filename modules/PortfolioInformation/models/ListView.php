<?php

class PortfolioInformation_ListView_Model extends Vtiger_ListView_Model{
    public function getListViewMassActions($linkParams) {
        $massActionLinks = parent::getListViewMassActions($linkParams);

        $massActionLink = array(
            'linktype' => 'LISTVIEWMASSACTION',
            'linklabel' => 'Get Report Pdf',
            'linkurl' => 'javascript:PortfolioInformation_List_Js.triggerReportPdf("index.php?module='.$this->getModule()->getName().'&view=ReportPdf&mode=showSelectReportForm");',
            'linkicon' => ''
        );
        $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);

        return $massActionLinks;
    }

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