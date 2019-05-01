<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_Record_Model extends Vtiger_Record_Model
{
    public function getRecord(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule();
        $record = $request->get("record");
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $recordModel = $recordModel->getData();
        $recordId = $recordModel["id"];
        $nameVTECampaign = $recordModel["vtecampaigns"];
        $subject = $recordModel["subject"];
        $sender = $recordModel["sender"];
        $totalRelated = $recordModel["total"];
        list($name, $email) = explode(" ", $sender);
        $email = explode("(", $email);
        $email = explode(")", $email[1]);
        $email = $email[0];
        $getTemplateEmail = $adb->pquery("SELECT * FROM vtiger_emailtemplates WHERE subject = ?", array($subject));
        $templateEmail = $adb->query_result($getTemplateEmail, 0, "templateid");
        $scheduled = self::getScheduledVTEEmailMarketing($recordId);
        $data = array();
        $data["record"] = $record;
        $data["recordId"] = $recordId;
        $data["campaign_name"] = $nameVTECampaign;
        $data["from_name"] = $name;
        $data["from_email"] = $email;
        $data["templateEmail"] = $templateEmail;
        $data["total_related"] = $totalRelated;
        $data["batch_delivery"] = $scheduled["batch_delivery"];
        $data["number_email"] = $scheduled["number_email"];
        $data["frequency"] = $scheduled["frequency"];
        $data["date"] = $scheduled["date"];
        $data["time"] = $scheduled["time"];
        return $data;
    }
    public function getScheduledVTEEmailMarketing($recordId)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid =? ", array($recordId));
        $batch_delivery = $adb->query_result($result, "0", "batch_delivery");
        $number_email = $adb->query_result($result, "0", "number_email");
        $frequency = $adb->query_result($result, "0", "frequency");
        $datetime = $adb->query_result($result, "0", "datetime");
        $datetimeByUser = DateTimeField::convertToUserTimeZone($datetime);
        $datetimeByUserFormat = DateTimeField::convertToUserFormat($datetimeByUser->format("Y-m-d H:i:s"));
        $datetimeUser = $datetimeByUserFormat;
        list($date, $time) = explode(" ", $datetimeUser);
        $data["batch_delivery"] = $batch_delivery;
        $data["number_email"] = $number_email;
        $data["frequency"] = $frequency;
        $data["date"] = $date;
        $data["time"] = $time;
        return $data;
    }
    public function getVTEEmailMarketing($recordId)
    {
        $recordVTEEmailMarketing = Vtiger_Record_Model::getInstanceById($recordId, "VTEEmailMarketing");
        $record = $recordVTEEmailMarketing->getData();
        $data = array();
        $data["id"] = $recordId;
        $data["campaignName"] = $record["vtecampaigns"];
        $data["sender"] = $record["sender"];
        $data["subject"] = $record["subject"];
        $data["countLeads"] = self::getCountRelated($recordId, "Leads");
        $data["countOrganization"] = self::getCountRelated($recordId, "Accounts");
        $data["countContacts"] = self::getCountRelated($recordId, "Contacts");
        return $data;
    }
    public function getCountRelated($id, $relmodule)
    {
        global $adb;
        $query = "SELECT COUNT(*) AS 'count' FROM vtiger_vteemailmarketingrel\n                  WHERE vtiger_vteemailmarketingrel.vteemailmarketingid = ? AND module = ?\n                    ";
        $params = array($id, $relmodule);
        $result = $adb->pquery($query, $params);
        $count = $adb->query_result($result, 0, "count");
        return $count;
    }
    public function getEmailTemplate($id)
    {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_emailtemplates WHERE templateid = ? AND deleted = 0", array($id));
        $data["id"] = $id;
        $data["subject"] = $adb->query_result($result, 0, "subject");
        $data["body"] = $adb->query_result($result, 0, "body");
        return $data;
    }
    public function getRelationId()
    {
        $moduleVTEEmailMarketing = Vtiger_Record_Model::getCleanInstance("VTEEmailMarketing")->getModule();
        $moduleAccounts = Vtiger_Record_Model::getCleanInstance("Accounts")->getModule();
        $moduleLeads = Vtiger_Record_Model::getCleanInstance("Leads")->getModule();
        $moduleContacts = Vtiger_Record_Model::getCleanInstance("Contacts")->getModule();
        $relationIdAccounts = self::getRelationIdModule($moduleVTEEmailMarketing->getId(), $moduleAccounts->getId());
        $relationIdLeads = self::getRelationIdModule($moduleVTEEmailMarketing->getId(), $moduleLeads->getId());
        $relationIdContacts = self::getRelationIdModule($moduleVTEEmailMarketing->getId(), $moduleContacts->getId());
        $data = array("relationIdAccounts" => $relationIdAccounts, "relationIdLeads" => $relationIdLeads, "relationIdContacts" => $relationIdContacts);
        return $data;
    }
    public function getRelationIdModule($tabid, $relatedTabid)
    {
        global $adb;
        $result = $adb->pquery("SELECT `relation_id` FROM vtiger_relatedlists WHERE tabid=? AND related_tabid =?", array($tabid, $relatedTabid));
        $relationId = $adb->query_result($result, 0, "relation_id");
        return $relationId;
    }
    public function getCountRecordFilter($cvId, $moduleName)
    {
        global $adb;
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId, "");
        $listQuery = $listViewModel->getQuery();
        $listQuery = split("FROM", $listQuery);
        $query = "SELECT COUNT(*) as 'count' FROM " . $listQuery[1];
        $listResult = $adb->pquery($query, array());
        $count = $adb->query_result($listResult, 0, "count");
        return $count;
    }
    public function getFilter()
    {
        global $adb;
        $query = "SELECT `cvid`,`viewname`,`entitytype` ,`first_name`,`last_name`\n              FROM vtiger_customview JOIN vtiger_users ON vtiger_customview.userid = vtiger_users.id\n              WHERE entitytype = ? OR entitytype = ? OR entitytype= ? ORDER BY( CASE entitytype WHEN 'Contacts' THEN 1 WHEN 'Leads' THEN 2 WHEN 'Accounts' THEN 3 ELSE 100 END), viewname ASC";
        $params = array("Leads", "Accounts", "Contacts");
        $result = $adb->pquery($query, $params);
        $numrows = $adb->num_rows($result);
        $data = array();
        for ($i = 0; $i < $numrows; $i++) {
            $data[$i]["cvid"] = $adb->query_result($result, $i, "cvid");
            $data[$i]["name_filter"] = $adb->query_result($result, $i, "viewname");
            $data[$i]["module"] = $adb->query_result($result, $i, "entitytype");
            $firstName = $adb->query_result($result, $i, "first_name");
            $lastName = $adb->query_result($result, $i, "last_name");
            $firstName == " " ? $data[$i]["name"] : $data[$i]["name"];
            $count = self::getCountRecordFilter($data[$i]["cvid"], $data[$i]["module"]);
            $data[$i]["count"] = $count;
        }
        return $data;
    }
    public function getAllRecordIdFilter($moduleName, $cvId)
    {
        global $adb;
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId, "");
        $moduleName = $listViewModel->getModule()->get("name");
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $listViewContoller = $listViewModel->get("listview_controller");
        $listQuery = $listViewModel->getQuery();
        $listResult = $adb->pquery($listQuery, array());
        $listViewEntries = $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult);
        $index = 0;
        $id = array();
        foreach ($listViewEntries as $recordId => $record) {
            $id[$index] = $recordId;
            $index++;
        }
        return $id;
    }
    public function getRecordRelatedSentEmail($recordId, $page = 1, $dispayType = "")
    {
        global $adb;
        $rsEmailId = $adb->pquery("CREATE TEMPORARY TABLE tbl_vteemailmarketing_tempemail (`mailid` int(11) primary key)\n                        (SELECT relcrmid as mailid\n                            FROM\n                                vtiger_crmentityrel\n                            WHERE vtiger_crmentityrel.crmid = ?\n                            AND vtiger_crmentityrel.relmodule = 'Emails'\n                        );", array($recordId));
        $arrEmailCreatedOn = array();
        $rsEmailDetail = $adb->pquery("SELECT vtiger_email_track.crmid, createdtime \n                    FROM vtiger_email_track\n                    INNER JOIN tbl_vteemailmarketing_tempemail ON tbl_vteemailmarketing_tempemail.mailid = vtiger_email_track.mailid\n                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_email_track.mailid\n                    WHERE vtiger_email_track.crmid <> ?", array($recordId));
        if (0 < $adb->num_rows($rsEmailDetail)) {
            while ($rowEmail = $adb->fetchByAssoc($rsEmailDetail)) {
                $arrEmailCreatedOn[$rowEmail["crmid"]] = $rowEmail["createdtime"];
            }
        }
        $startIndex = ($page - 1) * 20;
        $endIndex = 20;
        $dispayTypeConditions = "";
        $joinConditions = "";
        if ($dispayType == "queued") {
            $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND (ISNULL(rel.`status`) OR rel.`status` NOT IN (0, 1, 2))\n                    LIMIT " . $startIndex . "," . $endIndex;
        } else {
            if ($dispayType == "sent") {
                $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE mk.vteemailmarketingid = ? AND rel.`status` = 1\n                    LIMIT " . $startIndex . "," . $endIndex;
            } else {
                if ($dispayType == "failed_to_send") {
                    $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND rel.`status` IN (0, 2)\n                    LIMIT " . $startIndex . "," . $endIndex;
                } else {
                    if ($dispayType == "unique_open") {
                        $query = "SELECT DISTINCT c.label, rel.*\n                    FROM vtiger_email_track et\n                    INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                    INNER JOIN vtiger_crmentity c ON c.crmid = rel.crmid\n                    INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid AND ed.idlists LIKE CONCAT(rel.crmid, '@%')\n                    WHERE c.deleted = 0 AND mk.vteemailmarketingid = ? AND et.access_count > 0\n                    LIMIT " . $startIndex . "," . $endIndex;
                    } else {
                        if ($dispayType == "unopened") {
                            $query = "SELECT DISTINCT\n                            c.label ,\n                            rel.*\n                        FROM\n                            vtiger_vteemailmarketingrel rel\n                        INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                        INNER JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                        LEFT JOIN(\n                            SELECT DISTINCT\n                                c.label ,\n                                rel.*\n                            FROM\n                                vtiger_email_track et\n                            INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                            INNER JOIN vtiger_crmentity c ON c.crmid = mk.vteemailmarketingid\n                            INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                            INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid\n                            AND ed.idlists LIKE CONCAT(rel.crmid , '@%')\n                            WHERE\n                                c.deleted = 0\n                            AND et.access_count > 0\n                            AND et.crmid = " . $recordId . "\n                        ) tmp ON rel.crmid = tmp.crmid\n                        WHERE\n                        mk.vteemailmarketingid = ?\n                        AND rel. STATUS = 1\n                        AND ISNULL(tmp.crmid)\n                     LIMIT " . $startIndex . "," . $endIndex;
                        } else {
                            if ($dispayType == "unsubcribes") {
                                $query = "SELECT c.label, rel.*\n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing_unsubcribes un ON rel.crmid = un.crmid \n                    INNER JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE c.deleted = 0 \n                    GROUP BY rel.crmid\n                    ORDER BY rel.vteemailmarketingid DESC\n                LIMIT " . $startIndex . "," . $endIndex;
                            } else {
                                $query = "SELECT DISTINCT  vtiger_crmentity.label , vtiger_vteemailmarketingrel.* \n                    FROM vtiger_vteemailmarketingrel\n                    JOIN vtiger_crmentity ON vtiger_vteemailmarketingrel.crmid = vtiger_crmentity.crmid\n                    WHERE vtiger_vteemailmarketingrel.vteemailmarketingid = ?\n                    AND vtiger_vteemailmarketingrel.module IN('Contacts','Leads','Accounts')\n                    LIMIT " . $startIndex . "," . $endIndex;
                            }
                        }
                    }
                }
            }
        }
        $params = array($recordId);
        if ($dispayType == "unsubcribes") {
            $result = $adb->pquery($query);
        } else {
            $result = $adb->pquery($query, $params);
        }
        $numrow = $adb->num_rows($result);
        $data = array();
        for ($i = 0; $i < $numrow; $i++) {
            $relatedId = $adb->query_result($result, $i, "crmid");
            $relatedModule = $adb->query_result($result, $i, "module");
            $label = $adb->query_result($result, $i, "label");
            $errorInfo = $adb->query_result($result, $i, "error_info");
            $status = $adb->query_result($result, $i, "status");
            $data[$i]["error_info"] = $errorInfo;
            $data[$i]["status"] = $status;
            $data[$i]["sent_on"] = "Default";
            if ($arrEmailCreatedOn[$relatedId] && $status == 1) {
                $data[$i]["sent_on"] = self::convertTimeUser($arrEmailCreatedOn[$relatedId]);
            } else {
                if ($status != "" && $status != NULL) {
                    if ($errorInfo != "") {
                        if ($errorInfo == "Unsubscribed in previous campaign") {
                            $data[$i]["sent_on"] = "<b style=\"color: red\">Unsubcribed</b><a class=\"resubcribe\" relatedId=\"" . $relatedId . "\">Resubcribe</a>";
                            $data[$i]["error_info"] = "";
                        } else {
                            $data[$i]["sent_on"] = "<b style=\"color: red\">Failed to Send</b>";
                            $data[$i]["error_info"] = "<span class=\"glyphicon glyphicon-info-sign pull-right error_info\" style=\"cursor: pointer; color: black;margin-top:12px\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $errorInfo . "\"></span>";
                        }
                    }
                } else {
                    $data[$i]["sent_on"] = "";
                    $data[$i]["error_info"] = "";
                    $data[$i]["status"] = "";
                }
            }
            if ($relatedModule == "Contacts") {
                $getRecordContacts = $adb->pquery("SELECT * FROM vtiger_contactdetails WHERE contactid = ? ", array($relatedId));
                $data[$i]["record_type"] = $relatedModule;
                $data[$i]["name"] = $label;
                $data[$i]["email"] = $adb->query_result($getRecordContacts, 0, "email");
                $data[$i]["record_id"] = $relatedId;
                $data[$i]["data_url"] = "index.php?module=" . $relatedModule . "&view=Detail&record=" . $relatedId;
            } else {
                if ($relatedModule == "Leads") {
                    $getRecordLeads = $adb->pquery("SELECT * FROM vtiger_leaddetails WHERE leadid = ? ", array($relatedId));
                    $data[$i]["record_type"] = $relatedModule;
                    $data[$i]["name"] = $label;
                    $data[$i]["email"] = $adb->query_result($getRecordLeads, 0, "email");
                    $data[$i]["record_id"] = $relatedId;
                    $data[$i]["data_url"] = "index.php?module=" . $relatedModule . "&view=Detail&record=" . $relatedId;
                } else {
                    $getRecordAccounts = $adb->pquery("SELECT * FROM vtiger_account WHERE accountid = ?", array($relatedId));
                    $data[$i]["record_type"] = $relatedModule;
                    $data[$i]["name"] = $label;
                    $data[$i]["email"] = $adb->query_result($getRecordAccounts, 0, "email1");
                    $data[$i]["record_id"] = $relatedId;
                    $data[$i]["data_url"] = "index.php?module=" . $relatedModule . "&view=Detail&record=" . $relatedId;
                }
            }
        }
        return $data;
    }
    public function convertTimeUser($time)
    {
        $startDateTimeByUser = DateTimeField::convertToUserTimeZone(date("Y-m-d H:i:s", strtotime($time)));
        $startDateTimeByUserFormat = DateTimeField::convertToUserFormat($startDateTimeByUser->format("Y-m-d H:i:s"));
        list($startDate, $startTime) = explode(" ", $startDateTimeByUserFormat);
        $currentUser = Users_Record_Model::getCurrentUserModel();
        if ($currentUser->get("hour_format") == "12") {
            $startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);
        }
        return $startDate . " " . $startTime;
    }
    public function getPaginationRelatedSentEmail($recordId, $page = 1, $dispayType = "")
    {
        global $adb;
        $dispayTypeConditions = "";
        $joinConditions = "";
        if ($dispayType == "queued") {
            $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND (ISNULL(rel.`status`) OR rel.`status` NOT IN (0, 1, 2))";
        } else {
            if ($dispayType == "sent") {
                $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    WHERE c.deleted = 0 AND c.crmid = ? AND rel.status = 1";
            } else {
                if ($dispayType == "failed_to_send") {
                    $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND rel.`status` IN (0, 2)";
                } else {
                    if ($dispayType == "unique_open") {
                        $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_email_track et\n                    INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON c.crmid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                    INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid AND ed.idlists LIKE CONCAT(rel.crmid, '@%')\n                    WHERE c.deleted = 0 AND c.crmid = ? AND et.access_count > 0";
                    } else {
                        if ($dispayType == "unopened") {
                            $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    LEFT JOIN ( \n                        SELECT DISTINCT c.label, rel.*\n                        FROM vtiger_email_track et\n                        INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                        INNER JOIN vtiger_crmentity c ON c.crmid = mk.vteemailmarketingid\n                        INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                        INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid AND ed.idlists LIKE CONCAT(rel.crmid, '@%')\n                        WHERE c.deleted = 0 AND et.access_count > 0 AND et.crmid = " . $recordId . "\n                    ) tmp ON rel.crmid = tmp.crmid\n                    WHERE c.deleted = 0 AND c.crmid = ? AND rel.status = 1 AND ISNULL(tmp.crmid)";
                        } else {
                            if ($dispayType == "unsubcribes") {
                                $sql = "SELECT COUNT(rel.crmid) as 'count'\n                    FROM vtiger_vteemailmarketing_unsubcribes un\n                    INNER JOIN vtiger_vteemailmarketing mk ON un.vteemailmarketingid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    INNER JOIN vtiger_vteemailmarketingrel rel ON mk.vteemailmarketingid = rel.vteemailmarketingid AND rel.crmid = un.crmid\n                    WHERE c.deleted = 0";
                            } else {
                                $sql = "SELECT COUNT(vtiger_vteemailmarketingrel.crmid) as 'count' FROM vtiger_vteemailmarketingrel WHERE vteemailmarketingid = ?";
                            }
                        }
                    }
                }
            }
        }
        if ($dispayType == "unsubcribes") {
            $rsGetRecord = $adb->pquery($sql);
        } else {
            $rsGetRecord = $adb->pquery($sql, array($recordId));
        }
        $countTotalRecord = $adb->query_result($rsGetRecord, 0, "count");
        $pageLimit = 20;
        $totalPage = ceil(intval($countTotalRecord) / $pageLimit);
        $totalRecord = $countTotalRecord;
        $startIndex = ($page - 1) * $pageLimit;
        if ($dispayType == "queued") {
            $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND (ISNULL(rel.`status`) OR rel.`status` NOT IN (0, 1, 2))\n                    LIMIT " . $startIndex . "," . $pageLimit;
        } else {
            if ($dispayType == "sent") {
                $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    WHERE c.deleted = 0 AND c.crmid = ? AND rel.status = 1\n                    LIMIT " . $startIndex . "," . $pageLimit;
            } else {
                if ($dispayType == "failed_to_send") {
                    $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    JOIN vtiger_crmentity c ON rel.crmid = c.crmid\n                    WHERE rel.vteemailmarketingid = ?\n                    AND rel.module IN('Contacts','Leads','Accounts') \n                    AND rel.`status` IN (0, 2)\n                    LIMIT " . $startIndex . "," . $pageLimit;
                } else {
                    if ($dispayType == "unique_open") {
                        $query = "SELECT DISTINCT c.label, rel.*\n                    FROM vtiger_email_track et\n                    INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON c.crmid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                    INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid AND ed.idlists LIKE CONCAT(rel.crmid, '@%')\n                    WHERE c.deleted = 0 AND c.crmid = ? AND et.access_count > 0\n                    LIMIT " . $startIndex . "," . $pageLimit;
                    } else {
                        if ($dispayType == "unopened") {
                            $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketingrel rel\n                    INNER JOIN vtiger_vteemailmarketing mk ON mk.vteemailmarketingid = rel.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    LEFT JOIN ( \n                        SELECT DISTINCT c.label, rel.*\n                        FROM vtiger_email_track et\n                        INNER JOIN vtiger_vteemailmarketing mk ON et.crmid = mk.vteemailmarketingid\n                        INNER JOIN vtiger_crmentity c ON c.crmid = mk.vteemailmarketingid\n                        INNER JOIN vtiger_vteemailmarketingrel rel ON rel.vteemailmarketingid = et.crmid\n                        INNER JOIN vtiger_emaildetails ed ON ed.emailid = et.mailid AND ed.idlists LIKE CONCAT(rel.crmid, '@%')\n                        WHERE c.deleted = 0 AND et.access_count > 0 AND et.crmid = " . $recordId . "\n                    ) tmp ON rel.crmid = tmp.crmid\n                    WHERE c.deleted = 0 AND c.crmid = ? AND rel.status = 1 AND ISNULL(tmp.crmid)\n                    LIMIT " . $startIndex . "," . $pageLimit;
                        } else {
                            if ($dispayType == "unsubcribes") {
                                $query = "SELECT DISTINCT c.label , rel.* \n                    FROM vtiger_vteemailmarketing_unsubcribes un\n                    INNER JOIN vtiger_vteemailmarketing mk ON un.vteemailmarketingid = mk.vteemailmarketingid\n                    INNER JOIN vtiger_crmentity c ON mk.vteemailmarketingid = c.crmid\n                    INNER JOIN vtiger_vteemailmarketingrel rel ON mk.vteemailmarketingid = rel.vteemailmarketingid AND rel.crmid = un.crmid\n                    WHERE c.deleted = 0 AND c.crmid = ?\n                    LIMIT " . $startIndex . "," . $pageLimit;
                            } else {
                                $query = "SELECT vtiger_crmentity.label , vtiger_vteemailmarketingrel.* \n                    FROM vtiger_vteemailmarketingrel\n                    JOIN vtiger_crmentity ON vtiger_vteemailmarketingrel.crmid = vtiger_crmentity.crmid\n                    WHERE vtiger_vteemailmarketingrel.vteemailmarketingid = ?\n                    AND vtiger_vteemailmarketingrel.module IN('Contacts','Leads','Accounts')\n                    " . $dispayTypeConditions . "\n                    LIMIT " . $startIndex . "," . $pageLimit;
                            }
                        }
                    }
                }
            }
        }
        $params = array($recordId);
        $result = $adb->pquery($query, $params);
        $numRows = $adb->num_rows($result);
        $endRecord = $startIndex + $numRows;
        $startRecord = $startIndex + 1;
        $currentPage = $page;
        $sqlUnsubscribe = "SELECT DISTINCT count(rel.crmid) as \"count\"\n                FROM vtiger_vteemailmarketingrel rel\n                INNER JOIN vtiger_vteemailmarketing_unsubcribes un\n                ON rel.crmid = un.crmid WHERE rel.vteemailmarketingid = ? AND un.status = 1";
        $resultUnsubcribe = $adb->pquery($sqlUnsubscribe, array($recordId));
        $countUnsubcribe = $adb->query_result($resultUnsubcribe, 0, "count");
        $data = array("totalPage" => $totalPage, "currentPage" => $currentPage, "totalRecord" => $totalRecord, "startRecord" => $startRecord, "endRecord" => $endRecord, "countUnsubcribe" => $countUnsubcribe);
        return $data;
    }
}

?>