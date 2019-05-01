<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_TemplatesListView_Model extends Vtiger_ListView_Model
{
    private $querySelectColumns = array("templatename,foldername, subject", "systemtemplate", "module", "description");
    private $listViewColumns = array("templatename", "subject", "description", "module");
    /**
     * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
     * @param <String> $moduleName - Module Name
     * @param <Number> $viewId - Custom View Id
     * @return Vtiger_ListView_Model instance
     */
    public static function getInstance($moduleName, $viewId = 0)
    {
        $db = PearDatabase::getInstance();
        $modelClassName = Vtiger_Loader::getComponentClassName("Model", "TemplatesListView", $moduleName);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        return $instance->set("module", $moduleModel);
    }
    /**
     * Function to get the list view header
     * @return <Array> - List of Vtiger_Field_Model instances
     */
    public function getListViewHeaders()
    {
        $fieldObjects = array();
        $listViewHeaders = array("Template Name" => "templatename", "Subject" => "subject", "Description" => "description", "Module Name" => "module");
        foreach ($listViewHeaders as $key => $fieldName) {
            $fieldModel = new EmailTemplates_Field_Model();
            $fieldModel->set("name", $fieldName);
            $fieldModel->set("label", $key);
            $fieldModel->set("column", $fieldName);
            $fieldObjects[] = $fieldModel;
        }
        return $fieldObjects;
    }
    /**
     * Function to get the list view entries
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();
        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $orderBy = $this->getForSql("orderby");
        $sortOrder = $this->getForSql("sortorder");
        $listQuery = $this->getQuery();
        $sourceModule = $this->get("sourceModule");
        $searchKey = $this->get("search_key");
        $searchValue = $this->get("search_value");
        $whereQuery .= " WHERE ";
        if (!empty($searchKey) && !empty($searchValue)) {
            $whereQuery .= "templatename LIKE '%" . $searchValue . "%' OR description LIKE '%" . $searchValue . "%' AND ";
        }
        $moduleActiveCheck = " vtiger_emailtemplates.templateid > 16 AND (vtiger_tab.presence IN (0,2) OR vtiger_emailtemplates.module IS null OR vtiger_emailtemplates.module = \"\")";
        $listQuery .= $whereQuery . $moduleActiveCheck;
        if ($sourceModule) {
            $listQuery .= " AND vtiger_emailtemplates.module = '" . $sourceModule . "'";
        }
        if ($orderBy) {
            $listQuery .= " ORDER BY " . $orderBy . " " . $sortOrder;
        } else {
            $listQuery .= " ORDER BY templateid DESC";
        }
        $listQuery .= " LIMIT " . $startIndex . "," . ($pageLimit + 1);
        $result = $db->pquery($listQuery, array());
        $num_rows = $db->num_rows($result);
        $listViewRecordModels = array();
        for ($i = 0; $i < $num_rows; $i++) {
            $recordModel = new EmailTemplates_Record_Model();
            $recordModel->setModule("EmailTemplates");
            $row = $db->query_result_rowdata($result, $i);
            $recordModel->setRawData($row);
            foreach ($row as $key => $value) {
                if ($key == "module") {
                    $value = vtranslate($value, $value);
                }
                if (in_array($key, $this->listViewColumns)) {
                    $value = textlength_check($value);
                }
                $row[$key] = $value;
            }
            $listViewRecordModels[$row["templateid"]] = $recordModel->setData($row);
        }
        $pagingModel->calculatePageRange($listViewRecordModels);
        if ($pageLimit < $num_rows) {
            array_pop($listViewRecordModels);
            $pagingModel->set("nextPageExists", true);
        } else {
            $pagingModel->set("nextPageExists", false);
        }
        return $listViewRecordModels;
    }
    public function getQuery()
    {
        $listQuery = "SELECT vtiger_vteemailmarketing_emailtemplate.thumbnail,templateid," . implode(",", $this->querySelectColumns) . " FROM vtiger_emailtemplates\r\n\t\t\t\t\t\tLEFT JOIN vtiger_tab ON vtiger_tab.name = vtiger_emailtemplates.module\r\n\t\t\t\t\t\tAND (vtiger_tab.isentitytype=1 or vtiger_tab.name = \"Users\") \r\n\t\t\t\t\t\tJOIN vtiger_vteemailmarketing_emailtemplate ON vtiger_vteemailmarketing_emailtemplate.idtemplate = vtiger_emailtemplates.templateid";
        return $listQuery;
    }
    /**
     * Function to get the list view entries
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
    public function getListViewCount()
    {
        $db = PearDatabase::getInstance();
        $listQuery = $this->getQuery();
        $position = stripos($listQuery, "from");
        if ($position) {
            $split = spliti("from", $listQuery);
            $splitCount = count($split);
            $listQuery = "SELECT count(*) AS count ";
            for ($i = 1; $i < $splitCount; $i++) {
                $listQuery = $listQuery . " FROM " . $split[$i];
            }
        }
        $searchKey = $this->get("search_key");
        $searchValue = $this->get("search_value");
        $whereQuery .= " WHERE ";
        if (!empty($searchKey) && !empty($searchValue)) {
            $whereQuery .= (string) $searchKey . " LIKE '" . $searchValue . "%' AND ";
        }
        $moduleActiveCheck = "(vtiger_tab.presence IN (0,2) OR vtiger_emailtemplates.module IS null OR vtiger_emailtemplates.module = \"\")";
        $listQuery .= $whereQuery . $moduleActiveCheck;
        $sourceModule = $this->get("sourceModule");
        if ($sourceModule) {
            $listQuery .= " AND vtiger_emailtemplates.module= \"" . $sourceModule . "\" ";
        }
        $listResult = $db->pquery($listQuery, array());
        return $db->query_result($listResult, 0, "count");
    }
}

?>