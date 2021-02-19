<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_ReportPdf_View extends Vtiger_MassActionAjax_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showSelectReportForm');
    }
    
    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    
    function showSelectReportForm(Vtiger_Request $request) {
        
        global $current_user;
        $moduleName = $request->getModule();
        
        $viewer = $this->getViewer($request);
        $selectedIds = $this->getRecordsListFromRequest($request);
        $excludedIds = $request->get('excluded_ids');
        $cvId = $request->get('viewname');
        
        $user = Users_Record_Model::getCurrentUserModel();
        $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
        
        $viewer->assign('VIEWNAME', $cvId);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('USER_MODEL', $user);
        $viewer->assign('PHONE_FIELDS', $phoneFields);
        
        $searchKey = $request->get('search_key');
        
        $searchValue = $request->get('search_value');
        
        $operator = $request->get('operator');
        
        if(!empty($operator)) {
            $viewer->assign('OPERATOR',$operator);
            $viewer->assign('ALPHABET_VALUE',$searchValue);
            $viewer->assign('SEARCH_KEY',$searchKey);
        }
        
        $searchParams = $request->get('search_params');
        
        if(!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS',$searchParams);
        }
        
        $assetOptions = PortfolioInformation_Module_Model::GetReportSelectionOptions("asset_allocation");
        $viewer->assign('ASSET_DATE_OPTIONS',$assetOptions);
        
        $ghOptions = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh_report");
        $viewer->assign('GH_DATE_OPTIONS',$ghOptions);
        
        $gh2Options = PortfolioInformation_Module_Model::GetReportSelectionOptions("gh2_report");
        $viewer->assign('GH2_DATE_OPTIONS',$gh2Options);
        
        $start_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['start'];
        $end_date = PortfolioInformation_Module_Model::ReportValueToDate("ytd", false)['end'];
        $asset_end_date = PortfolioInformation_Module_Model::ReportValueToDate("current")['end'];
        
        $viewer->assign('START_DATE',$start_date);
        $viewer->assign('END_DATE',$end_date);
        $viewer->assign('ASSET_END_DATE',$asset_end_date);
        
        $currentUserEmail = $current_user->email1;
        
        $viewer->assign('USER_EMAIL', $currentUserEmail);
        
        echo $viewer->view('ReportPdf.tpl', 'PortfolioInformation', true);
        
    }
    
    
}
