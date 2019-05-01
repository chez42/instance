<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

include_once "modules/Vtiger/CRMEntity.php";
class VTEEmailMarketing extends Vtiger_CRMEntity
{
    public $table_name = "vtiger_vteemailmarketing";
    public $table_index = "vteemailmarketingid";
    public $related_tables = array("vtiger_vteemailmarketingcf" => array("vteemailmarketingid", "vtiger_vteemailmarketing", "vteemailmarketingid"));
    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array("vtiger_vteemailmarketingcf", "vteemailmarketingid");
    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array("vtiger_crmentity", "vtiger_vteemailmarketing", "vtiger_vteemailmarketingcf");
    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array("vtiger_crmentity" => "crmid", "vtiger_vteemailmarketing" => "vteemailmarketingid", "vtiger_vteemailmarketingcf" => "vteemailmarketingid");
    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array("Name" => array("vteemailmarketing", "vtecampaigns"), "Assigned To" => array("crmentity", "smownerid"));
    public $list_fields_name = array("Name" => "vtecampaigns", "Assigned To" => "assigned_user_id");
    public $list_link_field = "vtecampaigns";
    public $search_fields = array("Name" => array("vteemailmarketing", "vtecampaigns"), "Assigned To" => array("vtiger_crmentity", "assigned_user_id"));
    public $search_fields_name = array("Name" => "vtecampaigns", "Assigned To" => "assigned_user_id");
    public $popup_fields = array("vtecampaigns");
    public $def_basicsearch_col = "vtecampaigns";
    public $def_detailview_recname = "vtecampaigns";
    public $mandatory_fields = array("name", "assigned_user_id");
    public $default_order_by = "name";
    public $default_sort_order = "ASC";
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == "module.postinstall") {
            $this->init($moduleName);
            $this->createHandle($moduleName);
            $this->createTableCheckLisence();
            $this->RegisterScheduler();
            $this->AddTableWs_Entity();
            $this->addHeaderCss();
            self::checkEnable();
            self::resetValid();
            $this->moveMosaico();
            $this->updateUnsetCustomModel();
        } else {
            if ($eventType == "module.disabled") {
                $this->removeHandle($moduleName);
                $this->removeScheduler();
            } else {
                if ($eventType == "module.enabled") {
                    $this->createHandle($moduleName);
                    $this->RegisterScheduler();
                } else {
                    if ($eventType == "module.preuninstall") {
                        $this->removeHandle($moduleName);
                        $this->removeScheduler();
                        $this->removeHeaderCss();
                        self::removeValid();
                    } else {
                        if ($eventType == "module.preupdate") {
                            $this->createHandle($moduleName);
                            $this->removeScheduler();
                            $this->moveMosaico();
                        } else {
                            if ($eventType == "module.postupdate") {
                                $this->RegisterScheduler();
                                self::checkEnable();
                                self::resetValid();
                                $this->updateUnsetCustomModel();
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * When install module
     * @param $moduleName
     */
    public function init($moduleName)
    {
        global $adb;
        $module = Vtiger_Module::getInstance($moduleName);
        $activityFieldTypeId = 34;
        $this->addModuleRelatedToForEvents($module->name, $activityFieldTypeId);
        require_once "modules/ModTracker/ModTracker.php";
        ModTracker::enableTrackingForModule($module->id);
        $commentInstance = Vtiger_Module::getInstance("ModComments");
        $commentRelatedToFieldInstance = Vtiger_Field::getInstance("related_to", $commentInstance);
        $commentRelatedToFieldInstance->setRelatedModules(array($module->name));
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $moduleContacts = Vtiger_Module::getInstance("Contacts");
        $moduleContacts->setRelatedList($moduleInstance, "VTEEmailMarketing", "", "get_related_list");
        $moduleLeads = Vtiger_Module::getInstance("Leads");
        $moduleLeads->setRelatedList($moduleInstance, "VTEEmailMarketing", "", "get_related_list");
        $moduleAccounts = Vtiger_Module::getInstance("Accounts");
        $moduleAccounts->setRelatedList($moduleInstance, "VTEEmailMarketing", "", "get_related_list");
        $moduleRelatedId = array($moduleContacts->id, $moduleLeads->id, $moduleAccounts->id);
        for ($i = 0; $i < count($moduleRelatedId); $i++) {
            $adb->pquery("UPDATE vtiger_relatedlists SET actions = \"\" WHERE tabid = ? && related_tabid =?", array($moduleInstance->id, $moduleRelatedId[$i]));
        }
        $prefix = "NO";
        if (2 <= strlen($module->name)) {
            $prefix = substr($module->name, 0, 2);
            $prefix = strtoupper($prefix);
        }
        $this->customizeRecordNumbering($module->name, $prefix, 1);
        $tabid = getTabid($moduleName);
        $result = $adb->pquery("SELECT * FROM vtiger_app2tab WHERE tabid = ?", array($tabid));
        if ($adb->num_rows($result) == 0) {
            $adb->pquery("INSERT INTO `vtiger_app2tab` (`tabid`, `appname`, `sequence`) VALUES (?, 'MARKETING', '1')", array($tabid));
        }
        $adb->pquery("UPDATE vtiger_relatedlists SET actions = ? WHERE tabid = ?", array("", $tabid));
    }
    public static function resetValid()
    {
        global $adb;
//         $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array("VTEEmailMarketing"));
//         $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array("VTEEmailMarketing", "0"));
    }
    public static function removeValid()
    {
        global $adb;
//         $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array("VTEEmailMarketing"));
    }
    public static function checkEnable()
    {
        global $adb;
        $rs = $adb->pquery("SELECT `enable` FROM `vteemailmarketing_settings`;", array());
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `vteemailmarketing_settings` (`enable`) VALUES ('1');", array());
        }
    }
    /**
     * @param string $moduleName
     * @param int $fieldTypeId
     */
    public function addModuleRelatedToForEvents($moduleName, $fieldTypeId)
    {
        global $adb;
        $sqlCheckProject = "SELECT * FROM `vtiger_ws_referencetype` WHERE fieldtypeid = ? AND type = ?";
        $rsCheckProject = $adb->pquery($sqlCheckProject, array($fieldTypeId, $moduleName));
        if ($adb->num_rows($rsCheckProject) < 1) {
            $adb->pquery("INSERT INTO `vtiger_ws_referencetype` (`fieldtypeid`, `type`) VALUES (?, ?)", array($fieldTypeId, $moduleName));
        }
    }
    /**
     * @param string $sourceModule
     * @param string $prefix
     * @param int $sequenceNumber
     * @return array
     */
    public function customizeRecordNumbering($sourceModule, $prefix = "NO", $sequenceNumber = 1)
    {
        $moduleModel = Settings_Vtiger_CustomRecordNumberingModule_Model::getInstance($sourceModule);
        $moduleModel->set("prefix", $prefix);
        $moduleModel->set("sequenceNumber", $sequenceNumber);
        $result = $moduleModel->setModuleSequence();
        return $result;
    }
    private function createHandle($moduleName)
    {
        include_once "include/events/VTEventsManager.inc";
        global $adb;
        $em = new VTEventsManager($adb);
        $em->setModuleForHandler($moduleName, (string) $moduleName . "Handler.php");
        $em->registerHandler("vtiger.entity.aftersave", "modules/" . $moduleName . "/" . $moduleName . "Handler.php", (string) $moduleName . "Handler");
    }
    /**
     * @param string $moduleName
     */
    private function removeHandle($moduleName)
    {
        include_once "include/events/VTEventsManager.inc";
        global $adb;
        $em = new VTEventsManager($adb);
        $em->unregisterHandler((string) $moduleName . "Handler");
    }
    public function createTableCheckLisence()
    {
        global $adb;
        $adb->pquery("Create table if not EXISTS `vteemailmarketing_settings`(`enable`  int(3) NULL DEFAULT NULL )");
        $adb->pquery("CREATE TABLE IF NOT EXISTS `vte_modules` (\r\n                `module` VARCHAR (50) NOT NULL,\r\n                `valid` INT (1) NULL,\r\n                PRIMARY KEY (`module`)\r\n            )");
    }
    public function RegisterScheduler()
    {
        include_once "vtlib/Vtiger/Cron.php";
        Vtiger_Cron::register("Email Marketing (Email Sending Engine)", "modules/VTEEmailMarketing/cron/EmailMarketingSchedule.service", 900, "VTEEmailMarketing", 1, 10, "Recommended frequency for Email Marketing is 15 mins");
        Vtiger_Cron::register("Reset Email Marketing Scheduler", "modules/VTEEmailMarketing/cron/ResetEmailMarketingSchedule.service", 3600, "VTEEmailMarketing", 1, 11, "Recommended frequency for Reset Email Marketing is 1 hours");
        Vtiger_Cron::register("Archive Sent Emails (Email Marketing)", "modules/VTEEmailMarketing/cron/ArchiveSentEmails.service", 86400, "VTEEmailMarketing", 1, 12, "Recommended frequency for Archive Sent Emails 1 days");
        return true;
    }
    public function removeScheduler()
    {
        $adb = PearDatabase::getInstance();
        $adb->pquery("DELETE FROM `vtiger_cron_task` WHERE `name` = 'Schedule Email Marketing';", array());
        $adb->pquery("DELETE FROM `vtiger_cron_task` WHERE `name` = 'Reset Email Marketing Scheduler';", array());
        $adb->pquery("DELETE FROM `vtiger_cron_task` WHERE `name` = 'Archive Sent Emails (Email Marketing)';", array());
        return true;
    }
    public function get_emails($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $log;
        global $singlepane_view;
        global $currentModule;
        global $current_user;
        $log->debug("Entering get_emails(" . $id . ") method ...");
        $this_module = $currentModule;
        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once "modules/" . $related_module . "/" . $related_module . ".php";
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);
        $parenttab = getParentTab();
        if ($singlepane_view == "true") {
            $returnset = "&return_module=" . $this_module . "&return_action=DetailView&return_id=" . $id;
        } else {
            $returnset = "&return_module=" . $this_module . "&return_action=CallRelatedList&return_id=" . $id;
        }
        $button = "";
        $button .= "<input type=\"hidden\" name=\"email_directing_module\"><input type=\"hidden\" name=\"record\">";
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(",", strtoupper($actions));
            }
            if (in_array("SELECT", $actions) && isPermitted($related_module, 4, "") == "yes") {
                $button .= "<input title='" . getTranslatedString("LBL_SELECT") . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=" . $related_module . "&return_module=" . $currentModule . "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=" . $id . "&parenttab=" . $parenttab . "','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString("LBL_SELECT") . " " . getTranslatedString($related_module) . "'>&nbsp;";
            }
            if (in_array("ADD", $actions) && isPermitted($related_module, 1, "") == "yes") {
                $button .= "<input title='" . getTranslatedString("LBL_ADD_NEW") . " " . getTranslatedString($singular_modname) . "' accessyKey='F' class='crmbutton small create' onclick='fnvshobj(this,\"sendmail_cont\");sendmail(\"" . $this_module . "\"," . $id . ");' type='button' name='button' value='" . getTranslatedString("LBL_ADD_NEW") . " " . getTranslatedString($singular_modname) . "'></td>";
            }
        }
        $query = "select vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.semodule, vtiger_activity.activitytype," . " vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.status, vtiger_activity.priority, vtiger_crmentity.crmid," . " vtiger_crmentity.smownerid,vtiger_crmentity.modifiedtime, vtiger_users.user_name, vtiger_crmentityrel.crmid as parent_id " . " from vtiger_activity" . " inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid" . " inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid" . " left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid" . " left join vtiger_users on  vtiger_users.id=vtiger_crmentity.smownerid" . " left join vtiger_crmentityrel on vtiger_activity.activityid = vtiger_crmentityrel.relcrmid" . " where vtiger_activity.activitytype='Emails' and vtiger_crmentity.deleted=0 and vtiger_seactivityrel.crmid =" . $id . " and vtiger_crmentityrel.crmid != " . $id;
        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
        if ($return_value == NULL) {
            $return_value = array();
        }
        $return_value["CUSTOM_BUTTON"] = $button;
        $log->debug("Exiting get_emails method ...");
        return $return_value;
    }
    public function get_relatedlist_vteemailmarketing($id, $cur_tab_id, $rel_tab_id, $actions = false)
    {
        global $currentModule;
        global $app_strings;
        global $singlepane_view;
        $parenttab = getParentTab();
        $related_module = vtlib_getModuleNameById($rel_tab_id);
        $other = CRMEntity::getInstance($related_module);
        vtlib_setup_modulevars($currentModule, $this);
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = "SINGLE_" . $related_module;
        $button = "";
        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(",", strtoupper($actions));
            }
            if (in_array("SELECT", $actions) && isPermitted($related_module, 4, "") == "yes") {
                $button .= "<input title='" . getTranslatedString("LBL_SELECT") . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " . " type='button' onclick=\"return window.open('index.php?module=" . $related_module . "&return_module=" . $currentModule . "&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=" . $id . "&parenttab=" . $parenttab . "','test','width=640,height=602,resizable=0,scrollbars=0');\"" . " value='" . getTranslatedString("LBL_SELECT") . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
            }
            if (in_array("ADD", $actions) && isPermitted($related_module, 1, "") == "yes") {
                $button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" . "<input title='" . getTranslatedString("LBL_ADD_NEW") . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" . " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"" . $related_module . "\"' type='submit' name='button'" . " value='" . getTranslatedString("LBL_ADD_NEW") . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
            }
        }
        if ($singlepane_view == "true") {
            $returnset = "&return_module=" . $currentModule . "&return_action=DetailView&return_id=" . $id;
        } else {
            $returnset = "&return_module=" . $currentModule . "&return_action=CallRelatedList&return_id=" . $id;
        }
        $query = "SELECT vtiger_crmentity.*, " . $other->table_name . ".*";
        $userNameSql = getSqlForNameInDisplayFormat(array("first_name" => "vtiger_users.first_name", "last_name" => "vtiger_users.last_name"), "Users");
        $query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN " . $userNameSql . " ELSE vtiger_groups.groupname END AS user_name";
        $more_relation = "";
        if (!empty($other->related_tables)) {
            foreach ($other->related_tables as $tname => $relmap) {
                $query .= ", " . $tname . ".*";
                if (empty($relmap[1])) {
                    $relmap[1] = $other->table_name;
                }
                if (empty($relmap[2])) {
                    $relmap[2] = $relmap[0];
                }
                $more_relation .= " LEFT JOIN " . $tname . " ON " . $tname . "." . $relmap[0] . " = " . $relmap[1] . "." . $relmap[2];
            }
        }
        $query .= " FROM " . $other->table_name;
        $query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = " . $other->table_name . "." . $other->table_index;
        $query .= " INNER JOIN vtiger_vteemailmarketingrel ON( vtiger_vteemailmarketingrel.crmid = vtiger_crmentity.crmid OR vtiger_vteemailmarketingrel.vteemailmarketingid = vtiger_crmentity.crmid)";
        $query .= $more_relation;
        $query .= " LEFT  JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT  JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " WHERE vtiger_crmentity.deleted = 0 AND( vtiger_vteemailmarketingrel.vteemailmarketingid = " . $id . " OR vtiger_vteemailmarketingrel.crmid = " . $id . ")";
        if ($related_module == "Leads") {
            $query .= " AND vtiger_leaddetails.converted=0 ";
        }
        $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);
        if ($return_value == NULL) {
            $return_value = array();
        }
        $return_value["CUSTOM_BUTTON"] = $button;
        return $return_value;
    }
    public function save_related_module($module, $crmid, $with_module, $with_crmids, $otherParams = array())
    {
        $adb = PearDatabase::getInstance();
        if (!is_array($with_crmids)) {
            $with_crmids = array($with_crmids);
        }
        foreach ($with_crmids as $with_crmid) {
            if ($with_module == "Leads" || $with_module == "Contacts" || $with_module == "Accounts") {
                $checkResult = $adb->pquery("SELECT 1 FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ? AND crmid = ?", array($crmid, $with_crmid));
                if ($checkResult && 0 < $adb->num_rows($checkResult)) {
                    continue;
                }
                $sql = "INSERT INTO vtiger_vteemailmarketingrel(`vteemailmarketingid`,`crmid`,`module`) VALUES(?,?,?)";
                $adb->pquery($sql, array($crmid, $with_crmid, $with_module));
            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }
    public function addHeaderCss()
    {
        global $adb;
        $tabid = getTabid("VTEEmailMarketing");
        $sql = "SELECT id FROM vtiger_links_seq";
        $rs = $adb->pquery($sql);
        $linkid = $adb->query_result($rs, 0, "id");
        $linkid = intval($linkid) + 1;
        $adb->pquery("Update vtiger_links_seq set id = ?", array($linkid));
        $adb->pquery("INSERT INTO `vtiger_links` (`linkid`,`tabid`,`linktype`,`linklabel`,`linkurl`) VALUES(?,?,?,?,?)", array($linkid, $tabid, "HEADERCSS", "style vte email marketing", "layouts/v7/modules/VTEEmailMarketing/resources/Styles.css"));
    }
    public function removeHeaderCss()
    {
        global $adb;
        $tabid = getTabid("VTEEmailMarketing");
        $adb->pquery("DELETE FROM vtiger_links WHERE tabid = ? AND linkurl = ?", array($tabid, "layouts/v7/modules/VTEEmailMarketing/resources/Styles.css"));
    }
    public function AddTableWs_Entity()
    {
        global $adb;
        $rs = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = VTEEmailMarketing");
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `vtiger_ws_entity` (`name`,`handler_path`,`handler_class`,`ismodule`) VALUES(?,?,?,?)", array("VTEEmailMarketing", "include/Webservices/VtigerModuleOperation.php", "VtigerModuleOperation", "1"));
            $adb->pquery("UPDATE vtiger_ws_entity_seq SET id=(SELECT MAX(id) FROM vtiger_ws_entity)", array());
        }
    }
    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if ($file != "." && $file != "..") {
                if (is_dir($src . "/" . $file)) {
                    $this->recurse_copy($src . "/" . $file, $dst . "/" . $file);
                } else {
                    copy($src . "/" . $file, $dst . "/" . $file);
                }
            }
        }
        closedir($dir);
    }
    public function moveMosaico()
    {
        $this->recurse_copy("layouts/v7/modules/VTEEmailMarketing/mosaico", "test/mosaico");
    }
    public function updateUnsetCustomModel()
    {
        global $adb;
        $tabid = getTabid("VTEEmailMarketing");
        $adb->pquery("Update vtiger_tab set source = ? WHERE tabid = ?", array("", $tabid));
    }
}

?>