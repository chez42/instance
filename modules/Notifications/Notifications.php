<?php
include_once "modules/Vtiger/CRMEntity.php";

class Notifications extends Vtiger_CRMEntity
{
    public $table_name = "vtiger_notifications";
    public $table_index = "notificationsid";
   
    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_notificationscf', 'notificationsid');
    
    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_notifications', 'vtiger_notificationscf');
    
    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_notifications' => 'notificationsid',
        'vtiger_notificationscf'=>'notificationsid');
    
    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Notification Number' => Array('notifications', 'notificationno'),
        'Assigned To' => Array('crmentity','smownerid')
        );
    var $list_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Notification Number' => 'notificationno',
        'Assigned To' => 'assigned_user_id',
        );
    
    // Make the field link to detail view
    var $list_link_field = 'notificationno';
    
    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Notification Number' => Array('notifications', 'notificationno'),
        'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
    var $search_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Notification Number' => 'notificationno',
        'Assigned To' => 'assigned_user_id',
        );
    
    // For Popup window record selection
    var $popup_fields = Array ('notificationno');
    
    // For Alphabetical search
    var $def_basicsearch_col = 'notificationno';
    
    // Column value to use on detail view record text display
    var $def_detailview_recname = 'notificationno';
    
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('notificationno','assigned_user_id');
    
    var $default_order_by = 'notificationno';
    var $default_sort_order='ASC';
    /**
     * Invoked when special actions are performed on the module.
     * @param String $modulename - Module name
     * @param String $event_type - Event Type
     */
    public function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == "module.postinstall") {
            self::addWidgetTo($modulename);
        } else {
            if ($event_type == "module.disabled") {
                self::removeWidgetTo($modulename);
            } else {
                if ($event_type == "module.enabled") {
                    self::addWidgetTo($modulename);
                } else {
                    if ($event_type == "module.preuninstall") {
                        self::removeWidgetTo($modulename);
                    } else {
                        if ($event_type == "module.preupdate") {
                            self::removeWidgetTo($modulename);
                        } else {
                            if ($event_type == "module.postupdate") {
                                self::addWidgetTo($modulename);
                            }
                        }
                    }
                }
            }
        }
    }
    
     
    public static function addWidgetTo($moduleName)
    {
        global $adb;
        global $vtiger_current_version;
        if (version_compare($vtiger_current_version, "7.0.0", "<")) {
            $temp_dir = "vlayout";
        } else {
            $temp_dir = "v7";
        }
        $css_widgetType = "HEADERCSS";
        $css_widgetName = "Notifications";
        $css_link = "layouts/" . $temp_dir . "/modules/" . $moduleName . "/resources/" . $moduleName . "CSS.css";
        $js_widgetType = "HEADERSCRIPT";
        $js_widgetName = "Notifications";
        $js_link = "layouts/" . $temp_dir . "/modules/" . $moduleName . "/resources/" . $moduleName . "JS.js";
        $module = Vtiger_Module::getInstance($moduleName);
        if ($module) {
            $module->addLink($css_widgetType, $css_widgetName, $css_link);
            $module->addLink($js_widgetType, $js_widgetName, $js_link);
        }
       
    }
     
    public static function removeWidgetTo($moduleName)
    {
        global $adb;
        $css_widgetType = "HEADERCSS";
        $css_widgetName = "Notifications";
        $css_link = "layouts/vlayout/modules/" . $moduleName . "/resources/" . $moduleName . "CSS.css";
        $css_link_v7 = "layouts/v7/modules/" . $moduleName . "/resources/" . $moduleName . "CSS.css";
        $js_widgetType = "HEADERSCRIPT";
        $js_widgetName = "Notifications";
        $js_link = "layouts/vlayout/modules/" . $moduleName . "/resources/" . $moduleName . "JS.js";
        $js_link_v7 = "layouts/vlayout/modules/" . $moduleName . "/resources/" . $moduleName . "JS.js";
        $module = Vtiger_Module::getInstance($moduleName);
        if ($module) {
            $module->deleteLink($css_widgetType, $css_widgetName, $css_link);
            $module->deleteLink($css_widgetType, $css_widgetName, $css_link_v7);
            $module->deleteLink($js_widgetType, $js_widgetName, $js_link);
            $module->deleteLink($js_widgetType, $js_widgetName, $js_link_v7);
        }
    }
   
}

?>