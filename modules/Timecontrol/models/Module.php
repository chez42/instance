<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Timecontrol_Module_Model extends Vtiger_Module_Model {
    /**
   	 * Function to get Settings links
   	 * @return <Array>
   	 */
   	public function getSettingLinks(){
           $settingsLinks = parent::getSettingLinks();

//            $settingsLinks[] = array(
//                 'linktype' => 'LISTVIEWSETTING',
//                 'linklabel' => 'License Configuration',
//                  'linkurl' => 'index.php?parent=Settings&module=Timecontrol&view=LicenseManager'
//             );

//            $settingsLinks[] = array(
//                 'linktype' => 'LISTVIEWSETTING',
//                 'linklabel' => 'check Database',
//                  'linkurl' => 'index.php?parent=Settings&module=Timecontrol&view=CheckDB'
//             );
//            $settingsLinks[] = array(
//                 'linktype' => 'LISTVIEWSETTING',
//                 'linklabel' => 'check for Updates',
//                  'linkurl' => 'index.php?parent=Settings&module=Timecontrol&view=Upgrade'
//             );

       /* $settingsLinks[] = array(
                'linktype' => 'LISTVIEWSETTING',
                'linklabel' => 'old Records2Organizations',
                 'linkurl' => 'index.php?parent=Settings&module=Timecontrol&action=OldConnections'
            );*/

//            $settingsLinks[] = array(
//                 'linktype' => 'LISTVIEWSETTING',
//                 'linklabel' => 'Configuration',
//                  'linkurl' => 'index.php?parent=Settings&module=Timecontrol&view=Config'
//             );

//           $settingsLinks[] = array(
//                'linktype' => 'LISTVIEWSETTING',
//                'linklabel' => 'Settings',
//                 'linkurl' => 'index.php?parent=Settings&module=ProfitReport&view=Config'
//            );

           return $settingsLinks;
    }

    
	/**
	 * Function to get list view query for popup window
	 * @param <String> $sourceModule Parent module
	 * @param <String> $field parent fieldname
	 * @param <Integer> $record parent id
	 * @param <String> $listQuery
	 * @return <String> Listview Query
	 */
	public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
		
		if ($sourceModule === 'Invoice') {
			$condition = " vtiger_timecontrol.relatedto = '' ";

			$position = stripos($listQuery, 'where');
			if($position) {
				$split = spliti('where', $listQuery);
				$overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
			} else {
				$overRideQuery = $listQuery. ' WHERE ' . $condition;
			}
			return $overRideQuery;
		}
	}
	
	function isStarredEnabled(){
	    return false;
	}
	
	public function isSummaryViewSupported() {
	    return false;
	}
	
	public function getModuleBasicLinks(){
	    if(!$this->isEntityModule() && $this->getName() !== 'Users') {
	        return array();
	    }
	    $createPermission = Users_Privileges_Model::isPermitted($this->getName(), 'CreateView');
	    $moduleName = $this->getName();
	    $basicLinks = array();
	    if($createPermission) {
	        
            /*$basicLinks[] = array(
                'linktype' => 'BASIC',
                'linklabel' => 'LBL_ADD_RECORD',
                'linkurl' => $this->getCreateRecordUrl(),
                'linkicon' => 'fa-plus'
            );
            
	        $importPermission = Users_Privileges_Model::isPermitted($this->getName(), 'Import');
	        if($importPermission && $createPermission) {
	            $basicLinks[] = array(
	                'linktype' => 'BASIC',
	                'linklabel' => 'LBL_IMPORT',
	                'linkurl' => $this->getImportUrl(),
	                'linkicon' => 'fa-download'
	            );
	        }*/
	    }
	    return $basicLinks;
	}
	
	public function isExcelEditAllowed() {
	    return false;
	}
	
	public function isQuickCreateSupported() {
	    return false;
	}
	
}