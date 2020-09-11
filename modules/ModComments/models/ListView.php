<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * ModComments ListView Model Class
 */
class ModComments_ListView_Model extends Vtiger_ListView_Model {

	public function getBasicLinks(){
		$basicLinks = array();
		
		return $basicLinks;
	}
	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$links = parent::getListViewLinks($linkParams);
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = $this->getModule();

		//unset($links['LISTVIEW']);
		unset($links['LISTVIEWSETTING']);

		if($currentUserModel->isAdminUser()) {
			$settingsLink = array(
					'linktype' => 'LISTVIEWSETTING',
					'linklabel' => 'LBL_EDIT_WORKFLOWS',
					'linkurl' => 'index.php?parent=Settings&module=Workflow&sourceModule='.$moduleModel->getName(),
					'linkicon' => Vtiger_Theme::getImagePath('EditWorkflows.png')
			);
			$links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
		}

		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - empty array
	 */
	public function getListViewMassActions($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWMASSACTION');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);


		if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLinks[] = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
				'linkicon' => ''
			);
		}

		foreach($massActionLinks as $massActionLink) {
			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		return $links;
	}


	public function getListViewEntries($pagingModel) {
		$relatedToArray = array();
		$data = array();
		$db = PearDatabase::getInstance();
		$listViewRecordModels = parent::getListViewEntries($pagingModel);

		/*foreach($listViewRecordModels as $listViewRecordModel) {
			$rawData = $listViewRecordModel->getRawData();
			$relatedToArray[] = $rawData['related_to'];
		}

		if(!empty($relatedToArray)) {
			$result = $db->pquery("SELECT crmid, deleted, setype FROM vtiger_crmentity WHERE crmid IN (".generateQuestionMarks($relatedToArray).")", $relatedToArray);
			$count = $db->num_rows($result);
			for($i = 0; $i < $count; $i++) {
				$row = $db->query_result_rowdata($result, $i);
				$data[$row['crmid']] = array('deleted' => $row['deleted'], 'setype' => $row['setype']);
			}
		}

		foreach($listViewRecordModels as $key => $listViewRecordModel) {
			$rawData = $listViewRecordModel->getRawData();
			$relatedTo = $rawData['related_to'];
			if($data[$relatedTo]['deleted'] == '0') {
				$relatedToModel = Vtiger_Record_Model::getCleanInstance($data[$relatedTo]['setype'])->setId($relatedTo);
				$listViewRecordModel->set('related_to_model', $relatedToModel);
			} else {
				unset($listViewRecordModels[$key]);
			}
		}*/

		return $listViewRecordModels;
	}
	
	public function getName() {
        return 'ModComments';
    }
    
    public function getAdvancedLinks(){
        $moduleModel = $this->getModule();
        $advancedLinks = array();
        
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
