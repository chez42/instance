<?php
class PositionRollup_List_View extends Vtiger_List_View {
        var $generator;//The query generator
        /**
         * Calculates the global summary for the list view
         * @global type $current_user
         * @param Vtiger_Request $request
         * @param type $display
         * @return type
         */
        public function preProcess(Vtiger_Request $request, $display = true) {
            parent::preProcess($request, false);
            $viewer = $this->getViewer($request);
            if($request->get('custom_search_override')){
                $listViewModel = Vtiger_ListView_Model::getInstance("PositionRollup", $request->get('viewname'));
                $c = $listViewModel->getListViewCount();
                $viewer->assign('CUSTOM_SEARCH_OVERRIDE', 1);
                $viewer->assign('RECORD_COUNT', $c);
                $viewer->assign('VIEWNAME', $request->get('viewname'));
                $viewer->assign('SEARCH', $request->get('search'));
            }else{
                $viewer->assign('RECORD_COUNT', '');
            }
            $as_of = date('m-d-Y', strtotime('last day of previous month'));
            $viewer->assign("AS_OF", $as_of);

            $listViewModel = Vtiger_ListView_Model::getInstance("PortfolioInformation", 1353);
            $this->generator = $listViewModel->get('query_generator');
            $this->preProcessDisplay($request);
//            echo $request->set('module', 'PositionRollup');
        }

        public function process(Vtiger_Request $request) {
//            echo $request->set('module', 'PositionRollup');
            $viewer = $this->getViewer ($request);
            $moduleName = $request->getModule();
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $this->viewName = $request->get('viewname');

            $this->initializeListViewContents($request, $viewer);
            $viewer->assign('VIEW', $request->get('view'));
            $viewer->assign('MODULE_MODEL', $moduleModel);
            $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
            
            $global_summary = new PortfolioInformation_GlobalSummary_Model();
            $pie_values = $global_summary->GetTrailingFilterPieTotalsFromListViewID(1353);
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

            $listViewModel = Vtiger_ListView_Model::getInstance("PositionInformation", $request->get('viewname'));           
            $generator = $listViewModel->get('query_generator');
            $id = $request->get('viewname');

            if(!$id)
                $id = 897;
            $asset_pie_result = PositionRollup::GetListPieFromFilterID(1353);
            if(is_array($asset_pie_result)){
                foreach($asset_pie_result AS $k => $v){
                    $color = PortfolioInformation::GetChartColorForTitle($k);
                    if($color)
                        $asset_pie[] = array("title"=>$k, 
                                       "value"=>$v,
                                       "color"=>$color);
                    else
                        $asset_pie[] = array("title"=>$k,
                                       "value"=>$v);
                }
            }
            $asset_pie = json_encode($asset_pie);
            
            $asset_values = $global_summary->GetTrailingAUMFromListViewID(1353);
            $assets = json_encode($asset_values);

            $viewer->assign("PIE", $pie);
            $viewer->assign("ASSET_PIE", $asset_pie);
            $viewer->assign("ASSETS", $assets);

            $viewer->view('ListViewContents.tpl', $moduleName);
        }
        
        public function postProcess(Vtiger_Request $request) {
            parent::postProcess($request);
        }
                
        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
			$headerScriptInstances = parent::getHeaderScripts($request);
			$moduleName = $request->getModule();
			$jsFileNames = array(
				//"~/libraries/amcharts/2.9.0/amcharts/amcharts.js",
				//"~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
				
				"~/libraries/amcharts/amcharts/amcharts.js",
				"~/libraries/amcharts/amcharts/pie.js",
				"~/libraries/amcharts/amcharts/serial.js",
				
				"modules.PortfolioInformation.resources.portfolioinformation",
				"modules.$moduleName.resources.positionrollup", // . = delimiter
			);
			$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
			$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
			return $headerScriptInstances;
        }
        
}
?>