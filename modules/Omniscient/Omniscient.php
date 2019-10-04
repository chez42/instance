<?php

include_once('vtlib/Vtiger/Event.php');

class Omniscient{
    function vtlib_handler($moduleName, $eventType){
        echo "<h1>Module installed</h1>";
        
        if($eventType == 'module.postinstall'){
            echo "Module postinstall";
        }
        
        if($eventType == "module.enabled"){
            $HelpInstance = Vtiger_Module::getInstance("HelpDesk");
            Vtiger_Event::register($HelpInstance, 'vtiger.entity.beforesave.modifiable', 'OmniscientHandler', 'modules/Omniscient/Omniscient.php');
            echo "Module Enabled";
        }
    }
}


?>