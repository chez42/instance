<?php

class Group_Module_Model extends Vtiger_Module_Model
{
    
    /**
     * Function to check whether the module is an entity type module or not
     * @return <Boolean> true/false
     */
    public function isQuickCreateSupported() {
        return false;
    }
    
}