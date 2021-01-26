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
    var $tab_name = Array(/*'vtiger_crmentity', */'vtiger_notifications', 'vtiger_notificationscf');
    
    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        //'vtiger_crmentity' => 'crmid',
        'vtiger_notifications' => 'notificationsid',
        'vtiger_notificationscf'=>'notificationsid');
    
    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Notification Number' => Array('notifications', 'notificationno'),
        'Assigned To' => Array('notifications','smownerid')
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
        'Assigned To' => Array('notifications','assigned_user_id'),
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
    
    function initialize() {
        $moduleName = $this->moduleName;
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if($moduleModel && !$moduleModel->isEntityModule()) {
            return;
        }
        
        $userSpecificTableIgnoredModules = array('SMSNotifier', 'PBXManager', 'ModComments', $moduleName);
        if(in_array($moduleName, $userSpecificTableIgnoredModules)) return;
        
        $userSpecificTable = Vtiger_Functions::getUserSpecificTableName($moduleName);
        if(!in_array($userSpecificTable, $this->tab_name)) {
            $this->tab_name[] = $userSpecificTable;
            $this->tab_name_index [$userSpecificTable] = 'recordid';
        }
    }
    
    function insertIntoEntityTable($table_name, $module, $fileid = '') {
        
        global $adb;
        
        $date_var = date("Y-m-d H:i:s");
        
        $created_date_var = $adb->formatDate($date_var, true);
        
        $modified_date_var = $adb->formatDate($date_var, true);
        
        $insertion_mode = $this->mode;
        
        if ($insertion_mode != 'edit' && $table_name == 'vtiger_notifications') {
            
            $this->id = $adb->getUniqueID("vtiger_notifications");
            
            $this->column_fields['createdtime'] = $created_date_var;
            
        }
        
        $this->column_fields['modifiedtime'] = $modified_date_var;
        
        parent::insertIntoEntityTable($table_name, $module, $fileid);
        
        $sql = "update vtiger_notifications set ";
        
        $params = array();
        
        if ($insertion_mode != 'edit'){
            
            $sql .= " createdtime=?, ";
            
            $params[] = $created_date_var;
            
        }
        
        $sql .=" modifiedtime=?, source=? where notificationsid=?";
        
        $params[] = $modified_date_var;
        
        $params[] = strtoupper($this->column_fields['source']);
        
        $params[] = $this->id;
        
        $adb->pquery($sql,$params);
        
    }
    
    function retrieve_entity_info($record, $module, $allowDeleted = false) {
        global $adb, $log, $app_strings, $current_user;
        
        // INNER JOIN is desirable if all dependent table has entries for the record.
        // LEFT JOIN is desired if the dependent tables does not have entry.
        $join_type = 'LEFT JOIN';
        
        // Tables which has multiple rows for the same record
        // will be skipped in record retrieve - need to be taken care separately.
        $multirow_tables = NULL;
        if (isset($this->multirow_tables)) {
            $multirow_tables = $this->multirow_tables;
        } else {
            $multirow_tables = array(
                'vtiger_campaignrelstatus',
                'vtiger_attachments',
                //'vtiger_inventoryproductrel',
                //'vtiger_cntactivityrel',
                'vtiger_email_track'
            );
        }
        
        // Lookup module field cache
        if($module == 'Calendar' || $module == 'Events') {
            getColumnFields('Calendar');
            $cachedEventsFields = VTCacheUtils::lookupFieldInfo_Module('Events');
            $cachedCalendarFields = VTCacheUtils::lookupFieldInfo_Module('Calendar');
            $cachedModuleFields = array_merge($cachedEventsFields, $cachedCalendarFields);
        } else {
            $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
        }
        if ($cachedModuleFields === false) {
            // Pull fields and cache for further use
            $tabid = getTabid($module);
            
            $sql0 = "SELECT fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata,presence FROM vtiger_field WHERE tabid=?";
            // NOTE: Need to skip in-active fields which we will be done later.
            $result0 = $adb->pquery($sql0, array($tabid));
            if ($adb->num_rows($result0)) {
                while ($resultrow = $adb->fetch_array($result0)) {
                    // Update cache
                    VTCacheUtils::updateFieldInfo(
                        $tabid, $resultrow['fieldname'], $resultrow['fieldid'], $resultrow['fieldlabel'], $resultrow['columnname'], $resultrow['tablename'], $resultrow['uitype'], $resultrow['typeofdata'], $resultrow['presence']
                        );
                }
                // Get only active field information
                $cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
            }
        }
        
        if ($cachedModuleFields) {
            $column_clause = '';
            $from_clause   = '';
            $where_clause  = '';
            $limit_clause  = ' LIMIT 1'; // to eliminate multi-records due to table joins.
            
            $params = array();
            $required_tables = $this->tab_name_index; // copies-on-write
            
            foreach ($cachedModuleFields as $fieldinfo) {
                if (in_array($fieldinfo['tablename'], $multirow_tables)) {
                    continue;
                }
                // Added to avoid picking shipping tax fields for Inventory modules, the shipping tax detail are stored in vtiger_inventoryshippingrel
                // table, but in vtiger_field table we have set tablename as vtiger_inventoryproductrel.
                if(($module == 'Invoice' || $module == 'Quotes' || $module == 'SalesOrder' || $module == 'PurchaseOrder')
                    && stripos($fieldinfo['columnname'], 'shtax') !== false) {
                        continue;
                    }
                    
                    // Alias prefixed with tablename+fieldname to avoid duplicate column name across tables
                    // fieldname are always assumed to be unique for a module
                    $column_clause .=  $fieldinfo['tablename'].'.'.$fieldinfo['columnname'].' AS '.$this->createColumnAliasForField($fieldinfo).',';
            }
            $column_clause .= 'vtiger_notifications.deleted, vtiger_notifications.label';
            
            if (isset($required_tables['vtiger_notifications'])) {
                $from_clause  = ' vtiger_notifications';
                unset($required_tables['vtiger_notifications']);
                foreach ($required_tables as $tablename => $tableindex) {
                    if (in_array($tablename, $multirow_tables)) {
                        // Avoid multirow table joins.
                        continue;
                    }
                    $joinCondition = "($tablename.$tableindex = vtiger_notifications.notificationsid ";
                    if($current_user && Vtiger_Functions::isUserSpecificFieldTable($tablename, $module)) {
                        $joinCondition .= " AND $tablename.userid = ".$current_user->id;
                    }
                    $joinCondition .= " )";
                    $from_clause .= sprintf(' %s %s ON %s', $join_type,
                        $tablename, $joinCondition);
                }
            }
            
            $where_clause .= ' vtiger_notifications.notificationsid=?';
            $params[] = $record;
            
            $sql = sprintf('SELECT %s FROM %s WHERE %s %s', $column_clause, $from_clause, $where_clause, $limit_clause);
            
            $result = $adb->pquery($sql, $params);
            // initialize the object
            $this->column_fields = new TrackableObject();
            
            if (!$result || $adb->num_rows($result) < 1) {
                throw new Exception($app_strings['LBL_RECORD_NOT_FOUND'], -1);
            } else {
                $resultrow = $adb->query_result_rowdata($result);
                if (!$allowDeleted) {
                    if (!empty($resultrow['deleted'])) {
                        throw new Exception($app_strings['LBL_RECORD_DELETE'], 1);
                    }
                }
                if(!empty($resultrow['label'])){
                    $this->column_fields['label'] = $resultrow['label'];
                } else {
                    // added to compute label needed in event handlers
                    $entityFields = Vtiger_Functions::getEntityModuleInfo($module);
                    if(!empty($entityFields['fieldname'])) {
                        $entityFieldNames  = explode(',', $entityFields['fieldname']);
                        if(count($entityFieldNames) > 1) {
                            $this->column_fields['label'] = $resultrow[$entityFields['tablename'].$entityFieldNames[0]].' '.$resultrow[$entityFields['tablename'].$entityFieldNames[1]];
                        } else {
                            $this->column_fields['label'] = $resultrow[$entityFields['tablename'].$entityFieldNames[0]];
                        }
                    }
                }
                foreach ($cachedModuleFields as $fieldinfo) {
                    $fieldvalue = '';
                    $fieldkey = $this->createColumnAliasForField($fieldinfo);
                    //Note : value is retrieved with a tablename+fieldname as we are using alias while building query
                    if (isset($resultrow[$fieldkey])) {
                        $fieldvalue = $resultrow[$fieldkey];
                    }
                    $this->column_fields[$fieldinfo['fieldname']] = $fieldvalue;
                }
            }
        }
        
        $this->column_fields['record_id'] = $record;
        $this->column_fields['record_module'] = $module;
        $this->column_fields->startTracking();
    }
    
    function mark_deleted($id) {
        global $current_user;
        $date_var = date("Y-m-d H:i:s");
        $query = "UPDATE vtiger_notifications set deleted=1,modifiedtime=? where notificationsid=?";
        $this->db->pquery($query, array($this->db->formatDate($date_var, true), $id), true, "Error marking record deleted: ");
    }
    
    /** Function to delete an entity with given Id */
    function trash($module, $id) {
        global $log, $current_user, $adb;
        
        $this->mark_deleted($id);
        $this->unlinkDependencies($module, $id);
        
        require_once('libraries/freetag/freetag.class.php');
        $freetag = new freetag();
        $freetag->delete_all_object_tags_for_user($current_user->id, $id);
        
        $sql_recentviewed = 'DELETE FROM vtiger_tracker WHERE user_id = ? AND item_id = ?';
        $this->db->pquery($sql_recentviewed, array($current_user->id, $id));
        
    }
   
}

?>