<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "data/CRMEntity.php";
require_once "data/Tracker.php";
require_once "vtlib/Vtiger/Module.php";
class DragDropDocuments extends CRMEntity
{
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    public function vtlib_handler($modulename, $event_type)
    {
        global $adb;
        if ($event_type == "module.postinstall") {
            self::addHeadScript();
            
        } else {
            if ($event_type == "module.disabled") {
                self::removeHeadScript();
            } else {
                if ($event_type == "module.enabled") {
                    self::addHeadScript();
                } else {
                    if ($event_type == "module.preuninstall") {
                        self::removeHeadScript();
                       
                    } else {
                        if ($event_type == "module.preupdate") {
                            self::addHeadScript();
                        } else {
                            if ($event_type == "module.postupdate") {
                                self::addHeadScript();
                                
                            }
                        }
                    }
                }
            }
        }
    }
    public static function addHeadScript()
    {
        global $adb;
        global $vtiger_current_version;
        $widgetType = "HEADERSCRIPT";
        $widgetName = "DragDropDocuments";
        if (version_compare($vtiger_current_version, "7.0.0", "<")) {
            $template_folder = "layouts/vlayout";
        } else {
            $template_folder = "layouts/v7";
        }
        $link = $template_folder . "/modules/DragDropDocuments/resources/DragDropDocuments.js";
        $module = Vtiger_Module::getInstance("DragDropDocuments");
        $checkres = $adb->pquery("SELECT linkid FROM vtiger_links WHERE linkurl=?", array($link));
        if ($adb->num_rows($checkres) == 0) {
            $module->addLink($widgetType, $widgetName, $link);
        }
    }
    public static function removeHeadScript()
    {
        global $adb;
        global $vtiger_current_version;
        $widgetType = "HEADERSCRIPT";
        $widgetName = "DragDropDocuments";
        if (version_compare($vtiger_current_version, "7.0.0", "<")) {
            $template_folder = "layouts/vlayout";
        } else {
            $template_folder = "layouts/v7";
        }
        $link = $template_folder . "/modules/DragDropDocuments/resources/DragDropDocuments.js";
        $module = Vtiger_Module::getInstance("DragDropDocuments");
        $checkres = $adb->pquery("SELECT linkid FROM vtiger_links WHERE linkurl=?", array($link));
        if (0 < $adb->num_rows($checkres)) {
            $module->deleteLink($widgetType, $widgetName, $link);
        }
    }
    
}

?>