<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'vtlib/Vtiger/Module.php';
require_once('include/events/include.inc');

class MassDocumentUploader {

    const module = 'MassDocumentUploader';

    /**
     * Invoked when special actions are to be performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        
		$adb = PearDatabase::getInstance();
        
		$forModules = array();

		$doc_moduleInstance = Vtiger_Module::getInstance('Documents');
		
		$query = " SELECT vtiger_tab.tabid, vtiger_tab.name FROM `vtiger_tab` 
		INNER JOIN vtiger_relatedlists on vtiger_relatedlists.tabid = vtiger_tab.tabid 
		WHERE vtiger_relatedlists.related_tabid = '{$doc_moduleInstance->id}' 
		ORDER BY `vtiger_tab`.`tabid` ASC "; 
		$result = $adb->pquery($query, array()); 
		$totalRows = $adb->num_rows($result);

		for( $i=0; $i<$totalRows; $i++ ){
			$tabid = $adb->query_result($result, $i, 'tabid');
			$forModules[$tabid] = $adb->query_result($result, $i, 'name');
		}
        
		$widget_url = 'module=MassDocumentUploader&view=Upload';
		
        if ($eventType == 'module.postinstall') {
            $this->addWidgetforUploadAndSync( $forModules, 'DETAILVIEWSIDEBARWIDGET', 'Upload Documents', $widget_url);
        
		} else if ($eventType == 'module.disabled') {
            $this->removeWidgetforUploadAndSync( $forModules, 'DETAILVIEWSIDEBARWIDGET', 'Upload Documents', $widget_url);
        
		} else if ($eventType == 'module.enabled') {
            $this->addWidgetforUploadAndSync( $forModules, 'DETAILVIEWSIDEBARWIDGET', 'Upload Documents', $widget_url);
        
		} else if ($eventType == 'module.preuninstall') {
            $this->removeWidgetforUploadAndSync( $forModules, 'DETAILVIEWSIDEBARWIDGET', 'Upload Documents', $widget_url);
        
		} else if ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        
		} else if ($eventType == 'module.postupdate') {
            
        }
    }

    /**
     * Add widget to other module.
     * @param Array $moduleNames
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function addWidgetforUploadAndSync($moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Upload Documents', $widgetUrl = '') {
        if (empty($moduleNames))
            return;

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->addLink($widgetType, $widgetName, $widgetUrl, '', '', '');
            }
        }
    }

    /**
     * Remove widget from other modules.
     * @param Array $moduleNames
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function removeWidgetforUploadAndSync($moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Upload Documents', $widgetUrl = false) {
        if (empty($moduleNames))
            return;

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
            	//$widgetUrl = false;
                $module->deleteLink($widgetType, $widgetName, $widgetUrl);
            }
        }
    }

}

?>
