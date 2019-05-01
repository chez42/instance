<?php
require_once 'vtlib/Vtiger/Module.php';
require_once('include/events/include.inc');
class MSExchange {

    const module = 'MSExchange';
	var $LBL_OFFICE = 'LBL_MSExchange';

    /**
     * Invoked when special actions are to be performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        $adb = PearDatabase::getInstance();
        $syncModules = array('Contacts' => 'MSExchange Contacts', 'Calendar' => 'MSExchange Calendar');
        if ($eventType == 'module.postinstall') {
            $linkName =  'MSExchange';
            $linkurl = 'index.php?module=MSExchange&parent=Settings&view=Extension&extensionModule=MSExchange&extensionView=Index&mode=GlobalSettings';
            vtlib_addSettingsLink($linkName, $linkurl, "LBL_EXTENSIONS");
            $result = $adb->pquery('SELECT 1 FROM vtiger_settings_field WHERE name=? or linkto = ?', array($linkName, $linkurl));
            if ($result && !$adb->num_rows($result)) {
                $blockId = getSettingsBlockId("LBL_EXTENSIONS");
                $fieldSeqResult = $adb->pquery('SELECT MAX(sequence) AS sequence FROM vtiger_settings_field WHERE blockid=?', array($blockId));
                if ($db->num_rows($fieldSeqResult)) {
                    $fieldId = $db->getUniqueID('vtiger_settings_field');
                    $fieldSequence = $adb->query_result($fieldSeqResult, 0, 'sequence');
                    $adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active, pinned) VALUES(?,?,?,?,?,?,?,?,?)', array($fieldId, $blockId, $linkName, '', $linkName, $linkurl, $fieldSequence++, 0, 0));
                }
            }else {
                $adb->pquery("update vtiger_settings_field set name = ?, description = ? where linkto = ?",array($linkName,$linkName,$linkurl));
            }
            $this->addLinks($syncModules);
        } else if ($eventType == 'module.disabled') {
            $adb->pquery('UPDATE vtiger_settings_field SET active=1 WHERE name=?',array('MSExchange'));
            $this->removeLinks($syncModules);
		} else if ($eventType == 'module.enabled') {
		    $adb->pquery('UPDATE vtiger_settings_field SET active=0 WHERE name=?',array('MSExchange'));
		    $this->addLinks($syncModules);
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
                $linkURL = 'index.php?module='.$moduleName.'&view=Extension&extensionModule=MSExchange&extensionView=Index';
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
                $module->deleteLink($widgetType, 'MSExchange');
            }
        }
    }   
}

?>
