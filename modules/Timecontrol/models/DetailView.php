<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class TimeControl_DetailView_Model extends Vtiger_DetailView_Model {
    
    public function getDetailViewLinks($linkParams) {
        $linkTypes = array('DETAILVIEWBASIC','DETAILVIEW');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();
        
        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();
        
        $detailViewLink = array();
        $linkModelList = array();
//         if(Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
//             $detailViewLinks[] = array(
//                 'linktype' => 'DETAILVIEWBASIC',
//                 'linklabel' => 'LBL_EDIT',
//                 'linkurl' => $recordModel->getEditViewUrl(),
//                 'linkicon' => ''
//             );
            
//             foreach ($detailViewLinks as $detailViewLink) {
//                 $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
//             }
//         }
        
        if(Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
            $deletelinkModel = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_'. $moduleName, $moduleName)),
                'linkurl' => 'javascript:Vtiger_Detail_Js.deleteRecord("'.$recordModel->getDeleteUrl().'")',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($deletelinkModel);
        }
        
//         if($moduleModel->isDuplicateOptionAllowed('CreateView', $recordId)) {
//             $duplicateLinkModel = array(
//                 'linktype' => 'DETAILVIEWBASIC',
//                 'linklabel' => 'LBL_DUPLICATE',
//                 'linkurl' => $recordModel->getDuplicateRecordUrl(),
//                 'linkicon' => ''
//             );
//             $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($duplicateLinkModel);
//         }
        
        if($this->getModule()->isModuleRelated('Emails') && Vtiger_RecipientPreference_Model::getInstance($this->getModuleName())) {
            $emailRecpLink = array('linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_EMAIL_RECIPIENT_PREFS',  $this->getModuleName()),
                'linkurl' => 'javascript:Vtiger_Index_Js.showRecipientPreferences("'.$this->getModuleName().'");',
                'linkicon' => '');
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($emailRecpLink);
        }
        
        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
        if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
            $basicActionLink = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_ADD_COMMENT',
                'linkurl' => 'javascript:Vtiger_Detail_Js.triggerAddCommentForm("module='.$moduleModel->get('name').'&view=AddCommentFormDetailView&mode=showAddCommentForm")',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }
        $linkModelListDetails = Vtiger_Link_Model::getAllByType($moduleModel->getId(),$linkTypes,$linkParams);
        foreach($linkTypes as $linkType) {
            if(!empty($linkModelListDetails[$linkType])) {
                foreach($linkModelListDetails[$linkType] as $linkModel) {
                    // Remove view history, needed in vtiger5 to see history but not in vtiger6
                    if($linkModel->linklabel == 'View History') {
                        continue;
                    }
                    $linkModelList[$linkType][] = $linkModel;
                }
            }
            unset($linkModelListDetails[$linkType]);
        }
        
        $relatedLinks = $this->getDetailViewRelatedLinks();
        
        foreach($relatedLinks as $relatedLinkEntry) {
            $relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
            $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }
        
        $widgets = $this->getWidgets();
        foreach($widgets as $widgetLinkModel) {
            $linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
        }
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if($currentUserModel->isAdminUser()) {
            $settingsLinks = $moduleModel->getSettingLinks();
            foreach($settingsLinks as $settingsLink) {
                $linkModelList['DETAILVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
            }
        }
       
//         if($recordModel->get('invoiced_on') == ''){
//             $detailViewLinks[] = array(
//                 'linktype' => 'DETAILVIEWBASIC',
//                 'linklabel' => 'Add To Invoice',
//                 'linkurl' => 'javascript:Timecontrol_Detail_Js.SelectInvoice('.$recordId.',"Timecontrol","InvoicePopup")',
//                 'linkicon' => ''
//             );
            
//             foreach ($detailViewLinks as $detailViewLink) {
//                 $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
//             }
//         }
        
//         if($recordModel->get('relatedto') && getSalesEntityType($recordModel->get('relatedto')) == 'HelpDesk'){
//             $detailViewLinks[] = array(
//                 'linktype' => 'DETAILVIEWBASIC',
//                 'linklabel' => 'Ticket Preview',
//                 'linkurl' => 'javascript:Timecontrol_Detail_Js.TicketPreview('.$recordModel->get('relatedto').')',
//                 'linkicon' => ''
//             );
            
//             foreach ($detailViewLinks as $detailViewLink) {
//                 $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
//             }
//         }
        
        return $linkModelList;
    }
    
    public function getDetailViewRelatedLinks() {
       
        $recordModel = $this->getRecord();
        $moduleName = $recordModel->getModuleName();
        $parentModuleModel = $this->getModule();
        $relatedLinks = parent::getDetailViewRelatedLinks();
        $links = array();
        foreach ($relatedLinks as $relatedLink){
            if($relatedLink['linklabel'] != 'LBL_JOURNAL'){
                $links[] = $relatedLink;
            }
        }
        return $links;
    }
}