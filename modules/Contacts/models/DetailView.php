<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

//Same as Accounts Detail View
class Contacts_DetailView_Model extends Accounts_DetailView_Model {
    
    public function getDetailViewLinks($linkParams) {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $portfolioModuleModel = Vtiger_Module_Model::getInstance("PortfolioInformation");
        
        $linkModelList = parent::getDetailViewLinks($linkParams);
        $recordModel = $this->getRecord();
        if($recordModel->get('portal')){
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => 'LBL_RESET_PASSWORD',
                'linkurl' => 'javascript:Contacts_Detail_Js.triggerPortalResetPassword("index.php?module='.$recordModel->getModuleName().'&view=PortalResetPassword&record='.$this->getRecord()->getId().'");',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }
        return $linkModelList;
    }
    
    
}
