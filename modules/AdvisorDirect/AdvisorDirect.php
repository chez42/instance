<?php
include_once('vtlib/Vtiger/Event.php');

class AdvisorDirect{
    const module = 'AdvisorDirect';
    
    function vtlib_handler($moduleName, $eventType) {
        $adb = PearDatabase::getInstance();
        $forModules = array('Documents');
        $syncModules = array('Documents' => 'Advisor Direct');
        
        if ($eventType == 'module.enabled') {
            $this->addDirectWidget($forModules);
//            $this->addWidgetforSync($syncModules);
        }
    }
    
    /**
     * Add widget to other module.
     * @param Array $moduleNames
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function addDirectWidget($moduleNames, $widgetType = 'DETAILVIEWSIDEBARWIDGET', $widgetName = 'Advisor Direct') {
        if (empty($moduleNames))
            return;

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        foreach ($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->addLink($widgetType, $widgetName, 'module=AdvisorDirect&view=List', '', '', '');
            }
        }
    }    
    /**
     * Add widget to other module
     * @param String $widgetType
     * @param String $widgetName
     * @return
     */
    function addWidgetforSync($moduleNames, $widgetType = 'LISTVIEWSIDEBARWIDGET') {
        if (empty($moduleNames))
            return;

        if (is_string($moduleNames))
            $moduleNames = array($moduleNames);

        foreach ($moduleNames as $moduleName => $widgetName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if ($module) {
                $module->addLink($widgetType, $widgetName, "module=AdvisorDirect&view=List&sourcemodule=$moduleName", '', '', '');
            }
        }
    }    
}

?>