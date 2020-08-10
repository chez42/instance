<?php
require_once 'vtlib/Vtiger/Module.php';
require_once('include/events/include.inc');
class Office365 {
    
    const module = 'Office365';
    var $LBL_OFFICE = 'LBL_Office365';
    
    /**
     * Invoked when special actions are to be performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        $adb = PearDatabase::getInstance();
        $syncModules = array(/*'Contacts' => 'Office365 Contacts', */'Calendar' => 'Office365 Calendar'/*, 'Task' => 'Office365'*/);
        
        if ($eventType == 'module.postinstall') {
        
            $this->addLinks($syncModules);
			$this->createOffice365Tables();
            
        } else if ($eventType == 'module.disabled') {
            
            $this->removeLinks($syncModules);
            
        } else if ($eventType == 'module.enabled') {
           
            $this->addLinks($syncModules);
			$this->createOffice365Tables();
        
        } else if ($eventType == 'module.preuninstall') {
        
            $this->removeLinks($syncModules);
        
        } else if ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if ($eventType == 'module.postupdate') {
            
        }
    }
    
    /**
     * Add widget to other module
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function addLinks($moduleNames, $widgetType = 'EXTENSIONLINK') {
        
        if (empty($moduleNames))
            return;
            
		if (is_string($moduleNames))
			$moduleNames = array($moduleNames);
			
		foreach ($moduleNames as $moduleName => $widgetName) {
			$module = Vtiger_Module::getInstance($moduleName);
			if ($module) {
				$linkURL = 'index.php?module='.$moduleName.'&view=Extension&extensionModule=Office365&extensionView=Index';
				$module->addLink($widgetType, self::module, $linkURL, '', '', '');
			}
		}
    }
    
    /**
     * Remove widget from other modules.
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function removeLinks($moduleNames, $widgetType = 'EXTENSIONLINK') {
        if (empty($moduleNames))
            return;
		
		if (is_string($moduleNames))
			$moduleNames = array($moduleNames);
			
		foreach ($moduleNames as $moduleName => $widgetName) {
			$module = Vtiger_Module::getInstance($moduleName);
			if ($module) {
				$module->deleteLink($widgetType, 'Office365');
			}
		}
    }
	
	function createOffice365Tables (){
		
		global $adb;
		
		$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_office365_sync_settings ( 
			id INT(19) NOT NULL AUTO_INCREMENT , 
			user INT(19) NULL , 
			module VARCHAR(250) NULL , 
			direction VARCHAR(250) NULL , 
			sync_start_from DATE NULL , 
			enable_cron INT(19) NULL , 
			access_token TEXT NULL , 
			refresh_token TEXT NULL ,
            delta_token TEXT NULL 
			PRIMARY KEY (id));");

		$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_office365_sync ( 
			office365module VARCHAR(250) NULL , 
			user INT(19) NULL , 
			synctime DATETIME NULL , 
			lastsynctime DATETIME NULL , 
			vtigersynctime VARCHAR(255) NULL);");

		$adb->pquery("CREATE TABLE vtiger_office365_recordmapping ( 
			id BIGINT(20) NOT NULL AUTO_INCREMENT , 
			sync_id INT(19) NULL ,
			serverid VARCHAR(250) NULL , 
			officeid LONGTEXT NULL , 
			servermodifiedtime DATETIME NULL , 
			office365modifiedtime DATETIME NULL , 
			parent_exchangeid LONGTEXT NULL ,
			PRIMARY KEY (id));");

		$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_office365_fieldmapping ( 
			id INT(19) NOT NULL AUTO_INCREMENT , 
			userid VARCHAR(255) NULL , 
			module VARCHAR(255) NULL , 
			field_mapping LONGTEXT NULL , 
			PRIMARY KEY (id));");

	}
	
}

?>
