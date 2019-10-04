<?php

class ActivityActions{
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
            if($event_type == 'module.postinstall') {
                    // TODO Handle post installation actions
            } else if($event_type == 'module.disabled') {
                    // TODO Handle actions when this module is disabled.
            } else if($event_type == 'module.enabled') {
                    // TODO Handle actions when this module is enabled.
            } else if($event_type == 'module.preuninstall') {
                    // TODO Handle actions when this module is about to be deleted.
            } else if($event_type == 'module.preupdate') {
                    // TODO Handle actions before this module is updated.
            } else if($event_type == 'module.postupdate') {
                    // TODO Handle actions after this module is updated.
            }
    }

    static function isViewPermitted($linkData) {
            $moduleName = $linkData->getModule();
            $recordId = $linkData->getInputParameter('record');
            if(isPermitted($moduleName, 'DetailView', $recordId) == 'yes') {
                    return true;
            }
            return false;
    }
}

?>