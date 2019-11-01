<?php

class Vtiger_Billing_View extends Vtiger_Popup_View {
    function process(Vtiger_Request $request) {
        //$records = $request->get('records');
        //$records = explode(',', $records);
        
        $records = $this->getRecordsListFromRequest($request);
        
        $module = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($module);
        $fieldModels = $moduleModel->getFields();
        $as_of = date("m/d/Y");
        $default_file = 'billing_' . date("m_d_Y");
        $tmp_custodians = array();
        $tmp_rep = array();
        $tmp_omni = array();

        $current_date = date("Y-m-d");
        foreach($records as $record) {
            $model = Vtiger_Record_Model::getInstanceById($record);
            $tmp_custodians[] = $model->getDisplayValue("origination");
            $tmp_rep[] = $model->getDisplayValue("production_number");
            $tmp_omni[] = $model->getDisplayValue("omniscient_control_number");
            $data = $model->getData();

            $start_date = date("Y-m-d", strtotime("-" . $data['periodicity'] . " Months"));
            $data['in_arrears'] = Vtiger_Billing_Model::CalculateArrearBilling($data['account_number'],
                $start_date,
                $current_date,
                $data['annual_fee_percentage'],
                $data['periodicity'],$extra);
            $model->setData($data);
            $recordModels[] = $model;
        }
//        $recordModelsStored = json_encode($recordModels);
        $custodians = array_unique($tmp_custodians);
        $rep = array_unique($tmp_rep);
        $omni = array_unique($tmp_omni);

        $viewer = $this->getViewer($request);
        $viewer->assign('DEFAULT_FILE', $default_file);
        $viewer->assign('RECORDS', $records);
        $viewer->assign('RECORDMODELS', $recordModels);
        $viewer->assign("CUSTODIANS", $custodians);
        $viewer->assign("REP_CODES", $rep);
        $viewer->assign("AS_OF", $as_of);
        $viewer->assign("OMNI_CODES", $omni);
        $viewer->assign('FIELDS', $fieldModels);
        $viewer->assign('MODULE', $module);
        $viewer->assign('BUTTON_NAME', 'Export');
        $viewer->assign('RECORDMODELS_STORED', base64_encode(serialize($recordModels)));
        $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
        $viewer->view('Billing.tpl', $module);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
#        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(/*
            "~/libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js",
            "~/libraries/amcharts/amcharts/amcharts.js",
            "~/libraries/amcharts/amcharts/serial.js",
            "~/libraries/amcharts/amcharts/pie.js",
            "~/libraries/amcharts/amcharts/plugins/export/export.js",
#            "~/libraries/amcharts/2.0.5/amcharts/javascript/raphael.js",
            "~/libraries/jquery/acollaptable/jquery.aCollapTable.min.js",
#            "modules.PortfolioInformation.resources.DynamicChart",
            "modules.PortfolioInformation.resources.DynamicPie",
            "modules.$moduleName.resources.printing",
            "modules.$moduleName.resources.jqueryIdealforms",
            "modules.$moduleName.resources.OmniOverview",*/
            "~/layouts/v7/modules/Vtiger/resources/Billing.js"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
#        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $jsScriptInstances;
    }
    
    public function getRecordsListFromRequest(Vtiger_Request $request) {
        $cvId = $request->get('viewname');
        $module = $request->get('module');
        if(!empty($cvId) && $cvId=="undefined"){
            $sourceModule = $request->get('sourceModule');
            $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
        }
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        if(!empty($selectedIds) && $selectedIds != 'all') {
            if(!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }
            
            /**
             *  Mass action on Documents if we select particular folder is applying on all records irrespective of
             *  seleted folder
             */
            if ($module == 'Documents') {
                $customViewModel->set('folder_id', $request->get('folder_id'));
                $customViewModel->set('folder_value', $request->get('folder_value'));
            }
            
            $customViewModel->set('search_params',$request->get('search_params'));
            return $customViewModel->getRecordIds($excludedIds,$module);
        }
    }
    
}
