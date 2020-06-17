<?php

class PortfolioInformation_List_View extends Vtiger_List_View {
    var $generator;//The query generator
    /**
     * Calculates the global summary for the list view
     * @global type $current_user
     * @param Vtiger_Request $request
     * @param type $display
     * @return type
     */
    /*        public function preProcess(Vtiger_Request $request, $display = true) {
     $currentUserModel = Users_Record_Model::getCurrentUserModel();
     #            $global_summary = new PortfolioInformation_GlobalSummary_Model();
     $sub_admin = new Omniscient_SubAdmin_Model();
     #            $values = PortfolioInformation_GlobalSummary_Model::GetTotalsFromListViewID(1353);
     /*
     if($currentUserModel->isAdminUser() || $sub_admin->HasSubAdminAccess("PortfolioInformation") == "yes"){
     $values = $global_summary->getAdminSummaryValues($request);
     //                $pie_values = $global_summary->getFilterPie($request);
     }
     else{
     $values = $global_summary->getNonAdminSummaryValues($request);
     //                $pie_values = $global_summary->getFilterPie($request);
     }
     /*
     foreach($pie_values AS $k => $v){
     $pie[] = array("title"=>$k,
     "value"=>$v);
     }
     $pie = json_encode($pie);*/
    /*
     $viewer = $this->getViewer($request);
     
     //            $viewer->assign("PIE", $pie);
     $as_of = date('m-d-Y', strtotime('last day of previous month'));
     $viewer->assign("AS_OF", $as_of);
     #            $viewer->assign('GLOBAL_SUMMARY', $values);
     #            $viewer->assign('RESULT_SUMMARY', $result_values);
     
     return parent::preProcess($request, $display);
     }*/
    /*
     public function process(Vtiger_Request $request) {
     //            $global_summary = PositionInformation_PageSummary_Model::GetTotalsFromListViewID(1353);
     
     $viewer = $this->getViewer ($request);
     $moduleName = $request->getModule();
     $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
     
     $show_global_book = true;
     $customView = new CustomView();
     
     $this->viewName = $request->get('viewname');
     $this->initializeListViewContents($request, $viewer);
     
     /* ===== START : Felipe Project Run Changes ===== */
    
    //$default_view_id = $customView->GetDefaultView("PortfolioInformation");
    /*	        if(empty($this->viewName)){
     //If not view name exits then get it from custom view
     //This can return default view id or view id present in session
     $this->viewName = $customView->getViewId($moduleName);
     }
     $default_view_id = $customView->getViewId($moduleName);
     
     /* ===== END : Felipe Project Run Changes ===== */
    
    
    
    
    ////            if($default_view_id == $this->viewName || $this->viewName == '')
        ///                $show_global_book = false;
    
    /*            $global_summary = new PortfolioInformation_GlobalSummary_Model();
     $result_values = $global_summary->getResultValues($request, $this->generator);
     //            $pie_values = $global_summary->getTrailingFilterPie($request, $this->generator);
     $pie_values = $global_summary->GetTrailingFilterPieTotalsFromListViewID($this->viewName);
     if(is_array($pie_values)){
     foreach($pie_values AS $k => $v){
     $color = PortfolioInformation::GetChartColorForTitle($k);
     if($color)
     $pie[] = array("title"=>$k,
     "value"=>$v,
     "color"=>$color);
     else
     $pie[] = array("title"=>$k,
     "value"=>$v);
     }
     }
     $pie = json_encode($pie);
     
     $revenue_values = $global_summary->GetTrailingRevenueFromListViewID($this->viewName);
     $revenue = json_encode($revenue_values);
     
     //            $asset_values = $global_summary->getFilterAssets($request, $this->generator);
     $asset_values = $global_summary->GetTrailingAUMFromListViewID($this->viewName);
     $assets = json_encode($asset_values);
     
     $q = null;
     $active_values = $global_summary->GetTrailingAccountsCountFromListViewID($this->viewName, $q, 1);
     $new_accounts = $global_summary->GetTrailingNewAcccountsFromListViewID($this->viewName, $q, 0);
     $closed_accounts = $global_summary->GetTrailingClosedAcccountsFromListViewID($this->viewName, $q, 0);
     
     $account_activity = array();
     foreach($active_values AS $k => $v){
     $tmp = array();
     $tmp['date'] = $v['date'];
     $tmp['value'] = $v['value'];
     $tmp['new_accounts'] = $new_accounts[$k]['new_accounts'];
     $tmp['closed_accounts'] = $closed_accounts[$k]['closed_accounts'];
     $account_activity[] = $tmp;
     }
     $active = json_encode($account_activity);
     
     $viewer->assign("SHOW_GLOBAL_BOOK", $show_global_book);
     $viewer->assign('VIEW', $request->get('view'));
     $viewer->assign("RESULT_VALUES", $result_values);
     $viewer->assign('MODULE_MODEL', $moduleModel);
     $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
     $viewer->assign("PIE", $pie);
     $viewer->assign("REVENUE", $revenue);
     $viewer->assign("ASSETS", $assets);
     $viewer->assign("ACTIVE", $active);
     $viewer->view('ListViewContents.tpl', $moduleName);
     }
     
     public function postProcess(Vtiger_Request $request) {
     
     parent::postProcess($request);
     
     }
     
     /*
     * Function to initialize the required data in smarty to display the List View Contents
     */
     public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
         $moduleName = $request->getModule();
         $cvId = $this->viewName;
         $pageNumber = $request->get('page');
         $orderBy = $request->get('orderby');
         $sortOrder = $request->get('sortorder');
         $searchKey = $request->get('search_key');
         $searchValue = $request->get('search_value');
         $operator = $request->get('operator');
         $searchParams = $request->get('search_params');
         $tagParams = $request->get('tag_params');
         $starFilterMode = $request->get('starFilterMode');
         $listHeaders = $request->get('list_headers', array());
         $tag = $request->get('tag');
         $requestViewName = $request->get('viewname');
         $tagSessionKey = $moduleName.'_TAG';
         
         if(!empty($requestViewName) && empty($tag)) {
             unset($_SESSION[$tagSessionKey]);
         }
         
         if(empty($tag)) {
             $tagSessionVal = Vtiger_ListView_Model::getSortParamsSession($tagSessionKey);
             if(!empty($tagSessionVal)) {
                 $tag = $tagSessionVal;
             }
         }else{
             Vtiger_ListView_Model::setSortParamsSession($tagSessionKey, $tag);
         }
         
         $listViewSessionKey = $moduleName.'_'.$cvId;
         if(!empty($tag)) {
             $listViewSessionKey .='_'.$tag;
         }
         
         if(empty($cvId)) {
             $customView = new CustomView();
             $cvId = $customView->getViewId($moduleName);
         }
         
         $orderParams = Vtiger_ListView_Model::getSortParamsSession($listViewSessionKey);
         if($request->get('mode') == 'removeAlphabetSearch') {
             Vtiger_ListView_Model::deleteParamsSession($listViewSessionKey, array('search_key', 'search_value', 'operator'));
             $searchKey = '';
             $searchValue = '';
             $operator = '';
         }
         if($request->get('mode') == 'removeSorting') {
             Vtiger_ListView_Model::deleteParamsSession($listViewSessionKey, array('orderby', 'sortorder'));
             $orderBy = '';
             $sortOrder = '';
         }
         if(empty($listHeaders)) {
             $listHeaders = $orderParams['list_headers'];
         }
         
         if(!empty($tag) && empty($tagParams)){
             $tagParams = $orderParams['tag_params'];
         }
         
         if(empty($orderBy) && empty($searchValue) && empty($pageNumber)) {
             if($orderParams) {
                 $pageNumber = $orderParams['page'];
                 $orderBy = $orderParams['orderby'];
                 $sortOrder = $orderParams['sortorder'];
                 $searchKey = $orderParams['search_key'];
                 $searchValue = $orderParams['search_value'];
                 $operator = $orderParams['operator'];
                 if(empty($searchParams)) {
                     $searchParams = $orderParams['search_params'];
                 }
                 
                 if(empty($starFilterMode)) {
                     $starFilterMode = $orderParams['star_filter_mode'];
                 }
             }
         } else if($request->get('nolistcache') != 1) {
             $params = array('page' => $pageNumber, 'orderby' => $orderBy, 'sortorder' => $sortOrder, 'search_key' => $searchKey,
                 'search_value' => $searchValue, 'operator' => $operator, 'tag_params' => $tagParams,'star_filter_mode'=> $starFilterMode,'search_params' =>$searchParams);
             
             if(!empty($listHeaders)) {
                 $params['list_headers'] = $listHeaders;
             }
             Vtiger_ListView_Model::setSortParamsSession($listViewSessionKey, $params);
         }
         if($sortOrder == "ASC"){
             $nextSortOrder = "DESC";
             $sortImage = "icon-chevron-down";
             $faSortImage = "fa-sort-desc";
         }else{
             $nextSortOrder = "ASC";
             $sortImage = "icon-chevron-up";
             $faSortImage = "fa-sort-asc";
         }
         
         if(empty ($pageNumber)){
             $pageNumber = '1';
         }
         
         if(!$this->listViewModel) {
             $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId, $listHeaders);
         } else {
             $listViewModel = $this->listViewModel;
         }
         
         $currentUser = Users_Record_Model::getCurrentUserModel();
         
         $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);
         $linkModels = $listViewModel->getListViewMassActions($linkParams);
         
         if(!$this->pagingModel){
             $pagingModel = new Vtiger_Paging_Model();
             $pagingModel->set('page', $pageNumber);
             //$pagingModel->set('viewid', $request->get('viewname'));
         } else{
             $pagingModel = $this->pagingModel;
         }
         
         if(!empty($orderBy)) {
             $listViewModel->set('orderby', $orderBy);
             $listViewModel->set('sortorder',$sortOrder);
         }
         
         if(!empty($operator)) {
             $listViewModel->set('operator', $operator);
             $viewer->assign('OPERATOR',$operator);
             $viewer->assign('ALPHABET_VALUE',$searchValue);
         }
         if(!empty($searchKey) && !empty($searchValue)) {
             $listViewModel->set('search_key', $searchKey);
             $listViewModel->set('search_value', $searchValue);
         }
         
         if(empty($searchParams)) {
             $searchParams = array();
         }
         if(count($searchParams) == 2 && empty($searchParams[1])) {
             unset($searchParams[1]);
         }
         
         if(empty($tagParams)){
             $tagParams = array();
         }
         
         $searchAndTagParams = array_merge($searchParams, $tagParams);
         
         $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchAndTagParams, $listViewModel->getModule());
         $listViewModel->set('search_params',$transformedSearchParams);
         
         foreach($searchParams as $fieldListGroup){
             foreach($fieldListGroup as $fieldSearchInfo){
                 $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                 $fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
                 $fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
                 $searchParams[$fieldName] = $fieldSearchInfo;
             }
         }
         
         foreach($tagParams as $fieldListGroup){
             foreach($fieldListGroup as $fieldSearchInfo){
                 $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                 $fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
                 $fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
                 $tagParams[$fieldName] = $fieldSearchInfo;
             }
         }
         
         if(!$this->listViewHeaders){
             $this->listViewHeaders = $listViewModel->getListViewHeaders();
         }
         
         if(!$this->listViewEntries){
             $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
         }
         
         if(!$this->noOfEntries) {
             $this->noOfEntries = $pagingModel->get('_listcount');
         }
         if(!$this->noOfEntries) {
             $noOfEntries = count($this->listViewEntries);
         } else {
             $noOfEntries = $this->noOfEntries;
         }
         $viewer->assign('MODULE', $moduleName);
         
         if(!$this->listViewLinks){
             $this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
         }
         $viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);
         
         $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);
         
         $viewer->assign('PAGING_MODEL', $pagingModel);
         if(!$this->pagingModel){
             $this->pagingModel = $pagingModel;
         }
         $viewer->assign('PAGE_NUMBER',$pageNumber);
         
         if(!$this->moduleFieldStructure) {
             $recordStructure = Vtiger_RecordStructure_Model::getInstanceForModule($listViewModel->getModule(), Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
             $this->moduleFieldStructure = $recordStructure->getStructure();
         }
         
         if(!$this->tags) {
             $this->tags = Vtiger_Tag_Model::getAllAccessible($currentUser->id, $moduleName);
         }
         if(!$this->allUserTags) {
             $this->allUserTags = Vtiger_Tag_Model::getAllUserTags($currentUser->getId());
         }
         
         $listViewController = $listViewModel->get('listview_controller');
         $selectedHeaderFields = $listViewController->getListViewHeaderFields();
         
         $viewer->assign('ORDER_BY',$orderBy);
         $viewer->assign('SORT_ORDER',$sortOrder);
         $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
         $viewer->assign('SORT_IMAGE',$sortImage);
         $viewer->assign('FASORT_IMAGE',$faSortImage);
         $viewer->assign('COLUMN_NAME',$orderBy);
         $viewer->assign('VIEWNAME',$this->viewName);
         
         $viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
         $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
         $viewer->assign('LIST_HEADER_FIELDS', json_encode(array_keys($this->listViewHeaders)));
         $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
         $viewer->assign('TOTAL_ENTRIES_COUNT', $listViewModel->getListViewCount());
         $viewer->assign('MODULE_FIELD_STRUCTURE', $this->moduleFieldStructure);
         $viewer->assign('SELECTED_HEADER_FIELDS', $selectedHeaderFields);
         $viewer->assign('TAGS', $this->tags);
         $viewer->assign('ALL_USER_TAGS', $this->allUserTags);
         $viewer->assign('ALL_CUSTOMVIEW_MODEL', CustomView_Record_Model::getAllFilterByModule($moduleName));
         $viewer->assign('CURRENT_TAG',$tag);
         $appName = $request->get('app');
         if(!empty($appName)){
             $viewer->assign('SELECTED_MENU_CATEGORY',$appName);
         }
         if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
             if(!$this->listViewCount){
                 $this->listViewCount = $listViewModel->getListViewCount();
             }
             $totalCount = $this->listViewCount;
             $pageLimit = $pagingModel->getPageLimit();
             $pageCount = ceil((int) $totalCount / (int) $pageLimit);
             
             if($pageCount == 0){
                 $pageCount = 1;
             }
             $viewer->assign('PAGE_COUNT', $pageCount);
             $viewer->assign('LISTVIEW_COUNT', $totalCount);
         }
         $viewer->assign('LIST_VIEW_MODEL', $listViewModel);
         //      $viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
         $viewer->assign('IS_CREATE_PERMITTED', $listViewModel->getModule()->isPermitted('CreateView'));
         $viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
         $viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
         
         $this->generator = $listViewModel->get('query_generator');
         
         $viewer->assign('SEARCH_DETAILS', $searchParams);
         $viewer->assign('TAG_DETAILS', $tagParams);
         $viewer->assign('NO_SEARCH_PARAMS_CACHE', $request->get('nolistcache'));
         $viewer->assign('STAR_FILTER_MODE',$starFilterMode);
         $viewer->assign('VIEWID', $cvId);
         
         $viewer->assign('REQUEST_INSTANCE',$request);
         
         $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
         if($moduleModel->isQuickPreviewEnabled()){
             $viewer->assign('QUICK_PREVIEW_ENABLED', 'true');
         }
         
         $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
         $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
         
         global $adb;
         
         $query = $this->generator->getQuery();
         
         $totalQuery = explode('FROM', $query);
         
         $totalQuery[0] = 'SELECT count(*) as totalaccounts, SUM(vtiger_portfolioinformation.total_value) as totalassests';
         
         $finQuery = implode(' FROM ',$totalQuery);

         $finQuery = str_replace("LEFT JOIN vtiger_pc_account_custom ON vtiger_portfolioinformation.portfolioinformationid = vtiger_pc_account_custom.account_number", "", $finQuery);

         $queryResult = $adb->pquery($finQuery);
         
         $totalAccounts = '0';
         
         $totalAssests = '0';
         
         if($adb->num_rows($queryResult)){
             $totalAccounts = $adb->query_result($queryResult,0,'totalaccounts');
             $totalAssests = $adb->query_result($queryResult,0,'totalassests');
         }
         
         $viewer->assign('TOTAL_ACCOUNTS', $totalAccounts);
         $viewer->assign('TOTAL_ASSESTS', $totalAssests);
         $viewer->assign('USER_MODEL', $currentUser);
         
     }
}
?>