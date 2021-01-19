<?php

class Vtiger_GraphFilterList_View extends Vtiger_Index_View{
    
	function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);

		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName);
		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$this->viewName = $request->get('viewname');
		if(empty($this->viewName)){
			//If not view name exits then get it from custom view
			//This can return default view id or view id present in session
			$customView = new CustomView();
			$this->viewName = $customView->getViewId($moduleName);
		}

		$quickLinkModels = $listViewModel->getSideBarLinks($linkParams);
		$viewer->assign('QUICK_LINKS', $quickLinkModels);
		$this->initializeListViewContents($request, $viewer);
		$viewer->assign('VIEWID', $this->viewName);

		$search_params = $request->get('search_params');
        if(!empty($search_params))
			$viewer->assign('FILTER_STATEMENT', $this->getFilterStatement($search_params, $moduleName));
			
		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	function preProcessTplName(Vtiger_Request $request) {
		return 'GraphFilterListViewPreProcess.tpl';
	}


	function process (Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$this->viewName = $request->get('viewname');

		$this->initializeListViewContents($request, $viewer);
		$viewer->assign('VIEW', $request->get('view'));
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
		
		$search_params = $request->get('search_params');
        $viewer->assign('SEARCH_PARAMS', $search_params);
			
		$viewer->view('GraphFilterListViewContents.tpl', 'Vtiger');
	}

	function postProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		$viewer->view('ListViewPostProcess.tpl', $moduleName);
		parent::postProcess($request);
	}
	
	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		
		$jsFileNames = array(
		    'modules.Vtiger.resources.List',
		    "modules.$moduleName.resources.List",
		    'modules.Vtiger.resources.ListSidebar',
		    "modules.$moduleName.resources.ListSidebar",
		   
		    'modules.Vtiger.resources.GraphFilterList',
		    
		    "libraries.jquery.ckeditor.ckeditor",
		    "libraries.jquery.ckeditor.adapters.jquery",
		    "modules.Vtiger.resources.CkEditor",
		    //for vtiger7
		    "modules.Vtiger.resources.MergeRecords",
		    "~layouts/v7/lib/jquery/Lightweight-jQuery-In-page-Filtering-Plugin-instaFilta/instafilta.min.js",
		    'modules.Vtiger.resources.Tag',
		    "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/floatThead/jquery.floatThead.js",
		    "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/js/perfect-scrollbar.jquery.js"
			
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
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
		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
		}

		if(empty ($pageNumber)){
			$pageNumber = '1';
		}

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);
		$linkModels = $listViewModel->getListViewMassActions($linkParams);

		$linkModels = $this->getListViewMassActions($linkModels);
		
		if(!$this->pagingModel){
    		$pagingModel = new Vtiger_Paging_Model();
    		$pagingModel->set('page', $pageNumber);
    		$pagingModel->set('viewid', $request->get('viewname'));
		} else{
		    $pagingModel = $this->pagingModel;
		}
		
		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder',$sortOrder);
		}

		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		$operator = $request->get('operator');
		if(!empty($operator)) {
			$listViewModel->set('operator', $operator);
			$viewer->assign('OPERATOR',$operator);
			$viewer->assign('ALPHABET_VALUE',$searchValue);
		}
		if(!empty($searchKey) && !empty($searchValue)) {
			$listViewModel->set('search_key', $searchKey);
			$listViewModel->set('search_value', $searchValue);
		}

        $searchParmams = $request->get('search_params');
        if(empty($searchParmams)) {
            $searchParmams = array();
        }
		
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
        $listViewModel->set('search_params',$transformedSearchParams);


        //To make smarty to get the details easily accesible
        foreach($searchParmams as $fieldListGroup){
            foreach($fieldListGroup as $fieldSearchInfo){
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
                $searchParmams[$fieldName] = $fieldSearchInfo;
			}
		}
        
        if($request->get('contactage') && $moduleName == 'Transactions'){
            $listViewModel->set('contactage', $request->get('contactage'));
            $listViewModel->set('start', $request->get('start'));
            $listViewModel->set('end', $request->get('end'));
            
            $viewer->assign('CONTACTAGE', $request->get('contactage'));
            $viewer->assign('STARTDATE', $request->get('start'));
            $viewer->assign('ENDDATE', $request->get('end'));
        }
		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$noOfEntries = count($this->listViewEntries);

		$viewer->assign('MODULE', $moduleName);

		$viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

		$viewer->assign('PAGING_MODEL', $pagingModel);
		if(!$this->pagingModel){
		    $this->pagingModel = $pagingModel;
		}
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);

		$viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);

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
		$viewer->assign('GROUPS_IDS', Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId()));
		$viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
		$viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('VIEWID', $cvId);
        
        if($moduleName == 'PortfolioInformation'){
            
            global $adb;
            
            $queryGenerator = $listViewModel->get('query_generator');
            $searchParams = $transformedSearchParams;
            if(empty($searchParams)) {
                $searchParams = array();
            }
            $glue = "";
            if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
                $glue = QueryGenerator::$AND;
            }
            $queryGenerator->parseAdvFilterList($searchParams, $glue);
            
            $searchKey = $searchKey;
            $searchValue = $searchValue;
            $operator = $operator;
            if(!empty($searchKey)) {
                $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
            }
            
            $query = $queryGenerator->getQuery();
            
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

	/**
	 * Function returns the number of records for the current filter
	 * @param Vtiger_Request $request
	 */
	function getRecordsCount(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		$count = $this->getListViewCount($request);

		$result = array();
		$result['module'] = $moduleName;
		$result['viewname'] = $cvId;
		$result['count'] = $count;

		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult($result);
		$response->emit();
	}

	/**
	 * Function to get listView count
	 * @param Vtiger_Request $request
	 */
	function getListViewCount(Vtiger_Request $request){
		$moduleName = $request->getModule();
		$cvId = $request->get('viewname');
		if(empty($cvId)) {
			$cvId = '0';
		}

		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');

		$listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);

        $searchParmams = $request->get('search_params');
        $listViewModel->set('search_params',$this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule()));

		$listViewModel->set('search_key', $searchKey);
		$listViewModel->set('search_value', $searchValue);
		$listViewModel->set('operator', $request->get('operator'));

		$count = $listViewModel->getListViewCount();

		return $count;
	}



	/**
	 * Function to get the page count for list
	 * @return total number of pages
	 */
	function getPageCount(Vtiger_Request $request){
		$listViewCount = $this->getListViewCount($request);
		$pagingModel = new Vtiger_Paging_Model();
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $listViewCount / (int) $pageLimit);

		if($pageCount == 0){
			$pageCount = 1;
		}
		$result = array();
		$result['page'] = $pageCount;
		$result['numberOfRecords'] = $listViewCount;
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}


    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel) {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
    }
	
    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            "~layouts/".Vtiger_Viewer::getDefaultLayoutName()."/lib/jquery/perfect-scrollbar/css/perfect-scrollbar.css",
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
    
	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams, $moduleModel
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($links) {
		
		$supportedMassActions = array('LBL_EDIT', 'LBL_DELETE', 'LBL_ADD_COMMENT', 'LBL_SEND_EMAIL', 'LBL_TRANSFER_OWNERSHIP');
		
		if(isset($links['LISTVIEWMASSACTION']) && !empty($links['LISTVIEWMASSACTION'])){
			
			foreach($links['LISTVIEWMASSACTION'] as $link){
				
				if(!in_array($link->linklabel, $supportedMassActions)) continue;
				
				$linkURL = $link->getUrl();
				
				if(stripos($linkURL, 'javascript:') === 0){
					
					$linkUrl = "javascript:Vtiger_GraphFilterList".substr($linkURL,stripos($linkURL, '_Js.'));
					
					$link->set("linkurl",decode_html($linkUrl));
				}
			}
		}
		
		return $links;
	}

	function getFilterStatement($filters, $moduleName){
		
		$filterText = "";
		
		if(!empty($filters)){
			
			$andFilters = $filters[0];
			
			if(isset($filters[1]) && !empty($filters[1]))
				$orFilters = $filters[1];
			else
				$orFilters = array();
			
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
			
			$moduleFields = $moduleModel->getFields();
			
			$customView = new CustomView();
			$dateSpecificConditions = $customView->getStdFilterConditions();

			if($moduleName == 'Calendar'){
				$advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
			} else{
				$advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
			}
			
			$advanced_filter_options = Vtiger_Field_Model::getAdvancedFilterOptions();
			
			$dateFilters = Vtiger_Field_Model::getDateFilterTypes();
			foreach($dateFilters as $comparatorKey => $comparatorInfo) {
				$comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
				$comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
				$comparatorInfo['label'] = vtranslate($comparatorInfo['label'],$moduleName);
				$dateFilters[$comparatorKey] = $comparatorInfo;
			}
        
			$filterStatement = array();
		
			foreach($andFilters as $condition){
				
				$fieldModel = $moduleFields[$condition[0]];
				
				$fieldType = $fieldModel->getFieldDataType();
				
				if($fieldType == 'boolean'){
					
					if($condition[2] == 0)
						$condition_operator = "is disabled";
					else if($condition[2] == 0)
						$condition_operator = "is enabled";
					
					$condition[2] = "";
						
				} else {	
					
					if(in_array($condition[1], $dateSpecificConditions))
						$condition_operator = " between ";
					else
						$condition_operator = vtranslate($advanced_filter_options[$condition[1]], $moduleName);
				}
				
				if($condition[1] == 'y' || $condition[1] == 'ny')
					$condition[2] = "";
				
				if(is_array($condition[2]))	
				    $condition[2] = implode(',',$condition[2]);
					
				$filterStatement[] = vtranslate($fieldModel->get('label'), $moduleName)." ".$condition_operator." ".$condition[2];
			}
		
			if(!empty($filterStatement))
				$filterText = implode(" and ",$filterStatement);
			
			$filterStatement = array();
				
			if(!empty($orFilters)){
				
				foreach($orFilters as $condition){
						
					$fieldModel = $moduleFields[$condition[0]];

					$fieldType = $fieldModel->getFieldDataType();
					
					if($fieldType == 'boolean'){
						if($condition[2] == 0)
							$condition_operator = "is disabled";
						else if($condition[2] == 1)
							$condition_operator = "is enabled";
						
						$condition[2] = "";
						
					} else {	
						
						if(in_array($condition[1], $dateSpecificConditions))
							$condition_operator = " between ";
						else
							$condition_operator = vtranslate($advanced_filter_options[$condition[1]], $moduleName);
					}
					
					if($condition[1] == 'y' || $condition[1] == 'ny')
						$condition[2] = "";
				
					$filterStatement[] = vtranslate($fieldModel->get('label'), $moduleName)." ".$condition_operator." ".$condition[2];
				}
				
				if(!empty($filterStatement))
					$filterText .= " and " . implode(" or ",$filterStatement);
			
			}
		}
		
		return $filterText;
	}
}