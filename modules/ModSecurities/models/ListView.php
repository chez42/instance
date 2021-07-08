<?php

class ModSecurities_ListView_Model extends Vtiger_ListView_Model{
    
    public function getListViewMassActions($linkParams) {
        $massActionLinks = parent::getListViewMassActions($linkParams);
        
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		
	    $BillingModuleModel = Vtiger_Module_Model::getInstance('Billing');
        
        if($currentUserModel->hasModulePermission($BillingModuleModel->getId())) {
            $massActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'Add Price',
                'linkurl' => 'javascript:ModSecurities_List_Js.addNewPrice();',
                'linkicon' => ''
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }  
        
        
        return $massActionLinks;
    }
}
?>