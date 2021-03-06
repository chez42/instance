<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class PortfolioInformation_DetailView_Model extends Vtiger_DetailView_Model {

	/**
	 * Function to get the detail view links (links and widgets)
	 * @param <array> $linkParams - parameters which will be used to calicaulate the params
	 * @return <array> - array of link models in the format as below
	 *                   array('linktype'=>list of link models);
	 */
	public function getDetailViewLinks($linkParams) {
            $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
            $emailModuleModel = Vtiger_Module_Model::getInstance('Emails');
            $positionsModuleModel = Vtiger_Module_Model::getInstance("PositionInformation");

            $recordModel = $this->getRecord();

            $linkModelList = parent::getDetailViewLinks($linkParams);

            if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
                    $basicActionLink = array(
                            'linktype' => 'DETAILVIEWBASIC',
                            'linklabel' => 'LBL_SEND_EMAIL',
                            'linkurl' => 'javascript:Vtiger_Detail_Js.triggerSendEmail("index.php?module='.$this->getModule()->getName().
                                                            '&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
                            'linkicon' => ''
                    );
                    $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            }

        if($currentUserModel->hasModulePermission($positionsModuleModel->getId())) {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_CREATE_POSITION',
                'linkurl' => 'index.php?module=PositionInformation&view=Edit&account_number=' . $recordModel->get('account_number') .
                             '&custodian_control_number=' . $recordModel->get('production_number') .
                             '&omniscient_control_number=' . $recordModel->get('omniscient_control_number') .
                             '&cf_2662=1' . //Manual yes/no
                             '&custodian=Manual' .
                             '&custodian_source=Manual' .
                             '&household_account=' . $recordModel->get('household_account'),
                             '&assigned_user_id=' . $recordModel->get('assigned_user_id'),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        //TODO: update the database so that these separate handlings are not required
            $index=0;
            foreach($linkModelList['DETAILVIEW'] as $link) {
                    if($link->linklabel == 'View History' || $link->linklabel == 'Send SMS') {
                            unset($linkModelList['DETAILVIEW'][$index]);
                    } else if($link->linklabel == 'LBL_SHOW_ACCOUNT_HIERARCHY') {
                            $linkURL = 'index.php?module=Accounts&view=AccountHierarchy&record='.$recordModel->getId();
                            $link->linkurl = 'javascript:Accounts_Detail_Js.triggerAccountHierarchy("'.$linkURL.'");';
                            unset($linkModelList['DETAILVIEW'][$index]);
                            $linkModelList['DETAILVIEW'][$index] = $link;
                    }
                    $index++;
            }

/*            $CalendarActionLinks = array();
            $CalendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
            if($currentUserModel->hasModuleActionPermission($CalendarModuleModel->getId(), 'EditView')) {
                    $CalendarActionLinks[] = array(
                                    'linktype' => 'DETAILVIEW',
                                    'linklabel' => 'LBL_ADD_EVENT',
                                    'linkurl' => $recordModel->getCreateEventUrl(),
                                    'linkicon' => ''
                    );

                    $CalendarActionLinks[] = array(
                                    'linktype' => 'DETAILVIEW',
                                    'linklabel' => 'LBL_ADD_TASK',
                                    'linkurl' => $recordModel->getCreateTaskUrl(),
                                    'linkicon' => ''
                    );
            }*/

            $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
            if($currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
                    $basicActionLink = array(
                            'linktype' => 'DETAILVIEWBASIC',
                            'linklabel' => 'LBL_SEND_SMS',
                            'linkurl' => 'javascript:Vtiger_Detail_Js.triggerSendSms("index.php?module='.$this->getModule()->getName().
                                                            '&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                            'linkicon' => ''
                    );
                    $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
            }

            $moduleModel = $this->getModule();
            if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
                    $massActionLink = array(
                            'linktype' => 'LISTVIEWMASSACTION',
                            'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
                            'linkurl' => 'javascript:Vtiger_Detail_Js.triggerTransferOwnership("index.php?module='.$moduleModel->getName().'&view=MassActionAjax&mode=transferOwnership")',
                            'linkicon' => ''
                    );
                    $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
            }

/*            foreach($CalendarActionLinks as $basicLink) {
                    $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
            }*/

                $account_numbers = implode(',',PortfolioInformation_Module_Model::GetAccountNumbersFromCrmid($recordModel->getId()));
/*        if(strlen($account_numbers) > 1) {
            $intervals = array('linktype' => 'DETAILVIEWTAB',
                'linklabel' => 'Intervals',
                'linkurl' => 'index.php?module=PortfolioInformation&view=IntervalView&calling_module=PortfolioInformation&calling_record=' . $recordModel->getId() . '&account_numbers=' . $account_numbers,
                'linkicon' => '');
            $linkModelList['DETAILVIEWTAB'][] = Vtiger_Link_Model::getInstanceFromValues($intervals);
        }*/
            return $linkModelList;
	}

/**
	 * Function to get the detail view widgets
	 * @return <Array> - List of widgets , where each widget is an Vtiger_Link_Model
	 */
	public function getWidgets() {
		$moduleModel = $this->getModule();
                $tabid = getTabid($this->getModuleName());

                include_once("include/utils/Widget.php");
                $widget_loader = new cWidget();                
                $widgets = $widget_loader->GetDetailViewWidget($tabid, "&calling_record=".$this->getRecord()->getId());

/*		if($moduleModel->isTrackingEnabled()) {
			$widgets[4] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'LBL_UPDATES',
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&mode=showRecentActivities&page=1&limit=5',
                                        'sequence' => 1,
			);
		}*/
		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
		if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
			$widgets[2] = array(
					'linktype' => 'DETAILVIEWWIDGET',
					'linklabel' => 'ModComments',
					'linkurl' => 'module='.$this->getModuleName().'&view=Detail&record='.$this->getRecord()->getId().
							'&mode=showRecentComments&page=1&limit=5',
                                        'sequence' => 2,
			);
}
		$widgetLinks = array();
                ksort($widgets);

		foreach ($widgets as $widgetDetails) {
			$widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
		}
		return $widgetLinks;
	}
}
