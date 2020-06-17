<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_TemplatesListAjax_View extends EmailTemplates_List_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = "VTEEmailMarketing";
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign("VIEW", $request->get("view"));
        $viewer->assign("REQUEST_INSTANCE", $request);
        $viewer->assign("MODULE_MODEL", $moduleModel);
        $viewer->assign("CURRENT_USER_MODEL", Users_Record_Model::getCurrentUserModel());
        $defaultLayout = Vtiger_Viewer::getDefaultLayoutName();
        if ($request->get("viewType") == "grid" && $defaultLayout == "v7") {
            $viewer->view("GridViewContents.tpl", "VTEEmailMarketing");
        } else {
            $viewer->view("ListViewContents.tpl", "VTEEmailMarketing");
        }
    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName = "VTEEmailMarketing";
        $cvId = $request->get("viewname");
        $viewType = $request->get("viewType");
        $pageNumber = $request->get("page");
        $orderBy = $request->get("orderby");
        $sortOrder = $request->get("sortorder");
        $searchKey = $request->get("search_key");
        $searchValue = $request->get("search_value");
        $sourceModule = $request->get("sourceModule");
        $operator = $request->get("operator");
        if ($request->get("search_params") != "null" && empty($searchKey) && empty($searchKey)) {
            $searchKey = $request->get("search_params")[0][0][0];
            $searchValue = $request->get("search_params")[0][0][2];
        }
        if ($request->get("mode") == "removeAlphabetSearch") {
            Vtiger_ListView_Model::deleteParamsSession($moduleName, array("search_key", "search_value", "operator"));
            $searchKey = "";
            $searchValue = "";
            $operator = "";
        }
        if ($request->get("mode") == "removeSorting") {
            Vtiger_ListView_Model::deleteParamsSession($moduleName, array("orderby", "sortorder"));
            $orderBy = "";
            $sortOrder = "";
        }
        if (empty($orderBy) && empty($searchValue) && empty($pageNumber)) {
            $orderParams = Vtiger_ListView_Model::getSortParamsSession($moduleName);
            if ($orderParams) {
                $pageNumber = $orderParams["page"];
                $orderBy = $orderParams["orderby"];
                $sortOrder = $orderParams["sortorder"];
                $searchKey = $orderParams["search_key"];
                $searchValue = $orderParams["search_value"];
                $operator = $orderParams["operator"];
                $viewType = $orderParams["viewType"];
            }
        } else {
            if ($request->get("nolistcache") != 1) {
                $params = array("page" => $pageNumber, "orderby" => $orderBy, "sortorder" => $sortOrder, "search_key" => $searchKey, "search_value" => $searchValue, "operator" => $operator, "viewType" => $viewType);
                Vtiger_ListView_Model::setSortParamsSession($moduleName, $params);
            }
        }
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
            $faSortImage = "fa-sort-desc";
        } else {
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
            $faSortImage = "fa-sort-asc";
        }
        if (empty($pageNumber)) {
            $pageNumber = "1";
        }
        $listViewModel = VTEEmailMarketing_TemplatesListView_Model::getInstance("VTEEmailMarketing", $cvId);
        $linkParams = array("MODULE" => $moduleName, "ACTION" => $request->get("view"), "CVID" => $cvId);
        $linkModels = $listViewModel->getListViewMassActions($linkParams);
        if (!$this->pagingModel) {
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set("page", $pageNumber);
        } else {
            $pagingModel = $this->pagingModel;
        }
        if (!empty($orderBy)) {
            $listViewModel->set("orderby", $orderBy);
            $listViewModel->set("sortorder", $sortOrder);
        }
        if (!empty($operator)) {
            $listViewModel->set("operator", $operator);
            $viewer->assign("OPERATOR", $operator);
            $viewer->assign("ALPHABET_VALUE", $searchValue);
        }
        if (!empty($searchKey)) {
            $listViewModel->set("search_key", $searchKey);
            $listViewModel->set("search_value", $searchValue);
        }
        if (!empty($sourceModule)) {
            $listViewModel->set("sourceModule", $sourceModule);
        }
        if (empty($viewType)) {
            $viewType = "grid";
        }
        $listViewModel->set("viewType", $viewType);
        if (!$this->listViewHeaders) {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }
        if (!$this->listViewEntries) {
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        if (!$this->pagingModel) {
            $this->pagingModel = $pagingModel;
        }
        $noOfEntries = count($this->listViewEntries);
        $viewer->assign("VIEWID", $cvId);
        $viewer->assign("MODULE", $moduleName);
        if (!$this->listViewLinks) {
            $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
        }
        $viewer->assign("LISTVIEW_LINKS", $this->listViewLinks);
        $viewer->assign("LISTVIEW_MASSACTIONS", $linkModels["LISTVIEWMASSACTION"]);
        $viewer->assign("PAGING_MODEL", $pagingModel);
        $viewer->assign("PAGE_NUMBER", $pageNumber);
        $viewer->assign("VIEWTYPE", $viewType);
        $viewer->assign("ORDER_BY", $orderBy);
        $viewer->assign("SORT_ORDER", $sortOrder);
        $viewer->assign("SEARCH_VALUE", $searchValue);
        $viewer->assign("NEXT_SORT_ORDER", $nextSortOrder);
        $viewer->assign("SORT_IMAGE", $sortImage);
        $viewer->assign("COLUMN_NAME", $orderBy);
        $viewer->assign("FASORT_IMAGE", $faSortImage);
        $viewer->assign("LISTVIEW_ENTRIES_COUNT", $noOfEntries);
        $viewer->assign("RECORD_COUNT", $noOfEntries);
        $viewer->assign("LISTVIEW_HEADERS", $this->listViewHeaders);
        $viewer->assign("LISTVIEW_ENTRIES", $this->listViewEntries);
        if (PerformancePrefs::getBoolean("LISTVIEW_COMPUTE_PAGE_COUNT", false)) {
            if (!$this->listViewCount) {
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $viewer->assign("LISTVIEW_COUNT", $this->listViewCount);
        }
        $viewer->assign("LIST_VIEW_MODEL", $listViewModel);
        $viewer->assign("IS_CREATE_PERMITTED", $listViewModel->getModule()->isPermitted("CreateView"));
        $viewer->assign("IS_MODULE_EDITABLE", $listViewModel->getModule()->isPermitted("EditView"));
        $viewer->assign("IS_MODULE_DELETABLE", $listViewModel->getModule()->isPermitted("Delete"));
    }
    public function getQuery()
    {
        $listQuery = "SELECT templateid," . implode(",", $this->querySelectColumns) . " FROM vtiger_emailtemplates\r\n\t\t\t\t\t\tLEFT JOIN vtiger_tab ON vtiger_tab.name = vtiger_emailtemplates.module\r\n\t\t\t\t\t\tAND (vtiger_tab.isentitytype=1 or vtiger_tab.name = \"Users\")\r\n\t\t\t\t\t\tJOIN vtiger_vteemailmarketing_emailtemplate ON vtiger_vteemailmarketing_emailtemplate.idtemplate = vtiger_emailtemplates.templateid";
        return $listQuery;
    }
    public function getPageCount(Vtiger_Request $request)
    {
        $request->set("module", "EmailTemplates");
        $listViewCount = $this->getListViewCount($request);
        $pagingModel = new Vtiger_Paging_Model();
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $listViewCount / (int) $pageLimit);
        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $result = array();
        $result["page"] = $pageCount;
        $result["numberOfRecords"] = $listViewCount;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}

?>