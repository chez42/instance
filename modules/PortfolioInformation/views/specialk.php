<?php

class PortfolioInformation_SpecialK_View extends Vtiger_List_View {
        var $generator;//The query generator
        /**
         * Calculates the global summary for the list view
         * @global type $current_user
         * @param Vtiger_Request $request
         * @param type $display
         * @return type
         */
        public function preProcess(Vtiger_Request $request, $display = true) {
            $currentUserModel = Users_Record_Model::getCurrentUserModel();            
            $global_summary = new PortfolioInformation_GlobalSummary_Model();
            $sub_admin = new Omniscient_SubAdmin_Model();
            $values = PortfolioInformation_GlobalSummary_Model::GetTotalsFromListViewID(1353);
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

            $viewer = $this->getViewer($request);
            
//            $viewer->assign("PIE", $pie);
            $as_of = date('m-d-Y', strtotime('last day of previous month'));
            $viewer->assign("AS_OF", $as_of);
            $viewer->assign('GLOBAL_SUMMARY', $values);
            $viewer->assign('RESULT_SUMMARY', $result_values);
            
            return parent::preProcess($request, $display);
        }

        public function process(Vtiger_Request $request) {
//            $global_summary = PositionInformation_PageSummary_Model::GetTotalsFromListViewID(1353);
            
            $viewer = $this->getViewer ($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

            $show_global_book = true;
            $customView = new CustomView();

            $this->viewName = $request->get('viewname');
            $this->initializeListViewContents($request, $viewer);

            $default_view_id = $customView->GetDefaultView("PortfolioInformation");
////            if($default_view_id == $this->viewName || $this->viewName == '')
///                $show_global_book = false;

            $global_summary = new PortfolioInformation_GlobalSummary_Model();
            $result_values = $global_summary->getResultValues($request, $this->generator);
            $pie_values = $global_summary->getTrailingFilterPie($request, $this->generator);
            if(is_array($pie_values)){
                foreach($pie_values AS $k => $v){
                    $pie[] = array("title"=>$k, 
                                   "value"=>round($v,0));
                }
            }
            $pie = json_encode($pie);
            
            $revenue_values = $global_summary->getFilterRevenue($request, $this->generator);
            $revenue = json_encode($revenue_values);
            
            $asset_values = $global_summary->getFilterAssets($request, $this->generator);
            $assets = json_encode($asset_values);
            
            $viewer->assign("SHOW_GLOBAL_BOOK", $show_global_book);
            $viewer->assign("SCRIPTS_CUSTOM", $this->getCustomScripts($request));
            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign("RESULT_VALUES", $result_values);
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->assign("PIE", $pie);
            $viewer->assign("REVENUE", $revenue);
            $viewer->assign("ASSETS", $assets);
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

		$linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'), 'CVID'=>$cvId);
		$linkModels = $listViewModel->getListViewMassActions($linkParams);

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);

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
		if(!$this->listViewHeaders){
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
		}
		if(!$this->listViewEntries){
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		$noOfEntries = count($this->listViewEntries);

		$viewer->assign('MODULE', $moduleName);

		if(!$this->listViewLinks){
			$this->listViewLinks = $listViewModel->getListViewLinks($linkParams);
		}
                                
		$viewer->assign('LISTVIEW_LINKS', $this->listViewLinks);

		$viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);

		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('COLUMN_NAME',$orderBy);

		$viewer->assign('LISTVIEW_ENTIRES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
                $viewer->assign('TOTAL_ENTRIES_COUNT', $listViewModel->getListViewCount());
                
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

		$viewer->assign('IS_MODULE_EDITABLE', $listViewModel->getModule()->isPermitted('EditView'));
		$viewer->assign('IS_MODULE_DELETABLE', $listViewModel->getModule()->isPermitted('Delete'));
                
                $this->generator = $listViewModel->get('query_generator');
	}
        
	// Injecting custom javascript resources
	public function getCustomScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$jsFileNames = array(
			// "~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
			// "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
			
			"~/libraries/amcharts/amcharts/amcharts.js",
			"~/libraries/amcharts/amcharts/pie.js",
			"~/libraries/amcharts/amcharts/serial.js",
		
			"modules.$moduleName.resources.portfolioinformation", // . = delimiter
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $jsScriptInstances;
	}
}
?>
