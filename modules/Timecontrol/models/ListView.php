<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Timecontrol_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
	    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
	    $moduleModel = $this->getModule();
	    
	    $linkTypes = array('LISTVIEWMASSACTION');
	    $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
	    
	    
	    $massActionLinks = array();
// 	    if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
// 	        $massActionLinks[] = array(
// 	            'linktype' => 'LISTVIEWMASSACTION',
// 	            'linklabel' => 'LBL_EDIT',
// 	            'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
// 	            'linkicon' => ''
// 	        );
// 	    }
	    if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
	        $massActionLinks[] = array(
	            'linktype' => 'LISTVIEWMASSACTION',
	            'linklabel' => 'LBL_DELETE',
	            'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
	            'linkicon' => ''
	        );
	    }
	    
	    $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
	    if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('CreateView')) {
	        $massActionLinks[] = array(
	            'linktype' => 'LISTVIEWMASSACTION',
	            'linklabel' => 'LBL_ADD_COMMENT',
	            'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showAddCommentForm',
	            'linkicon' => ''
	        );
	    }
	    
	    foreach($massActionLinks as $massActionLink) {
	        $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
	    }
		$emailModuleModel = Vtiger_Module_Model::getInstance('Emails');

// 		if($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
// 			$massActionLink = array(
// 				'linktype' => 'LISTVIEWMASSACTION',
// 				'linklabel' => 'Create Invoice',
// 				'linkurl' => 'javascript:Timecontrol.createBill();',
// 				'linkicon' => ''
// 			);
// 			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		
// 			$massActionLink = array(
// 			    'linktype' => 'LISTVIEWMASSACTION',
// 			    'linklabel' => 'Add To Invoice',
// 			    'linkurl' => 'javascript:Timecontrol.SelectInvoice("Timecontrol","InvoicePopup")',
// 			    'linkicon' => ''
// 			);
// 			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
			
// 		}


		return $links;
	}
	
	public function getAdvancedLinks(){
	    $moduleModel = $this->getModule();
	    $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'CreateView');
	    $advancedLinks = array();
// 	    $importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
// 	    if($importPermission && $createPermission) {
// 	        $advancedLinks[] = array(
// 	            'linktype' => 'LISTVIEW',
// 	            'linklabel' => 'LBL_IMPORT',
// 	            'linkurl' => $moduleModel->getImportUrl(),
// 	            'linkicon' => ''
// 	        );
// 	    }
	    
// 	    $duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
// 	    $editPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
// 	    if($duplicatePermission && $editPermission) {
// 	        $advancedLinks[] = array(
// 	            'linktype' => 'LISTVIEWMASSACTION',
// 	            'linklabel' => 'LBL_FIND_DUPLICATES',
// 	            'linkurl' => 'Javascript:Vtiger_List_Js.showDuplicateSearchForm("index.php?module='.$moduleModel->getName().
// 	            '&view=MassActionAjax&mode=showDuplicatesSearchForm")',
// 	            'linkicon' => ''
// 	        );
// 	    }
	    
	    $exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
	    if($exportPermission) {
	        $advancedLinks[] = array(
	            'linktype' => 'LISTVIEW',
	            'linklabel' => 'LBL_EXPORT',
	            'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$this->getModule()->getExportUrl().'")',
	            'linkicon' => ''
	        );
	    }
	    
	    return $advancedLinks;
	}
	
	
}