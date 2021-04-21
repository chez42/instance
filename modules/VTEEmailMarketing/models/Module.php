<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class VTEEmailMarketing_Module_Model
 */
class VTEEmailMarketing_Module_Model extends Vtiger_Module_Model
{
    /**
     * @return array
     */
    public function getSettingLinks()
    {
        $vTELicense = new VTEEmailMarketing_VTELicense_Model("VTEEmailMarketing");
        $settingsLinks = parent::getSettingLinks();
        $settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Settings", "linkurl" => "index.php?module=VTEEmailMarketing&parent=Settings&view=Settings", "linkicon" => "");
        /*$settingsLinks[] = array("linktype" => "MODULESETTING", "linklabel" => "Uninstall", "linkurl" => "index.php?module=VTEEmailMarketing&parent=Settings&view=Uninstall", "linkicon" => "");*/
        return $settingsLinks;
    }
    /**
     * Function to get relation query for particular module with function name
     * @param <record> $recordId
     * @param <String> $functionName
     * @param Vtiger_Module_Model $relatedModule
     * @return <String>
     */
    public function getRelationQuery($recordId, $functionName, $relatedModule, $relationId = 0)
    {
        global $vtiger_current_version;
        if ($functionName === "get_activities") {
            $focus = CRMEntity::getInstance($this->getName());
            $focus->id = $recordId;
            $userNameSql = getSqlForNameInDisplayFormat(array("first_name" => "vtiger_users.first_name", "last_name" => "vtiger_users.last_name"), "Users");
            $query = "SELECT CASE WHEN (vtiger_users.user_name not like '') THEN " . $userNameSql . " ELSE vtiger_groups.groupname END AS user_name,\r\n\t\t\t\t\t\tvtiger_crmentity.*, vtiger_activity.activitytype, vtiger_activity.subject, vtiger_activity.date_start, vtiger_activity.time_start,\r\n\t\t\t\t\t\tvtiger_activity.recurringtype, vtiger_activity.due_date, vtiger_activity.time_end, vtiger_activity.visibility, vtiger_seactivityrel.crmid AS parent_id,\r\n\t\t\t\t\t\tCASE WHEN (vtiger_activity.activitytype = 'Task') THEN (vtiger_activity.status) ELSE (vtiger_activity.eventstatus) END AS eventstatus\r\n\t\t\t\t\t\tFROM vtiger_activity\r\n\t\t\t\t\t\tINNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid\r\n\t\t\t\t\t\tLEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid\r\n\t\t\t\t\t\tLEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid\r\n\t\t\t\t\t\tLEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid\r\n\t\t\t\t\t\tLEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid\r\n\t\t\t\t\t\t\tWHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype <> 'Emails'\r\n\t\t\t\t\t\t\t\tAND (vtiger_seactivityrel.crmid = " . $recordId;
            $query .= ")";
            $relatedModuleName = $relatedModule->getName();
            $query .= $this->getSpecificRelationQuery($relatedModuleName);
            $nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
            if ($nonAdminQuery) {
                $query = appendFromClauseToQuery($query, $nonAdminQuery);
            }
            $query .= " GROUP BY vtiger_activity.activityid";
        } else {
            if (version_compare($vtiger_current_version, "7.0.0", "<")) {
                $query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
            } else {
                $query = parent::getRelationQuery($recordId, $functionName, $relatedModule, $relationId);
            }
        }
        return $query;
    }
    public static function getIdCampainRelated($idEmailMarketing)
    {
        global $adb;
        $query = "SELECT vtecampaignsid FROM vtiger_vteemailmarketing WHERE vteemailmarketingid = ?";
        $result = $adb->pquery($query, array($idEmailMarketing));
        return $adb->query_result($result, 0, "vtecampaignsid");
    }
    public function getSummaryViewFieldsList()
    {
        if (!$this->summaryFields) {
            $summaryFields = array();
            if ($_REQUEST["relmodule"] == "VTEEmailMarketing" && $_REQUEST["view"] == "Popup") {
                $summaryFields["vteemailmarketingno"] = Vtiger_Field_Model::getInstance("vteemailmarketingno", $this);
                $summaryFields["vtecampaigns"] = Vtiger_Field_Model::getInstance("vtecampaigns", $this);
                $summaryFields["subject"] = Vtiger_Field_Model::getInstance("subject", $this);
                $summaryFields["sender"] = Vtiger_Field_Model::getInstance("sender", $this);
                $summaryFields["total"] = Vtiger_Field_Model::getInstance("total", $this);
                $summaryFields["scheduled"] = Vtiger_Field_Model::getInstance("scheduled", $this);
                $summaryFields["batch_delivery"] = Vtiger_Field_Model::getInstance("batch_delivery", $this);
                $summaryFields["createdtime"] = Vtiger_Field_Model::getInstance("createdtime", $this);
                $summaryFields["assigned_user_id"] = Vtiger_Field_Model::getInstance("assigned_user_id", $this);
            } else {
                $fields = $this->getFields();
                foreach ($fields as $fieldName => $fieldModel) {
                    if ($fieldModel->isSummaryField() && $fieldModel->isActiveField()) {
                        $summaryFields[$fieldName] = $fieldModel;
                    }
                }
            }
            $this->summaryFields = $summaryFields;
        }
        return $this->summaryFields;
    }
    
    
    /**
     * Function to check whether the module is an entity type module or not
     * @return <Boolean> true/false
     */
    public function isQuickCreateSupported() {
        return false;
    }
    
}

?>