<?php

class Group_ListView_Model extends Vtiger_ListView_Model{
    
    public function getListViewMassActions($linkParams) {
        $massActionLinks = parent::getListViewMassActions($linkParams);
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $massActionLink = array(
            'linktype' => 'BASIC',
            'linklabel' => 'Generate Statement',
            'linkurl' => 'javascript:Group_List_Js.triggerGroupBillingReportPdf("index.php?module='.$this->getModule()->getName().'&view=GroupBillingReportPdf&mode=GenrateLink");',
            'linkicon' => ''
        );
        $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        
        return $massActionLinks;
    }

}

?>